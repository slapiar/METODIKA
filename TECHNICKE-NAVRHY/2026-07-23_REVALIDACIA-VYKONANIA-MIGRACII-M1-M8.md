# ReValidácia vykonania migrácií M1 až M8

## Stav dokumentu

```text
PRACOVNÝ
```

## Predmet

Praktické vykonanie CodeIgniter migrácií M1 až M8 nad produkčnou databázou METODIKY na Hostingeri po úspešnej diagnostike databázových schopností a následné čítacie overenie fyzickej schémy cez `INFORMATION_SCHEMA`.

## Overený priebeh

Pred vykonaním migrácií príkaz:

```text
php spark migrate:status
```

potvrdil:

```text
všetkých osem migrácií bolo rozpoznaných
+
žiadna migrácia ešte nebola vykonaná
+
poradie bolo M1 až M8
+
databázové spojenie z Hostinger CLI bolo funkčné
```

Následne príkaz:

```text
php spark migrate
```

vykonal postupne:

```text
M1 CreateQuestionDerivationRequestReservations
M2 CreateQuestionDerivationRuns
M3 CreateQuestionDerivationRunDomainTerms
M4 CreateQuestionDerivationBranches
M5 CreateQuestionDerivationBranchDependencies
M6 CreateQuestionDerivationCandidates
M7 CreateQuestionDerivationRunResults
M8 CreateQuestionDerivationTraces
```

Výsledok CodeIgnitera:

```text
Migrations complete.
```

## Spätné overenie migrácií

Opakovaný príkaz:

```text
php spark migrate:status
```

potvrdil pre všetkých osem migrácií:

```text
Group       = default
Migrated On = 2026-07-23 08:36:20 UTC
Batch       = 1
```

## Fyzické overenie schémy

Po nasadení release `1.0.9` bol na Hostingeri spustený čítací príkaz:

```text
php spark metodika:verify-question-schema
```

Príkaz čítaním `INFORMATION_SCHEMA` potvrdil:

```text
8 z 8 očakávaných tabuliek existuje
+
všetkých 8 tabuliek používa InnoDB
+
všetkých 8 tabuliek používa utf8mb4_bin
+
10 z 10 fyzických cudzích kľúčov existuje
+
všetkých 10 cudzích kľúčov používa DELETE RESTRICT a UPDATE RESTRICT
```

Výsledok príkazu:

```text
Overenie úspešné: 8 tabuliek a 10 riadkov cudzích kľúčov.
```

## Kritériá

```text
K1 — produkčné CLI načítalo externé prostredie a pripojilo sa k databáze,
K2 — pred vykonaním bolo rozpoznaných presne osem nevykonaných migrácií,
K3 — migrácie boli vykonané v poradí M1 až M8,
K4 — CodeIgniter neohlásil chybu ani prerušenie,
K5 — spätný status eviduje všetkých osem migrácií ako vykonaných,
K6 — všetky migrácie patria do skupiny default,
K7 — všetky migrácie patria do jedného batchu 1,
K8 — všetky migrácie majú zhodný čas vykonania,
K9 — fyzicky existuje presne všetkých osem očakávaných tabuliek,
K10 — všetky očakávané tabuľky používajú InnoDB a utf8mb4_bin,
K11 — fyzicky existuje všetkých desať očakávaných väzieb,
K12 — všetky fyzické väzby používajú DELETE RESTRICT a UPDATE RESTRICT.
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
```

## Výsledok reValidácie

```text
VALIDATION_RESULT
=
VALID_WITH_LIMITATIONS
```

Význam:

```text
migrácie M1 až M8 boli prakticky vykonané a evidované CodeIgniterom
+
produkčná databázová schéma bola založená v rozsahu migrácií
+
fyzické tabuľky, engine, collation a cudzie kľúče boli samostatne potvrdené
+
repository adaptéry a integračné testy ešte neboli vykonané
```

## Obmedzenia

```text
L1 — repository adaptéry ešte neexistujú,
L2 — aplikačné integračné testy nad produkčnou schémou ešte neboli vykonané.
```

## Nasledujúci krok

```text
implementovať repository adaptéry
→ vykonať integračné testy
→ vytvoriť ďalšiu reValidáciu implementovaného stavu
```
