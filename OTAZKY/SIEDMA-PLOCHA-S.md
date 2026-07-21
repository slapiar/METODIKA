# SIEDMA PLOCHA S — vzťah medzi Z a T

## Účel dokumentu

Tento dokument určuje, ako v METODIKE vzniká a vyhodnocuje sa logický vzťah medzi samostatnými rozmermi Z a T.

```text
S = logický vzťah(Z, T)
```

S nie je ďalší základný rozmer popri X, Y, Z a T. Je metaplochou, ktorá určuje, ako sa majú dve samostatné odpovede Z a T spojiť v konkrétnom zloženom hodnotení.

---

# Základné rozlíšenie

```text
qZ = elementárna otázka rozmeru Z
qT = elementárna otázka rozmeru T
z  = odpoveď [1/0] na qZ
t  = odpoveď [1/0] na qT
S  = určený logický vzťah medzi z a t
s  = výsledok [1/0] vyhodnotenia vzťahu S
```

Preto platí:

```text
qZ ≠ qT
qZ ≠ S
qT ≠ S
z ≠ t
S ≠ s
```

`S` je pravidlo zloženia. `s` je výsledok použitia tohto pravidla na konkrétne odpovede `z` a `t`.

---

# Ako otázka vyvolá vzťah S

Samostatná elementárna otázka Z ani samostatná elementárna otázka T samy osebe vzťah S nevyvolávajú.

Vzťah S vzniká až vtedy, keď skúmaný význam vyžaduje rozhodnúť, ako spolu súvisia dve samostatne vyhodnotené podmienky:

```text
podmienka Z
podmienka T
```

Najprv sa preto musia položiť a vyhodnotiť dve elementárne otázky:

```text
qZ → z[1/0]
qT → t[1/0]
```

Až potom možno použiť určený vzťah:

```text
S(z,t) → s[1/0]
```

Otázka teda nevyvolá S tým, že vo vete obsahuje slová hodnoty a času. Vyvolá ho iba význam zloženého rozhodnutia, ktoré potrebuje spojiť výsledok Z s výsledkom T.

---

# S patrí zloženému hodnoteniu

Elementárna otázka podľa platného pravidla skúma jednu podmienku a primárne jeden rozmer.

Preto vzťah S nepatrí do odpovede jednej elementárnej otázky Z ani T. Patrí do definície zloženého hodnotenia, ktoré určuje:

```text
1. ktorú otázku qZ používa,
2. ktorú otázku qT používa,
3. na aký spoločný predmet alebo vzťah sa obe odpovede viažu,
4. aký logický operátor medzi nimi platí,
5. v akom poradí sú argumenty operátora,
6. aký význam má výsledok s[1/0].
```

Pracovný zápis:

```text
H_S = (qZ, qT, S)
```

Pri konkrétnom použití:

```text
qZ(predmet) → z
qT(predmet) → t
S(z,t)       → s
```

Tento zápis zatiaľ neurčuje databázovú tabuľku ani technickú identitu. Určuje iba metodickú podstatu.

---

# Operátor musí byť určený pred vyhodnotením

Operátor S sa nesmie vyberať podľa výsledkov `z` a `t` až po ich zistení. To by znamenalo prispôsobovať pravidlo požadovanému výsledku.

Správne poradie je:

```text
1. určiť význam zloženého hodnotenia,
2. určiť qZ,
3. určiť qT,
4. určiť operátor S a poradie argumentov,
5. získať a Validovať z,
6. získať a Validovať t,
7. vypočítať s = S(z,t),
8. uchovať dôkaz pôvodu z, t aj použitého S.
```

Preto platí:

```text
S musí byť definované ex ante, nie ex post.
```

---

# Základné vzťahy

## AND — súčasné potvrdenie

```text
S = Z ∧ T
```

Význam:

```text
zmysel je potvrdený
A ZÁROVEŇ
je potvrdený príslušný čas
```

| z | t | s = z ∧ t |
|---|---|-----------|
| 0 | 0 | 0 |
| 0 | 1 | 0 |
| 1 | 0 | 0 |
| 1 | 1 | 1 |

Používa sa iba vtedy, keď zložené rozhodnutie vyžaduje súčasné potvrdenie oboch podmienok.

## OR — potvrdenie aspoň jednej podmienky

```text
S = Z ∨ T
```

| z | t | s = z ∨ t |
|---|---|-----------|
| 0 | 0 | 0 |
| 0 | 1 | 1 |
| 1 | 0 | 1 |
| 1 | 1 | 1 |

Používa sa vtedy, keď význam rozhodnutia pripúšťa potvrdenie zmyslu, času alebo oboch.

## IF T THEN Z — čas zaväzuje zmysel

```text
S = T → Z
```

| t | z | s = t → z |
|---|---|-----------|
| 0 | 0 | 1 |
| 0 | 1 | 1 |
| 1 | 0 | 0 |
| 1 | 1 | 1 |

Význam:

```text
Ak je potvrdený čas alebo časová priorita T,
musí byť potvrdený aj zmysel Z.
```

Vzťah je nepravdivý iba v stave:

```text
t = 1
z = 0
```

Tento vzťah nie je totožný s `Z ∧ T`.

## IF Z THEN T — zmysel zaväzuje čas

```text
S = Z → T
```

| z | t | s = z → t |
|---|---|-----------|
| 0 | 0 | 1 |
| 0 | 1 | 1 |
| 1 | 0 | 0 |
| 1 | 1 | 1 |

Význam:

```text
Ak je potvrdený zmysel Z,
musí byť určený alebo potvrdený aj príslušný čas T.
```

Poradie argumentov je podstatné:

```text
T → Z ≠ Z → T
```

## XOR — práve jedna podmienka

```text
S = Z ⊕ T
```

| z | t | s = z ⊕ t |
|---|---|-----------|
| 0 | 0 | 0 |
| 0 | 1 | 1 |
| 1 | 0 | 1 |
| 1 | 1 | 0 |

Používa sa iba vtedy, keď význam hodnotenia vyžaduje výlučnosť a súčasné potvrdenie oboch podmienok má byť nepravdivé.

---

# Príklad jedného predmetu

Predmet hodnotenia:

```text
urgentná projektová úloha
```

Otázka Z:

```text
qZ: Má vykonanie úlohy pre projekt potvrdený zmysel? [1/0]
```

Otázka T:

```text
qT: Má úloha v aktuálnom čase potvrdenú prioritu? [1/0]
```

Ak metodické pravidlo znie:

```text
Ak má úloha aktuálnu prioritu, musí mať potvrdený zmysel.
```

potom:

```text
S = T → Z
```

Výsledky:

```text
t = 1, z = 1 → s = 1
 t = 1, z = 0 → s = 0
 t = 0, z = 1 → s = 1
 t = 0, z = 0 → s = 1
```

Posledné dva stavy nehovoria, že úloha má zmysel alebo že sa má vykonať. Hovoria iba, že podmienka `T → Z` nebola porušená, pretože T nebolo potvrdené.

Výsledok logického vzťahu sa preto nesmie zamieňať s výsledkom jednotlivých otázok ani s konečným rozhodnutím o konaní.

---

# Stav nezistené

Vzťah S možno binárne vyhodnotiť iba vtedy, keď sú obe vstupné odpovede spoľahlivo určené a Validované.

Ak platí:

```text
z = nezistené
alebo
t = nezistené
```

potom výsledok `s` spravidla tiež zostáva:

```text
s = nezistené
```

`Nezistené` nie je tretia pravdivostná hodnota operátora. Je stavom poznania alebo spracovania. Výnimka môže vzniknúť iba vtedy, keď konkrétny operátor a známy vstup už matematicky určujú výsledok bez ohľadu na neznámy vstup; také zjednodušenie však musí byť výslovne povolené metodikou hodnotenia, nie domyslené implementáciou.

---

# Dôkaznosť a Validácia výsledku S

Výsledok `s` musí byť spätne odvoditeľný najmenej z:

```text
qZ
× z
× dôkaz z
× Validácia z
× qT
× t
× dôkaz t
× Validácia t
× definícia operátora S
× poradie argumentov
× časová a rozsahová platnosť
```

Samotná pravdivostná tabuľka Validuje iba správnosť logického výpočtu. Nevaliduje pravdivosť vstupných odpovedí `z` a `t` ani oprávnenosť výberu operátora S.

Pre úplnú Validáciu treba oddeliť:

```text
Validáciu vstupu Z
Validáciu vstupu T
Validáciu významu a výberu S
Validáciu výpočtu s
```

---

# Pracovný záver

```text
Z a T sa najprv skúmajú samostatne.
S je vopred určené pravidlo ich zloženia.
s je výsledok použitia S na odpovede z a t.
S patrí zloženému hodnoteniu, nie jednej elementárnej otázke.
Výsledok s nie je automaticky konečným rozhodnutím o konaní.
```

Technické uloženie vzťahu S, väzieb na otázky a výsledkov jeho použití možno navrhnúť až v minimálnom logickom modeli databázy.