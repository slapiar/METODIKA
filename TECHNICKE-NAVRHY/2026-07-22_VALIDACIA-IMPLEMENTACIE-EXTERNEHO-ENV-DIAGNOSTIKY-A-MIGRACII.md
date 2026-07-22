# Validácia implementácie externého env, diagnostiky a migrácií

## Stav dokumentu

```text
PRACOVNÝ
```

## Predmet

```text
ExternalEnvironment
+ webový a CLI bootstrap
+ VerifyDatabaseCapabilities
+ migrácie M1 až M8
```

Táto Validácia neposudzuje výsledok príkazu na Hostingeri ani vykonanú databázovú schému.

## Kritériá

```text
K1 — externý env sa načíta pred bootstrapom webu,
K2 — externý env sa načíta pred bootstrapom CLI,
K3 — prednosť má METODIKA_ENV_FILE,
K4 — predvolený súbor je mimo /codei,
K5 — loader nevypisuje hodnoty tajomstiev,
K6 — diagnostika nevypisuje prihlasovacie údaje ani DSN,
K7 — diagnostika overuje verziu, InnoDB, utf8mb4_bin a DATETIME(6),
K8 — neúspešná diagnostika vracia chybu,
K9 — existuje presne osem migrácií M1 až M8,
K10 — migračné poradie rešpektuje rodičovské väzby,
K11 — tabuľky používajú InnoDB a utf8mb4_bin,
K12 — historické cudzie kľúče používajú RESTRICT,
K13 — migrácie nepoužívajú ON DELETE CASCADE ani triggery,
K14 — každá down operácia odstraňuje iba vlastnú tabuľku,
K15 — migrácie neboli vydávané za vykonané bez serverového výsledku.
```

## Výsledky

```text
K1  = 1
K2  = 1
K3  = 1
K4  = 1
K5  = 1
K6  = 1
K7  = 1
K8  = 1
K9  = 1
K10 = 1
K11 = 1
K12 = 1
K13 = 1
K14 = 1
K15 = 1
```

## Obmedzenia

```text
L1 — serverová verzia nebola prakticky zistená,
L2 — InnoDB nebolo prakticky potvrdené,
L3 — utf8mb4_bin nebolo prakticky potvrdené,
L4 — DATETIME(6) nebolo prakticky potvrdené,
L5 — migrácie neboli spustené,
L6 — PHP syntax nebola vykonaná v serverovom runtime,
L7 — fyzické cudzie kľúče neboli integračne overené.
```

## Výsledok Validácie

```text
VALIDATION_RESULT
=
VALID_WITH_LIMITATIONS
```

Význam:

```text
implementácia je staticky zhodná s Validovaným databázovým návrhom
+
možno vykonať bezpečnú diagnostiku servera
+
migrácie sa smú spustiť iba po úplnom úspechu diagnostiky
+
implementovaný databázový stav zatiaľ nie je potvrdený
```

## Nasledujúci krok

```text
vykonať diagnostický príkaz na Hostingeri
→ zaznamenať iba výsledok schopností
→ pri úplnom úspechu spustiť migrácie
→ overiť stav migrácií a fyzické väzby
→ vytvoriť reValidáciu implementovaného stavu
```
