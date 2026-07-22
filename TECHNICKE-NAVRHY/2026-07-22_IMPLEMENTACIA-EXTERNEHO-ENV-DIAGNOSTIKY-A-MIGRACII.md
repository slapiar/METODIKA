# Implementácia externého env, diagnostiky a migrácií

## Stav dokumentu

```text
ČIASTOČNE-IMPLEMENTOVANÝ
```

Implementované:

```text
externý env loader pred bootstrapom webu aj CLI,
diagnostický príkaz databázových schopností,
migrácie M1 až M8 podľa Validovaného databázového návrhu.
```

Nevykonané:

```text
praktická diagnostika na Hostingeri,
spustenie migrácií,
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

Bezpečné poradie:

```text
diagnostika servera
→ pri úplnom úspechu spustenie migrácií
→ kontrola stavu migrácií a fyzických väzieb
→ repository adaptéry
→ integračné testy
→ Validácia implementovaného stavu
```

Ak diagnostika zlyhá, migrácie sa nesmú spustiť.
