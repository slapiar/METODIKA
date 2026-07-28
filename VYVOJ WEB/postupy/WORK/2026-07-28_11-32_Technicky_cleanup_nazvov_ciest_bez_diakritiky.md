# Technický cleanup názvov hlavných pracovných ciest bez diakritiky

Dátum: 2026-07-28 11:32 Europe/Bratislava

## Stav dokumentu

```text
PRACOVNÝ
GATE=OPEN
INI=VYVOJ WEB/postupy/WORK/INI/2026-07-28_11-31_INI_Technicky_cleanup_nazvov_ciest_bez_diakritiky.md
HEAD_PRI_OTVORENÍ=4641b24730bfdb43c05e8fbe115f41c874cf4dc6
```

## Účel kroku

Tento pracovný záznam dokumentuje samostatný technický cleanup názvov hlavných pracovných ciest bez diakritiky.

Zásah nemení význam dokumentov, stav brán, výsledky testov, produkciu ani databázu.

## Rozsah

Povolený je výhradne tento typ zmeny:

```text
názov hlavného pracovného adresára s diakritikou -> názov hlavného pracovného adresára bez diakritiky
```

Následne sa nové názvy aplikujú vo všetkých dotknutých markdown záznamoch, registroch a odkazoch.

## Pravidlo premenovania

```text
á -> a
ä -> a
č -> c
ď -> d
é -> e
í -> i
ľ -> l
ĺ -> l
ň -> n
ó -> o
ô -> o
ŕ -> r
š -> s
ť -> t
ú -> u
ý -> y
ž -> z
Á -> A
Ä -> A
Č -> C
Ď -> D
É -> E
Í -> I
Ľ -> L
Ĺ -> L
Ň -> N
Ó -> O
Ô -> O
Ŕ -> R
Š -> S
Ť -> T
Ú -> U
Ý -> Y
Ž -> Z
```

Medzery, spojovníky, podčiarkovníky a veľkosť písmen sa týmto krokom nemenia.

## Vykonané hlavné zmeny

```text
povodny koreň metodického vývoja -> VYVOJ METODIKY1
povodny koreň webového vývoja -> VYVOJ WEB
povodny koreň pracovných poznámok -> poznamky
povodny vnútorný adresár pracovných poznámok vo webovom vývoji -> VYVOJ WEB/poznamky
```

Názvy jednotlivých historických dokumentov sa týmto cleanupom nemenili. Účelom zásahu bolo obnoviť viditeľnosť a čitateľnosť hlavných pracovných koreňov repozitára.

## Validácia po vykonaní

Po presune musí platiť:

```text
HLAVNE_PRACOVNE_KORENE_BEZ_DIAKRITIKY=true
BROKEN_REFERENCES_TO_OLD_DIACRITIC_PATHS=0
PRODUCTION_CHANGED=false
DATABASE_CHANGED=false
```

## Rollback

Rollback je návrat na commit pred začiatkom cleanupu:

```text
ROLLBACK_HEAD=4641b24730bfdb43c05e8fbe115f41c874cf4dc6
```

## Stav

```text
KROK=VYKONANÝ_LOKÁLNE_A_PUSHNUTÝ_NA_VETVU_JOYEE_PRIORITY
POVOLENÝ_ĎALŠÍ_ÚKON=Overiť odkazy v dotknutých záznamoch a následne rozhodnúť o merge do main
```
