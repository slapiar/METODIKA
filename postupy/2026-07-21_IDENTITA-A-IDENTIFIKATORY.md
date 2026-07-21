# Identita objektov a minimálne identifikátory METODIKY

## Stav dokumentu

Tento dokument pokračuje v minimálnom logickom modeli METODIKY krokmi 7.2 a 7.3.

Určuje:

```text
- čo robí objekt tým istým objektom,
- kedy vzniká nový objekt alebo nová udalosť,
- čo musí byť možné stabilne citovať,
- aké minimálne identifikačné údaje vyžaduje logický model.
```

Dokument ešte neurčuje SQL dátové typy, fyzické názvy stĺpcov, spôsob generovania kľúčov ani konkrétnu databázovú technológiu.

---

# 7.2 Totožnosť objektov

## Základné rozlíšenie

```text
identita objektu ≠ identifikátor objektu
```

Identita odpovedá na otázku:

```text
Čo robí túto vec práve touto vecou?
```

Identifikátor odpovedá na otázku:

```text
Ako sa na túto vec jednoznačne odvoláme?
```

Technický identifikátor preto nesmie určovať význam objektu. Iba ho stabilne označuje.

```text
ID nie je dôkaz totožnosti.
ID je odkaz na totožnosť určenú metodikou.
```

---

## 1. Identita otázky

Otázka nie je totožná so svojím textovým zápisom.

Rovnaká elementárna podmienka môže byť vyjadrená rozdielnymi vetami a stále môže ísť o tú istú otázku. Naopak, aj malá textová zmena môže zmeniť skúmanú podmienku a vytvoriť inú otázku.

```text
QUESTION identity
=
význam jednej elementárnej podmienky
v jednom určenom rozmere X, Y, Z alebo T
```

Pravidlo:

```text
zmena pravopisu alebo formy bez zmeny významu
→ tá istá otázka

zmena toho, čo má odpoveď [1/0] potvrdiť
→ iná otázka
```

Príklad:

```text
Existuje lehota?
```

nie je totožné s:

```text
Existuje primeraná lehota?
```

Druhá veta už skúma inú podmienku a pravdepodobne aj iný rozmer.

Ak bola otázka už použitá v hodnotení, musí zostať spätne zistiteľné aj presné znenie, ktoré bolo pri danom hodnotení použité. Preto treba rozlišovať:

```text
významovú identitu otázky
×
konkrétne zaznamenané znenie alebo revíziu
```

Presný technický model revízií ešte nie je určený.

---

## 2. Identita predmetu

Predmet nie je totožný so svojím názvom ani so súborom aktuálnych atribútov.

Osoba zostáva tou istou osobou po zmene adresy. Organizácia môže zostať tou istou organizáciou po zmene názvu. Nová právnická osoba s podobným názvom však nie je pokračovaním pôvodnej identity iba preto, že sa podobá.

```text
SUBJECT identity
=
kontinuita existencie určeného prejavu
```

Toto pravidlo sa musí vykladať podľa druhu predmetu. Kontinuita osoby, dokumentu, procesu, vzťahu, rozhodnutia a organizácie sa nemusí dokazovať rovnakým spôsobom.

Preto METODIKA nemôže zaviesť jeden univerzálny prirodzený identifikátor všetkých predmetov.

```text
názov ≠ identita
atribút ≠ identita
podobnosť ≠ kontinuita
```

---

## 3. Identita dôkazu

Dôkaz musí byť spätne citovateľný v podobe, v ktorej bol pri hodnotení použitý.

```text
EVIDENCE identity
=
konkrétny nemenný dôkazný obsah
spolu s jeho určiteľným pôvodom
```

Ak sa zmení dôkazný obsah, nesmie sa potichu prepísať pôvodný dôkaz.

```text
zmena obsahu
→ nový dôkaz alebo nová revízia dôkazu
```

Rozhodnutie medzi novým dôkazom a novou revíziou závisí od povahy zdroja. METODIKA však musí vždy zachovať možnosť zistiť, ktorá konkrétna podoba bola použitá.

Digitálny odtlačok môže neskôr technicky overovať nemennosť digitálneho obsahu, ale nie je univerzálnou definíciou dôkazu. Pozorovanie, fyzický stav alebo výpoveď nemusia mať pôvodný digitálny súbor.

---

## 4. Identita Autority

Autorita Validácie nie je totožná so svojím aktuálnym oprávnením.

Človek, orgán, organizácia alebo systém môže zostať tým istým agentom, aj keď sa jeho oprávnenia v čase zmenia.

```text
AUTHORITY identity
=
kto alebo čo Autoritou je
```

Oddelene treba určiť:

```text
- pôvod Oprávnenia,
- rozsah Oprávnenia,
- začiatok a koniec jeho platnosti,
- Povinnosti,
- Zodpovednosť,
- spôsob Validácie.
```

Preto platí:

```text
AUTHORITY identity
≠
authority mandate
```

Historická Validácia musí odkazovať nielen na Autoritu, ale aj na oprávnenie platné pri jej vykonaní. Presná entita oprávnenia bude odvodená v ďalšom kroku.

---

## 5. Identita hodnotenia

Hodnotenie je konkrétna historická udalosť.

```text
EVALUATION
=
použitie konkrétnej otázky
na konkrétny predmet
v určenom význame a čase
```

Nový dôkaz, nový čas, nový rozsah alebo nová odpoveď nevykonávajú spätnú opravu starej udalosti. Vytvárajú nové hodnotenie.

```text
nové poznanie
→ nové EVALUATION
```

Oprava chybného technického zápisu môže opraviť záznam iba vtedy, ak nemení tvrdenie o tom, čo sa historicky stalo. Ak mení obsah hodnotenia, musí vzniknúť opravná alebo nahrádzajúca udalosť so zachovaným odkazom na pôvodnú.

```text
EVALUATION identity
=
jedinečný výskyt hodnotiacej udalosti
```

Dvojica `(QUESTION, SUBJECT)` preto nikdy nie je dostatočným identifikátorom hodnotenia.

---

## 6. Identita Validácie

Validácia je konkrétny akt overenia.

```text
VALIDATION
=
overenie konkrétneho hodnotenia
konkrétnou Autoritou
v konkrétnom rozsahu a čase
```

Nové overenie, iná Autorita, iný rozsah alebo zmena výsledku vytvárajú novú validačnú udalosť.

```text
VALIDATION identity
=
jedinečný výskyt validačnej udalosti
```

Validácia sa neprepisuje na aktuálny stav. Aktuálny stav sa odvodzuje z platných validačných udalostí.

---

## 7. Identita zloženého hodnotenia S

Zložené hodnotenie je nemenný logický výpočet nad konkrétnymi vstupnými hodnoteniami.

```text
COMPOSITE_EVALUATION_S
=
S(EVALUATION_Z, EVALUATION_T)
```

Jeho totožnosť určujú najmenej:

```text
- konkrétny vstup EVALUATION_Z,
- konkrétny vstup EVALUATION_T,
- vopred určené pravidlo S,
- poradie argumentov,
- konkrétny výskyt výpočtu.
```

Ak sa zmení vstup alebo pravidlo, vzniká nové zložené hodnotenie.

Ani rovnaké vstupy a rovnaké pravidlo však nesmú automaticky zrušiť identitu nového výskytu, ak sa výpočet vykonal znovu v inom rozhodovacom kontexte. Preto výsledný technický model nesmie používať iba zložený prirodzený kľúč zo vstupov.

---

# Univerzálny princíp histórie

```text
ENTITY
=
trvajúca identita, ktorej stav sa môže v čase meniť

EVENT
=
jedinečný historický výskyt, ktorý sa po vzniku neprepisuje
```

Z toho vyplýva:

```text
História sa neopravuje prepísaním významu.
História sa dopĺňa novou udalosťou,
ktorá pôvodnú udalosť potvrdí, nahradí,
zneplatní alebo opraví.
```

METODIKA sa preto významovo približuje režimu:

```text
Create
Read
Append
Invalidate
```

Nie každá entita musí byť technicky realizovaná úplným event sourcingom. Nemennosť hodnotiacich, validačných a zložených udalostí však vyplýva priamo z metodiky, nie z voľby módnej architektúry.

---

# 7.3 Minimálne identifikátory

## Základné pravidlo

Každý samostatne citovateľný objekt a každá historická udalosť musia mať stabilný vnútorný identifikátor.

```text
internal_id
```

Jeho úlohou je:

```text
- jednoznačne označiť jeden záznam,
- zostať stabilný počas celej existencie záznamu,
- neobsahovať meniaci sa obchodný alebo opisný význam,
- umožniť väzby a spätné citovanie.
```

Typ a spôsob generovania `internal_id` zatiaľ nie sú určené.

```text
číselné ID, UUID, ULID ani hash
zatiaľ nie sú metodickým rozhodnutím
```

---

## Dva druhy identifikácie

METODIKA musí rozlišovať:

```text
1. vnútorný stabilný identifikátor
2. vonkajšie alebo doménové identifikátory
```

Vonkajším identifikátorom môže byť napríklad registračné číslo, číslo dokumentu, URI, kód zariadenia alebo označenie iného systému.

Vonkajší identifikátor:

```text
- nemusí existovať,
- nemusí byť globálne jedinečný,
- môže sa zmeniť,
- môže byť chybný,
- môže mať význam iba v určenom registri alebo priestore.
```

Preto nesmie bez osobitného dôkazu nahradiť vnútornú identitu METODIKY.

Minimálne musí byť pri vonkajšom identifikátore určiteľné:

```text
hodnota
×
identifikačný priestor alebo vydavateľ
×
časová platnosť, ak sa môže meniť
```

---

## Minimálna identifikácia samostatných identít

### QUESTION

```text
question_id
```

Musí stabilne označovať významovú identitu otázky.

Ak sa budú samostatne uchovávať použité znenia alebo revízie, každá spätne citovateľná revízia potrebuje vlastný identifikátor:

```text
question_revision_id
```

Hodnotenie musí odkazovať na presnú revíziu použitú pri hodnotení, nie iba na dnešnú podobu otázky.

### SUBJECT

```text
subject_id
```

Označuje kontinuitu jedného predmetu v METODIKE.

Názvy, kódy a vonkajšie identifikátory sú jeho identifikačné údaje, nie automaticky jeho vnútorný kľúč.

### EVIDENCE

```text
evidence_id
```

Označuje konkrétny spätne citovateľný dôkazný obsah.

Ak zdroj vytvára viac verzií, každá verzia použitá v hodnotení musí byť jednoznačne citovateľná. Technické overenie obsahu môže používať odtlačok, ale odtlačok nenahrádza pôvod, význam ani kontext dôkazu.

### AUTHORITY

```text
authority_id
```

Označuje človeka, orgán, organizáciu, systém alebo iného agenta ako trvajúcu identitu.

Oprávnenie Autority potrebuje samostatnú historickú identifikáciu, pretože rovnaká Autorita môže mať viac oprávnení v rôznych obdobiach a rozsahoch. Minimálny model tejto identity ešte treba odvodiť.

---

## Minimálna identifikácia udalostí

Každá udalosť potrebuje vlastný identifikátor výskytu:

```text
evaluation_id
validation_id
composite_evaluation_id
evaluation_evidence_id
```

Ani úplná zhoda vstupných údajov nesmie automaticky znamenať, že ide o tú istú udalosť.

### EVALUATION

Minimálne musí byť spätne určiteľné:

```text
evaluation_id
question_revision_id
subject_id
čas, ku ktorému sa hodnotenie vzťahuje
čas vykonania alebo zistenia
výsledok alebo stav poznania
```

Dôkazy a Validácie sa viažu ako samostatné udalosti alebo vzťahy.

### EVALUATION_EVIDENCE

Minimálne musí byť spätne určiteľné:

```text
evaluation_evidence_id
evaluation_id
evidence_id
úloha dôkazu v hodnotení
čas pripojenia alebo použitia
```

Tým sa zachová nielen existencia dôkazu, ale aj význam jeho použitia.

### VALIDATION

Minimálne musí byť spätne určiteľné:

```text
validation_id
evaluation_id
authority_id
oprávnenie použité pri Validácii
čas Validácie
výsledok Validácie
```

Samotný `authority_id` nestačí, pretože Autorita nemusela mať v danom čase oprávnenie pre daný rozsah.

### COMPOSITE_EVALUATION_S

Minimálne musí byť spätne určiteľné:

```text
composite_evaluation_id
evaluation_z_id
evaluation_t_id
pravidlo S a poradie argumentov
čas vykonania
výsledok s[1/0]
```

---

# Čas udalosti a čas zápisu

Každá historická udalosť môže mať najmenej dva rozdielne časy:

```text
occurred_at
=
kedy sa udalosť významovo stala

recorded_at
=
kedy bola zapísaná do METODIKY
```

Tieto časy sa nesmú automaticky zlúčiť.

Príklad:

```text
pozorovanie vykonané 10. júla
zapísané 12. júla
```

Rozdiel môže byť podstatný pre dôkaz, poradie udalostí, oprávnenie aj časovú platnosť.

Presný model času, intervalov a neistoty časového údaja ešte nie je určený.

---

# Pôvod záznamu

Stabilný identifikátor neurčuje, odkiaľ záznam pochádza.

Pri každej udalosti musí byť spätne určiteľný jej pôvod:

```text
kto alebo čo záznam vytvorilo
×
z akého zdroja vznikol
×
kedy bol zaznamenaný
```

Pôvodca zápisu sa nesmie automaticky zamieňať s Autoritou Validácie.

```text
recording agent ≠ validating Authority
```

Presná spoločná identita agentov, používateľov, systémov a Autorít bude predmetom ďalšieho odvodenia.

---

# Minimálne univerzálne požiadavky

Každý citovateľný záznam musí vedieť odpovedať aspoň na tieto otázky:

```text
1. Ktorý konkrétny objekt alebo výskyt to je?
2. Akého druhu objekt alebo udalosť to je?
3. Na ktoré presné vstupy alebo identity odkazuje?
4. Kedy udalosť nastala alebo ku ktorému času sa vzťahuje?
5. Kedy bola zaznamenaná?
6. Odkiaľ záznam pochádza?
7. Bol neskôr nahradený, opravený alebo zneplatnený?
```

Posledná otázka neznamená prepísanie pôvodného záznamu. Vyžaduje vzťah na neskoršiu udalosť, ktorá jeho stav mení.

---

# Čo zatiaľ nie je rozhodnuté

```text
- konkrétny formát vnútorných identifikátorov,
- globálna alebo lokálna jedinečnosť identifikátorov,
- presný model revízií otázok a dôkazov,
- univerzálna identita agenta pôvodu,
- samostatná entita oprávnenia Autority,
- presný model opráv, náhrad a zneplatnení,
- reprezentácia nepresného alebo intervalového času,
- technický spôsob kontroly nemennosti,
- fyzická databázová schéma.
```

---

# Pracovný záver

```text
Identita určuje, čo vec je.
Identifikátor iba umožňuje sa na ňu odvolať.

Entity nesú kontinuitu existencie.
Udalosti nesú jedinečnosť historického výskytu.

Každá spätne citovateľná identita,
revízia a udalosť potrebuje stabilný identifikátor.

Minulosť sa významovo neprepisuje.
Nové poznanie sa pripája ako nový záznam
s určeným vzťahom k pôvodnému.
```

Tento krok ešte nevytvára primárne ani cudzie kľúče SQL. Určuje však, ktoré významové objekty budú musieť byť v budúcom modeli stabilne a samostatne citovateľné.
