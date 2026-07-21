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

## Čo určuje rozmer otázky

Rozmer otázky neurčuje predmet, o ktorom sa hovorí, ani samotné použité podstatné meno. Určuje ho tá skutočnosť, ktorú má odpoveď `[1/0]` potvrdiť alebo nepotvrdiť.

```text
rozmer otázky = druh skutočnosti overovanej odpoveďou
```

Preto sa pri určovaní rozmeru nepýtame iba:

```text
O čom veta hovorí?
```

ale predovšetkým:

```text
Čo sa zmení medzi odpoveďou 1 a odpoveďou 0?
```

Ak sa medzi `1` a `0` mení potvrdenie predmetu, jeho identity, prítomnosti alebo hranice, otázka patrí do X.

Ak sa mení potvrdenie spôsobu, stavu, usporiadania, priebehu alebo mechanizmu prejavu, otázka patrí do Y.

Ak sa mení potvrdenie hodnoty, miery, rozsahu, primeranosti, významu alebo zmyslu prejavu, otázka patrí do Z.

Ak sa mení potvrdenie času, trvania, poradia, okamihu, platnosti alebo priority, otázka patrí do T.

## Rozlišovacie pravidlá

### X — predmet a prejavená existencia

Otázka patrí do X, ak odpoveď určuje najmä:

```text
čo je predmetom,
či sa predmet alebo jav prejavuje,
či je rozlíšený od iného predmetu,
kde sú jeho významové hranice,
čo do skúmaného celku patrí alebo nepatrí.
```

Príklady:

```text
Existuje pre túto povinnosť určená lehota?
Je tento dokument predmetom Validácie?
Patrí táto úloha do rozsahu projektu?
```

### Y — spôsob prejavu

Otázka patrí do Y, ak odpoveď určuje najmä:

```text
ako sa predmet prejavuje,
akým spôsobom vzniká alebo pôsobí,
v akom stave alebo usporiadaní sa nachádza,
aký proces alebo mechanizmus používa,
akým spôsobom možno jeho prejav pozorovať alebo dokázať.
```

Príklady:

```text
Počíta sa lehota od doručenia rozhodnutia?
Plní sa povinnosť automatizovaným procesom?
Je dokument Validovaný porovnaním so zdrojom?
```

### Z — hodnota a zmysel prejavu

Otázka patrí do Z, ak odpoveď určuje najmä:

```text
akú hodnotu alebo význam má prejav,
aká je jeho miera alebo rozsah,
či je primeraný konkrétnemu vzťahu,
či napĺňa svoj účel,
akú váhu má pre konkrétnu individuálnu formu alebo rozhodnutie.
```

Príklady:

```text
Je tridsaťdňová lehota primeraná rozsahu povinnosti?
Je dôkaz dostatočný na potvrdenie odpovede?
Má tento dokument význam pre prijatie rozhodnutia?
```

### T — časová platnosť a priorita

Otázka patrí do T, ak odpoveď určuje najmä:

```text
kedy prejav alebo zmysel platí,
kedy vzniká, trvá alebo zaniká,
ako dlho trvá,
v akom poradí má nastať,
kedy nadobúda alebo stráca prioritu.
```

Príklady:

```text
Trvá lehota tridsať dní?
Je povinnosť platná v deň hodnotenia?
Má táto úloha prioritu pred nasledujúcim krokom?
```

## Rovnaký predmet v štyroch rozmeroch

Ten istý predmet možno skúmať v každom rozmere. Napríklad lehota:

```text
X: Existuje pre povinnosť lehota?
Y: Počíta sa lehota od doručenia rozhodnutia?
Z: Je lehota primeraná rozsahu povinnosti?
T: Trvá lehota tridsať dní?
```

Predmetom všetkých štyroch otázok je lehota. Rozmer sa však mení podľa toho, čo má odpoveď potvrdiť.

Preto platí:

```text
predmet otázky ≠ rozmer otázky
```

## Gramatická forma nie je rozhodujúca

Slová `čo`, `ako`, `koľko` a `kedy` môžu pomôcť pri orientácii, ale samy osebe rozmer neurčujú.

Napríklad:

```text
Ako dlho trvá lehota?
```

obsahuje slovo „ako“, ale zisťuje trvanie, preto patrí do T.

Otázka:

```text
Kedy je rozhodnutie primerané?
```

obsahuje slovo „kedy“, ale ak odpoveď overuje primeranosť v určenom vzťahu, jadro hodnotenia patrí do Z; čas môže byť súčasťou kontextu alebo samostatnej otázky T.

Rozmer musí byť preto určený z celého významu podmienky, nie mechanicky podľa jedného slova.

## Rozmer otázky a kontext použitia

Otázka môže mať všeobecné znenie, ale jej rozmer musí zostať jednoznačný. Ak rovnaká veta v inom kontexte overuje iný druh skutočnosti, nejde iba o inú odpoveď na tú istú otázku. Ide o významovo odlišnú otázku alebo o otázku, ktorú treba spresniť.

Konkrétny predmet hodnotenia nemení rozmer otázky iba preto, že obsahuje časový, hodnotový alebo procesný prvok. Môže však odhaliť, že všeobecné znenie bolo nejednoznačné.

Pracovné pravidlo:

```text
Rozmer patrí významu elementárnej otázky.
Predmet a čas použitia patria hodnotiacemu záznamu.
Ak kontext zmení to, čo odpoveď potvrdzuje, zmenil sa význam otázky.
```

Toto pravidlo zatiaľ neurčuje technickú databázovú identitu otázky. Určuje iba jej metodickú príslušnosť k rozmeru.

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
otázka             = čo a akým logickým spôsobom sa zisťuje
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
□ Je určený rozmer X, Y, Z alebo T podľa toho, čo odpoveď potvrdzuje?
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
Predmet otázky ≠ rozmer otázky
```

Elementárna otázka je základný logický nástroj METODIKY. Jej rozmer určuje druh skutočnosti overovanej odpoveďou. Jej technické uloženie možno navrhnúť až po potvrdení jej metodickej podstaty a vzťahov k rozmerom, odpovedi, dôkazu, Validácii a Autorite.
