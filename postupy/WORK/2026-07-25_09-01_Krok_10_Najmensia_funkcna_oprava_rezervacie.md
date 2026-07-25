# Krok 10 — Najmenšia funkčná oprava koreňovej príčiny

## Stav

```text
SPLNENÉ
```

## Inicializácia

- `postupy/WORK/INI/2026-07-25_08-14_INI_Dokoncenie_Kroku_10_po_STOP.md`
- `GATE=OPEN`

## Predmet

Opraviť iba potvrdenú chybu prvého prijatia, pri ktorej druhý tok s rovnakou `REQUEST_REFERENCE` a odlišnou `derivation_reference` pri `DBDebug=false` pokračoval nesprávne ako `CREATED` a následne zlyhal pri zakladaní počiatočného history runu.

## Náprava chybného tvrdenia o prostredí

Tvrdenie o chýbajúcom Composer-i a DNS sa týkalo interného pomocného sandboxu asistenta, nie projektového Codespace ani autoritatívneho izolovaného testovacieho prostredia. Pre rozhodovanie o projekte bolo neplatné a bolo výslovne zrušené v INI zázname.

Praktický prístup a prostredie boli obnovené rovnakou cestou ako pri Kroku 7:

```text
pôvodný workflow run = 30089261354
opakovaný job overenia prostredia = 89652985988
výsledok = success
```

Overené boli PHP 8.4, Composer 2, MySQLi, MariaDB 11.4, uzamknuté závislosti, migrácie M1–M8, schéma, izolácia, dve databázové spojenia, rollback a cleanup.

## Potvrdená príčina

V `RequestReferenceRepository::reserveFirstAcceptance()` boli súčasne prítomné dva nedostatky:

1. návratová hodnota rezervačného `insert()` sa nekontrolovala,
2. spätné overenie po inserte používalo iba `REQUEST_REFERENCE`.

Pri `DBDebug=false` preto duplicitný kľúč mohol vrátiť `false` bez výnimky. Druhý tok následne načítal rezerváciu prvého toku iba podľa `REQUEST_REFERENCE`, označil výsledok ako `CREATED` a pokúsil sa vytvoriť history run bez vlastnej presnej rezervácie `REQUEST_REFERENCE + derivation_reference`.

## Najmenší funkčný zásah

### `codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php`

- výsledok `insert()` sa explicitne kontroluje,
- chyba duplicitného kľúča MariaDB/MySQL `1062` sa vracia ako `ALREADY_EXISTS`,
- iná databázová chyba sa nezamaskuje a vyvolá `DatabaseException`,
- výsledok `CREATED` je povolený iba po spätnej zhode:

```text
REQUEST_REFERENCE
+ payload_fingerprint
+ derivation_reference
```

- existujúca rezervácia sa vracia jedinou pomocnou cestou `alreadyExistingReservation()`.

### `codei/app/Commands/ReproduceFirstAcceptanceRootCause.php`

Pôvodná reprodukcia chyby bola zmenená na regresný dôkaz:

- spojenie A musí skončiť `RESERVATION_CREATED`,
- spojenie B musí skončiť `ALREADY_EXISTS`,
- rezervácia vrátená spojeniu B musí patriť presne toku A,
- zostáva jediná rezervácia, jediný počiatočný history run a dve doménové väzby,
- druhý tok nevytvorí vlastné riadky,
- cleanup musí skončiť počtami `0 + 0 + 0`.

### `.github/workflows/krok-7-root-cause-reproduction.yml`

Historický workflow Kroku 7 bol zachovaný na pôvodnej ceste, ale zmenený na trvalú regresnú Validáciu Kroku 10:

- PR a push na `main`,
- PHP 8.4 a Composer 2,
- izolovaná MariaDB 11.4,
- syntax oboch dotknutých PHP súborov,
- cielené jednotkové testy bez nepožadovaného coverage režimu,
- migrácie M1–M8,
- overenie schémy,
- povinné explicitné markery výsledku a cleanupu.

## Validácia

### PR

- PR: `#2` — `Krok 10: presná rezervácia prvého prijatia`
- validovaný head SHA: `ca765c737bc5dffb90361fb89b22f5b00e6b97f8`
- workflow run: `30148480939`
- job: `89654680309`
- výsledok: `success`

Úspešne prešli:

1. inicializácia izolovaného MariaDB containera,
2. checkout presného PR SHA,
3. PHP 8.4, Composer 2 a potrebné rozšírenia,
4. inštalácia uzamknutých závislostí,
5. syntax repository a regresného príkazu,
6. `FirstAcceptanceServiceTest`: 2 testy, 4 tvrdenia,
7. izolovaný databázový cieľ a InnoDB,
8. migrácie M1–M8,
9. overenie otázkovej schémy,
10. regresia prvého prijatia s dvoma nezávislými MySQLi spojeniami,
11. odstránenie testovacích dát a service containera.

Povinné výsledky:

```text
FIRST_OUTCOME=RESERVATION_CREATED
SECOND_OUTCOME=ALREADY_EXISTS
NO_DUPLICATE_INITIAL_HISTORY_RUN=true
SECOND_PARTICIPANT_LEFT_NO_ROWS=true
CLEANUP_CONFIRMED
```

### Diagnostický medzikrok

Prvý PR beh `30148343895` odhalil infraštruktúrne varovanie PHPUnit o chýbajúcom coverage driveri. Oba testy pritom prešli. Diagnostický beh `30148423529` zachoval úplný výstup v artefakte a potvrdil:

```text
Tests: 2
Assertions: 4
OK, but PHPUnit Warnings: 1
No code coverage driver available
```

Workflow bol zosúladený s už nastaveným `coverage: none` použitím `--no-coverage`; test ani jeho kritériá sa nezmäkčili.

## Výsledný commit

Funkčný PR bol zlúčený squash metódou do jediného commitu:

```text
c90ae562a859de9fe0b2174f39e924c7f7bc6a4e
```

Spätné načítanie z `main` potvrdilo výsledné bloby:

```text
RequestReferenceRepository.php = f247d5e7289ad3dec23560a3bc3c1369ec22ea98
ReproduceFirstAcceptanceRootCause.php = 4a0fc26b476b0622aedb2e423009263c533ccb43
krok-7-root-cause-reproduction.yml = 934944dc0e4ab5242d61fc685d5082d8bc4ebc00
```

## Hranice zásahu

Nezmenili sa:

- databázová schéma,
- diagnostické rozlíšenie Kroku 8,
- `DiagnosticsConcurrencyRunStore::load()`,
- timeoutová poistka,
- UI,
- release skripty,
- produkčné prostredie.

## Kritérium úspechu

```text
prvý tok = CREATED
paralelný druhý tok = ALREADY_EXISTS
presná rezervácia zostáva vlastníctvom prvého toku
žiadny druhý počiatočný history run nevzniká
cleanup = potvrdený
KROK_10 = SPLNENÉ
```

## Rollback

Vrátiť jediný funkčný commit:

```text
c90ae562a859de9fe0b2174f39e924c7f7bc6a4e
```

Validačná infraštruktúra môže zostať zachovaná ako regresná ochrana; ak sa vracia aj ona, musí sa vrátiť samostatne a bez zásahu do databázovej schémy či produkcie.

## Nasledujúci povolený krok

```text
Krok 11 — Úplná lokálna a integračná Validácia
```

Krok 11 sa nesmie začať bez vlastného nového INI záznamu a deviatich doložených hodnôt `ÁNO`.
