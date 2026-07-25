# Krok 11 — Analýza a návrh úplnej validačnej zostavy

## Stav

```text
ANALÝZA_DOKONČENÁ=ÁNO
NÁVRH_DOKONČENÝ=ÁNO
IMPLEMENTÁCIA_TESTOVACÍCH_ARTEFAKTOV=ÁNO
VALIDÁCIA=NEVYKONANÁ
```

## Inicializácia

- `postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md`
- `GATE=OPEN`

## Predmet

Určiť presné existujúce pokrytie deviatich povinných blokov Kroku 11, oddeliť použiteľné dôkazy od simulácií a doplniť iba chýbajúce testovacie artefakty bez zmeny produkčných kontrolérov, služieb, databázovej schémy alebo release procesu.

## Kontinuita východiska

Porovnanie funkčného commitu Kroku 10 `c90ae562a859de9fe0b2174f39e924c7f7bc6a4e` s metodickým HEAD pred implementáciou Kroku 11 potvrdilo, že sa nezmenili:

- aplikačný a infraštruktúrny kód,
- existujúce testy,
- migrácie M1–M8,
- `composer.lock`,
- validačné workflowy.

Existujúce dôkazy z Krokov 7 až 10 preto zostávajú použiteľné.

## Analýza deviatich blokov

| Blok | Existujúce pokrytie | Presná medzera pred Krok 11 | Rozhodnutie |
|---:|---|---|---|
| 1. Run store a validator | unit testy save/load, atomickej mutácie, stabilného locku, validácie dokumentu a prechodov; dvojprocesový test bariéry a timeoutu | nový spoločný beh ešte nebol vykonaný | zaradiť existujúce testy bez zmeny produkčného kódu |
| 2. `FirstAcceptanceService` | 2 unit testy/4 tvrdenia; Krok 10 DB regresia `CREATED + ALREADY_EXISTS` | treba spoločný beh na presnom SHA Kroku 11 | zaradiť unit suite a existujúci DB regresný príkaz |
| 3. Diagnostické chybové fázy | `DiagnosticsConcurrencyFailureReporterTest` pokrýva všetkých päť fáz; session test blokuje raw výnimku | spoločný beh ešte nebol vykonaný | zaradiť unit a session suite |
| 4. Session a security endpointy | disabled/missing/wrong token, login form, autorizovaná DB stránka, feature flag, CSRF routes a neplatný participant token | chýbal priamy dôkaz uloženia login session, `/database/run` a odstránenia session pri logout | pridať samostatný session regresný test |
| 5. Integračný DB rollback | príkaz `metodika:verify-first-acceptance-transaction` s úspešnou a úmyselne chybnou vetvou a cleanupom | treba vykonať na izolovanej DB Kroku 11 | zaradiť existujúci príkaz |
| 6. Skutočný súbeh | dvojprocesový file-store test; `metodika:verify-concurrent-first-acceptance` používa dve MySQLi spojenia a `MYSQLI_ASYNC` | asynchrónny DB príkaz ešte nebol súčasťou jedného úplného behu | zaradiť oba existujúce dôkazy |
| 7. `START → HIT A/B → RESULT` | feature test prechádza routami, ale je jednovláknový, participant A je ručne predpripravený a runner/persistence sú mockované | chýba skutočný HTTP server, session cookie, CSRF, dve paralelné HIT požiadavky a reálna MariaDB | pridať izolovaný HTTP E2E shell harness |
| 8. Tombstone a sweep | read-once, redakcia a expirovaný sweep sú pokryté feature testami | chýba potvrdenie v reálnom HTTP E2E toku | HTTP harness overí JSON/lock, temp súbory, read-once, redakciu a sweep |
| 9. Login/database/logout | login a database majú čiastočné feature pokrytie; logout route a implementácia existujú | neexistoval samostatný logout test; login test po presmerovaní manuálne vkladal session | nový session test + reálny HTTP login/database/logout tok |

## Najmenší bezpečný návrh

Vytvoriť iba:

1. `codei/tests/session/DiagnosticsAuthenticationFlowTest.php`,
2. `codei/tests/integration/krok11-http-e2e.sh`,
3. `.github/workflows/krok-11-full-validation.yml`.

Návrh výslovne nemení:

- produkčné kontroléry,
- služby,
- repository adaptéry,
- run-store a validator,
- databázovú schému,
- migrácie,
- release skripty,
- produkciu.

## Implementované testovacie artefakty

### Session regresia

Overuje:

- správny token nastaví oba autorizačné session kľúče,
- `/diagnostics/database/run` odmietne neautorizovanú session,
- autorizovaná vetva `run` presmeruje na databázovú diagnostiku,
- logout odstráni oba session kľúče a nasledujúci prístup skončí 404.

### Skutočný HTTP E2E harness

Overuje:

- spustenie skutočného CodeIgniter servera,
- login cez serverový token, CSRF a cookie jar,
- databázovú stránku `PRIPRAVENE`,
- START cez routovaný produkčný kontrolér,
- fyzický JSON a stabilný lock,
- skutočne paralelné HTTP HIT A/B s rovnakou session cookie,
- reálny `FirstAcceptanceService` nad izolovanou MariaDB,
- výsledky `CREATED + ALREADY_EXISTS`,
- `DB_UNIQUENESS=true`,
- `CLEANUP=true`,
- `STATE=COMPLETED_SUCCESS`,
- read-once a redakciu tombstone,
- neprítomnosť temp súborov,
- sweep JSON aj locku,
- nulové databázové riadky,
- logout a následnú 404,
- núdzový cleanup pri každom ukončení.

### Spoločný workflow

Workflow vykoná:

1. presný checkout SHA,
2. PHP 8.4, Composer 2, `pcntl`, MySQLi a `MYSQLI_ASYNC`,
3. izolovanú MariaDB 11.4,
4. migrácie M1–M8 a schému,
5. počiatočné nulové počty,
6. celé unit a session suite,
7. dvojprocesový file-store test,
8. integračný transakčný rollback,
9. asynchrónny súbežný DB test,
10. regresiu presnej rezervácie Kroku 10,
11. skutočný HTTP E2E,
12. finálne nulové DB počty a neprítomnosť run-store artefaktov.

## Kritérium Validácie

```text
DB_UNIQUENESS=true
AND OUTCOMES=CREATED+ALREADY_EXISTS
AND CLEANUP=true
AND STATE=COMPLETED_SUCCESS
```

Doplňujúce povinné markery:

```text
TOMBSTONE=true
SWEEP=true
LOGIN_DATABASE_LOGOUT=true
KROK11_FINAL_CLEANUP_CONFIRMED
```

## Rollback

Vrátiť iba testovací commit alebo výsledný squash commit Kroku 11. Produkčný commit Kroku 10 sa automaticky nevracia. Izolované DB dáta, `.env`, run-store JSON, lock, temp súbory a serverový proces sa musia odstrániť.

## Nasledujúci povolený úkon

```text
Otvoriť PR pracovnej vetvy krok11-uplna-validacia,
spustiť presnú validačnú zostavu,
a podľa prvého výsledku buď odstrániť iba preukázanú testovaciu chybu,
alebo pri aplikačnom zlyhaní zastaviť Krok 11 a zaznamenať presnú príčinu.
```
