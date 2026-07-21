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
- umožniť algoritmické rozhodovanie založené na veľkom množstve jednoznačne overených podmienok.

---

# Základný princíp

Každý projekt, proces a prejav je živý systém.

Pred vykonaním práce musí človek alebo AI agent obnoviť pamäť v tomto poradí:

```text
1. Univerzálna metodika
2. Zoznam projektov
3. Identifikácia konkrétneho projektu
4. Projektové metodické pokyny
5. Analýza aktuálneho stavu
6. Návrh riešenia
7. Implementácia
8. Záznam zmien
```

---

# Štruktúra repozitára

```text
METODIKA/
│
├── README.md
├── uQestions.md
├── DISCIPLINA.md
├── AUTORITA.md
│
├── PRINCIPY/
│   ├── HermetickePrincipy.md
│   ├── UniverzalnePrincipy.md
│   └── RiadenieZnalosti.md
│
├── PROJEKTY/
│   ├── ZoznamProjektov.md
│   └── SablonaProjektu.md
│
├── CHECKLISTY/
│   ├── StartProjektu.md
│   ├── PokracovaniePrace.md
│   ├── MigraciaProjektu.md
│   └── UkonceniePrace.md
│
├── AGENTI/
│   ├── ChatGPT.md
│   ├── Copilot.md
│   ├── Codex.md
│   ├── Claude.md
│   └── SpolupracaAgentov.md
│
├── POSTUPY/
│   ├── Analyza.md
│   ├── Implementacia.md
│   ├── Dokumentacia.md
│   └── Checkpointy.md
│
└── ARCHIV/
```

---

# Hierarchia znalostí

```text
Univerzálna metodika
        ↓
Projekt
        ↓
Modul
        ↓
Súbor
        ↓
Funkcia
```

Každá nižšia úroveň dedí pravidlá úrovne nad sebou. Projekt môže univerzálnu metodiku podrobnejšie rozpracovať, ale nesmie meniť jej podstatu.

---

# Sedem hermetických princípov metodiky

1. Mentalizmus
2. Korešpondencia
3. Vibrácia
4. Polarita
5. Rytmus
6. Príčina a následok
7. Generativita

Hermetické princípy nie sú oddelenými vrstvami, poradím pracovných krokov ani hierarchiou. Tvoria jeden spoločný sedemrozmerný priestor skúmania života každého projektu, procesu, otázky a prejavu.

Život je jeden. Každý jeho prejav je životom samým a musí byť možné skúmať ho prostredníctvom všetkých siedmich princípov súčasne.

Podrobnejší opis sa nachádza v súbore `PRINCIPY/HermetickePrincipy.md`.

---

# Logické skúmanie existencie

## Logickosť a overiteľnosť

> Čo je logické, musí byť overiteľné.

Každá kontrolná otázka musí umožniť jednoznačnú odpoveď:

```text
1 = áno
0 = nie
```

Binárna odpoveď nezjednodušuje život na mechanický výpočet. Overuje, či je otázka položená dostatočne presne a či je skúmaný jav logicky uchopiteľný.

Nula sama osebe automaticky neznamená neexistenciu. Môže upozorniť, že:

- otázka nie je položená správne,
- predmet nie je dostatočne presne určený,
- všeobecný pojem označuje viac samostatných procesov,
- otázku treba rozložiť na menšie, jednoznačne overiteľné otázky.

## Sedemnásobná kontrola otázky

Každú logickú otázku treba preskúmať všetkými siedmimi princípmi:

```text
P1 × P2 × P3 × P4 × P5 × P6 × P7
```

Ak sú všetky hodnoty `Pn = 1`, stav možno komprimovane zapísať ako `P^7`.

Každé pole predstavuje jednu kontrolnú otázku odvodenú z príslušného princípu a jednu odpoveď `1/0`.

## Matica 7 × 7

Každý zo siedmich princípov možno skúmať prostredníctvom všetkých siedmich princípov vrátane seba samého:

```text
                    KONTROLNÝ PRINCÍP
                M   K   V   P   R   N   G
SKÚMANÝ     M   □   □   □   □   □   □   □
PRINCÍP     K   □   □   □   □   □   □   □
            V   □   □   □   □   □   □   □
            P   □   □   □   □   □   □   □
            R   □   □   □   □   □   □   □
            N   □   □   □   □   □   □   □
            G   □   □   □   □   □   □   □
```

```text
M = mentalizmus
K = korešpondencia
V = vibrácia
P = polarita
R = rytmus
N = príčina a následok
G = generativita
```

Matica obsahuje presne 49 otázok a 49 jednoznačných odpovedí. Nie je pracovným postupom. Je bázou, z ktorej možno odvodzovať konkrétne otázky, algoritmy a checklisty.

Úplné univerzálne znenie otázok sa nachádza v súbore:

- [uQestions.md](uQestions.md) — univerzálna objektívna matica otázok X/Y.

Prvý úplný príklad sa nachádza v súbore:

- [DISCIPLINA.md](DISCIPLINA.md) — disciplína ako vlastnosť Zodpovednosti a príklad použitia 49 otázok.

Metodický základ Autority sa nachádza v súbore:

- [AUTORITA.md](AUTORITA.md) — základný vzorec Autority a plán jeho principiálneho rozkladu.

---

# Rozmery prejavu a zmyslu

Sedem princípov overuje úplnosť a správnosť položenej otázky. Rozmery **X, Y, Z a T** určujú, čo touto otázkou skúmame.

```text
X = čo?
Y = ako?
Z = koľko?
T = kedy?
```

## Objektivita — X/Y

Objektivita hovorí o **prejave v existencii**.

```text
X — čo sa prejavuje?
Y — ako sa to prejavuje?
```

Bez otázky „čo?“ nie je určený predmet. Bez otázky „ako?“ nie je určený spôsob jeho prejavu.

```text
X × Y = [1/0]^2
```

## Subjektivita — Z,T

Subjektivita hovorí o **zmysle prejavu**.

```text
Z — akú má prejav mieru, rozsah, primeranosť a význam?
T — kedy má prejav zmysel, platnosť, životaschopnosť alebo prioritu?
```

```text
X × Y × Z = [1/0]^3
X × Y × Z × T = [1/0]^4
```

Tieto zápisy sa nesmú interpretovať ako mechanická brána, v ktorej každá nula ruší existenciu všetkého pred ňou.

Môže platiť:

```text
X = 1
Y = 1
Z = 1
T = 0
```

Takýto stav znamená, že predmet, spôsob a zmysel sú určené, ale prejav nie je prítomný v skúmanom čase. Môže ešte len vzniknúť alebo už zanikol v danom časovom bode. Časová nula nepopiera jeho pôvod, možnosť ani zmysel.

Objektivita a subjektivita žijú samy osebe, ale prejavujú sa inak:

- objektivita určuje prejav v existencii,
- subjektivita určuje zmysel jeho prejavu.

---

# Disciplína a Zodpovednosť

Disciplína nie je samostatnou Autoritou ani priamym súčiniteľom základného vzorca Autority.

> Disciplína je vlastnosťou Zodpovednosti, ktorá spoluurčuje schopnosť Zodpovednosť niesť a tým determinuje Autoritu.

Disciplína predstavuje harmonický alebo disharmonický stav principiálnych podmienok opísaných konkrétnou otázkou. Nehodnotí sa teda všeobecnou vetou „je niekto disciplinovaný“, ale otázkou viazanou na konkrétnu vec, záväzok, skutok alebo rozhodnutie.

Doteraz sme disciplínu skúmali predovšetkým v objektívnej báze X/Y, pretože jej subjektívna miera a časový zmysel Z/T sa musia posudzovať až vo vzťahu ku konkrétnej Zodpovednosti.

Bez disciplíny niet Zodpovednosti v plnom zmysle. Presný vnútorný vzorec Zodpovednosti však ešte nie je uzavretý a nesmie sa domyslieť bez ďalšieho skúmania.

---

# Základný vzorec Autority

METODIKA preberá autoritatívny pracovný základ z projektu `slapiar/Nov-svet`, odsek 16:

```text
Auth = (Oprávnenia × Povinnosti × Zodpovednosť) / Validácia
```

Vzorec je pracovný a môže sa ďalej spresňovať. Jeho podstata však musí zostať rovnaká vo všetkých projektoch, kým nebude metodicky a autoritatívne zmenená.

Z neho vyplýva:

- Autorita sa nerovná Zodpovednosť,
- Zodpovednosť je rovnocenným súčiniteľom Oprávnení a Povinností,
- Validácia je samostatný menovateľ vzorca,
- nijaký projekt nesmie Autoritu definovať iným konkurenčným vzorcom,
- projekt môže jednotlivé súčinitele iba podrobnejšie skúmať.

## Minimálny principiálny rozklad Autority

Každý člen vzorca musí dostať vlastnú maticu 7 × 7:

```text
Oprávnenia    → 49 otázok a odpovedí 1/0
Povinnosti    → 49 otázok a odpovedí 1/0
Zodpovednosť  → 49 otázok a odpovedí 1/0
Validácia     → 49 otázok a odpovedí 1/0
```

Minimálny priamy rozbor Autority preto obsahuje:

```text
4 × 49 = 196 principiálnych otázok
```

Toto je iba prvý stupeň. Každý súčiniteľ sa môže rozkladať na ďalšie atribúty a procesy. Pri Zodpovednosti je jedným z takýchto atribútov disciplína, ktorá sa sama môže skúmať ďalšou maticou otázok.

Každá elementárna otázka vytvára jeden bit `1/0`. Veľké množstvo týchto bitov môže tvoriť binárne hodnotiace slovo. Spôsob ich algoritmického zloženia a redukcie na výsledný bit Autority ešte nie je definitívne určený a nesmie sa nahradiť dojmom ani jednoduchým mechanickým súčinom.

Projekt Nový svet predpokladá aj 1024-bitovú aritmetiku na prácu s rozsiahlym binárnym hodnotením. Správnosť rozhodnutia však nezabezpečuje samotná veľkosť slova. Zabezpečiť ju môže iba správna metodika otázok, dôkladná algoritmizácia, dôkaznosť odpovedí a správne určená Autorita výsledku.

## Autorita a priorita

Autorita objektívne existuje prostredníctvom svojich Oprávnení, Povinností, Zodpovednosti a Validácie. Jej priorita je subjektívnym určením miery a časového zmyslu tejto Autority v konkrétnej veci.

```text
X — čoho sa Autorita týka a ktoré členy vzorca ju tvoria?
Y — ako sa každý člen prejavuje a dokazuje?
Z — akú mieru, rozsah a váhu má každý člen vo veci?
T — kedy je Autorita platná a kedy má prioritu?
```

Autorita môže existovať, aj keď v konkrétnej veci alebo čase nemá prioritu. Zánik aktuálnej priority automaticky neznamená zánik samotnej Autority.

---

# Význam dôkazu v širšom zmysle

Tento rámec rozlišuje medzi:

- logickou možnosťou a správnosťou otázky,
- objektívnym prejavom v existencii,
- subjektívnym zmyslom prejavu,
- časovým stavom uskutočnenia,
- Autoritou, ktorá je oprávnená výsledok potvrdiť.

Tým vzniká spoločný logický priestor pre metafyziku a fyziku, možnosť a prejav, vedu a náboženstvo, pôvod, zmysel, uskutočnenie a dôkaz.

---

# Hľadanie správnych otázok

Správna otázka musí byť:

- presne vymedzená,
- logicky overiteľná,
- skúmateľná všetkými siedmimi princípmi,
- rozložiteľná na jednoznačné odpovede `1/0`,
- schopná rozlíšiť objektívny prejav od subjektívneho zmyslu,
- schopná určiť mieru a čas bez zamieňania neprítomnosti prejavu za neexistenciu,
- viazaná na určenú Autoritu, ktorá môže odpoveď potvrdiť.

Ak nie je možné spoľahlivo odpovedať, odpoveď sa nesmie domyslieť. Musí sa spresniť predmet alebo rozložiť otázka.

Checklist nevytvára princípy ani procesy. Vzniká až z otázok, ktoré boli metodicky vyvodené a možno ich spoľahlivo validovať.

---

# Základné pravidlá

1. Nič sa nevymýšľa bez overenia.
2. Najprv analýza, až potom implementácia.
3. Vždy sa identifikuje aktuálny stav projektu.
4. Každá významná zmena musí mať checkpoint.
5. Pred ukončením práce sa vytvára sumarizácia.
6. Každý projekt musí mať vlastné metodické pokyny.
7. Univerzálna metodika má vyššiu prioritu než projektové pokyny.
8. Po každom zápise sa overí skutočný obsah súboru v repozitári.
9. Agent nesmie predpokladať, že si metodiku pamätá.
10. Pred začatím práce musí prečítať platnú univerzálnu aj projektovú metodiku.
11. Kým nie je obnovená pamäť, agent nesmie domýšľať ani implementovať.
12. Pracovný postup sa nesmie vydávať za univerzálny princíp.
13. Pri nejasnosti sa najprv hľadá správna otázka.
14. Podstata Autority sa medzi projektmi nesmie meniť.
15. Konkurenčný vzorec Autority sa nesmie zaviesť bez autoritatívnej zmeny univerzálnej metodiky.

---

# Prvé otázky AI agenta

```text
□ Prečítal som si celú platnú univerzálnu metodiku?
□ Prečítal som si špecifickú metodiku projektu?
□ Poznám názov projektu a správny repozitár?
□ Mám funkčný prístup na čítanie a zápis?
□ Poznám autoritatívny koreň projektu?
□ Poznám aktuálny stav a posledný checkpoint?
□ Rozumiem tomu, čo sa tvorí tu a teraz, bez domýšľania?
□ Poznám Autoritu svojej úlohy, jej Oprávnenia, Povinnosti a Zodpovednosť?
□ Viem, aká Validácia je potrebná pre výsledok?
```

Kladná odpoveď nesmie byť deklaráciou ani odhadom. Musí byť logicky overiteľná a podľa potreby doložená dôkazom.

---

# Dlhodobý cieľ

Vybudovať univerzálnu metodiku, v ktorej možno každú elementárnu vec preskúmať dostatočným množstvom jednoznačných otázok, uložiť výsledky do binárneho hodnotenia a na konci získať rozhodovací bit, ktorého pôvod, logika, dôkaz a Autorita sú spätne overiteľné.

> Keď dokážeme potvrdiť správnosť metodiky otázok, algoritmizácie a Autority výsledku, môžeme zodpovedne tvoriť čokoľvek.
