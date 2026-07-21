# Logický model databázy METODIKY

**Dátum a čas:** 2026-07-21 13:52 CEST  
**Skratka:** LOG-MODEL-METODIC  
**Stav:** pracovný návrh pred vytvorením SQL schémy

---

## 1. Účel

Tento dokument určuje významové skupiny údajov, ktoré má uchovávať databáza `u550121827_metodic`.

Zatiaľ neurčuje konečné názvy SQL tabuliek ani redukčný algoritmus. Jeho úlohou je zabrániť zmiešaniu otázok, predmetov hodnotenia a výsledkov hodnotenia.

---

## 2. Základné skupiny údajov

### 2.1 Princípy

Register siedmich princípov:

```text
M — mentalizmus
K — korešpondencia
V — vibrácia
P — polarita
R — rytmus
N — príčina a následok
G — generativita
```

Každá pozícia matice 7 × 7 odkazuje na:

- skúmaný princíp,
- kontrolný princíp.

### 2.2 Rozmery

Register rozmerov:

```text
X — čo sa prejavuje
Y — ako sa to prejavuje
Z — akú hodnotu a zmysel má prejav
T — kedy tento zmysel platí alebo nadobúda prioritu
```

Každý samostatne hodnotený rozmer používa jednotku `[1/0]`.

### 2.3 Druhy otázok

Otázky sa ukladajú v troch oddelených významových druhoch:

```text
univerzálna otázka
atribútová otázka
projektová otázka
```

Univerzálna otázka nie je viazaná na konkrétny atribút ani projektový predmet.

Atribútová otázka je odvodená pre konkrétny atribút, napríklad Disciplínu ako atribút Zodpovednosti.

Projektová otázka je viazaná na konkrétnu vec, jav, úlohu, rozhodnutie, osobu, proces alebo projektový predmet.

### 2.4 Otázka a jej verzia

Otázka potrebuje trvalú identitu nezávislú od zmien svojho znenia.

Preto sa oddeľuje:

```text
identita otázky
verzia otázky
```

Verzia uchováva najmenej:

- presné znenie,
- druh otázky,
- platnosť verzie,
- pôvod,
- autora alebo zdroj,
- väzbu na predchádzajúcu verziu,
- dôvod zmeny.

Hodnotiaci záznam musí vždy odkazovať na konkrétnu verziu otázky, aby sa dal spätne rekonštruovať jeho význam.

### 2.5 Pozícia v matici

Otázka môže byť previazaná s pozíciou matice 7 × 7:

```text
skúmaný princíp × kontrolný princíp
```

Pozícia matice nesmie byť odvodená iba z poradia riadku. Musí byť uložená významovo pomocou identít oboch princípov.

### 2.6 Atribúty

Atribút je pomenovaná vlastnosť alebo vnútorný proces skúmaného celku.

Príklad:

```text
Autorita
→ Zodpovednosť
→ Disciplína
```

Atribúty musia umožňovať hierarchické zaradenie, pretože jeden atribút môže byť súčasťou iného atribútu.

### 2.7 Projektové predmety

Projektový predmet je konkrétna vec, na ktorú sa otázka použije.

Môže ním byť napríklad:

- projekt,
- modul,
- súbor,
- funkcia,
- osoba alebo agent,
- rozhodnutie,
- úloha,
- skutok,
- proces,
- dokument,
- konkrétny vzťah medzi viacerými predmetmi.

Projektový predmet musí mať vlastnú identitu a väzbu na projekt alebo inštanciu, z ktorej pochádza.

### 2.8 Balíky otázok

Balík združuje otázky určené na spoločné použitie.

Balík musí umožňovať:

- vlastnú identitu,
- verziu,
- účel,
- poradie otázok,
- zaradenie otázky bez kopírovania jej znenia,
- použitie jednej otázky vo viacerých balíkoch.

Počet otázok ani počet balíkov sa nesmie pevne zakódovať do databázovej štruktúry.

---

## 3. Hodnotiaci záznam

Hodnotiaci záznam vzniká použitím konkrétnej verzie otázky na konkrétny predmet v určenom čase.

Jeho základný vzťah je:

```text
verzia otázky
× predmet hodnotenia
× hodnoty rozmerov [1/0]
× dôkazy
× Validácie
× Autority Validácií
× časová platnosť
```

### 3.1 Hodnoty rozmerov

Každý hodnotený rozmer sa uchováva samostatne:

```text
X[1/0]
Y[1/0]
Z[1/0]
T[1/0]
```

Databáza nesmie predpokladať, že každý hodnotiaci záznam musí obsahovať všetky štyri rozmery. Musí však presne vedieť, ktoré rozmery boli hodnotené.

Pri hodnote `0` sa uchováva jej význam, napríklad:

- neprítomnosť prejavu,
- nesplnenie podmienky,
- chýbajúci dôkaz,
- nesprávny alebo nenastávajúci čas,
- potreba ďalšieho rozkladu otázky.

### 3.2 Dôkazy

Jeden hodnotiaci záznam môže mať viac dôkazov.

Dôkaz musí uchovávať:

- druh dôkazu,
- opis alebo obsah,
- zdroj,
- väzbu na súbor, údaj, meranie, skutok alebo pozorovanie,
- čas vzniku alebo zistenia,
- informáciu, ktorú hodnotu alebo rozmer podporuje.

### 3.3 Validácie

Jeden hodnotiaci záznam môže byť validovaný viackrát a rôznymi Autoritami.

Validácia musí uchovávať:

- čo presne validuje,
- výsledok Validácie,
- rozsah Validácie,
- čas platnosti,
- použitú Autoritu Validácie,
- prípadný dôvod odmietnutia alebo obmedzenia.

### 3.4 Autority Validácií

Autorita Validácie musí mať samostatnú identitu a určený rozsah Oprávnení.

Môže ňou byť:

- človek,
- organizácia,
- server,
- agent,
- algoritmus,
- kombinovaná alebo delegovaná Autorita.

Samotné meno alebo označenie nestačí. Musí byť možné spätne určiť, na základe čoho bola Autorita oprávnená danú Validáciu vykonať.

---

## 4. Väzby medzi otázkami

Otázky môžu byť navzájom previazané najmenej týmito vzťahmi:

- odvodená z,
- spresňuje,
- rozkladá,
- nahrádza,
- je opakom,
- je podmienkou,
- súvisí s,
- patrí do rovnakého balíka.

Vzťah nesmie byť uložený iba textovou poznámkou. Musí prepájať identity konkrétnych otázok alebo ich verzií.

---

## 5. Základný rozhodovací reťazec

Databáza musí umožniť spätne prejsť celý reťazec:

```text
univerzálna otázka
→ atribútová otázka
→ projektová otázka
→ konkrétna verzia otázky
→ predmet hodnotenia
→ hodnoty X/Y/Z/T [1/0]
→ dôkazy
→ Validácie
→ Autority Validácií
→ výsledok použitý v ďalšom hodnotení
```

Výsledok jedného hodnotenia môže byť vstupom do iného hodnotenia. Napríklad výsledok matice Disciplíny môže vstúpiť do hodnotenia Zodpovednosti a výsledok Zodpovednosti do hodnotenia Autority.

Databáza však v tejto fáze neurčuje spôsob redukcie viacerých hodnôt na jeden výsledný bit.

---

## 6. Povinné významové pravidlá

1. Otázka a hodnotiaci záznam sú rozdielne entity.
2. Znenie otázky sa nesmie kopírovať do každého hodnotenia.
3. Hodnotenie vždy odkazuje na konkrétnu verziu otázky.
4. Univerzálna, atribútová a projektová otázka musia byť rozlíšiteľné.
5. Atribút musí byť oddelený od konkrétneho predmetu hodnotenia.
6. Hodnoty X, Y, Z a T sa ukladajú samostatne.
7. Hodnota `0` musí mať uchovaný význam.
8. Dôkaz, Validácia a Autorita Validácie sú samostatné, navzájom prepojené údaje.
9. História verzií a pôvod otázok sa nesmú prepísať ani stratiť.
10. Databáza musí umožniť spätnú rekonštrukciu celého rozhodovacieho reťazca.

---

## 7. Otvorené rozhodnutia pred SQL schémou

Pred vytvorením tabuliek treba ešte potvrdiť:

1. či sa objektívne a subjektívne otázky ukladajú ako samostatné balíky alebo ako rôzne rozmery jednej otázkovej identity,
2. či sa vzťah univerzálna → atribútová → projektová otázka eviduje vždy povinne alebo iba tam, kde je pôvod známy,
3. aké typy predmetov hodnotenia potrebujeme v prvej verzii,
4. aké významy hodnoty `0` budú tvoriť základný číselník,
5. čo presne predstavuje výsledok hodnotiaceho záznamu, ak obsahuje viac rozmerov `[1/0]`,
6. ako sa zaznamená použitie výsledku jedného hodnotenia ako vstupu do iného hodnotenia.

Až po potvrdení týchto bodov možno vytvoriť prvú SQL schému bez zakódovania nesprávnej elementárnej logiky.
