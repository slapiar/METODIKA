# HODNOTENIA — metodický koreň

## Účel dokumentu

Tento priestor je významovo oddelený od definícií otázok. Určuje elementárnu podstatu odpovede a hodnotiaceho záznamu bez predčasného určenia konečnej databázovej architektúry.

---

# Odpoveď

> Odpoveď je výsledok použitia jednej elementárnej otázky na jeden určený predmet v jednom určenom význame a čase.

Elementárna odpoveď používa jednotku:

```text
[1/0]
```

```text
1 = skúmaná podmienka je v presne určenom význame potvrdená
0 = skúmaná podmienka nie je v presne určenom význame potvrdená
```

Odpoveď nie je všeobecnou vlastnosťou otázky. Vzniká až pri konkrétnom použití otázky.

Tá istá otázka môže mať pre rozdielne predmety, časy, rozsahy alebo Autority rozdielne pravdivé odpovede.

---

# Význam hodnoty 0

Hodnota `0` nesmie zostať bez významového určenia. Môže znamenať napríklad:

```text
- podmienka nie je splnená,
- prejav nie je prítomný,
- predmet nie je správne určený,
- dôkaz chýba alebo nestačí,
- ešte nenastal alebo už uplynul príslušný čas,
- otázka nie je elementárna,
- otázku treba spresniť alebo rozložiť,
- Validácia nebola vykonaná alebo neuspela.
```

Tieto významy sa nesmú zamieňať. Nula bez určeného dôvodu neposkytuje dostatočný podklad pre ďalšie rozhodovanie.

---

# Neznámy stav

METODIKA nesmie predstierať odpoveď tam, kde odpoveď nebola zistená.

Preto treba metodicky rozlišovať:

```text
0        = podmienka bola vyhodnotená a nie je potvrdená v určenom význame
nezistené = odpoveď zatiaľ nie je spoľahlivo známa
```

`Nezistené` nie je tretia pravdivostná hodnota skúmanej podmienky. Je stavom poznania alebo spracovania hodnotenia.

Do výsledného binárneho rozhodovania nesmie vstúpiť ako domyslená nula ani jednotka.

---

# Hodnotiaci záznam

Hodnotiaci záznam vzniká použitím konkrétnej otázky na konkrétny predmet v určenom čase.

Jeho potvrdené významové zloženie je:

```text
otázka
× predmet hodnotenia
× odpoveď [1/0]
× význam odpovede
× dôkaz
× Validácia
× Autorita Validácie
× časová platnosť
```

Znak `×` tu vyjadruje spoločnú prítomnosť samostatných významových častí záznamu. Nie je automaticky aritmetickým násobením ani logickým AND.

---

# Predmet hodnotenia

Predmet hodnotenia je konkrétna vec, jav, proces, vzťah, osoba, agent, rozhodnutie, dokument, úloha alebo iný určený prejav, na ktorý sa otázka použila.

Predmet musí byť určený dostatočne presne, aby sa odpoveď nedala omylom preniesť na inú vec alebo iný rozsah.

---

# Dôkaz

Dôkaz je overiteľný podklad, o ktorý sa odpoveď opiera.

Môže ním byť napríklad:

- údaj,
- dokument,
- meranie,
- pozorovanie,
- vykonaný alebo nevykonaný skutok,
- výpočtový výsledok,
- záznam systému,
- iný spätne overiteľný prejav.

Dôkaz nie je totožný s odpoveďou. Odpoveď je logický výsledok posúdenia dôkazu vo vzťahu ku konkrétnej otázke.

---

# Validácia

Validácia overuje, či:

```text
- bola použitá správna otázka,
- bol správne určený predmet,
- odpoveď zodpovedá dôkazu,
- význam hodnoty 0 alebo 1 je správne interpretovaný,
- rozsah a časová platnosť sú určené,
- výsledok potvrdila oprávnená Autorita.
```

Validácia nesmie byť zamieňaná s dôkazom ani s Autoritou Validácie.

---

# Autorita Validácie

Autorita Validácie určuje, kto alebo čo je oprávnené Validáciu vykonať a v akom rozsahu.

Samotné meno, funkcia alebo technická schopnosť nestačia. Musí byť určiteľný pôvod Oprávnenia, príslušné Povinnosti, Zodpovednosť a spôsob Validácie.

---

# Časová platnosť

Odpoveď nemusí platiť bez časového obmedzenia.

Treba rozlišovať najmenej:

```text
- čas zistenia,
- čas Validácie,
- začiatok platnosti,
- koniec platnosti alebo podmienku zániku,
- čas, ku ktorému sa otázka vzťahuje.
```

Presná technická reprezentácia týchto časov ešte nie je určená. Ich významové rozlíšenie však nesmie databázový model stratiť.

---

# Rozmery odpovede

Každý samostatne skúmaný rozmer nesie vlastnú odpoveď:

```text
X[1/0]
Y[1/0]
Z[1/0]
T[1/0]
```

Hodnotiaci záznam nemusí vždy obsahovať všetky štyri rozmery. Musí však byť jednoznačne známe, na ktorý rozmer sa každá odpoveď vzťahuje.

Ak sa výsledky Z a T spájajú, musí byť osobitne určený vzťah siedmej plochy:

```text
S = logický vzťah(Z, T)
```

Výsledok S nesmie vzniknúť automatickým použitím AND.

---

# Oddelenie otázky a hodnotenia

```text
otázka       = opakovateľný logický nástroj
hodnotenie   = konkrétne použitie otázky
odpoveď      = výsledok konkrétneho použitia
dôkaz        = podklad výsledku
Validácia    = overenie správnosti výsledku
Autorita     = oprávnenie Validáciu potvrdiť
```

Jedna otázka môže mať mnoho hodnotiacich záznamov. Jeden predmet môže byť hodnotený mnohými otázkami.

Preto sa otázky a hodnotenia nesmú ukladať v jednom nerozlíšenom priestore.

---

# Otvorené rozhodnutia

Pred návrhom databázy treba ešte potvrdiť:

```text
1. povinné a nepovinné časti minimálneho hodnotiaceho záznamu,
2. presný číselník významov hodnoty 0,
3. spôsob zápisu stavu „nezistené“,
4. spôsob väzby odpovede na rozmer X, Y, Z alebo T,
5. spôsob zápisu operátora a výsledku siedmej plochy S,
6. minimálny spôsob identifikácie predmetu hodnotenia,
7. časové údaje potrebné v prvej verzii,
8. spôsob použitia výsledku jedného hodnotenia v ďalšom hodnotení.
```

---

# Pracovný záver

Textový adresár slúži na definície a pracovné príklady. Prevádzkové hodnotenia budú neskôr ukladané štruktúrovane, ale až po potvrdení ich elementárnej metodickej podstaty.

```text
Odpoveď bez otázky nemá určený význam.
Odpoveď bez predmetu nemá určený rozsah.
Odpoveď bez dôkazu nie je overiteľná.
Odpoveď bez Validácie nemá potvrdenú správnosť.
Validácia bez Autority nemá určené oprávnenie.
Výsledok bez časovej platnosti sa môže nesprávne preniesť do iného času.
```
