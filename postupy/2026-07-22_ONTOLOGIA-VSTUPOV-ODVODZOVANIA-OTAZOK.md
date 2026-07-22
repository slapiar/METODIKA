# Ontológia vstupov odvodzovania špecifických otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument zapisuje pracovnú ontológiu vstupov algoritmu odvodzovania špecifických otázok. Nadväzuje na `postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md`, autoritatívne pravidlá otázok v `OTAZKY/README.md`, definície `SUBJECT`, `IDENTITA`, `ÚKON` a `VALIDÁCIA` v `POJMY-A-DEFINICIE.md` a pracovné rozlíšenie identít a vzťahových udalostí v `postupy/2026-07-21_MINIMALNY-LOGICKY-MODEL.md`.

Neurčuje SQL schému, názvy PHP tried, databázové tabuľky ani používateľské rozhranie. Určuje významové objekty, ktoré musí budúca implementácia zachovať.

---

# 1. Predmet ontológie

Algoritmus nevytvára otázku voľným prepisom textu. Vykonáva metodický odvodzovací úkon:

```text
zdrojová univerzálna otázka
+
logicky určený SUBJECT
+
účel odvodzovania
+
kontext a rozsah
+
doménové pojmy
→
kandidát špecifickej otázky
+
záznam odvodenia
```

Predmetom tejto ontológie je určiť:

```text
- ktoré vstupy sú samostatné identity,
- ktoré vstupy sú určením jedného odvodzovacieho úkonu,
- aké väzby musia byť zachované,
- čo nie je vstupom algoritmu,
- kedy sa odvodzovanie musí zastaviť.
```

---

# 2. Základné rozlíšenie

```text
trvalá alebo opakovateľne použiteľná identita
≠
konkrétny odvodzovací úkon
≠
vlastnosť alebo obmedzenie úkonu
≠
výstup úkonu
≠
neskoršie hodnotenie otázky
```

Pre tento algoritmus platí:

```text
QUESTION_SOURCE
SUBJECT
DOMAIN_TERM
=
opakovateľne použiteľné vstupné identity alebo odkazy na identity
```

```text
QUESTION_DERIVATION
=
konkrétny metodický odvodzovací úkon
```

```text
DERIVATION_PURPOSE
DERIVATION_CONTEXT
DERIVATION_SCOPE
=
určenia konkrétneho odvodzovacieho úkonu
```

```text
QUESTION_CANDIDATE
DERIVATION_TRACE
=
výstupy odvodzovacieho úkonu
```

---

# 3. Zdrojová univerzálna otázka

## 3.1 Ontologický druh

Zdrojová univerzálna otázka je existujúca identita `QUESTION`, nie text vložený do formulára bez pôvodu.

```text
QUESTION_SOURCE ⊂ QUESTION
```

Musí byť možné spätne určiť najmenej:

```text
QUESTION_ID
QUESTION_VERSION alebo významovo určený stav otázky
QUESTION_TEXT
UNIVERSAL_CONDITION
PRIMARY_DIMENSION
PRINCIPLE_X
PRINCIPLE_Y
SOURCE_DOCUMENT alebo iný autoritatívny pôvod
```

## 3.2 Záväzná podmienka

Text otázky a univerzálna podmienka nie sú totožné údaje.

```text
QUESTION_TEXT
≠
UNIVERSAL_CONDITION
```

Gramatická forma sa môže pri špecifikácii zmeniť. Univerzálna skúmaná podmienka sa zmeniť nesmie.

## 3.3 Identita zdrojovej otázky

Algoritmus sa nesmie opierať iba o aktuálne znenie bez určenia, z ktorej otázky a z akého významového stavu vychádza.

```text
neidentifikovaný text otázky
→ STOP SOURCE_QUESTION_NOT_IDENTIFIED
```

---

# 4. SUBJECT

## 4.1 Ontologický druh

`SUBJECT` je samostatná identita predmetu skúmania. Nie je iba podstatné meno dosadené do vety.

Musí byť určené najmenej:

```text
čo sa skúma
prečo ide o samostatný predmet
kde sú jeho hranice
čím sa odlišuje od iných predmetov
aký rozsah SUBJECT-u vstupuje do odvodzovania
čo musí zostať zachované, aby išlo stále o ten istý SUBJECT
```

## 4.2 SUBJECT a prejav SUBJECT-u

Algoritmus neodvodzuje otázku automaticky na celý SUBJECT. Najprv hľadá prejav SUBJECT-u, ktorý zodpovedá univerzálnej podmienke.

```text
SUBJECT
→ mapovanie univerzálnej podmienky
→ SUBJECT_MANIFESTATION
```

`SUBJECT_MANIFESTATION` nie je automaticky novou identitou SUBJECT-u. Môže byť:

```text
- časťou SUBJECT-u,
- vlastnosťou SUBJECT-u,
- stavom SUBJECT-u,
- procesom SUBJECT-u,
- vzťahom SUBJECT-u,
- udalosťou patriacou SUBJECT-u.
```

Ak má mať prejav vlastnú identitu, musí prejsť samostatným logickým zdôvodnením SUBJECT-u.

## 4.3 Zastavenie

```text
SUBJECT bez určenej identity, hraníc alebo rozsahu
→ STOP SUBJECT_NOT_JUSTIFIED
```

```text
univerzálnej podmienke nezodpovedá žiadny prejav SUBJECT-u
→ STOP NO_RELEVANT_MANIFESTATION
```

---

# 5. Účel odvodzovania

## 5.1 Ontologický druh

`DERIVATION_PURPOSE` je určením konkrétneho odvodzovacieho úkonu. Nie je odpoveďou, výsledkom ani vlastnosťou zdrojovej otázky.

```text
DERIVATION_PURPOSE ∈ QUESTION_DERIVATION
```

Musí pomenovať, aké poznanie má odvodená otázka umožniť, napríklad:

```text
poznanie aktuálneho stavu
odhalenie príčiny problému
porovnanie variantov
Validácia funkcie
posúdenie rizika
príprava rozhodnutia
```

## 5.2 Neutralita

Účel nesmie obsahovať želaný výsledok.

```text
účel poznania
≠
predurčenie odpovede
```

Neprípustný účel:

```text
dokázať, že riešenie funguje správne
```

Prípustný účel:

```text
zistiť, či určený vstup spúšťa určený proces
```

## 5.3 Zastavenie

```text
neurčený účel
→ STOP PURPOSE_NOT_DEFINED
```

```text
účel predurčuje odpoveď
→ STOP PURPOSE_NOT_NEUTRAL
```

---

# 6. Kontext a rozsah

## 6.1 Ontologický druh

`DERIVATION_CONTEXT` a `DERIVATION_SCOPE` sú určenia jedného konkrétneho odvodzovacieho úkonu. Nemajú sa automaticky premieňať na časť textu otázky.

Musia vedieť oddelene zachytiť relevantný:

```text
vecný rozsah
časový rozsah
priestorový rozsah
procesný rozsah
verziu alebo stav
vzťah k iným SUBJECT-om
projektový alebo doménový kontext
```

## 6.2 Kontext otázky a kontext hodnotenia

Treba zachovať rozlíšenie:

```text
REQUIRED_QUESTION_CONTEXT
=
kontext nevyhnutný na jednoznačný význam otázky
```

```text
EVALUATION_CONTEXT
=
kontext konkrétneho budúceho použitia otázky
```

Do kandidáta otázky patrí iba kontext, bez ktorého by sa menil alebo strácal význam skúmanej podmienky.

Konkrétny čas pozorovania, dôkaz, odpoveď a stav predmetu pri jednom použití patria až hodnotiacemu záznamu.

## 6.3 Zastavenie

```text
rozsah umožňuje preniesť odpoveď na iný predmet alebo inú verziu
→ STOP SCOPE_AMBIGUOUS
```

---

# 7. Doménové pojmy

## 7.1 Ontologický druh

`DOMAIN_TERM` je opakovateľne použiteľný pojem s určeným významom v konkrétnej doméne alebo projekte.

Nie každé slovo použité pri formulácii potrebuje samostatnú identitu. Samostatnú identitu alebo spätne citovateľný odkaz potrebuje pojem, ktorého význam rozhoduje o zachovaní skúmanej podmienky.

Musí byť určiteľné najmenej:

```text
TERM
DEFINED_MEANING
DOMAIN alebo PROJECT_SCOPE
SOURCE_OF_DEFINITION
VALIDITY alebo VERSION, ak sa význam môže meniť
```

## 7.2 Doménové dosadenie

```text
univerzálny pojem
+
doménovo určený ekvivalent
→
doménové dosadenie
```

Dosadenie nesmie:

```text
- meniť primárny rozmer otázky,
- vytvoriť nový predpoklad,
- rozšíriť alebo zúžiť podmienku bez záznamu,
- nahradiť neznámy pojem odhadom.
```

## 7.3 Väzba na odvodzovanie

Jedno odvodzovanie môže použiť viac doménových pojmov a jeden pojem môže byť použitý vo viacerých odvodzovaniach.

```text
QUESTION_DERIVATION M : N DOMAIN_TERM
```

Samotné technické riešenie tejto väzby zatiaľ nie je určené.

## 7.4 Zastavenie

```text
neznámy alebo viacznačný rozhodujúci pojem
→ STOP DOMAIN_TERM_AMBIGUOUS
```

---

# 8. Odvodzovací úkon

## 8.1 Ontologický druh

`QUESTION_DERIVATION` je historicky zachytiteľný metodický úkon. Vzniká konkrétnym spojením vstupov v určenom čase a rozsahu.

```text
QUESTION_DERIVATION
=
derive(
    QUESTION_SOURCE,
    SUBJECT,
    DERIVATION_PURPOSE,
    DERIVATION_CONTEXT,
    DERIVATION_SCOPE,
    DOMAIN_TERMS
)
```

Nie je totožný so zdrojovou otázkou ani s výsledným kandidátom.

```text
QUESTION_SOURCE
≠ QUESTION_DERIVATION
≠ QUESTION_CANDIDATE
```

## 8.2 Minimálne určenie úkonu

Odvodzovací úkon musí vedieť zachytiť najmenej:

```text
DERIVATION_ID alebo iný jednoznačný záznam úkonu
SOURCE_QUESTION_REFERENCE
SUBJECT_REFERENCE
PURPOSE
CONTEXT
SCOPE
USED_DOMAIN_TERMS
TIME_OF_DERIVATION
ACTOR
DERIVATION_STATE
```

`ACTOR` úkon vykonáva. Samotné vykonanie úkonu ešte neurčuje jeho Autoritu ani prijateľnosť výstupu.

## 8.3 Kardinality

```text
QUESTION_SOURCE 1 : N QUESTION_DERIVATION
SUBJECT         1 : N QUESTION_DERIVATION
QUESTION_DERIVATION M : N DOMAIN_TERM
QUESTION_DERIVATION 1 : N QUESTION_CANDIDATE
```

Jedno odvodzovanie môže vytvoriť viac kandidátov, ak univerzálnej podmienke zodpovedá viac samostatných prejavov alebo ak sa zložená podmienka musí rozložiť.

---

# 9. Výstupy

## 9.1 Kandidát otázky

`QUESTION_CANDIDATE` je kandidát novej identity `QUESTION`.

```text
QUESTION_CANDIDATE
≠
ACCEPTED QUESTION
```

Musí obsahovať alebo jednoznačne odkazovať najmenej na:

```text
question text
source universal question
source universal condition
SUBJECT
SUBJECT_MANIFESTATION
primary dimension
specific condition
meaning of 1
meaning of 0
required question context
derivation trace
state = CANDIDATE
```

Kandidát sa stane prijatou otázkou až samostatným metodickým prijatím a Validáciou podľa pravidiel, ktoré tento dokument neurčuje.

## 9.2 Záznam odvodenia

`DERIVATION_TRACE` je historický záznam toho, ako kandidát vznikol. Musí umožniť bez domýšľania odpovedať:

```text
z ktorej otázky kandidát vznikol
ktorú univerzálnu podmienku zachoval
na ktorý SUBJECT sa viaže
ktorý prejav SUBJECT-u konkretizoval
ktoré doménové pojmy použil
aký kontext a rozsah boli rozhodujúce
prečo zostal v rovnakom primárnom rozmere
ktoré kontroly prešli alebo neprešli
```

`DERIVATION_TRACE` nie je dôkazom budúcej odpovede na otázku. Je dôkazovým záznamom pôvodu kandidáta otázky.

---

# 10. Čo nie je vstupom odvodzovania

Do algoritmu odvodzovania špecifickej otázky nepatria ako vstupy budúceho hodnotenia:

```text
ANSWER [1/0]
EVALUATION
EVIDENCE hodnotenia
VALIDATION hodnotenia
AUTHORITY_VALIDATION hodnotenia
COMPOSITE_EVALUATION_S
```

Dôvod:

```text
otázka sa najprv odvodí a prijme
→ potom sa použije na SUBJECT
→ až tým vzniká EVALUATION
→ odpoveď, dôkaz a Validácia patria ku konkrétnemu hodnoteniu
```

Odvodzovací úkon však môže mať vlastného `ACTOR-a` a neskôr vlastnú Validáciu kandidáta. To sa nesmie zamieňať s dôkazom a Validáciou odpovede pri budúcom použití otázky.

---

# 11. Minimálny vstupný kontrakt

Významový kontrakt algoritmu je:

```text
DERIVATION_INPUT
{
    source_question: QUESTION_SOURCE,
    subject: SUBJECT,
    purpose: DERIVATION_PURPOSE,
    context: DERIVATION_CONTEXT,
    scope: DERIVATION_SCOPE,
    domain_terms: set<DOMAIN_TERM>
}
```

Každý vstup musí byť buď:

```text
- jednoznačne identifikovanou existujúcou identitou,
- alebo explicitne určenou vlastnosťou konkrétneho odvodzovacieho úkonu.
```

Voľný text bez určeného významu môže byť používateľským podkladom na doplnenie vstupu, ale nesmie byť bez kontroly považovaný za platný ontologický vstup.

---

# 12. Minimálny výstupný kontrakt

```text
DERIVATION_RESULT
{
    derivation: QUESTION_DERIVATION,
    candidates: list<QUESTION_CANDIDATE>,
    trace: DERIVATION_TRACE,
    state,
    stop_reason?
}
```

Prípustné základné výsledky procesu:

```text
CANDIDATES_CREATED
STOPPED_INSUFFICIENT_INPUT
STOPPED_AMBIGUOUS_INPUT
STOPPED_NO_RELEVANT_MANIFESTATION
RETURNED_FOR_DECOMPOSITION
```

Tieto označenia sú pracovné a zatiaľ netvoria potvrdený číselník METODIKY.

---

# 13. Vzťahový obraz

```text
QUESTION_SOURCE ───────┐
                       │
SUBJECT ───────────────┤
                       │
DERIVATION_PURPOSE ────┤
                       ├── QUESTION_DERIVATION ───┬── QUESTION_CANDIDATE
DERIVATION_CONTEXT ────┤                          └── DERIVATION_TRACE
                       │
DERIVATION_SCOPE ──────┤
                       │
DOMAIN_TERM ───────────↔┘
```

Kandidát zachováva väzby:

```text
QUESTION_CANDIDATE
→ derived_from QUESTION_SOURCE
→ applies_to SUBJECT
→ specializes UNIVERSAL_CONDITION
→ uses SUBJECT_MANIFESTATION
→ constrained_by REQUIRED_QUESTION_CONTEXT
```

---

# 14. Hranica dokumentu

Tento dokument zatiaľ neurčuje:

```text
- fyzickú databázovú schému,
- názvy CodeIgniter tried a namespaces,
- spôsob technického verzovania otázok,
- definitívnu identitu DOMAIN_TERM,
- číselník stavov odvodzovania,
- Autoritu prijatia kandidáta,
- úplnú Validáciu kandidáta,
- výber relevantných univerzálnych otázok,
- vykonanie hodnotenia a odvodenie odpovede.
```

Najbližší technický krok môže vzniknúť až po preskúmaní tejto ontológie a pracovného algoritmu ako jedného významového celku.
