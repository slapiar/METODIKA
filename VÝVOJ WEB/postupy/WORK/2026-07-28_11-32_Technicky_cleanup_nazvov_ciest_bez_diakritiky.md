# Technický cleanup názvov ciest bez diakritiky

Dátum: 2026-07-28 11:32 Europe/Bratislava

## Stav dokumentu

```text
PRACOVNÝ
GATE=OPEN
INI=VÝVOJ WEB/postupy/WORK/INI/2026-07-28_11-31_INI_Technicky_cleanup_nazvov_ciest_bez_diakritiky.md
HEAD_PRI_OTVORENÍ=4641b24730bfdb43c05e8fbe115f41c874cf4dc6
```

## Účel kroku

Tento pracovný záznam dokumentuje samostatný technický cleanup názvov ciest bez diakritiky.

Zásah nemení význam dokumentov, stav brán, výsledky testov, produkciu ani databázu.

## Rozsah

Povolený je výhradne tento typ zmeny:

```text
názov adresára alebo súboru s diakritikou -> názov adresára alebo súboru bez diakritiky
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

## Predpokladané hlavné zmeny

```text
VÝVOJ METODIKY1 -> VYVOJ METODIKY1
VÝVOJ WEB -> VYVOJ WEB
poznámky -> poznamky
Inicializácia práce.md -> Inicializacia prace.md
```

Ďalšie názvy súborov s diakritikou budú upravené podľa rovnakého pravidla.

## Validácia po vykonaní

Po presune musí platiť:

```text
GIT_PATHS_WITH_DIACRITICS=0
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
KROK=OTVORENÝ
POVOLENÝ_ĎALŠÍ_ÚKON=Vykonať fyzické premenovanie ciest a aktualizovať odkazy v dotknutých záznamoch
```