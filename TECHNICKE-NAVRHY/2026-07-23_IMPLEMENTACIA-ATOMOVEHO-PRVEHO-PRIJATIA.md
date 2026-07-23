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
codei/app/Commands/VerifyFirstAcceptanceTransaction.php
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

## Praktické overenie

V Codespaces nad PHP `8.4.15` prešla syntaktická kontrola všetkých implementovaných súborov bez chyby.

Unit test:

```text
Tests: 2
Assertions: 4
2 / 2 = 100 %
```

Overené unit scenáre:

```text
RESERVATION_CREATED
→ rezervácia a historický beh sa vykonajú v jednej hranici,

ALREADY_EXISTS
→ nový historický beh sa nevytvorí.
```

Varovanie `No code coverage driver available` neovplyvnilo výsledok testu; znamená iba, že aktuálny runtime nemá Xdebug ani PCOV pre meranie pokrytia.

Composer vytvoril reprodukovateľný `codei/composer.lock`. Lokálny adresár `codei/build/`, ktorý PHPUnit používa na cache a výstupy, je ignorovaný v `.gitignore`.

## Praktické MySQL integračné overenie

V release `1.0.11` bol na Hostinger MySQLi/InnoDB databáze spustený príkaz:

```bash
php spark metodika:verify-first-acceptance-transaction
```

Potvrdené boli oba scenáre:

```text
Scenár A
→ rezervácia, beh a dve doménové väzby vznikli spolu,
→ nadradený rollback odstránil všetky testovacie zápisy,

Scenár B
→ úmyselná chyba vznikla po založení historického behu,
→ aplikačná transakcia vrátila späť rezerváciu, beh aj doménové väzby.
```

Po oboch scenároch zostali počty testovacích dát `0 + 0 + 0`.

Podrobný záznam je v `2026-07-23_INTEGRACNE-OVERENIE-ATOMOVEHO-PRVEHO-PRIJATIA.md`.

## Otvorené obmedzenia

```text
súbežný test dvoch prvých prijatí cez samostatné databázové spojenia,
RequestReplayGuard,
ďalšie operácie DerivationHistoryPort pre bránu, vetvy, výsledok a trace,
meranie code coverage v Codespaces runtime.
```

## Aktuálny výsledok

```text
IMPLEMENTATION_RESULT
=
MYSQL_TRANSACTION_VALIDATED_WITH_CONCURRENCY_LIMITATION
```

## Nasledujúci krok

```text
pripraviť súbežný test dvoch prvých prijatí
→ overiť kolíziu REQUEST_REFERENCE
→ reValidovať replay hranicu
→ až potom pripojiť RequestReplayGuard
```
