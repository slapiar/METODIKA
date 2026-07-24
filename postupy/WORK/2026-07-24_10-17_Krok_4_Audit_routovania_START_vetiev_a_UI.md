# Krok 4 — Audit routovania, START vetiev a UI

## Stav kroku

```text
SPLNENÉ
```

## Väzba na záväzný plán

Tento dokument uzatvára výhradne `Krok 4 — Audit routovania, START vetiev a UI` dokumentu:

`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`

Ďalší krok nebol počas vykonania tohto kroku otvorený.

---

# 1. Produkčný START endpoint

Aktívna route je:

```text
POST /diagnostics/concurrency/start
→ DiagnosticsConcurrencyStartController::start
→ filter csrf
```

Dôkaz je v `codei/app/Config/Routes.php`.

Záver:

```text
PRODUCTION_START_CONTROLLER=DiagnosticsConcurrencyStartController
PRODUCTION_START_METHOD=start
```

`DiagnosticsConcurrencyStartController` je aktívny produkčný kontrolér, nie historický ani mŕtvy súbor.

---

# 2. Druhá START vetva v DiagnosticsController

`DiagnosticsController` stále obsahuje verejnú metódu:

```text
startConcurrencyRun()
```

Žiadna route na ňu nesmeruje. Pri vypnutom auto-routing-u ju nemožno dosiahnuť verejným HTTP kontraktom.

Záver:

```text
DiagnosticsController::startConcurrencyRun = MŔTVA / NEROUTOVANÁ VETVA
```

Riziká:

- dve implementácie rovnakého významového úkonu sa môžu časom rozísť,
- budúci test alebo refaktor môže omylom zmeniť nesprávnu vetvu,
- čitateľ kódu nemusí bez auditu vedieť, ktorá implementácia je produkčná,
- rovnaký názov významu je implementovaný na dvoch miestach bez spoločnej továrne dokumentu.

---

# 3. Porovnanie oboch START dokumentov

Obe vetvy vytvárajú rovnaký základný run dokument:

- `version = 1`,
- `state = CREATED`,
- `runId`, `createdAt`, `expiresAt`,
- nemenný `input`,
- participantov A a B s hashmi tokenov,
- bariéru,
- finalization,
- cleanup,
- assertions.

Základná štruktúra je dnes významovo zhodná.

Nie sú však zhodné verejné odpovede a chybové správanie.

## Aktívna vetva

`DiagnosticsConcurrencyStartController::start()`:

- vracia `ok = true`,
- vracia obnovený `csrfHash`,
- pri nedostupnej diagnostike vracia bezpečný JSON 404,
- zachytáva výnimky START fázy,
- klasifikuje ich na konkrétne START errorCode,
- zapisuje detail do serverového logu,
- pri chybe vracia bezpečný JSON 500.

## Mŕtva vetva

`DiagnosticsController::startConcurrencyRun()`:

- nevracia pole `ok`,
- nevracia `csrfHash`,
- pri gate chybe vracia HTML fallback 404,
- nemá vlastný `try/catch` START fázy,
- nemá START klasifikáciu výnimiek,
- pri runtime chybe nemá zhodný verejný kontrakt.

Záver:

```text
RUN_DOCUMENT_SHAPE=ZHODNÝ V ZÁKLADE
PUBLIC_RESPONSE_CONTRACT=NEZHODNÝ
ERROR_CONTRACT=NEZHODNÝ
```

---

# 4. Testy a produkčná vetva

Session test:

```text
DiagnosticsControllerTest::testConcurrencyStartCreatesRunAndReturnsTokens()
```

volá verejnú route:

```text
POST /diagnostics/concurrency/start
```

Preto testuje rovnakú START vetvu, akú používa produkcia, teda `DiagnosticsConcurrencyStartController::start()`.

Test netestuje mŕtvu metódu `DiagnosticsController::startConcurrencyRun()`.

Záver:

```text
TESTED_START_BRANCH=PRODUCTION_BRANCH
DEAD_START_BRANCH_TESTED=NO
```

Obmedzenie z Kroku 3 zostáva: test používa frameworkový jeden proces a lokálny file store; nepotvrdzuje celý produkčný súbeh.

---

# 5. Audit UI — oddelenie transportu a aplikačného výsledku

UI má dve samostatné zobrazovacie oblasti.

## HTTP transport

Samostatne zobrazuje:

```text
START
HIT A
HIT B
RESULT
```

Každý krok dostáva HTTP stav alebo sieťovú chybu.

## Aplikačný výsledok

Samostatne zobrazuje:

```text
DB unikátnosť
Aplikačný replay
Cleanup
Celkovo
```

Tým UI základné rozlíšenie:

```text
HTTP transport ≠ aplikačný výsledok
```

implementuje.

Záver:

```text
TRANSPORT_AND_APPLICATION_AXES_SEPARATED=YES
```

---

# 6. Potvrdená chyba prezentácie COMPLETED_FAILED

Po úspešnom načítaní RESULT JSON UI vždy vykoná:

```text
setProgress(100, 'Dokoncene')
statusNode.textContent = 'Hotovo. Stav runu: ' + result.state
addLog('Diagnostika dokoncena.', 'ok')
```

Táto vetva sa vykoná aj vtedy, keď:

```text
result.state = COMPLETED_FAILED
```

alebo keď:

```text
assertions.overallSuccess = false
```

Osové hodnoty síce ukážu `NEPOTVRDENE`, ale nadradený stav a posledný log používajú jazyk a vizuálnu triedu úspešného dokončenia.

Záver:

```text
COMPLETED_FAILED_CAN_APPEAR_AS_SUCCESS=YES
```

Ide o skutočnú UI chybu, nie iba chýbajúci test.

Požadovaný budúci kontrakt musí rozlišovať najmenej:

```text
COMPLETED_SUCCESS
→ úspešná Validácia

COMPLETED_FAILED
→ dokončený transport, neúspešná Validácia

COMPLETED_FAILED_CLEANUP
→ dokončený transport, neúspešná Validácia a cleanup
```

Stav `Hotovo` môže označovať ukončenie procesu, ale nesmie byť jediným ani dominantným signálom výsledku.

---

# 7. Súvisiace UI riziká

1. `addLog('Diagnostika dokoncena.', 'ok')` používa úspešnú CSS triedu bez kontroly `overallSuccess`.
2. `setProgress(100, 'Dokoncene')` nerozlišuje úspešné a neúspešné dokončenie.
3. Status uvádza technický stav runu, ale neposkytuje zrozumiteľný nadradený záver `ÚSPECH / NEÚSPECH`.
4. RESULT HTTP 200 správne znamená iba úspešný transport výsledku, ale používateľ ho môže spolu so zeleným logom interpretovať ako úspech Validácie.
5. UI testy overujú prítomnosť prvkov a úspešnú vetvu, nie prezentáciu `COMPLETED_FAILED`.

---

# 8. Záväzné zistenia pre neskoršie opravy

| ID | Zistenie | Budúci krok / dôkaz |
|---|---|---|
| R01 | produkčný START obsluhuje `DiagnosticsConcurrencyStartController::start()` | zachovať ako jediný verejný START kontrakt alebo vedome nahradiť |
| R02 | `DiagnosticsController::startConcurrencyRun()` je mŕtva neroutovaná duplicita | v opravnom kroku odstrániť alebo zlúčiť bez zmeny verejného kontraktu |
| R03 | základný run dokument je duplicitne skladaný na dvoch miestach | rozhodnúť o jednej továrni alebo jedinom kontroléri |
| R04 | verejné START odpovede a chyby nie sú medzi vetvami zhodné | testovať iba autoritatívny verejný kontrakt |
| R05 | testy START používajú rovnakú route ako produkcia | zachovať regresný test aktívnej route |
| R06 | UI oddeľuje HTTP transport a aplikačné osi | zachovať toto rozlíšenie |
| R07 | `COMPLETED_FAILED` môže pôsobiť ako úspech | pridať explicitný neúspešný UI stav a test |
| R08 | UI nemá samostatný regresný test failed výsledku | doplniť test `COMPLETED_FAILED` a `COMPLETED_FAILED_CLEANUP` |

Tieto zistenia sa pripájajú k záväznému registru dier z Kroku 3.

---

# 9. Rozhodovacia brána 4

Kroky 1 až 4 sú týmto auditne uzavreté bez zmeny vykonateľného kódu.

Rozhodovacia brána 4 je splnená v auditnom zmysle:

```text
CHECKLIST_AUDITED=YES
TEST_MATRIX_AUDITED=YES
ROUTING_AND_UI_AUDITED=YES
CODE_CHANGED=NO
```

Brána nepovoľuje náhodnú opravu. Ďalší krok zostáva presne ten, ktorý určuje lineárny plán.

---

# 10. Validácia a uzavretie

Kritérium Kroku 4 bolo splnené:

- produkčný START kontrolér je jednoznačne určený,
- stav samostatného START kontroléra je určený,
- obe START vetvy sú porovnané,
- testovaná a produkčná vetva sú porovnané,
- oddelenie HTTP transportu a aplikačného výsledku je preverené,
- prezentácia `COMPLETED_FAILED` je jednoznačne vyhodnotená.

```text
KROK_4=SPLNENÉ
NEXT_ALLOWED_STEP=Krok 5 — Pokus o získanie historického produkčného dôkazu
```

Vykonateľný kód, testy ani produkčné prostredie neboli týmto krokom zmenené.
