# CHECKPOINT — Krok 10: presná rezervácia prvého prijatia

## Stav

```text
POSLEDNÝ_UZAVRETÝ_KROK=KROK_10
KROK_10=SPLNENÉ
JEDINÝ_NASLEDUJÚCI_KROK=KROK_11
KROK_11_GATE=CLOSED
```

## Autoritatívny zdroj

```text
repozitár = slapiar/METODIKA
vetva = main
technický koreň = /codei
```

## Inicializačný dôkaz

- `postupy/WORK/INI/2026-07-25_08-14_INI_Dokoncenie_Kroku_10_po_STOP.md`
- všetkých deväť bodov: `ÁNO`
- `GATE=OPEN`

## Uzavretý výsledok

Koreňová chyba bola odstránená v jedinom funkčnom squash commite:

```text
c90ae562a859de9fe0b2174f39e924c7f7bc6a4e
```

Zmena zabezpečuje:

```text
prvý tok = RESERVATION_CREATED
druhý tok = ALREADY_EXISTS
vlastník presnej rezervácie = prvý tok
druhý počiatočný history run = nevznikne
cleanup = potvrdený
```

## Dotknuté súbory

- `codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php`
- `codei/app/Commands/ReproduceFirstAcceptanceRootCause.php`
- `.github/workflows/krok-7-root-cause-reproduction.yml`

Výsledné bloby na `main`:

```text
f247d5e7289ad3dec23560a3bc3c1369ec22ea98
4a0fc26b476b0622aedb2e423009263c533ccb43
934944dc0e4ab5242d61fc685d5082d8bc4ebc00
```

## Validácia

```text
PR = #2
validovaný SHA = ca765c737bc5dffb90361fb89b22f5b00e6b97f8
workflow run = 30148480939
job = 89654680309
výsledok = success
```

Prešli:

- PHP 8.4 a Composer 2,
- uzamknuté závislosti,
- syntax dotknutých PHP súborov,
- `FirstAcceptanceServiceTest`: 2 testy a 4 tvrdenia,
- izolovaná MariaDB 11.4 a InnoDB,
- migrácie M1–M8,
- overenie otázkovej schémy,
- regresia s dvoma nezávislými MySQLi spojeniami,
- explicitné výsledkové markery,
- databázový cleanup a odstránenie containera.

## Pracovný záznam

- `postupy/WORK/2026-07-25_09-01_Krok_10_Najmensia_funkcna_oprava_rezervacie.md`

## Hranice zachované

Nezmenili sa:

- databázová schéma,
- diagnostické rozlíšenie Kroku 8,
- `DiagnosticsConcurrencyRunStore::load()`,
- timeoutová poistka,
- UI,
- release skripty,
- produkčné prostredie.

## Rollback

Vrátiť jediný funkčný commit:

```text
c90ae562a859de9fe0b2174f39e924c7f7bc6a4e
```

## Otvorený rozsah

Krok 10 nepotvrdzuje úplnú lokálnu, integračnú a end-to-end Validáciu celej diagnostickej cesty. Tá patrí výhradne do Kroku 11.

## Jediný nasledujúci povolený úkon

```text
Vytvoriť nový INI záznam Kroku 11
→ znovu načítať aktuálny vzdialený stav
→ prakticky overiť deväť bodov
→ GATE zostáva CLOSED, kým všetko nie je ÁNO
```

Bez samostatného `GATE=OPEN` Kroku 11 sa nesmie spustiť jeho testovacia matica, integračné testy, end-to-end scenár ani ďalší návrh.
