# Implementácia REQUEST_REFERENCE repository adaptéra

## Stav dokumentu

```text
ČIASTOČNE-IMPLEMENTOVANÝ
```

## Zdroj

Implementácia vychádza z:

```text
2026-07-22_REPOSITORY-KONTRAKT-REQUEST-REFERENCE.md
+
2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md
+
fyzicky potvrdenej databázovej schémy M1, M2 a M7
```

## Implementované súbory

```text
codei/app/Application/QuestionDerivation/Contracts/RequestReferenceRepositoryPort.php
codei/app/Application/QuestionDerivation/Data/RequestReferenceReservation.php
codei/app/Application/QuestionDerivation/Data/ReservationResult.php
codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php
```

## Implementované operácie

```text
reserveFirstAcceptance
findByRequestReference
markRunning
attachCompletedResult
loadCompletedResult
```

## Zachované invarianty

```text
REQUEST_REFERENCE sa nemení,
payload_fingerprint sa nemení,
derivation_reference sa nemení,
ALREADY_EXISTS nevytvára nový beh,
kolízia cudzej derivation_reference nie je vydaná za replay,
finálny výsledok sa pripája iba pri úplnej korelácii,
repository nerozhoduje replay ani konflikt,
repository nevykonáva doménový algoritmus,
repository samo neotvára ani nepotvrdzuje transakciu prvého prijatia.
```

## Transakčná hranica

Adaptér rezervácie zámerne nevytvára historický `QUESTION_DERIVATION`. Atómová hranica prvého prijatia je implementovaná koordináciou:

```text
TransactionBoundaryPort
→ RequestReferenceRepositoryPort.reserveFirstAcceptance
→ DerivationHistoryPort.createInitialRun
→ commit
```

Pri chybe musí nadradená transakčná hranica vykonať rollback všetkých zápisov.

Implementované sú:

```text
DerivationHistoryPort,
DerivationHistoryRepository,
TransactionBoundaryPort,
DatabaseTransactionBoundary,
FirstAcceptanceService,
FirstAcceptanceServiceFactory.
```

Všetky databázové komponenty továrne používajú tú istú `BaseConnection`.

## Praktické overenie

Syntaktická kontrola repository, history a transaction súborov prešla bez chyby.

Unit test `FirstAcceptanceServiceTest` bol prakticky spustený v Codespaces nad PHP `8.4.15`:

```text
Tests: 2
Assertions: 4
2 / 2 = 100 %
```

Test potvrdil, že `RESERVATION_CREATED` založí historický beh v rovnakej hranici a `ALREADY_EXISTS` ďalší historický beh nezaloží.

## Praktické integračné overenie

V release `1.0.11` bol nad Hostinger MySQLi/InnoDB databázou spustený príkaz `metodika:verify-first-acceptance-transaction`.

Výsledok:

```text
úspešné prvé prijatie vytvorilo rezerváciu, historický beh a dve doménové väzby v jednej transakčnej hranici,
nadradený rollback odstránil všetky testovacie zápisy,
úmyselná chyba po historickom zápise vrátila späť rezerváciu, beh aj doménové väzby,
po oboch scenároch nezostali testovacie dáta.
```

## Nevykonané

```text
RequestReplayGuard,
súbežný test dvoch prvých prijatí cez samostatné databázové spojenia,
priame integračné overenie operácií markRunning, attachCompletedResult a loadCompletedResult,
ďalšie operácie DerivationHistoryPort pre bránu, vetvy, výsledok a trace.
```

## Aktuálny výsledok

```text
IMPLEMENTATION_RESULT
=
MYSQL_FIRST_ACCEPTANCE_VALIDATED_WITH_REPLAY_LIMITATIONS
```

## Nasledujúci krok

```text
pripraviť súbežný test dvoch samostatných prijatí rovnakej REQUEST_REFERENCE
→ overiť unikátnosť a kolízny výsledok
→ reValidovať replay hranicu
→ až potom pripojiť RequestReplayGuard
```
