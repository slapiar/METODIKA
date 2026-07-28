# Ontológia vstupov odvodzovania špecifických otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument zapisuje pracovnú ontológiu vstupov algoritmu odvodzovania špecifických otázok. Nadväzuje na `postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md`, autoritatívne pravidlá otázok v `OTAZKY/README.md`, definície `SUBJECT`, `IDENTITA`, `AKTOR`, `AUTORITA`, `ÚKON` a `VALIDÁCIA` v `POJMY-A-DEFINICIE.md` a pracovné rozlíšenie identít a vzťahových udalostí v `postupy/2026-07-21_MINIMALNY-LOGICKY-MODEL.md`.

Neurčuje SQL schému, názvy PHP tried, databázové tabuľky ani používateľské rozhranie. Určuje významové objekty a vzťahy, ktoré musí budúca implementácia zachovať.

---

# 1. Predmet ontológie

Algoritmus nevytvára otázku voľným prepisom textu. Vykonáva metodický odvodzovací úkon:

```text
existujúca univerzálna QUESTION v úlohe zdrojovej otázky
+
logicky určený DERIVATION_SUBJECT
+
účel odvodzovania
+
kontext
+
rozsah
+
doménové významy
+
ACTOR
+
kontext Autority
→
QUESTION_DERIVATION
→
QUESTION_CANDIDATE
+
DERIVATION_TRACE
```

Predmetom tejto ontológie je určiť:

```text
- ktoré vstupy sú odkazy na samostatné identity,
- ktoré vstupy sú určením jedného odvodzovacieho úkonu,
- kto úkon vykonáva a v akom kontexte Autority,
- aké väzby musia byť zachované,
- čo nie je vstupom algoritmu,
- kedy sa odvodzovanie musí zastaviť.
```

---

# 2. Základné rozlíšenia

```text
trvalá alebo opakovateľne použiteľná identita
≠
rola identity v konkrétnom úkone
≠
konkrétny metodický úkon
≠
vlastnosť alebo obmedzenie úkonu
≠
výstup úkonu
≠
technický záznam úkonu
≠
neskoršie hodnotenie otázky
```

Pre tento algoritmus platí:

```text
QUESTION
SUBJECT
ACTOR
AUTHORITY
=
samostatné identity alebo odkazy na identity
```

```text
SOURCE_QUESTION_REFERENCE
DERIVATION_SUBJECT_REFERENCE
ACTOR_REFERENCE
AUTHORITY_CONTEXT
DOMAIN_TERM_REFERENCE
=
roly alebo odkazy použité v konkrétnom odvodzovacom úkone
```

```text
QUESTION_DERIVATION
=
konkrétny historicky zachytiteľný metodický úkon
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

```text
QUESTION_DERIVATION_RECORD
=
technický záznam úkonu, nie úkon samotný
```

---

# 3. Zdrojová univerzálna otázka

Zdrojová otázka nie je nový ontologický druh otázky. Je existujúcou identitou `QUESTION`, ktorá v konkrétnom odvodzovaní hrá rolu zdroja.

```text
SOURCE_QUESTION_REFERENCE
→ odkazuje na QUESTION
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

Text otázky a univerzálna podmienka nie sú totožné údaje:

```text
QUESTION_TEXT
≠
UNIVERSAL_CONDITION
```

Gramatická forma sa môže pri špecifikácii zmeniť. Univerzálna skúmaná podmienka sa zmeniť nesmie.

```text
neidentifikovaná otázka alebo neurčený významový stav
→ STOP SOURCE_QUESTION_NOT_IDENTIFIED
```

---

# 4. DERIVATION_SUBJECT

`DERIVATION_SUBJECT` je rola existujúceho `SUBJECT-u` v odvodzovacom úkone. Nie je iba podstatné meno dosadené do vety.

Musí byť určené najmenej:

```text
čo sa skúma
prečo ide o samostatný predmet
kde sú jeho hranice
čím sa odlišuje od iných predmetov
aký rozsah SUBJECT-u vstupuje do odvodzovania
čo musí zostať zachované, aby išlo stále o ten istý SUBJECT
```

Algoritmus neodvodzuje otázku automaticky na celý SUBJECT. Najprv hľadá prejav SUBJECT-u, ktorý zodpovedá univerzálnej podmienke:

```text
DERIVATION_SUBJECT
→ mapovanie univerzálnej podmienky
→ SUBJECT_MANIFESTATION
```

`SUBJECT_MANIFESTATION` nie je automaticky novou identitou SUBJECT-u. Môže byť časťou, vlastnosťou, stavom, procesom, vzťahom alebo udalosťou patriacou SUBJECT-u. Ak má mať vlastnú identitu, musí prejsť samostatným logickým zdôvodnením SUBJECT-u.

Treba zachovať rozlíšenie:

```text
DERIVATION_SUBJECT
=
predmet, z ktorého významu sa otázka odvodzuje
```

```text
EVALUATION_SUBJECT
=
konkrétny predmet, na ktorý sa prijatá otázka neskôr použije
```

Tieto SUBJECT-y môžu byť totožné, ale totožnosť nie je automatická. Rozsah použiteľnosti kandidáta musí byť osobitne určený pri jeho prijatí.

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

`DERIVATION_PURPOSE` je určením konkrétneho odvodzovacieho úkonu. Nie je odpoveďou, výsledkom ani vlastnosťou zdrojovej otázky.

```text
DERIVATION_PURPOSE ∈ QUESTION_DERIVATION
```

Musí pomenovať, aké poznanie má odvodená otázka umožniť, napríklad poznanie aktuálneho stavu, odhalenie príčiny problému, porovnanie variantov, Validáciu funkcie, posúdenie rizika alebo prípravu rozhodnutia.

Účel nesmie obsahovať želaný výsledok:

```text
účel poznania
≠
predurčenie odpovede
```

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

`DERIVATION_CONTEXT` a `DERIVATION_SCOPE` sú samostatné určenia jedného odvodzovacieho úkonu.

```text
DERIVATION_CONTEXT
=
okolnosti a významové vzťahy, v ktorých sa odvodzovanie vykonáva
```

Môže zahŕňať najmä:

```text
projektový alebo doménový kontext
vzťah k iným SUBJECT-om
relevantný proces alebo situáciu
verziu alebo stav ako okolnosť významu
```

```text
DERIVATION_SCOPE
=
hranice toho, čo odvodzovanie zahŕňa a čo už nezahŕňa
```

Musí vedieť určiť najmenej:

```text
vecné hranice
časové hranice
priestorové hranice
procesné hranice
zahrnuté a vylúčené prejavy SUBJECT-u
```

Rovnaký údaj nesmie byť bez odôvodnenia evidovaný raz ako kontext a inokedy ako rozsah.

Treba zachovať aj rozlíšenie:

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

Do kandidáta otázky patrí iba kontext, bez ktorého by sa menil alebo strácal význam skúmanej podmienky. Konkrétny čas pozorovania, dôkaz, odpoveď a stav predmetu pri jednom použití patria až hodnotiacemu záznamu.

```text
rozsah umožňuje preniesť odpoveď na iný predmet alebo inú verziu
→ STOP SCOPE_AMBIGUOUS
```

---

# 7. Doménové významy

Definitívna ontologická identita doménového pojmu ešte nie je potvrdená. Preto tento dokument používa pojem:

```text
DOMAIN_TERM_REFERENCE
```

Ide o spätne citovateľný odkaz na určený význam pojmu v konkrétnej doméne alebo projekte.

Musí byť určiteľné najmenej:

```text
TERM
DEFINED_MEANING
DOMAIN alebo PROJECT_SCOPE
SOURCE_OF_DEFINITION
VALIDITY alebo VERSION, ak sa význam môže meniť
```

Nie každé slovo použité pri formulácii potrebuje samostatný odkaz. Odkaz potrebuje pojem, ktorého význam rozhoduje o zachovaní skúmanej podmienky.

Doménové dosadenie nesmie:

```text
- meniť primárny rozmer otázky,
- vytvoriť nový predpoklad,
- rozšíriť alebo zúžiť podmienku bez záznamu,
- nahradiť neznámy pojem odhadom.
```

Jedno odvodzovanie môže použiť viac doménových významov a jeden význam môže byť použitý vo viacerých odvodzovaniach. Technická reprezentácia tejto väzby zatiaľ nie je určená.

```text
neznámy alebo viacznačný rozhodujúci pojem
→ STOP DOMAIN_TERM_AMBIGUOUS
```

---

# 8. ACTOR a kontext Autority

Každý `QUESTION_DERIVATION` musí mať určiteľného `ACTOR-a`.

```text
ACTOR
=
SUBJECT, ktorý vykonáva odvodzovací úkon
```

Samotné vykonanie úkonu nepotvrdzuje jeho oprávnenosť:

```text
ACTOR
≠
AUTHORITY
```

Preto musí odvodzovací úkon zachytiť aj:

```text
AUTHORITY_CONTEXT
```

Ten určuje najmenej:

```text
na základe akého tvrdeného alebo potvrdeného oprávnenia ACTOR koná
na aký úkon a SUBJECT sa oprávnenie vzťahuje
v akom rozsahu a čase platí
aký je stav jeho Validácie
```

Autorita nemusí byť pri začatí úkonu potvrdená. Jej stav však nesmie byť domyslený ani vynechaný. Môže byť napríklad neurčený alebo neValidovaný, kým nebude samostatne posúdený.

```text
ACTOR nie je určený
→ STOP ACTOR_NOT_IDENTIFIED
```

```text
kontext Autority nie je zachytený
→ STOP AUTHORITY_CONTEXT_MISSING
```

Zastavenie neznamená, že ACTOR Autoritu nemá. Znamená, že úkon nemožno bezpečne a spätne preskúmateľne vykonať bez určenia jeho oprávnenostného kontextu.

---

# 9. Odvodzovací úkon

`QUESTION_DERIVATION` je historicky zachytiteľný metodický úkon. Vzniká konkrétnym spojením vstupov v určenom čase a rozsahu.

```text
QUESTION_DERIVATION
=
derive(
    SOURCE_QUESTION_REFERENCE,
    DERIVATION_SUBJECT_REFERENCE,
    DERIVATION_PURPOSE,
    DERIVATION_CONTEXT,
    DERIVATION_SCOPE,
    DOMAIN_TERM_REFERENCES,
    ACTOR_REFERENCE,
    AUTHORITY_CONTEXT
)
```

Nie je totožný so zdrojovou otázkou ani s výsledným kandidátom:

```text
QUESTION
≠ QUESTION_DERIVATION
≠ QUESTION_CANDIDATE
```

Odvodzovací úkon musí vedieť zachytiť najmenej:

```text
DERIVATION_ID alebo iný jednoznačný záznam úkonu
SOURCE_QUESTION_REFERENCE
DERIVATION_SUBJECT_REFERENCE
PURPOSE
CONTEXT
SCOPE
USED_DOMAIN_TERM_REFERENCES
TIME_OF_DERIVATION
ACTOR_REFERENCE
AUTHORITY_CONTEXT
DERIVATION_STATE
```

Technické uloženie úkonu je iba jeho záznam:

```text
QUESTION_DERIVATION_RECORD
≠
QUESTION_DERIVATION
```

Jedno odvodzovanie môže vytvoriť viac kandidátov, ak univerzálnej podmienke zodpovedá viac samostatných prejavov alebo ak sa zložená podmienka musí rozložiť.

---

# 10. Výstupy

## 10.1 Kandidát otázky

`QUESTION_CANDIDATE` je kandidát novej identity `QUESTION`.

```text
QUESTION_CANDIDATE
≠
ACCEPTED QUESTION
```

Musí obsahovať alebo jednoznačne odkazovať najmenej na:

```text
question text
source QUESTION
source universal condition
DERIVATION_SUBJECT
SUBJECT_MANIFESTATION
primary dimension
specific condition
meaning of 1
meaning of 0
required question context
intended applicability scope
derivation trace
state = CANDIDATE
```

Kandidát sa stane prijatou otázkou až samostatným metodickým prijatím a Validáciou podľa pravidiel, ktoré tento dokument neurčuje.

## 10.2 DERIVATION_TRACE

`DERIVATION_TRACE` je provenienčný a auditný záznam toho, ako kandidát vznikol. Nie je `DÔKAZOM` budúcej odpovede na odvodenú otázku.

```text
DERIVATION_TRACE
≠
EVIDENCE hodnotenia
```

Musí umožniť bez domýšľania odpovedať:

```text
z ktorej QUESTION kandidát vznikol
ktorú univerzálnu podmienku zachoval
na ktorý DERIVATION_SUBJECT sa viaže
ktorý prejav SUBJECT-u konkretizoval
ktoré doménové významy použil
aký kontext a rozsah boli rozhodujúce
kto odvodzovanie vykonal
v akom kontexte Autority konal
prečo zostal kandidát v rovnakom primárnom rozmere
ktoré kontroly prešli alebo neprešli
```

---

# 11. Čo nie je vstupom odvodzovania

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
→ potom sa použije na EVALUATION_SUBJECT
→ až tým vzniká EVALUATION
→ odpoveď, dôkaz a Validácia patria ku konkrétnemu hodnoteniu
```

Odvodzovací úkon má vlastného `ACTOR-a`, vlastný kontext Autority a môže mať vlastnú Validáciu kandidáta. To sa nesmie zamieňať s dôkazom a Validáciou odpovede pri budúcom použití otázky.

---

# 12. Minimálny vstupný kontrakt

```text
DERIVATION_INPUT
{
    source_question: SOURCE_QUESTION_REFERENCE,
    derivation_subject: DERIVATION_SUBJECT_REFERENCE,
    purpose: DERIVATION_PURPOSE,
    context: DERIVATION_CONTEXT,
    scope: DERIVATION_SCOPE,
    domain_terms: set<DOMAIN_TERM_REFERENCE>,
    actor: ACTOR_REFERENCE,
    authority_context: AUTHORITY_CONTEXT
}
```

Každý vstup musí byť buď:

```text
- jednoznačne identifikovanou existujúcou identitou,
- spätne citovateľným odkazom na určený význam,
- alebo explicitne určenou vlastnosťou konkrétneho odvodzovacieho úkonu.
```

Voľný text bez určeného významu môže byť používateľským podkladom na doplnenie vstupu, ale nesmie byť bez kontroly považovaný za platný ontologický vstup.

---

# 13. Minimálny výstupný kontrakt

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

# 14. Vzťahový obraz

```text
QUESTION ──(rola SOURCE)────────────┐
                                     │
SUBJECT ──(rola DERIVATION_SUBJECT)──┤
                                     │
DERIVATION_PURPOSE ──────────────────┤
DERIVATION_CONTEXT ──────────────────┤
DERIVATION_SCOPE ────────────────────┤
DOMAIN_TERM_REFERENCE ──────────────↔┤
ACTOR ───────────────────────────────┤
AUTHORITY_CONTEXT ───────────────────┤
                                     ├── QUESTION_DERIVATION
                                     │        ├── QUESTION_CANDIDATE
                                     │        └── DERIVATION_TRACE
                                     │
                                     └── QUESTION_DERIVATION_RECORD
                                          zaznamenáva úkon,
                                          ale nie je úkonom samotným
```

Kandidát zachováva väzby:

```text
QUESTION_CANDIDATE
→ derived_from QUESTION
→ derived_for DERIVATION_SUBJECT
→ specializes UNIVERSAL_CONDITION
→ uses SUBJECT_MANIFESTATION
→ constrained_by REQUIRED_QUESTION_CONTEXT
→ declares INTENDED_APPLICABILITY_SCOPE
```

---

# 15. Hranica dokumentu

Tento dokument zatiaľ neurčuje:

```text
- fyzickú databázovú schému,
- názvy CodeIgniter tried a namespaces,
- spôsob technického verzovania otázok,
- definitívnu identitu doménových pojmov,
- číselník stavov odvodzovania,
- spôsob Validácie Autority ACTOR-a,
- Autoritu prijatia kandidáta,
- úplnú Validáciu kandidáta,
- konečné pravidlá rozsahu opakovaného použitia prijatej otázky,
- výber relevantných univerzálnych otázok,
- vykonanie hodnotenia a odvodenie odpovede.
```

---

# 16. Výsledok pracovnej revízie

Ontológia bola preskúmaná proti autoritatívnym definíciám a pracovným dokumentom METODIKY. Revízia odstránila tieto nedostatky:

```text
ACTOR a AUTHORITY_CONTEXT chýbali vo vstupnom kontrakte
CONTEXT a SCOPE nemali oddelené významové hranice
zdrojová otázka bola nepresne označená ako nový druh identity
DERIVATION_SUBJECT nebol odlíšený od budúceho EVALUATION_SUBJECT-u
DOMAIN_TERM bol predčasne vyhlásený za definitívnu identitu
DERIVATION_TRACE bol nepresne približovaný k DÔKAZU
metodický úkon nebol odlíšený od jeho technického záznamu
```

Po tejto revízii zostáva dokument v stave `PRACOVNÝ`. Pred technickou implementáciou sa musí preskúmať spolu s algoritmom `2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md` ako jeden významový celok.