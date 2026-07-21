# Postup — databázové uloženie otázok METODIKY a mapovanie projektov

**Dátum a čas:** 2026-07-21 10:23 CEST  
**Skratka:** DB-METODIKA-MAPMET  
**Stav:** záväzný pracovný postup ďalšieho rozvoja METODIKY

---

## 1. Účel postupu

Tento postup určuje poradie ďalšej práce pri tvorbe METODIKY, ukladaní otázok a budúcom mapovaní metodiky v jednotlivých projektoch.

V súčasnej fáze sa netvoria redukčné algoritmy ani hotové rozhodovacie mechanizmy. Najprv sa musí objasniť elementárna logika, význam atribútov, druhy otázok a spôsob ich jednoznačného uloženia.

Algoritmus sa chápe ako budúci prekladový slovník alebo systém interpretácie atribútov hodnotenia, ktorý umožní ich kontrolovanú komprimáciu do binárneho kódu. O algoritmizácii možno začať hovoriť až po dostatočnom objasnení elementárnej logiky.

---

## 2. Databázové prostredia

### 2.1 Databáza projektu METODIKA

```text
Databáza: u550121827_metodic
Používateľ: u550121827_metodic
```

Táto databáza je určená na ukladanie dohodnutých balíkov otázok, potrebných na vytvorenie METODIKY ako samostatného projektu.

Heslo a ďalšie potrebné prístupové údaje alebo API kľúče budú uložené v lokálnom súbore `local-config` spolu s ostatnými prístupmi potrebnými na zapojenie systému.

Databáza `u550121827_metodic` bude uchovávať najmä:

- univerzálne principiálne otázky,
- otázky konkrétnych preverovaných atribútov,
- väzby otázok na princípy a pozície matice 7 × 7,
- verzie otázok a ich pôvod,
- vzťahy medzi otázkami,
- odpovede typu `1/0`,
- dôkazy odpovedí,
- Validáciu odpovedí,
- Autoritu odpovede alebo validujúceho zdroja,
- podklady potrebné na rekonštrukciu hodnotenia.

### 2.2 Databáza metodickej mapy

```text
Databáza servera: u550121827_mapmet
```

Táto databáza je určená pre metodickú mapu, ktorá bude kontrolovať a mapovať všetky projekty z ostatných inštancií.

Jej úlohou bude najmä:

- prijímať alebo získavať metodické mapy jednotlivých projektov,
- porovnávať ich s platnou METODIKOU,
- kontrolovať správnosť tvorby a používania otázok,
- mapovať pôvod otázok, odpovedí, dôkazov, Validácií a Autorít,
- umožniť decentralizované preverovanie,
- umožniť overenie povereným serverom alebo plávajúcim algoritmom v sieti,
- zachovať možnosť spätnej rekonštrukcie rozhodovacieho reťazca.

Databázy `u550121827_metodic` a `u550121827_mapmet` sa nesmú významovo zamieňať:

```text
u550121827_metodic = tvorba, uchovávanie a vývoj METODIKY
u550121827_mapmet   = mapovanie a kontrola používania METODIKY v projektoch
```

---

## 3. Oddelenie druhov otázok

Pred vytváraním databázových tabuliek a ďalších balíkov otázok treba dôsledne oddeliť najmenej tieto skupiny:

### 3.1 Univerzálne principiálne otázky

Otázky použiteľné na preverovanie čohokoľvek. Nie sú viazané na konkrétny atribút ani projektový predmet.

### 3.2 Otázky preverovaných atribútov

Otázky odvodené pre konkrétny atribút, napríklad:

- Disciplínu,
- Zodpovednosť,
- Oprávnenia,
- Povinnosti,
- Validáciu,
- Autoritu.

### 3.3 Otázky konkrétnych predmetov hodnotenia

Otázky viazané na konkrétnu vec, jav, úlohu, rozhodnutie, osobu, proces alebo projekt.

### 3.4 Odpovede a ich potvrdenie

Samostatne sa ukladajú:

- odpovede `1/0`,
- význam konkrétnej jednotky alebo nuly,
- dôkaz,
- Validácia,
- Autorita odpovede,
- časová platnosť,
- väzba na konkrétny projektový predmet.

Tieto skupiny sa nesmú ukladať do jedného nerozlíšeného priestoru.

---

## 4. Úloha textových dokumentov a databázy

### Textové dokumenty `.md`

Slúžia na:

- vysvetlenie pojmov a definícií,
- opis metodiky tvorby otázok,
- pracovné príklady,
- zdôvodnenie vzťahov,
- ľudskú čitateľnosť,
- spätnú rekonštrukciu vývoja poznania.

### Databáza

Slúži na:

- jednoznačné štruktúrované uloženie otázok,
- zaradenie otázky podľa druhu,
- väzbu na princíp a pozíciu v matici 7 × 7,
- verziovanie bez straty pôvodu,
- väzbu na atribúty a konkrétne projektové predmety,
- uchovávanie odpovedí, dôkazov, Validácií a Autorít,
- mapovanie medzi inštanciami,
- budúcu algoritmickú interpretáciu.

Textové dokumenty a databáza sa dopĺňajú. Text nevytvára prevádzkovú databázu a databáza nenahrádza vysvetlenie metodiky.

---

## 5. Poradie ďalšej práce

### Krok 1 — opakované obnovenie základných pojmov

Pred každým významným návrhom treba znovu prečítať platné základné definície METODIKY.

V tejto fáze sa nesmie predpokladať, že obsah je už dostatočne zapamätaný. Opakované čítanie je náhradou za algoritmy obnovy kontextu, ktoré ešte neexistujú.

### Krok 2 — reorganizácia ukladania otázok

Treba oddeliť:

1. univerzálne platné otázky,
2. atribútové otázky,
3. projektové otázky,
4. odpovede, dôkazy, Validácie a Autority.

### Krok 3 — návrh databázového modelu `u550121827_metodic`

Databázová štruktúra musí umožniť:

- jednoznačnú identitu otázky,
- určenie jej pôvodu,
- zaradenie podľa princípu,
- určenie pozície v matici 7 × 7,
- určenie typu otázky,
- verziovanie,
- skladanie balíkov otázok,
- väzbu na atribút,
- väzbu na konkrétny predmet,
- uloženie odpovede a jej významu,
- pripojenie dôkazu,
- Validáciu,
- Autoritu validácie,
- časový stav a platnosť,
- úplnú spätnú rekonštrukciu.

### Krok 4 — určenie rozhrania metodickej mapy `u550121827_mapmet`

Až po objasnení uloženia otázok v METODIKE treba určiť, ktoré údaje a väzby bude metodická mapa preberať alebo kontrolovať v ostatných projektových inštanciách.

### Krok 5 — dokončenie definície otázok Disciplíny

Treba presne rozlíšiť:

- univerzálnu otázku,
- atribútovú otázku Disciplíny,
- konkrétnu otázku disciplíny vo veci,
- objektivitu X/Y,
- subjektivitu Z/T,
- spôsob zaradenia odpovede do hodnotenia Zodpovednosti.

Disciplína zostáva vlastnosťou Zodpovednosti:

> Disciplína predstavuje harmonický alebo disharmonický stav principiálnych podmienok opísaných otázkou vedúcou k zisteniu stavu disciplíny vo veci.

### Krok 6 — dokončenie definície otázok Autority

Autorita sa skúma pri zachovaní spoločného pracovného vzorca:

```text
Auth = (Oprávnenia × Povinnosti × Zodpovednosť) / Validácia
```

Najprv treba objasniť elementárnu logiku každého súčiniteľa a menovateľa, ich vzájomný vzťah a druhy otázok. Až potom možno dokončovať balíky otázok Autority.

### Krok 7 — dokončenie konkrétnych balíkov otázok

Po objasnení metodiky ich tvorby a uloženia možno dokončiť otázky pre:

- Disciplínu,
- Autoritu,
- ďalšie atribúty, ktoré sa postupne preukážu ako potrebné.

### Krok 8 — uloženie univerzálne platných otázok

Univerzálne otázky sa ukladajú oddelene od atribútových a projektových otázok. Ich textové vysvetlenie môže zostať v dokumentácii, prevádzková verzia však musí byť uložená v databáze.

### Krok 9 — až následná algoritmizácia

Algoritmizácia môže začať až vtedy, keď je potvrdené:

- čo každý atribút znamená,
- akými otázkami sa skúma,
- čo znamenajú odpovede `1/0`,
- ako sa odpovede validujú,
- kto má Autoritu ich potvrdiť,
- ako sa uchová pôvod a celý rozhodovací reťazec.

Až potom možno tvoriť interpretačné a komprimačné pravidlá vedúce k výslednému binárnemu kódu.

---

## 6. Hranice aktuálnej fázy

V tejto chvíli sa postup vedome nezaoberá:

- licenčnými opatreniami,
- osobitnými bezpečnostnými opatreniami,
- konečnou redukciou hodnotiacich bitov,
- hotovým algoritmom Autority,
- automatickým rozhodovaním,
- vytváraním veľkého množstva matíc bez objasnenej elementárnej logiky.

Tieto oblasti sa nesmú nepozorovane miešať do aktuálnej práce a odvádzať ju od tvorby základných pojmov, otázok a databázového modelu.

---

## 7. Kontrolné pravidlo postupu

Pred každým ďalším krokom sa musí overiť:

```text
1. Čítali sme znovu platné definície?
2. Vieme, aký druh otázky tvoríme?
3. Vieme, kam sa otázka uloží?
4. Vieme, s akým atribútom a predmetom súvisí?
5. Vieme, čo bude znamenať jej odpoveď 1 alebo 0?
6. Vieme, aký dôkaz a Validáciu bude odpoveď potrebovať?
7. Nezačali sme predčasne tvoriť algoritmus?
```

Ak na niektorú otázku nemožno spoľahlivo odpovedať, ďalší návrh sa nesmie domyslieť. Treba sa vrátiť k pojmom, definíciám a elementárnej logike.

---

## 8. Záväzný záver

> Najprv sa oddelia druhy otázok, navrhne sa ich databázové uloženie a dokončia sa definície Disciplíny a Autority. Až potom sa dokončia samotné balíky otázok, zapíšu univerzálne otázky do databázy a začne sa tvorba interpretačných algoritmov.

Databáza `u550121827_metodic` je pracovným priestorom tvorby METODIKY. Databáza `u550121827_mapmet` je budúcim priestorom jej mapovania a kontroly naprieč projektmi a inštanciami.
