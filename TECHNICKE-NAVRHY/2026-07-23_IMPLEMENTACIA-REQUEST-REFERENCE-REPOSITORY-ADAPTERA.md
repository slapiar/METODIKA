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

Adaptér rezervácie zámerne nevytvára historický `QUESTION_DERIVATION`. Atómová hranica prvého prijatia musí vzniknúť až koordináciou:

```text
TransactionBoundaryPort
→ RequestReferenceRepositoryPort.reserveFirstAcceptance
→ DerivationHistoryPort.createHistoricalRun
→ commit
```

Pri chybe musí nadradená transakčná hranica vykonať rollback oboch zápisov.

## Nevykonané

```text
DerivationHistoryPort a jeho adaptér,
TransactionBoundaryPort a jeho adaptér,
RequestReplayGuard,
aplikačná služba,
automatické testy repository adaptéra,
integračné overenie nad databázou.
```

## Aktuálny výsledok

```text
IMPLEMENTATION_RESULT
=
IMPLEMENTED_WITHOUT_RUNTIME_VALIDATION
```

## Nasledujúci krok

```text
implementovať DerivationHistoryPort pre založenie historického behu
→ implementovať TransactionBoundaryPort
→ otestovať atómovú hranicu prvého prijatia
→ až potom pripojiť RequestReplayGuard
```
