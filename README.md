# METODIKA

## Univerzálny systém skúmania, riadenia projektov, AI agentov a uchovávania znalostí

---

# Poslanie projektu

Projekt **METODIKA** je centrálnym systémom uchovávania univerzálnych princípov skúmania a riadenia živých projektov.

Jeho cieľom je:

- zachovať kontinuitu práce medzi ľuďmi a AI agentmi,
- zabrániť strate znalostí počas migrácií projektov,
- vytvoriť jednotnú metodiku použiteľnú pre ľubovoľný predmet skúmania,
- minimalizovať chyby spôsobené nesprávne položenými otázkami,
- vytvoriť dlhodobo udržateľnú operačnú pamäť projektov,
- umožniť algoritmické rozhodovanie založené na jednoznačne overených podmienkach.

---

# Povinné poradie práce

Pred vykonaním práce musí človek alebo AI agent obnoviť pamäť a postupovať v tomto poradí:

```text
1. Univerzálna metodika
2. Zoznam projektov
3. Identifikácia konkrétneho projektu
4. Projektové metodické pokyny
5. Analýza aktuálneho stavu
6. Návrh riešenia
7. Implementácia
8. Záznam a overenie zmien
```

Implementácia nesmie predbehnúť metodiku. Databázová alebo softvérová štruktúra môže vzniknúť až ako dôsledok potvrdených významových vzťahov.

---

# Skutočná štruktúra repozitára

Nasledujúci strom zachytáva aktuálne evidované súbory a významové korene vetvy `main`. Prázdne adresáre môžu byť v Gite neprítomné, kým neobsahujú súbor.

```text
METODIKA/
│
├── README.md
├── POJMY-A-DEFINICIE.md
├── AUTORITA.md
├── CHANGELOG.md
├── uQestions.md
├── DISCIPLINA.md
├── lara_dep.md
├── startApp.sh
├── .gitignore
│
├── OTAZKY/
│   ├── README.md
│   ├── SIEDMA-PLOCHA-S.md
│   ├── UNIVERZALNE/
│   │   ├── README.md
│   │   └── Objektivita-XY.md
│   ├── ATRIBUTOVE/
│   │   ├── README.md
│   │   └── ZODPOVEDNOST/
│   │       └── Disciplina.md
│   └── PROJEKTOVE/
│       └── README.md
│
├── HODNOTENIA/
│   └── README.md
│
├── PRINCIPY/
│   └── HermetickePrincipy.md
│
├── PROJEKTY/
│   └── ZoznamProjektov.md
│
├── CHECKLISTY/
│   └── StartProjektu.md
│
├── postupy/
│   ├── README.md
│   ├── Inicializácia práce.md
│   ├── 2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md
│   ├── 2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md
│   ├── 2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
│   ├── 2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md
│   └── ďalšie metodické a historické pracovné dokumenty
│
├── TECHNICKE-NAVRHY/
│   ├── README.md
│   ├── 2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md
│   ├── 2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md
│   ├── 2026-07-22_VALIDACIA-APLIKACNEJ-SLUZBY-ODVODZOVANIA-OTAZOK.md
│   ├── 2026-07-22_POLITIKA-OPAKOVANEJ-REQUEST-REFERENCE.md
│   └── 2026-07-22_VALIDACIA-POLITIKY-OPAKOVANEJ-REQUEST-REFERENCE.md
│
├── poznámky/
│   ├── README.md
│   └── 2026-07-21_08-06_DB-OTAZKY-ALG.md
│
├── AGENTI/
├── ARCHIV/
│
├── app/
│   └── setup.php
│
└── codei/
    ├── app/
    ├── public/
    ├── tests/
    └── ďalšie súčasti CodeIgniter 4.7.4
```

---

# Autoritatívne dokumenty a registre

- [POJMY-A-DEFINICIE.md](POJMY-A-DEFINICIE.md) — platné základné pojmy, rozmery a logické rozlíšenia.
- [AUTORITA.md](AUTORITA.md) — spoločný pracovný koreň Autority.
- [OTAZKY/README.md](OTAZKY/README.md) — pravidlá otázok a ich rozmerového zaradenia.
- [OTAZKY/UNIVERZALNE/Objektivita-XY.md](OTAZKY/UNIVERZALNE/Objektivita-XY.md) — univerzálna objektívna matica otázok.
- [OTAZKY/SIEDMA-PLOCHA-S.md](OTAZKY/SIEDMA-PLOCHA-S.md) — metodický koreň logického vzťahu S medzi Z a T.
- [OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md](OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md) — Disciplína ako atribút Zodpovednosti.
- [HODNOTENIA/README.md](HODNOTENIA/README.md) — pravidlá hodnotiacich záznamov.
- [postupy/README.md](postupy/README.md) — záväzný register stavu pracovných postupov.
- [TECHNICKE-NAVRHY/README.md](TECHNICKE-NAVRHY/README.md) — záväzný register technických návrhov oddelených od metodických dokumentov.
- [poznámky/README.md](poznámky/README.md) — záväzný register stavu pracovných poznámok.
- [CHANGELOG.md](CHANGELOG.md) — stručný záznam zmien a odkazy na miesta platných definícií.

Dokumenty v `poznámky/`, `postupy/` a `TECHNICKE-NAVRHY/` nemajú vyššiu Autoritu než platné definície. Ich aktuálny stav sa neurčuje dojmom ani názvom súboru, ale registrami v príslušných adresároch.

Základné oddelenie:

```text
postupy/
= významové a metodické pracovné dokumenty

TECHNICKE-NAVRHY/
= technická reprezentácia odvodeného a Validovaného významu

codei/
= vykonateľná implementácia
```

---

# Sedem hermetických princípov metodiky

1. Mentalizmus
2. Korešpondencia
3. Vibrácia
4. Polarita
5. Rytmus
6. Príčina a následok
7. Generativita

Princípy nie sú poradím pracovných krokov ani oddelenými vrstvami. Tvoria spoločný sedemrozmerný priestor skúmania každého projektu, procesu, otázky a prejavu.

Každý princíp možno skúmať prostredníctvom všetkých siedmich princípov vrátane seba samého. Tak vzniká matica 7 × 7, teda 49 principiálnych polí.

---

# Elementárna logika

> Čo je logické, musí byť overiteľné.

Elementárna otázka musí viesť k jednej presne určenej odpovedi:

```text
1 = skúmaná podmienka je v určenom význame potvrdená
0 = skúmaná podmienka nie je v určenom význame potvrdená
```

Hodnota `0` sama osebe neznamená absolútnu neexistenciu. Môže znamenať neprítomnosť prejavu, nesplnenie podmienky, chýbajúci dôkaz, nesprávny čas, nepresne určený predmet alebo potrebu ďalšieho rozkladu otázky.

---

# Rozmery života

```text
X = čo sa prejavuje?
Y = ako sa to prejavuje?
Z = akú hodnotu, mieru, rozsah, primeranosť a význam má prejav?
T = kedy tento zmysel platí, trvá alebo nadobúda prioritu?
```

## Objektivita

```text
Objektivita = X × Y
```

Objektivita predstavuje prejav v existencii. Znak `×` vyjadruje spoločné skúmanie samostatných rozmerov; nie je automaticky logickým AND ani obyčajným aritmetickým násobením.

## Subjektivita

```text
Subjektivita = (Z, T)
```

Z a T sú samostatné rozmery subjektivity. Zápis `Subjektivita = Z × T` sa nepoužíva, pretože by nepresne predurčoval ich logický vzťah.

## Život

```text
Život = X × Y × Z × T

X × Y         = [1/0]^2
X × Y × Z     = [1/0]^3
X × Y × Z × T = [1/0]^4
```

Exponent označuje počet samostatne hodnotených rozmerov. Jedna nula automaticky neruší význam ostatných rozmerov.

---

# Siedma plocha zmyslu života

Samotné hodnoty Z a T ešte neurčujú, ako spolu v konkrétnej otázke logicky súvisia.

```text
S = logický vzťah(Z, T)
```

Siedma plocha je metaplochou vzťahov. Nie je ďalším základným rozmerom popri X, Y, Z a T.

Podľa významu konkrétnej otázky môže vzťah používať napríklad:

```text
Z ∧ T   — AND
Z ∨ T   — OR
T → Z   — IF T THEN Z
Z → T   — IF Z THEN T
```
