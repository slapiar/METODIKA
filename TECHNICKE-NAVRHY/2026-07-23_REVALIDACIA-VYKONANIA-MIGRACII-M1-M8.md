# ReValidácia vykonania migrácií M1 až M8

## Stav dokumentu

```text
PRACOVNÝ
```

## Predmet

Praktické vykonanie CodeIgniter migrácií M1 až M8 nad produkčnou databázou METODIKY na Hostingeri po úspešnej diagnostike databázových schopností.

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

## Spätné overenie

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

## Kritériá

```text
K1 — produkčné CLI načítalo externé prostredie a pripojilo sa k databáze,
K2 — pred vykonaním bolo rozpoznaných presne osem nevykonaných migrácií,
K3 — migrácie boli vykonané v poradí M1 až M8,
K4 — CodeIgniter neohlásil chybu ani prerušenie,
K5 — spätný status eviduje všetkých osem migrácií ako vykonaných,
K6 — všetky migrácie patria do skupiny default,
K7 — všetky migrácie patria do jedného batchu 1,
K8 — všetky migrácie majú zhodný čas vykonania.
```

## Výsledky

```text
K1 = 1
K2 = 1
K3 = 1
K4 = 1
K5 = 1
K6 = 1
K7 = 1
K8 = 1
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
repository adaptéry a integračné testy nad fyzickými väzbami ešte neboli vykonané
```

## Obmedzenia

```text
L1 — fyzické cudzie kľúče neboli samostatne prečítané z INFORMATION_SCHEMA,
L2 — repository adaptéry ešte neexistujú,
L3 — aplikačné integračné testy nad produkčnou schémou ešte neboli vykonané.
```

## Nasledujúci krok

```text
overiť fyzické tabuľky a cudzie kľúče
→ implementovať repository adaptéry
→ vykonať integračné testy
→ vytvoriť ďalšiu reValidáciu implementovaného stavu
```
