# Logický model databázy METODIKY — revízny záznam

**Dátum a čas pôvodného návrhu:** 2026-07-21 13:52 CEST  
**Skratka:** LOG-MODEL-METODIC  
**Stav:** neplatný pracovný návrh určený na revíziu

---

## 1. Dôvod zmeny stavu

Pôvodná verzia tohto dokumentu vznikla skôr, než boli uzavreté elementárne definície METODIKY. Obsahovala viacero možných databázových a architektonických riešení, ktoré ešte neboli metodicky odvodené ani autoritatívne potvrdené.

Dokument preto nesmie byť použitý ako podklad na vytvorenie SQL schémy.

Jeho pôvodná verzia zostáva zachovaná v histórii Git repozitára ako záznam vývoja myslenia.

---

## 2. Potvrdené jadro

Databázový model môže v tejto chvíli vychádzať iba z nasledujúceho potvrdeného hodnotiaceho záznamu:

```text
otázka
× predmet hodnotenia
× odpoveď [1/0]
× dôkaz
× Validácia
× Autorita Validácie
× časová platnosť
```

Potvrdené je tiež rozlíšenie:

```text
OTAZKY/      = definície otázok
HODNOTENIA/  = konkrétne hodnotiace záznamy
```

Otázka a hodnotiaci záznam nie sú ten istý druh údaja a nesmú sa ukladať v jednom nerozlíšenom priestore.

---

## 3. Potvrdené rozmery života

```text
X × Y                 = [1/0]^2
X × Y × Z             = [1/0]^3
X × Y × Z × T         = [1/0]^4
```

Platí:

```text
X × Y = objektivita — prejavená existencia
Z     = hodnota a zmysel tejto existencie
T     = čas, v ktorom zmysel platí a určuje prioritu
```

Znak `×` v tomto zápise znamená spoločné skúmanie samostatných rozmerov. Neznamená automaticky aritmetický súčin ani logický operátor AND.

Subjektivita sa zapisuje:

```text
Subjektivita = (Z, T)
```

Nie:

```text
Subjektivita = Z × T
```

---

## 4. Siedma plocha zmyslu života

Z a T sú samostatné rozmery. Ich konkrétny logický vzťah určuje siedma plocha zmyslu života:

```text
S = logický vzťah(Z, T)
```

Podľa významu konkrétnej otázky môže byť vzťahom napríklad:

```text
Z ∧ T       — AND
Z ∨ T       — OR
T → Z       — IF T THEN Z
Z → T       — IF Z THEN T
Z ⊕ T       — XOR
```

Operátor sa nesmie vybrať automaticky. Musí byť odvodený z presného významu otázky a predmetu hodnotenia.

Siedma plocha nie je ďalším základným rozmerom popri X, Y, Z a T. Je metaplochou logických vzťahov medzi Z a T.

---

## 5. Čo zatiaľ nie je potvrdené

Nasledujúce návrhy z pôvodnej verzie dokumentu sa vracajú medzi otvorené otázky:

- trvalá identita a verzovanie otázok,
- povinné členenie otázok na databázové druhy,
- hierarchia atribútov,
- samostatná identita projektových predmetov,
- balíky otázok a ich verzie,
- pevne určené väzby medzi otázkami,
- počet dôkazov a Validácií na jeden hodnotiaci záznam,
- typológia Autorít Validácie,
- povinný reťazec univerzálna → atribútová → projektová otázka,
- ukladanie samostatných hodnôt X, Y, Z a T do každého hodnotiaceho záznamu,
- použitie výsledku jedného hodnotenia ako vstupu do ďalšieho hodnotenia,
- spôsob redukcie viacerých bitov na výsledný bit.

Tieto možnosti môžu byť neskôr správne, ale nesmú sa zakódovať do databázy skôr, než budú metodicky odvodené a potvrdené.

---

## 6. Povinný ďalší postup

Pred návrhom SQL schémy treba postupovať v tomto poradí:

```text
1. potvrdiť elementárne pojmy a definície
2. potvrdiť jednotku [1/0] a význam odpovede
3. potvrdiť štruktúru hodnotiaceho záznamu
4. určiť, čo presne predstavuje otázka
5. určiť, kedy otázka skúma X, Y, Z alebo T
6. určiť použitie siedmej plochy S pre vzťahy Z a T
7. až potom odvodiť minimálny logický dátový model
8. až z potvrdeného modelu vytvoriť SQL schému
```

---

## 7. Zákaz predčasnej implementácie

Kým nie sú vyššie uvedené body potvrdené, nesmie sa:

- vytvoriť nová SQL schéma databázy METODIKY,
- zaviesť neoverený databázový model ako platnú architektúru,
- vytvárať nové balíky otázok iba podľa predpokladanej databázovej štruktúry,
- zameniť pracovnú možnosť za záväznú definíciu.

---

## 8. Pracovný záver

Pôvodný logický model splnil svoju úlohu tým, že odhalil, ktoré rozhodnutia ešte neboli metodicky pripravené.

Jeho správnym pokračovaním nie je SQL implementácia, ale návrat k elementárnej logike a postupné odvodzovanie každej ďalšej entity z potvrdených definícií.
