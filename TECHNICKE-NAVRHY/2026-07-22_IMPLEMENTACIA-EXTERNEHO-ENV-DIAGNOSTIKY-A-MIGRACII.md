# Implementácia externého env, diagnostiky a migrácií

## Stav dokumentu

```text
ČIASTOČNE-IMPLEMENTOVANÝ
```

Implementované:

```text
externý env loader pred bootstrapom webu aj CLI,
diagnostický príkaz databázových schopností,
migrácie M1 až M8 podľa Validovaného databázového návrhu,
praktická diagnostika databázy na Hostingeri,
vykonanie migrácií M1 až M8 v skupine default a batchi 1.
```

Nevykonané:

```text
samostatné overenie fyzických cudzích kľúčov cez INFORMATION_SCHEMA,
repository adaptéry,
integračné testy nad skutočnou databázou.
```

`ExternalEnvironment` najprv použije serverovú premennú `METODIKA_ENV_FILE`. Ak nie je nastavená, hľadá súrodenecký súbor `private/metodika.env` mimo `/codei`. Už existujúce serverové premenné neprepisuje a hodnoty tajomstiev nevypisuje.

Diagnostický príkaz `metodika:db-capabilities` čítaním overuje verziu servera, InnoDB, `utf8mb4_bin` a `DATETIME(6)`. Úspech platí iba pri potvrdení všetkých kontrol.

Migrácie:

```text
M1 request reservations
M2 derivation runs
M3 run domain terms
M4 branches
M5 branch dependencies
M6 candidates
M7 run results
M8 traces
```

Používajú InnoDB, `utf8mb4_bin`, `DATETIME(6)`, reštriktívne cudzie kľúče a unikátne korelačné obmedzenia. Nepoužívajú kaskádové mazanie, triggery ani produkčné seed údaje.

## Prakticky potvrdený stav

Dňa 2026-07-23 boli migrácie vykonané z Hostinger CLI príkazom:

```text
php spark migrate
```

CodeIgniter potvrdil dokončenie všetkých ôsmich migrácií bez chyby. Následný príkaz `php spark migrate:status` evidoval všetkých osem migrácií:

```text
Group       = default
Migrated On = 2026-07-23 08:36:20 UTC
Batch       = 1
```

Podrobný záznam a reValidácia sú v [`2026-07-23_REVALIDACIA-VYKONANIA-MIGRACII-M1-M8.md`](2026-07-23_REVALIDACIA-VYKONANIA-MIGRACII-M1-M8.md).

Bezpečné poradie pokračovania:

```text
overenie fyzických tabuliek a cudzích kľúčov
→ repository adaptéry
→ integračné testy
→ reValidácia implementovaného stavu
```
