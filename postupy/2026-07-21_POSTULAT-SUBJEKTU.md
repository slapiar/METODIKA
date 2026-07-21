# Postulát subjektu v METODIKE

## Stav dokumentu

Tento dokument určuje základný postulát pojmu `SUBJECT` a jeho minimálne metodické hranice. Neurčuje ešte fyzickú databázovú reprezentáciu, typológiu predmetov ani SQL schému.

---

# Základný postulát

```text
SUBJECT
=
čokoľvek logicky zdôvodnené ako predmet skúmania.
```

Subjektom teda nemusí byť iba fyzická vec, osoba alebo dokument. Subjektom môže byť aj jav, proces, vzťah, rozhodnutie, stav, udalosť, výpočet, pravidlo, tvrdenie, plán, hypotéza, množina, abstraktný model alebo iný určiteľný prejav.

Rozhodujúce nie je, do akej bežnej kategórie patrí, ale či je logicky zdôvodnené, prečo má byť samostatným predmetom elementárnej otázky.

---

# Logické zdôvodnenie subjektu

Samotné pomenovanie nestačí. Aby niečo mohlo byť v METODIKE evidované ako `SUBJECT`, musí byť možné určiť najmenej:

```text
1. čo je predmetom skúmania,
2. prečo je oddelený od iných predmetov,
3. v akom rozsahu sa skúma,
4. podľa čoho sa rozpozná jeho totožnosť,
5. na čo sa vzťahuje položená otázka.
```

Logické zdôvodnenie nemusí byť rozsiahly text. Musí však zabrániť tomu, aby sa jedna odpoveď omylom preniesla na iný predmet, inú časť, inú verziu alebo iný rozsah.

---

# Subjekt nie je obmedzený existenciou vo fyzickom svete

METODIKA môže skúmať aj predmety, ktoré:

```text
- existovali v minulosti,
- ešte len môžu vzniknúť,
- sú plánované,
- sú hypotetické,
- sú abstraktné,
- sú odvodené výpočtom,
- sú tvorené vzťahom viacerých iných predmetov,
- boli vyvrátené alebo zanikli.
```

Podmienkou nie je fyzická existencia. Podmienkou je logická určiteľnosť predmetu a význam otázky, ktorá sa naň používa.

Preto môže byť subjektom napríklad aj:

```text
- plánovaný let,
- neuskutočnené rozhodnutie,
- simulovaný stav atmosféry,
- právny vzťah,
- výsledok modelu,
- tvrdenie, ktorého pravdivosť sa skúma,
- historický stav už neexistujúceho systému.
```

---

# Hranica postulátu

Z postulátu nevyplýva:

```text
čokoľvek pomenované
=
automaticky platný SUBJECT
```

Platí:

```text
pomenovanie
+
logické zdôvodnenie
+
určený rozsah
+
rozlíšiteľná totožnosť
→
kandidát na SUBJECT
```

Ak nie je možné určiť, čo presne sa skúma, nejde ešte o dostatočne určený subjekt.

---

# Subjekt a otázka

Možnosť položiť gramatickú otázku sama osebe nestačí. Otázka musí byť elementárna a subjekt musí byť určený tak, aby odpoveď `[1/0]` mala jednoznačný rozsah.

```text
QUESTION + logicky zdôvodnený SUBJECT
→
možnosť vzniku EVALUATION
```

Bez logického zdôvodnenia subjektu môže byť otázka formálne správna, ale hodnotenie nebude mať spoľahlivý význam.

---

# Subjekt môže byť zložený

Subjekt môže vzniknúť aj ako vzťah alebo zloženie iných predmetov.

Príklad:

```text
SUBJECT_A = pilot
SUBJECT_B = plánovaný let
SUBJECT_C = meteorologická situácia

SUBJECT_R = vzťah(pilot, plánovaný let, meteorologická situácia)
```

`SUBJECT_R` však nevzniká automaticky iba spoločným uvedením troch predmetov. Musí byť určené:

```text
- aký vzťah sa skúma,
- prečo tvorí samostatný predmet,
- aké sú jeho hranice,
- podľa čoho zostáva tým istým vzťahom.
```

---

# Subjekt ako výsledok predchádzajúceho procesu

Aj výsledok hodnotenia, Validácie alebo výpočtu sa môže neskôr stať novým subjektom ďalšieho skúmania.

To však neznamená, že výsledok je automaticky samostatnou identitou už v pôvodnej udalosti.

Platí:

```text
výsledok udalosti
≠
automaticky SUBJECT
```

ale:

```text
výsledok udalosti
+
nové logické zdôvodnenie jeho samostatného skúmania
→
nový SUBJECT
```

Tým sa zachová rozdiel medzi vlastnosťou jednej udalosti a predmetom nasledujúcej otázky.

---

# Totožnosť subjektu

Základné pravidlo zostáva:

```text
SUBJECT identity
=
logicky zdôvodnená kontinuita predmetu skúmania.
```

Pre rôzne druhy subjektov sa kontinuita môže určovať odlišne:

```text
- pri osobe kontinuitou osoby,
- pri dokumente kontinuitou konkrétneho dokumentu,
- pri procese kontinuitou určeného procesu,
- pri hypotéze kontinuitou jej významu,
- pri vzťahu kontinuitou členov a významu vzťahu,
- pri historickom stave určeným časom a rozsahom,
- pri simulácii vstupmi, pravidlami a konkrétnym behom modelu.
```

Preto nemôže existovať jedno univerzálne technické pravidlo totožnosti pre všetky typy subjektov. Univerzálny je postulát; pravidlá kontinuity sa odvodzujú podľa druhu predmetu.

---

# Pracovný test prijatia subjektu

Pred prijatím nového subjektu treba vedieť odpovedať:

```text
1. Čo presne skúmame?
2. Prečo to tvorí samostatný predmet?
3. Kde sú hranice predmetu?
4. Čím sa odlišuje od podobných predmetov?
5. Čo musí zostať zachované, aby šlo stále o ten istý subjekt?
6. Môže odpoveď patriaca tomuto subjektu omylom platiť aj na niečo iné?
```

Ak posledná otázka odhalí nejednoznačnosť, subjekt treba spresniť alebo rozdeliť.

---

# Dôsledok pre typológiu

METODIKA nesmie vytvoriť uzavretý zoznam povolených druhov subjektov.

Typológia môže pomáhať:

```text
- určovať pravidlá totožnosti,
- určovať povinné údaje,
- Validovať rozsah,
- zjednodušovať vyhľadávanie,
- zdieľať pravidlá medzi podobnými predmetmi.
```

Nesmie však obmedziť základný postulát.

```text
typ SUBJECT-u
je pomôcka pre určenie pravidiel,
nie podmienka práva existovať ako SUBJECT.
```

---

# Pracovný záver

```text
Subjektom môže byť čokoľvek,
ak je logicky zdôvodnené,
čo sa skúma,
prečo je to samostatný predmet,
aký má rozsah
a podľa čoho sa určuje jeho totožnosť.
```

Tým vzniká otvorený, ale nie bezhraničný model predmetu.

Otvorenosť zabezpečuje slovo `čokoľvek`.

Hranicu zabezpečuje požiadavka `logicky zdôvodnené`.
