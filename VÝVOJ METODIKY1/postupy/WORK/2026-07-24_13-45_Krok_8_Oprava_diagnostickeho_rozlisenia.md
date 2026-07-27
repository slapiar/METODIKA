# Krok 8 — Oprava diagnostického rozlíšenia

## Stav

```text
SPLNENÉ
```

## Inicializácia

- `postupy/WORK/INI/2026-07-24_13-34_INI_Zosuladenie_Kroku_7_a_Krok_8.md`
- `GATE=OPEN`

## Východisko

Krok 7 prakticky reprodukoval koreňovú príčinu mimo produkcie. Krok 8 nemení rezerváciu ani transakčnú logiku; oddeľuje iba diagnostické fázy a bezpečné verejné kódy.

## Vykonané zmeny

1. Vznikol `DiagnosticsConcurrencyFailureReporter` s fázami:
   - `BUILD_INITIAL_RUN`,
   - `LOAD_PAYLOAD_FINGERPRINT`,
   - `CREATE_ACCEPTANCE_RUNNER`,
   - `APPLICATION_ACCEPT`,
   - `WRITE_PARTICIPANT_RESULT`.
2. Bezpečný kód má tvar `FÁZA_TRIEDA_CHYBY`.
3. Serverový log obsahuje bezpečný kód, fázu, triedu a správu výnimky, `runId` a participant A/B.
4. `executeAcceptIfReady()` oddelene zachytáva každú fázu.
5. `DiagnosticsConcurrencyAcceptanceRunner::acceptOrThrow()` umožňuje kontroléru rozlíšiť chybu aplikačnej služby; pôvodné `accept()` zostáva spätne kompatibilné.
6. Pri chybe zápisu participant outcome sa raw výnimka nevráti klientovi; vznikne bezpečný kód `WRITE_PARTICIPANT_RESULT_*`.
7. Funkčná koreňová chyba v repository vrstve sa nemenila.

## Validácia

GitHub Actions run: `30095033806`  
Východiskový SHA workflowu: `170f7c1d4f98f30172f8648717eaae53ac3e3caf`

Úspešne prešli:

```text
php -l dotknutých PHP súborov
vendor/bin/phpunit tests/unit/DiagnosticsConcurrencyFailureReporterTest.php
vendor/bin/phpunit --filter DiagnosticsControllerTest
```

Testy potvrdili:

- odlišný bezpečný kód pre každú diagnostickú fázu,
- klasifikáciu runtime, input a JSON chyby,
- neprítomnosť raw exception textu v HTTP odpovedi a uloženom run dokumente,
- zachovanie bezpečného kódu `APPLICATION_ACCEPT_RUNTIME_ERROR`.

## Rollback

Vrátiť samostatný commit Kroku 8. Funkčný kód rezervácie, databázová schéma ani produkčné prostredie neboli zmenené.

## Záver

```text
KROK_8=SPLNENÉ
ĎALŠÍ_POVOLENÝ_KROK=Krok 9 — Audit a test bariéry, load() a timeoutu
```