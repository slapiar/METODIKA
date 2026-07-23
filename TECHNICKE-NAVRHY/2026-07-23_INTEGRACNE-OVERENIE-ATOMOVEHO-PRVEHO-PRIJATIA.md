# Integračné overenie atómového prvého prijatia

## Stav dokumentu

```text
ČIASTOČNE-IMPLEMENTOVANÝ
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

## Aktuálny výsledok

```text
IMPLEMENTED_WITHOUT_MYSQL_RUNTIME_VALIDATION
```

Príkaz bol spätne načítaný a porovnaný s implementáciou vnorených transakcií v CodeIgniter 4.7.4. Praktické spustenie nad MySQLi databázou ešte nebolo vykonané.

## Otvorené obmedzenia

```text
syntaktická kontrola v aktuálnom Codespaces runtime,
registrácia príkazu v Spark zozname,
praktické spustenie nad fyzickou MySQLi schémou,
súbežný test dvoch samostatných databázových spojení.
```

## Nasledujúci krok

```text
synchronizovať aktuálny main
→ php -l codei/app/Commands/VerifyFirstAcceptanceTransaction.php
→ v Codespaces overiť php spark list | grep verify-first-acceptance
→ po vytvorení release spustiť príkaz nad Hostinger MySQLi
→ zapísať výsledok a reValidovať transakčnú hranicu
```
