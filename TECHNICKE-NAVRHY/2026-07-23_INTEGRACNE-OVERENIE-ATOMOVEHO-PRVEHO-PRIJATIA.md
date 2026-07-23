# Integračné overenie atómového prvého prijatia

## Stav dokumentu

```text
IMPLEMENTOVANÝ
```

## Predmet

Bezpečné integračné overenie skutočných MySQLi repository adaptérov a transakčnej hranice prvého prijatia nad fyzickou schémou M1 až M3.

Overenie sa vykonáva príkazom:

```bash
php spark metodika:verify-first-acceptance-transaction
```

## Implementovaný súbor

```text
codei/app/Commands/VerifyFirstAcceptanceTransaction.php
```

## Kontrolované scenáre

### Scenár A — úplný zápis

```text
nadradená testovacia transakcia
→ FirstAcceptanceService
→ rezervácia REQUEST_REFERENCE
→ počiatočný DERIVATION_RUN
→ dve zoradené DOMAIN_TERM_REFERENCE väzby
→ kontrola počtov 1 + 1 + 2
→ rollback nadradenej testovacej transakcie
→ kontrola počtov 0 + 0 + 0
```

Vnorený commit aplikačnej transakcie nepotvrdí fyzický zápis samostatne. CodeIgniter pri vnorených transakciách vykonáva fyzický commit alebo rollback iba na najvyššej úrovni.

### Scenár B — úmyselná chyba

```text
FirstAcceptanceService
→ rezervácia REQUEST_REFERENCE
→ počiatočný DERIVATION_RUN
→ dve DOMAIN_TERM_REFERENCE väzby
→ úmyselná výnimka po historickom zápise
→ rollback aplikačnej transakcie
→ kontrola počtov 0 + 0 + 0
```

## Bezpečnostné vlastnosti

```text
každé spustenie používa náhodný 128-bitový suffix,
úspešný scenár je vždy vrátený nadradeným rollbackom,
chybový scenár musí zostať bez riadkov,
finally blok vykoná núdzový rollback a cielené čistenie iba podľa vytvorených request_reference,
čistenie rešpektuje poradie cudzích kľúčov: domain terms → runs → reservations,
príkaz nemení migrácie ani produkčné významové údaje.
```

## Kritérium úspechu

```text
Scenár A: počas transakcie 1 rezervácia, 1 beh, 2 doménové väzby; po rollbacku všetko 0.
Scenár B: po úmyselnej výnimke všetko 0.
Príkaz skončí EXIT_SUCCESS.
```

## Praktické vykonanie

Príkaz bol prakticky spustený 2026-07-23 v release `1.0.11` nad Hostinger MySQLi/InnoDB databázou po úspešnom overení fyzickej schémy:

```text
8/8 tabuliek = InnoDB + utf8mb4_bin,
10/10 cudzích kľúčov = DELETE RESTRICT + UPDATE RESTRICT.
```

Výsledok integračného príkazu:

```text
Scenár A: OK — rezervácia, beh a doménové väzby vznikli spolu a po rollbacku nezostali v databáze.
Scenár B: OK — chyba vrátila späť rezerváciu, beh aj doménové väzby.
EXIT_SUCCESS
```

## Aktuálny výsledok

```text
INTEGRATION_RESULT
=
MYSQL_TRANSACTION_ATOMICITY_VALIDATED
```

Potvrdený rozsah:

```text
úspešné prvé prijatie vytvorí rezerváciu, historický beh a doménové väzby v jednej transakčnej hranici,
rollback nadradenej testovacej transakcie odstráni všetky testovacie zápisy,
úmyselná chyba po historickom zápise vráti späť rezerváciu, beh aj doménové väzby,
po oboch scenároch nezostali testovacie dáta.
```

## Otvorené obmedzenia

```text
súbežný test dvoch samostatných databázových spojení,
RequestReplayGuard,
ďalšie operácie DerivationHistoryPort pre bránu, vetvy, výsledok a trace.
```

## Nasledujúci krok

```text
pripraviť súbežný test dvoch prvých prijatí
→ overiť unikátnosť a správanie pri kolízii REQUEST_REFERENCE
→ reValidovať replay hranicu
→ pripojiť RequestReplayGuard
```
