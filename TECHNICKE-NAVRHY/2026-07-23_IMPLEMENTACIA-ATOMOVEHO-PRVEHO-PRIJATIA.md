# Implementácia atómového prvého prijatia

## Stav dokumentu

```text
ČIASTOČNE-IMPLEMENTOVANÝ
```

## Predmet

Implementácia krátkej technickej hranice:

```text
REQUEST_REFERENCE_RESERVATION
+
počiatočný DERIVATION_RUN_RECORD
+
zoradené DOMAIN_TERM_REFERENCE väzby
```

Buď vzniknú všetky zápisy, alebo sa transakcia vráti späť.

## Implementované súbory

```text
codei/app/Application/QuestionDerivation/Data/InitialDerivationRun.php
codei/app/Application/QuestionDerivation/Contracts/DerivationHistoryPort.php
codei/app/Application/QuestionDerivation/Contracts/TransactionBoundaryPort.php
codei/app/Application/QuestionDerivation/FirstAcceptanceService.php
codei/app/Infrastructure/Persistence/QuestionDerivation/DerivationHistoryRepository.php
codei/app/Infrastructure/Persistence/QuestionDerivation/DatabaseTransactionBoundary.php
codei/app/Infrastructure/Persistence/QuestionDerivation/FirstAcceptanceServiceFactory.php
codei/tests/unit/FirstAcceptanceServiceTest.php
```

## Zachované invarianty

```text
jedna REQUEST_REFERENCE → najviac jedna derivation_reference,
rezervácia a historický beh používajú rovnakú request_reference a derivation_reference,
run_mode = PARTIAL_RUN_WITH_ATOMIC_GATE,
počiatočný beh vzniká pred vykonaním vstupnej brány,
ALREADY_EXISTS nevytvára ďalší historický beh,
repository ani transakčná hranica nerozhodujú replay alebo konflikt,
všetky databázové adaptéry sú zostavené nad jednou BaseConnection.
```

Interné `reservation_id` zostáva v infraštruktúrnej vrstve. Aplikačný dátový tvar ho neodhaľuje.

`domain_term_references` môže byť prázdny zoznam. Implementácia nepridáva neautorizovanú podmienku, že každý beh musí používať doménový pojem. Ak je rozhodujúci pojem neznámy alebo viacznačný, rieši to metodická vstupná brána.

## Testovanie

Unit test overuje:

```text
RESERVATION_CREATED
→ rezervácia a historický beh sa vykonajú v jednej hranici,

ALREADY_EXISTS
→ nový historický beh sa nevytvorí.
```

Test ešte nebol prakticky spustený v aktuálnom Codespaces runtime.

## Otvorené obmedzenia

```text
runtime spustenie unit testu,
integračný test nad MySQL/MariaDB,
rollback test pri zlyhaní založenia behu alebo doménovej väzby,
súbežný test dvoch prvých prijatí,
RequestReplayGuard,
ďalšie operácie DerivationHistoryPort pre bránu, vetvy, výsledok a trace.
```

## Nasledujúci krok

```text
spustiť syntaktickú kontrolu a unit test
→ doplniť bezpečný integračný CLI test s dočasnými transakčnými dátami
→ overiť rollback a súbežnosť
→ reValidovať prvé prijatie
```
