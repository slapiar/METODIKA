# METODIKA

## Univerzálny systém skúmania, riadenia projektov, AI agentov a uchovávania znalostí

---

# Poslanie projektu

Projekt **METODIKA** vznikol ako centrálny systém uchovávania univerzálnych princípov skúmania a riadenia živých projektov.

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

# Aktuálna štruktúra repozitára

```text
METODIKA/
│
├── README.md
├── POJMY-A-DEFINICIE.md
├── AUTORITA.md
├── CHANGELOG.md
├── uQestions.md                  # rozcestník pre staršie odkazy
├── DISCIPLINA.md                 # rozcestník pre staršie odkazy
├── .gitignore
│
├── OTAZKY/
│   ├── UNIVERZALNE/
│   │   ├── README.md
│   │   └── Objektivita-XY.md
│   ├── ATRIBUTOVE/
│   │   └── ZODPOVEDNOST/
│   │       └── Disciplina.md
│   └── PROJEKTOVE/
│
├── HODNOTENIA/
│
├── PRINCIPY/
├── PROJEKTY/
├── CHECKLISTY/
├── AGENTI/
├── postupy/
├── poznámky/
├── ARCHIV/
│
└── app/
    └── setup.php
```

Tento strom zachytáva významové korene. Prázdne alebo ďalej rozvíjané adresáre sa dopĺňajú až podľa metodicky potvrdenej potreby.

---

# Autoritatívne dokumenty

- [POJMY-A-DEFINICIE.md](POJMY-A-DEFINICIE.md) — platné základné pojmy, rozmery a logické rozlíšenia.
- [AUTORITA.md](AUTORITA.md) — spoločný pracovný koreň Autority.
- [OTAZKY/UNIVERZALNE/Objektivita-XY.md](OTAZKY/UNIVERZALNE/Objektivita-XY.md) — univerzálna objektívna matica otázok.
- [OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md](OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md) — disciplína ako atribút Zodpovednosti.
- [CHANGELOG.md](CHANGELOG.md) — priebežný záznam podstatných zmien.

Dokumenty v `poznámky/` a `postupy/` môžu zachytávať vývoj, pracovné hypotézy a neplatné návrhy. Nesmú mať vyššiu Autoritu než platné definície.

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

Hodnota `0` sama osebe neznamená absolútnu neexistenciu. Môže znamenať napríklad:

- neprítomnosť prejavu,
- nesplnenie podmienky,
- chýbajúci dôkaz,
- nesprávny čas,
- nepresne určený predmet,
- potrebu ďalšieho rozkladu otázky.

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
```

```text
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
Z ⊕ T   — XOR
```

Logický operátor sa nesmie vybrať automaticky. Musí ho určiť presný význam otázky.

`AND` a `IF` nie sú zameniteľné:

```text
Z ∧ T
```

vyžaduje súčasné potvrdenie Z aj T, kým:

```text
T → Z
```

je nepravdivé iba vtedy, keď `T = 1` a `Z = 0`.

---

# Otázka, odpoveď a hodnotiaci záznam

Otázka je presne vymedzený logický nástroj skúmania jednej podmienky na jednom určenom predmete, v jednom určenom význame, ktorý umožňuje odpoveď `[1/0]`.

Otázka nie je odpoveď, dôkaz ani hodnotiaci záznam.

Hodnotiaci záznam vzniká použitím otázky na konkrétny predmet v určenom čase a obsahuje najmenej:

```text
otázku
× predmet hodnotenia
× odpoveď [1/0]
× dôkaz
× Validáciu
× Autoritu Validácie
× časovú platnosť
```

Jedna otázka môže mať mnoho hodnotiacich záznamov. Preto sa otázky a hodnotenia nesmú ukladať v jednom nerozlíšenom priestore.

---

# Autorita

Základný pracovný vzorec zostáva:

```text
Auth = (Oprávnenia × Povinnosti × Zodpovednosť) / Validácia
```

Autorita sa nerovná Zodpovednosť. Disciplína je atribútom Zodpovednosti, nie samostatným súčiniteľom vzorca Autority.

Spôsob algoritmického zloženia veľkého množstva elementárnych odpovedí do výsledného bitu Autority ešte nie je definitívne určený a nesmie sa nahradiť dojmom ani jednoduchým mechanickým súčinom.

---

# Základné pravidlá

1. Nič sa nevymýšľa bez overenia.
2. Najprv analýza, až potom implementácia.
3. Vždy sa identifikuje aktuálny stav projektu.
4. Každá významná zmena musí mať checkpoint a záznam.
5. Po každom zápise sa overí skutočný obsah súboru v repozitári.
6. Agent nesmie predpokladať, že si metodiku pamätá.
7. Pred prácou musí prečítať platnú univerzálnu aj projektovú metodiku.
8. Pracovný postup ani poznámka sa nesmú vydávať za platnú definíciu.
9. Pri nejasnosti sa najprv hľadá správna otázka.
10. SQL schéma ani softvérový model nesmú predbehnúť elementárnu logiku.

---

# Aktuálny smer ďalšej práce

```text
1. potvrdiť elementárnu podstatu otázky
2. potvrdiť elementárnu podstatu odpovede [1/0]
3. určiť príslušnosť otázky k X, Y, Z alebo T
4. určiť spôsob výberu logického vzťahu S medzi Z a T
5. potvrdiť minimálne zloženie hodnotiaceho záznamu
6. až potom odvodiť logický model databázy
7. SQL vytvoriť až ako výsledok potvrdenej metodiky
```

> Keď dokážeme potvrdiť správnosť otázok, odpovedí, logických vzťahov, dôkazov, Validácie a Autority výsledku, môžeme zodpovedne tvoriť čokoľvek.
