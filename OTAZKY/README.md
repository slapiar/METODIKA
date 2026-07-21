# OTÁZKY — metodický koreň

## Účel dokumentu

Tento dokument určuje spoločnú elementárnu podstatu otázky v projekte METODIKA. Neurčuje ešte konečný databázový model, identifikátory, verzie ani balíky otázok.

---

# Elementárna otázka

> Otázka je presne vymedzený logický nástroj skúmania jednej podmienky na jednom určenom predmete, v jednom určenom význame, ktorý umožňuje jednu jednoznačnú odpoveď `[1/0]`.

Elementárna otázka nesmie v jednom výroku spájať viac podmienok, ak ich možno pravdivo hodnotiť rozdielne.

Napríklad otázka:

> Je dokument úplný a platný?

nie je elementárna, pretože dokument môže byť úplný a zároveň neplatný alebo neúplný a platný v inom rozsahu.

Musí sa rozložiť najmenej na:

```text
Je dokument úplný v určenom rozsahu? [1/0]
Je dokument platný v určenom čase a vzťahu? [1/0]
```

---

# Minimálne určenie otázky

Aby bolo možné otázku jednoznačne vyhodnotiť, musí byť z jej významu určiteľné:

```text
1. čo sa skúma,
2. ktorá jedna podmienka sa skúma,
3. v akom význame sa podmienka používa,
4. na aký predmet sa otázka vzťahuje,
5. ktorý rozmer X, Y, Z alebo T otázka skúma,
6. aký dôkaz môže potvrdiť odpoveď,
7. kto alebo čo má Autoritu odpoveď Validovať,
8. v akom čase alebo rozsahu odpoveď platí.
```

Nie všetky údaje musia byť súčasťou samotnej vety otázky. Musia však byť jednoznačne určené v kontexte jej použitia a hodnotiaceho záznamu.

---

# Otázka a rozmery

Každá elementárna otázka musí mať určené, ktorý rozmer skúma:

```text
X — čo sa prejavuje?
Y — ako sa to prejavuje?
Z — akú hodnotu, mieru, rozsah, primeranosť alebo význam má prejav?
T — kedy zmysel platí, trvá alebo nadobúda prioritu?
```

Jedna elementárna otázka má primárne skúmať jeden rozmer.

Ak veta súčasne vyžaduje odpoveď na viac rozmerov, musí sa buď:

- rozložiť na samostatné elementárne otázky,
- alebo musí byť výslovne určené, že ide o zložené hodnotenie samostatných odpovedí.

Zápis:

```text
X × Y × Z × T
```

neznamená jednu otázku so štyrmi nerozlíšenými podmienkami. Znamená spoločné skúmanie štyroch samostatných rozmerov.

---

# Otázka a siedma plocha

Siedma plocha zmyslu života určuje logický vzťah medzi samostatnými rozmermi Z a T:

```text
S = logický vzťah(Z, T)
```

Otázka, ktorá pracuje so Z a T, musí určiť, aký vzťah skúma. Môže ísť napríklad o:

```text
Z ∧ T   — obe podmienky musia platiť súčasne
Z ∨ T   — platí aspoň jedna podmienka
T → Z   — ak nastane T, musí platiť Z
Z → T   — ak platí Z, musí byť určené alebo potvrdené T
Z ⊕ T   — platí práve jedna podmienka
```

Operátor nesmie byť doplnený automaticky podľa použitého slova „a“, „alebo“ či „ak“. Musí zodpovedať skutočnému významu otázky.

---

# Otázka nie je hodnotenie

Otázka je nástroj skúmania. Sama neobsahuje konkrétnu odpoveď na konkrétny predmet.

Treba rozlišovať:

```text
otázka            = čo a akým logickým spôsobom sa zisťuje
predmet hodnotenia = na čo sa otázka práve používa
odpoveď            = výsledok [1/0]
dôkaz               = o čo sa výsledok opiera
Validácia           = overenie správnosti výsledku
Autorita Validácie  = oprávnenie Validáciu vykonať
```

Jedna otázka môže byť použitá mnohokrát na rôzne predmety, v rôznych časoch a s rôznymi výsledkami.

---

# Univerzálna, atribútová a projektová otázka

Aktuálne rozlišujeme tri významové priestory:

```text
OTAZKY/UNIVERZALNE/
OTAZKY/ATRIBUTOVE/
OTAZKY/PROJEKTOVE/
```

## Univerzálna otázka

Skúma všeobecnú podmienku použiteľnú bez väzby na jediný konkrétny projektový predmet.

## Atribútová otázka

Skúma podmienku odvodenú pre určitú vlastnosť alebo vnútorný proces, napríklad Disciplínu ako atribút Zodpovednosti.

## Projektová otázka

Používa metodiku na konkrétnu vec, jav, rozhodnutie, úlohu, osobu, agenta, proces alebo projektový predmet.

Toto rozlíšenie zatiaľ neurčuje, či ide v databáze o tri druhy identity, tri balíky alebo tri úrovne odvodenia. Také rozhodnutie musí vzniknúť až z ďalšieho metodického skúmania.

---

# Kontrola elementárnosti otázky

Pred prijatím otázky treba overiť:

```text
□ Skúma presne jednu podmienku?
□ Je predmet skúmania jednoznačne určený?
□ Je význam použitých slov jednoznačný?
□ Je určený rozmer X, Y, Z alebo T?
□ Umožňuje odpoveď [1/0] bez dohadu?
□ Je známe, čo môže hodnotu 1 potvrdiť?
□ Je známe, čo môže hodnota 0 znamenať?
□ Je určiteľný dôkaz?
□ Je určiteľná Validácia a jej Autorita?
□ Je určená časová alebo rozsahová platnosť?
```

Ak niektorá podmienka nie je splnená, otázka sa nesmie domyslene vyhodnotiť. Musí sa spresniť alebo rozložiť.

---

# Pracovný záver

```text
Otázka ≠ odpoveď
Otázka ≠ dôkaz
Otázka ≠ hodnotiaci záznam
Otázka ≠ automaticky databázová entita s už určenou architektúrou
```

Elementárna otázka je základný logický nástroj METODIKY. Jej technické uloženie možno navrhnúť až po potvrdení jej metodickej podstaty a vzťahov k rozmerom, odpovedi, dôkazu, Validácii a Autorite.
