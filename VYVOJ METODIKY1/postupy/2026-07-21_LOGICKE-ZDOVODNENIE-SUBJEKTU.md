# Logické zdôvodnenie SUBJECT-u

## Stav dokumentu

Tento dokument odvodzuje minimálne podmienky, za ktorých možno niečo prijať ako `SUBJECT` v METODIKE.

Nadväzuje na postulát:

```text
SUBJECT
=
čokoľvek logicky zdôvodnené ako predmet skúmania.
```

Neurčuje ešte SQL schému ani technický formulár registrácie predmetu.

---

# 1. Význam pojmu „logicky zdôvodnené“

Logicky zdôvodnené neznamená:

```text
pravdivé,
reálne,
vedecky potvrdené,
morálne správne,
spoločensky prijateľné,
ani už Validované.
```

Znamená iba, že existuje preskúmateľný dôvod, prečo má byť daný objekt, jav, predstava, stav, udalosť alebo vzťah samostatne skúmaný.

Preto možno ako SUBJECT prijať aj:

```text
hypotézu,
plán,
simuláciu,
fiktívny model,
sporné tvrdenie,
nepotvrdený jav,
budúcu možnosť,
historicky neúplne doloženú udalosť.
```

Ich prijatie ako SUBJECT-u nepotvrdzuje ich existenciu ani pravdivosť. Potvrdzuje iba oprávnenosť ich samostatného skúmania.

---

# 2. Základné rozlíšenie

```text
prijatie SUBJECT-u
≠
potvrdenie tvrdenia o SUBJECT-e
```

Príklad:

```text
„Život na Marse“
```

môže byť prijatý ako SUBJECT, hoci otázka:

```text
Existuje dnes na Marse život?
```

môže zostať `nezistené` alebo môže byť hodnotená odpoveďou `0` v presne určenom význame.

SUBJECT teda vytvára predmet hodnotenia, nie výsledok hodnotenia.

---

# 3. Minimálny test prijatia SUBJECT-u

Kandidát na SUBJECT je logicky zdôvodnený, ak spĺňa všetky nasledujúce podmienky.

## 3.1 Určiteľnosť

Musí byť možné povedať:

```text
čo presne sa má skúmať.
```

Názov sám osebe nestačí. Musí byť určený význam, na ktorý názov odkazuje.

Príliš neurčité:

```text
„systém“
„problém“
„to“
„situácia“
```

Určiteľné:

```text
„proces automatického načítania TEMP po kliknutí na mapu
v aplikácii TermikaXC vo verzii nasadenej dňa ...“
```

Určiteľnosť nevyžaduje úplné poznanie predmetu. Vyžaduje iba dostatočné vymedzenie toho, na čo sa hodnotenia vzťahujú.

## 3.2 Rozlíšiteľnosť

Musí byť možné odlíšiť kandidáta:

```text
od iných predmetov,
od jeho častí,
od jeho stavov,
od jeho kópií,
od podobných pomenovaní.
```

Ak nemožno určiť, či dve označenia hovoria o tom istom alebo o inom predmete, SUBJECT ešte nemá spoľahlivú identitu.

## 3.3 Dôvod skúmania

Musí existovať výslovne uvedený dôvod:

```text
prečo má byť tento predmet samostatne skúmaný.
```

Dôvodom môže byť napríklad:

```text
potreba rozhodnutia,
potreba poznania,
existencia rizika,
potreba porovnania,
potreba Validácie,
potreba sledovania zmeny,
potreba odvodiť ďalší výsledok.
```

Samotné tvrdenie „chcem to evidovať“ ešte nemusí byť metodickým dôvodom. Musí byť zrejmé, akú úlohu má predmet v poznaní alebo rozhodovaní.

## 3.4 Možnosť položiť zmysluplnú elementárnu otázku

Kandidát musí umožňovať aspoň jednu otázku, pri ktorej možno určiť:

```text
jednu skúmanú podmienku,
rozmer X, Y, Z alebo T,
význam odpovede 1,
význam odpovede 0.
```

Ak o kandidátovi nemožno vytvoriť ani jednu elementárnu otázku bez rozpadu významu, nejde ešte o použiteľný SUBJECT.

To neznamená, že otázka už musí byť zodpovedaná. Musí byť iba významovo zostaviteľná.

## 3.5 Určený rozsah

Musí byť zrejmé, kde SUBJECT začína a končí aspoň vzhľadom na plánované skúmanie.

Rozsah môže byť:

```text
vecný,
priestorový,
časový,
procesný,
organizačný,
modelový,
verziový.
```

Bez rozsahu by rovnaké pomenovanie mohlo označovať neobmedzene veľa rôznych predmetov.

## 3.6 Nezávislosť od želaného výsledku

SUBJECT nesmie byť vytvorený iba tak, aby už svojou definíciou vynútil požadovanú odpoveď.

Neprípustné:

```text
„nepochybne správne rozhodnutie vedenia“
```

ako predmet otázky:

```text
Bolo rozhodnutie správne?
```

Takéto pomenovanie už predurčuje výsledok.

Prípustné:

```text
„rozhodnutie vedenia zo dňa ... o ...“
```

Hodnotenie správnosti vznikne až otázkou a dôkazmi.

---

# 4. Operatívny zápis

Minimálne logické zdôvodnenie možno zapísať:

```text
SUBJECT_JUSTIFICATION
=
IDENTIFICATION
+
DISTINCTION
+
PURPOSE
+
QUESTIONABILITY
+
SCOPE
+
RESULT_NEUTRALITY
```

Kde:

```text
IDENTIFICATION
=
vieme určiť, čo skúmame

DISTINCTION
=
vieme to odlíšiť od iných predmetov

PURPOSE
=
vieme vysvetliť dôvod samostatného skúmania

QUESTIONABILITY
=
vieme položiť aspoň jednu elementárnu otázku

SCOPE
=
vieme určiť hranice významu predmetu

RESULT_NEUTRALITY
=
definícia nevynucuje želaný výsledok
```

V tejto fáze nejde o binárne hodnotenie existencie SUBJECT-u, ale o metodický test jeho prijateľnosti.

---

# 5. Čo logické zdôvodnenie nevyžaduje

Pre prijatie SUBJECT-u sa nevyžaduje:

```text
úplný dôkaz jeho existencie,
úplná znalosť jeho vlastností,
kladné hodnotenie,
Validácia všetkých tvrdení,
zaradenie do uzavretého typu,
fyzická existencia,
súčasná existencia.
```

Inak by nebolo možné skúmať hypotézy, budúce stavy, modely, riziká ani sporné javy.

---

# 6. Zdôvodnenie je samostatne citovateľné

Zdôvodnenie prijatia SUBJECT-u musí byť spätne preskúmateľné.

Preto nestačí iba evidencia:

```text
subject_id
name
```

Musí byť možné zistiť:

```text
kto alebo čo navrhlo SUBJECT,
ako bol vymedzený,
prečo bol prijatý,
na aký rozsah sa prijatie vzťahovalo,
kedy bolo zdôvodnenie zaznamenané,
ktorá Autorita ho prípadne Validovala.
```

To naznačuje kandidáta na ďalší významový objekt:

```text
SUBJECT_JUSTIFICATION
```

Zatiaľ však nie je potvrdené, či pôjde o:

```text
samostatnú historickú udalosť,
revíziu definície SUBJECT-u,
alebo väzbu medzi SUBJECT-om a dôvodom jeho skúmania.
```

Toto rozhodnutie sa nesmie predčasne nahradiť SQL tabuľkou.

---

# 7. Zmena zdôvodnenia

Ak sa zmení iba opis dôvodu, ale SUBJECT zostáva významovo tým istým predmetom, môže ísť o nové zdôvodnenie alebo revíziu zdôvodnenia.

Ak sa však zmení:

```text
čo sa skúma,
hranica predmetu,
kontinuita existencie,
alebo rozlíšenie od iných predmetov,
```

môže vzniknúť nový SUBJECT.

Preto:

```text
zmena dôvodu skúmania
≠
automaticky nový SUBJECT

zmena identity predmetu
→
nový SUBJECT
```

---

# 8. Príklady

## Prijatý fyzický predmet

```text
SUBJECT:
most cez rieku Hron pri Badíne

Dôvod:
posúdenie technickej spôsobilosti

Rozsah:
konkrétna mostná konštrukcia v určenom mieste

Možná otázka X:
Existuje na nosníku viditeľná trhlina širšia než určený limit?
```

## Prijatá hypotéza

```text
SUBJECT:
hypotéza, že lokálna orografia zosilňuje stúpanie
v určenom priestore a meteorologickej situácii

Dôvod:
porovnanie modelu s historickými letmi

Rozsah:
určené územie, čas a vstupné meteorologické údaje

Možná otázka Z:
Má hypotéza pre plánovanie letu prediktívnu hodnotu?
```

## Neprijatý kandidát

```text
SUBJECT:
„všetko, čo nefunguje“
```

Nie je dostatočne:

```text
určiteľný,
rozlíšiteľný,
ohraničený,
ani neutrálne definovaný.
```

Musí sa rozložiť na konkrétne predmety skúmania.

---

# Pracovný záver

```text
Logicky zdôvodnený SUBJECT
nie je predmet, ktorého pravdivosť je potvrdená.

Je to predmet, pri ktorom možno preskúmať:
čo sa skúma,
prečo sa to skúma,
kde sú hranice predmetu
a akú elementárnu podmienku o ňom možno hodnotiť
bez predurčenia výsledku.
```

Tým vzniká vstupná metodická brána:

```text
určiteľnosť
∧ rozlíšiteľnosť
∧ dôvod skúmania
∧ možnosť elementárnej otázky
∧ rozsah
∧ neutralita výsledku
→
prijateľný kandidát na SUBJECT
```

Tento zápis zatiaľ vyjadruje súčasnú potrebu všetkých podmienok prijatia. Nie je ešte potvrdené, či každá z nich bude v budúcom systéme reprezentovaná samostatnou otázkou, Validáciou alebo povinným údajom.