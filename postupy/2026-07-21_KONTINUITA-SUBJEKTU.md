# 7.8 — Kontinuita SUBJECT-u

## Východisko

SUBJECT je čokoľvek logicky zdôvodnené ako samostatný predmet skúmania.

Samotná zmena preto ešte neznamená vznik nového SUBJECT-u. Najprv treba určiť, či sa zmenila iba niektorá jeho vlastnosť, stav, vzťah alebo označenie, alebo či sa zmenilo to, čo tvorí jeho jadro totožnosti.

## Pravidlo kontinuity

```text
SUBJECT zostáva tým istým SUBJECT-om,
pokiaľ je zachovaná kontinuita jeho jadra totožnosti.
```

Zmena názvu, stavu, vlastnosti, umiestnenia, vlastníka, hodnotenia alebo vnútorného zloženia sama osebe identitu SUBJECT-u neruší.

Nový SUBJECT vzniká až vtedy, keď pôvodný predmet už nemožno bez logického rozporu považovať za pokračovanie toho istého predmetu skúmania.

## Jadro totožnosti

Jadro totožnosti nie je totožné s názvom ani s úplným zoznamom vlastností.

Je to minimálny súbor určujúcich znakov a väzieb, bez ktorých by predmet prestal byť tým istým predmetom.

Pracovný zápis:

```text
SUBJECT_IDENTITY_CORE
=
minimálny súbor určujúcich znakov a väzieb,
ktoré musia zostať zachované,
aby trvala totožnosť SUBJECT-u.
```

Jadro totožnosti sa musí určovať podľa druhu konkrétneho SUBJECT-u. Nemožno ho univerzálne nahradiť jediným technickým atribútom.

## Zmeny, ktoré spravidla zachovávajú identitu

Za predpokladu zachovania jadra totožnosti môže SUBJECT prežiť najmä:

- zmenu názvu alebo označenia,
- zmenu stavu,
- zmenu vlastníka alebo správcu,
- zmenu umiestnenia,
- zmenu rozsahu nepodstatných alebo rozšíriteľných častí,
- vznik nových udalostí,
- nové alebo zmenené hodnotenia,
- opravu nepresného opisu,
- vnútornú reorganizáciu,
- technologickú alebo formálnu transformáciu.

Tieto zmeny sa evidujú ako revízie, stavy, udalosti alebo vzťahy SUBJECT-u, nie automaticky ako nový SUBJECT.

## Zmeny, ktoré môžu vytvoriť nový SUBJECT

Nový SUBJECT vzniká najmä vtedy, keď:

1. zanikne kontinuita jadra totožnosti,
2. zmení sa základný predmet alebo účel natoľko, že pôvodné a nové nemožno skúmať ako tú istú vec,
3. jeden SUBJECT sa rozdelí na viac samostatných predmetov bez zachovania jedného určiteľného pokračovateľa,
4. viac SUBJECT-ov sa zlúči do nového predmetu, ktorého identita nie je iba pokračovaním jedného z nich,
5. vznikne odvodený výsledok, stav, udalosť alebo vzťah, ktorý je logicky zdôvodnený ako nový samostatný predmet skúmania,
6. zmena rozsahu odstráni alebo nahradí určujúce jadro pôvodného SUBJECT-u.

## Test kontinuity

Pri každej podstatnej zmene sa položia tieto otázky:

```text
1. Čo tvorilo jadro totožnosti pred zmenou?
2. Zostalo toto jadro po zmene zachované?
3. Možno nový stav bez rozporu označiť za pokračovanie pôvodného predmetu?
4. Sú pôvodné udalosti a hodnotenia stále pravdivo priraditeľné k tej istej kontinuite?
5. Vznikol popri pôvodnom predmete nový samostatne skúmateľný predmet?
```

Ak odpovede nepotvrdia kontinuitu, musí vzniknúť nový SUBJECT a jeho vzťah k pôvodnému SUBJECT-u sa zaznamená osobitne.

## Dôležité rozlíšenie

```text
zmena SUBJECT-u
≠
nový SUBJECT
```

Zmena SUBJECT-u znamená zmenu pri zachovaní jeho totožnosti.

Nový SUBJECT znamená vznik novej samostatnej kontinuity totožnosti.

## Rozdelenie a zlúčenie

Pri rozdelení alebo zlúčení sa nesmie kontinuita domyslieť iba podľa názvu alebo právneho nástupníctva.

Musí sa určiť:

- či niektorý následník zachováva jadro totožnosti pôvodného SUBJECT-u,
- či vznikli iba nové časti pôvodného SUBJECT-u,
- alebo či vznikli nové samostatné SUBJECT-y.

Preto môžu existovať vzťahy napríklad:

```text
CONTINUES
DERIVED_FROM
SPLIT_FROM
MERGED_FROM
REPLACES
PART_OF
```

Tieto vzťahy opisujú dej alebo pôvod. Samy osebe neurčujú totožnosť.

## Príklady

### Zmena názvu firmy

Ak organizácia zachová právnu, organizačnú a činnostnú kontinuitu, zmena názvu spravidla nevytvára nový SUBJECT.

### Rozšírenie projektu o modul

Ak zostáva zachovaný základný cieľ a kontinuita projektu, nový modul nemení identitu projektu. Modul však môže byť samostatným SUBJECT-om.

### Úplná zmena účelu projektu

Ak sa projekt simulácie termického lietania zmení na účtovný systém a pôvodné jadro sa nezachová, nejde už iba o nový stav. Vzniká nový SUBJECT, aj keby zostal rovnaký názov alebo repozitár.

### Revízia dokumentu

Nová revízia môže byť stavom alebo verziou toho istého dokumentu, ak zachováva jeho dokumentovú kontinuitu. Ak však vznikne samostatné dielo s novým účelom a vlastnou históriou, môže ísť o nový SUBJECT odvodený od pôvodného.

## Dôsledky pre METODIKU

METODIKA musí oddeliť:

```text
SUBJECT_IDENTITY
SUBJECT_DEFINITION
SUBJECT_SCOPE
SUBJECT_STATE
SUBJECT_EVENT
SUBJECT_RELATION
```

Zmena definície alebo rozsahu sa nesmie automaticky prepísať bez histórie.

Pri zmene musí byť možné spätne zistiť:

- čo bolo pred zmenou považované za jadro totožnosti,
- čo sa zmenilo,
- kto zmenu opísal,
- kedy zmena nastala,
- či bola kontinuita potvrdená,
- a ak nie, ktorý nový SUBJECT vznikol.

## Otvorená otázka pre 7.9

Kontinuita SUBJECT-u závisí od určenia jeho jadra totožnosti.

Ďalšia otázka preto znie:

```text
Ako sa pre konkrétny druh SUBJECT-u určí a Validuje jeho jadro totožnosti bez svojvôle?
```
