<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$readmePath = $root . '/postupy/README.md';
$changelogPath = $root . '/CHANGELOG.md';

$readme = file_get_contents($readmePath);
if (! is_string($readme)) {
    throw new RuntimeException('Cannot read postupy/README.md');
}

$header = "| Dokument | Stav | Autoritatívny cieľ alebo poznámka |\n|---|---|---|\n";
$row = '| `WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md` | PRACOVNÝ | Uzavretý záznam Kroku 9 s výsledkom `SPLNENÉ`: dvojprocesový test rozlíšil raw stav od projekcie `load()`, potvrdil ochranu pred falošným timeoutom a zdôvodnil ponechanie `load()` bez zmeny. |' . "\n";
if (! str_contains($readme, $row)) {
    $readme = str_replace($header, $header . $row, $readme, $headerCount);
    if ($headerCount !== 1) {
        throw new RuntimeException('Register header mismatch.');
    }
}

$oldPlan = '| `PLAN/2026-07-24_08-04_Plán_práce.md` | PRACOVNÝ — **ZÁVÄZNÝ** | Opravená striktne lineárna verzia. Kroky 1 až 8 sú `SPLNENÉ`; jediným nasledujúcim povoleným krokom je Krok 9 — audit a test bariéry, `load()` a timeoutu. |';
$newPlan = '| `PLAN/2026-07-24_08-04_Plán_práce.md` | PRACOVNÝ — **ZÁVÄZNÝ** | Opravená striktne lineárna verzia. Kroky 1 až 9 sú `SPLNENÉ`; jediným nasledujúcim povoleným krokom je Krok 10 — najmenšia funkčná oprava koreňovej príčiny. |';
$readme = str_replace($oldPlan, $newPlan, $readme, $planCount);
if ($planCount !== 1) {
    throw new RuntimeException('Plan row mismatch.');
}

file_put_contents($readmePath, $readme);

$changelog = file_get_contents($changelogPath);
if (! is_string($changelog)) {
    throw new RuntimeException('Cannot read CHANGELOG.md');
}

$marker = "## 2026-07-24\n\n";
$addition = <<<'MD'
### Krok 9 — audit bariéry, `load()` a timeoutu

- GitHub Actions run `30098298849` na PHP 8.4.23 s `pcntl_fork` úspešne vykonal existujúce run-store testy aj nový skutočný dvojprocesový test,
- fyzicky uložený stav `EXECUTING` bol presne odlíšený od návratovej projekcie `BARRIER_OPEN`, ktorú `load()` poskytuje čakajúcemu requestu bez zmeny raw JSON,
- oneskorený pokus zapísať `PARTNER_TIMEOUT` po otvorení bariéry bol pod exkluzívnym zámkom odmietnutý; raw dokument zostal nezmenený,
- zmena `load()` nebola preukázaná ako nevyhnutná, preto produkčný run store, kontrolér a stavový automat zostali bez zmeny,
- výsledok je v [`postupy/WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md`](postupy/WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md),
- jediným ďalším povoleným krokom je Krok 10 — najmenšia funkčná oprava koreňovej príčiny.

MD;

if (! str_contains($changelog, '### Krok 9 — audit bariéry, `load()` a timeoutu')) {
    $changelog = str_replace($marker, $marker . $addition, $changelog, $changeCount);
    if ($changeCount !== 1) {
        throw new RuntimeException('CHANGELOG marker mismatch.');
    }
}

file_put_contents($changelogPath, $changelog);

echo "KROK_9_FINALIZED\n";
