# Krok 9 — Audit a test bariéry, `load()` a timeoutu

## Stav

```text
SPLNENÉ
```

## Inicializácia

- `postupy/WORK/INI/2026-07-24_15-08_INI_Krok_9_Audit_bariery_load_timeoutu.md`
- `postupy/WORK/INI/2026-07-24_15-42_INI_Krok_9_Oprava_pociatocneho_stavu_testu.md`
- `GATE=OPEN`

## Predmet auditu

Audit rozlíšil:

1. fyzicky uložený run dokument,
2. interpretovanú návratovú kópiu z `DiagnosticsConcurrencyRunStore::load()`,
3. stavový prechod `BARRIER_OPEN → EXECUTING`,
4. pretekové okno medzi otvorením bariéry a oneskoreným timeout pokusom,
5. poslednú poistku proti falošnému `PARTNER_TIMEOUT` pod exkluzívnym file-lockom.

## Zistenia

### Uložený stav a projekcia `load()`

`load()` načíta a validuje uložený dokument, ale pred návratom volá `exposeOpenedBarrierToWaitingRequest()`.

Ak je na disku:

```text
state = EXECUTING
barrier.openedAt != null
participant A.startedAt = null
participant B.startedAt != null
```

čakajúci request dostane návratovú kópiu:

```text
state = BARRIER_OPEN
```

Uložený JSON sa tým nemení. Ide o účelovú projekciu pre čakajúci request, nie o zmenu stavového automatu na disku.

### Timeoutová poistka

`mutate()` pracuje nad skutočne uloženým dokumentom pod `LOCK_EX`. Ak mutácia zavádza nový `PARTNER_TIMEOUT`, ale uložený dokument už obsahuje `barrier.openedAt`, store vráti pôvodný dokument bez Validácie a bez zápisu navrhnutej timeout mutácie.

Tým je oneskorený timeout po otvorení bariéry odmietnutý aj v prípade, že druhý participant už posunul uložený stav do `EXECUTING`.

## Dvojprocesová Validácia

GitHub Actions run:

```text
30098298849
```

Východiskový SHA:

```text
949bc6d26960996cb437826e2d4ed6213ad13837
```

Prostredie:

```text
PHP 8.4.23
pcntl_fork = dostupné
file-store = spoločný dočasný adresár
produkcia = nepoužitá
```

Úspešne prešli všetky kroky jobu `Audit stored state, load projection and timeout guard`:

```text
Verify process environment
Syntax validation
Run existing run-store tests
Run two-process barrier and timeout test
```

Dvojprocesový test potvrdil:

1. participant A čaká nad jedným store,
2. druhý proces otvorí bariéru,
3. druhý proces uloží stav `EXECUTING`,
4. raw JSON zostáva `EXECUTING`,
5. čakajúci proces cez `load()` rozpozná `BARRIER_OPEN`,
6. oneskorený pokus zapísať `PARTNER_TIMEOUT` vráti pôvodný stav `EXECUTING`,
7. timeout outcome, errorCode a finalization claim sa nezapíšu,
8. raw dokument pred timeout pokusom a po ňom je zhodný.

## Klasifikácia skorších session zlyhaní

Dve skoršie očakávania `EXECUTING`, ktoré dostali `BARRIER_OPEN`, nepreukazovali chybný uložený stav. Zachytili zámernú projekciu `load()` pre čakajúci request.

Tretia UI odchýlka nesúvisí s bariérou, `load()` ani timeoutovou poistkou a zostáva mimo rozsahu Kroku 9.

## Rozhodnutie o `load()`

```text
ZMENA_LOAD=NEVYHNUTNÁ_NIE
```

`load()` sa nemení, pretože:

- projekcia umožňuje čakajúcemu requestu rozpoznať už otvorenú bariéru aj po prechode uloženého stavu do `EXECUTING`,
- dvojprocesový test potvrdil, že projekcia nemení raw dokument,
- posledná timeoutová poistka pracuje nad uloženým stavom pod exkluzívnym zámkom,
- falošný timeout sa po otvorení bariéry nezapíše.

## Zmeny v repozitári

Pribudol iba integračný test:

```text
codei/tests/integration/DiagnosticsConcurrencyBarrierProcessTest.php
```

Produkčný run store, kontrolér, stavový automat, databáza ani UI sa nemenili.

## Rollback

Vrátiť commit integračného testu a odstrániť jednorazový workflow Kroku 9. Produkčný kód ani produkčné prostredie nevyžadujú rollback.

## Záver

```text
KROK_9=SPLNENÉ
ULOŽENÝ_STAV_A_PROJEKCIA=ROZLÍŠENÉ
FALOŠNÝ_TIMEOUT_PO_OTVORENÍ_BARIÉRY=NEZAPÍŠE_SA
LOAD=BEZ_ZMENY
ĎALŠÍ_POVOLENÝ_KROK=Krok 10 — Najmenšia funkčná oprava koreňovej príčiny
```
