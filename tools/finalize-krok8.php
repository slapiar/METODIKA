<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$readmePath = $root . '/postupy/README.md';
$changelogPath = $root . '/CHANGELOG.md';
$workPath = $root . '/postupy/WORK/2026-07-24_13-45_Krok_8_Oprava_diagnostickeho_rozlisenia.md';
$runId = getenv('GITHUB_RUN_ID') ?: 'unknown';
$sha = getenv('GITHUB_SHA') ?: 'unknown';

$readme = file_get_contents($readmePath);
if (! is_string($readme)) {
    throw new RuntimeException('Cannot read postupy/README.md');
}

$oldPlan = '| `PLAN/2026-07-24_08-04_Plán_práce.md` | PRACOVNÝ — **ZÁVÄZNÝ** | Opravená striktne lineárna verzia. Kroky 1 až 6 sú `SPLNENÉ`; jediným nasledujúcim povoleným krokom je Krok 7 — reprodukcia koreňovej príčiny mimo produkcie. |';
$newPlan = '| `PLAN/2026-07-24_08-04_Plán_práce.md` | PRACOVNÝ — **ZÁVÄZNÝ** | Opravená striktne lineárna verzia. Kroky 1 až 8 sú `SPLNENÉ`; jediným nasledujúcim povoleným krokom je Krok 9 — audit a test bariéry, `load()` a timeoutu. |';
$readme = str_replace($oldPlan, $newPlan, $readme, $planCount);
if ($planCount !== 1) {
    throw new RuntimeException('Plan row was not replaced exactly once.');
}

$header = "| Dokument | Stav | Autoritatívny cieľ alebo poznámka |\n|---|---|---|\n";
$rows = '';
$entries = [
    '| `WORK/2026-07-24_13-45_Krok_8_Oprava_diagnostickeho_rozlisenia.md` | PRACOVNÝ | Uzavretý záznam Kroku 8 s výsledkom `SPLNENÉ`: diagnostické zlyhania sú rozlíšené podľa fázy a triedy chyby; interný log nesie úplný kontext a verejný dokument iba bezpečný kód. Dve zlyhania `load()`/bariéry zostávajú dôkazom pre Krok 9 a známa UI odchýlka ostáva otvorená mimo rozsahu Kroku 8. |',
    '| `WORK/2026-07-24_13-25_Krok_7_Reprodukcia_korenovej_priciny_mimo_produkcie.md` | PRACOVNÝ | Uzavretý záznam Kroku 7 s výsledkom `SPLNENÉ`: koreňová príčina bola reprodukovaná v izolovanej MariaDB 11.4 cez reálnu MySQLi/InnoDB cestu, s potvrdeným rollbackom a cleanupom. |',
    '| `WORK/INI/2026-07-24_13-34_INI_Zosuladenie_Kroku_7_a_Krok_8.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre zosúladenie Kroku 7 a samostatnú diagnostickú opravu Kroku 8. |',
    '| `WORK/INI/2026-07-24_14-47_INI_Krok_8_Korekcia_rozsahu_validacie.md` | PRACOVNÝ | Dôkazový záznam korekcie validačného rozsahu Kroku 8 po oddelení mimo-rozsahových zlyhaní bariéry, `load()` a UI. |',
];
foreach ($entries as $entry) {
    if (! str_contains($readme, $entry)) {
        $rows .= $entry . "\n";
    }
}
$readme = str_replace($header, $header . $rows, $readme, $headerCount);
if ($headerCount !== 1) {
    throw new RuntimeException('Register header was not found exactly once.');
}
file_put_contents($readmePath, $readme);

$changelog = file_get_contents($changelogPath);
if (! is_string($changelog)) {
    throw new RuntimeException('Cannot read CHANGELOG.md');
}

$marker = "## 2026-07-24\n\n";
$addition = <<<'MD'
### Krok 8 — oprava diagnostického rozlíšenia

- vonkajšie spracovanie v `DiagnosticsController::executeAcceptIfReady()` rozlišuje fázy `BUILD_INITIAL_RUN`, `LOAD_PAYLOAD_FINGERPRINT`, `CREATE_ACCEPTANCE_RUNNER`, `APPLICATION_ACCEPT` a `WRITE_PARTICIPANT_RESULT`,
- bezpečný kód nesie súčin `fáza × trieda chyby`; raw text výnimky sa zapisuje iba do serverového logu spolu s `runId` a participantom,
- verejný run dokument a UI dostávajú iba bezpečný kód,
- `DiagnosticsConcurrencyAcceptanceRunner` zachováva pôvodné bezpečné `accept()` a pre kontrolér poskytuje `acceptOrThrow()` na vonkajšie fázové rozlíšenie,
- prešlo šesť unit testov diagnostických fáz a samostatný session test neprítomnosti raw exception textu na PHP 8.4,
- úplný beh `DiagnosticsControllerTest` zároveň potvrdil dve staršie odchýlky uloženého a interpretovaného stavu `load()`/bariéry, ktoré sú vstupom pre Krok 9, a jednu známu UI odchýlku mimo rozsahu Kroku 8,
- výsledok je v [`postupy/WORK/2026-07-24_13-45_Krok_8_Oprava_diagnostickeho_rozlisenia.md`](postupy/WORK/2026-07-24_13-45_Krok_8_Oprava_diagnostickeho_rozlisenia.md),
- funkčná koreňová chyba rezervácie sa v Kroku 8 nemenila; jediným ďalším povoleným krokom je Krok 9.

### Krok 7 — reprodukcia koreňovej príčiny mimo produkcie

- izolovaný GitHub Actions run `30089261354` nad PHP 8.4 a MariaDB 11.4 úspešne vykonal migrácie M1–M8, overenie schémy a reprodukciu cez nezmenenú aplikačnú cestu,
- potvrdená bola trieda príčiny `DBDebug=false + nekontrolovaný insert rezervácie + postcheck iba podľa REQUEST_REFERENCE`,
- druhý tok skončil presnou `RuntimeException` vo fáze `CREATE_INITIAL_HISTORY_RUN`, jeho transakcia bola vrátená späť a cleanup potvrdil počty `0 + 0 + 0`,
- výsledok je v [`postupy/WORK/2026-07-24_13-25_Krok_7_Reprodukcia_korenovej_priciny_mimo_produkcie.md`](postupy/WORK/2026-07-24_13-25_Krok_7_Reprodukcia_korenovej_priciny_mimo_produkcie.md).

MD;

if (! str_contains($changelog, '### Krok 8 — oprava diagnostického rozlíšenia')) {
    $changelog = str_replace($marker, $marker . $addition, $changelog, $changeCount);
    if ($changeCount !== 1) {
        throw new RuntimeException('CHANGELOG date marker was not found exactly once.');
    }
}
file_put_contents($changelogPath, $changelog);

$work = <<<MD
# Krok 8 — Oprava diagnostického rozlíšenia

## Stav

```text
SPLNENÉ
```

## Inicializácia

- `postupy/WORK/INI/2026-07-24_13-34_INI_Zosuladenie_Kroku_7_a_Krok_8.md`
- `postupy/WORK/INI/2026-07-24_14-47_INI_Krok_8_Korekcia_rozsahu_validacie.md`
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

GitHub Actions run: `{$runId}`  
Východiskový SHA workflowu: `{$sha}`

Úspešne prešli:

```text
php -l dotknutých PHP súborov
vendor/bin/phpunit --no-coverage tests/unit/DiagnosticsConcurrencyFailureReporterTest.php
vendor/bin/phpunit --no-coverage --filter testApplicationFailurePersistsOnlySafePhaseCode tests/session/DiagnosticsControllerTest.php
```

Testy potvrdili:

- odlišný bezpečný kód pre každú diagnostickú fázu,
- klasifikáciu runtime, input a JSON chyby,
- neprítomnosť raw exception textu v HTTP odpovedi a uloženom run dokumente,
- zachovanie bezpečného kódu `APPLICATION_ACCEPT_RUNTIME_ERROR`.

## Mimo rozsahu Kroku 8 — zachované otvorené dôkazy

Úplný beh `DiagnosticsControllerTest` v rune `30094146624` odhalil tri zlyhania, ktoré Krok 8 neopravuje:

1. uložený stav `EXECUTING` sa pri `load()` interpretuje ako `BARRIER_OPEN`,
2. rovnaká odchýlka ovplyvnila nový session scenár pri čítaní uloženého dokumentu,
3. UI test očakáva `/diagnostics/concurrency/result/`, ktorý aktuálna odpoveď neobsahuje.

Prvé dve zistenia sú priamym vstupom Kroku 9. UI odchýlka zostáva evidovaná mimo rozsahu Kroku 8.

## Rollback

Vrátiť samostatný commit Kroku 8. Funkčný kód rezervácie, databázová schéma ani produkčné prostredie neboli zmenené.

## Záver

```text
KROK_8=SPLNENÉ
ĎALŠÍ_POVOLENÝ_KROK=Krok 9 — Audit a test bariéry, load() a timeoutu
```
MD;

file_put_contents($workPath, $work);

echo "KROK_8_DOCUMENTATION_FINALIZED\n";
