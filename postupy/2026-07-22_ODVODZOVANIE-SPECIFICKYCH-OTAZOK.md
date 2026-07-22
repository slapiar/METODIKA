# Odvodzovanie špecifických otázok z univerzálnych otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument určuje prvý minimálny významový algoritmus, ktorým možno z existujúcej univerzálnej `QUESTION` v úlohe zdrojovej otázky odvodiť kandidáta špecifickej otázky pre jeden logicky vymedzený `DERIVATION_SUBJECT`.

Algoritmus je významovo viazaný na pracovnú ontológiu v `postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md`.

Neurčuje databázovú schému, programovací jazyk, názvy PHP tried, používateľské rozhranie ani autonómne rozhodovanie bez Validácie.

---

# 1. Účel algoritmu

Algoritmus má zabezpečiť, aby špecifická otázka nevznikla iba voľným preformulovaním textu, ale ako spätne preskúmateľný metodický úkon:

```text
SOURCE_QUESTION_REFERENCE
+
DERIVATION_SUBJECT_REFERENCE
+
DERIVATION_PURPOSE
+
DERIVATION_CONTEXT
+
DERIVATION_SCOPE
+
DOMAIN_TERM_REFERENCES
+
ACTOR_REFERENCE
+
AUTHORITY_CONTEXT
→
QUESTION_DERIVATION
→
QUESTION_CANDIDATE
+
DERIVATION_TRACE
```

Výstupom algoritmu ešte nie je prijatá otázka, odpoveď, hodnotenie ani pravda. Výstupom je kandidát otázky a auditná stopa jeho odvodenia pripravené na samostatnú metodickú kontrolu a Validáciu.

---

# 2. Základné rozlíšenia

```text
QUESTION
≠ SOURCE_QUESTION_REFERENCE
≠ QUESTION_DERIVATION
≠ QUESTION_CANDIDATE
≠ ACCEPTED QUESTION
```

```text
DERIVATION_SUBJECT
≠ automaticky EVALUATION_SUBJECT
```

```text
ACTOR
≠ AUTHORITY
```

```text
vykonanie odvodzovacieho úkonu
≠ oprávnenosť úkonu
```

```text
DERIVATION_CONTEXT
≠ DERIVATION_SCOPE
```

```text
DERIVATION_TRACE
≠ EVIDENCE budúceho hodnotenia
```

```text
QUESTION_DERIVATION
≠ QUESTION_DERIVATION_RECORD
```

```text
odvodenie otázky
≠ použitie otázky
```

```text
jazyková správnosť
≠ metodická správnosť
```

Univerzálna otázka nesie všeobecnú skúmanú podmienku. Kandidát špecifickej otázky túto podmienku zachováva, ale viaže ju na presne určený predmet odvodenia, jeho relevantný prejav, význam, kontext a rozsah.

---

# 3. Minimálny vstupný kontrakt

Algoritmus sa nesmie spustiť bez významovo určeného vstupu:

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

Každý vstup musí byť buď jednoznačne identifikovaným odkazom na existujúcu identitu, alebo explicitne určenou vlastnosťou konkrétneho odvodzovacieho úkonu.

Voľný text môže byť podkladom na doplnenie vstupu, ale nesmie byť bez kontroly považovaný za platný ontologický vstup.

## 3.1 SOURCE_QUESTION_REFERENCE

Zdrojová otázka je existujúca `QUESTION`, ktorá v konkrétnom úkone hrá rolu zdroja. Musí byť možné určiť najmenej:

```text
QUESTION_ID
QUESTION_VERSION alebo významovo určený stav
QUESTION_TEXT
UNIVERSAL_CONDITION
PRIMARY_DIMENSION
PRINCIPLE_X
PRINCIPLE_Y
SOURCE_DOCUMENT alebo iný autoritatívny pôvod
```

Pri otázkach objektívnej matice predstavujú `PRINCIPLE_X` a `PRINCIPLE_Y` polohu v matici 7 × 7.

```text
QUESTION_TEXT
≠ UNIVERSAL_CONDITION
```

Gramatická forma sa môže pri špecifikácii zmeniť. Univerzálna skúmaná podmienka sa zmeniť nesmie.

## 3.2 DERIVATION_SUBJECT_REFERENCE

`DERIVATION_SUBJECT` je rola existujúceho `SUBJECT-u` v odvodzovacom úkone. Musí byť logicky zdôvodnené:

```text
čo sa skúma
prečo je to samostatný predmet
kde sú jeho hranice
čím sa odlišuje od iných predmetov
aký rozsah SUBJECT-u vstupuje do odvodzovania
čo musí zostať zachované, aby išlo stále o ten istý SUBJECT
```

Predmet, z ktorého významu sa otázka odvodila, nemusí byť automaticky totožný s každým budúcim predmetom hodnotenia. Rozsah použiteľnosti kandidáta sa musí určiť pri jeho prijatí.

## 3.3 DERIVATION_PURPOSE

Účel určuje, aké poznanie má odvodená otázka umožniť, napríklad:

```text
poznanie aktuálneho stavu
odhalenie príčiny problému
porovnanie variantov
Validácia funkcie
posúdenie rizika
príprava rozhodnutia
```

Účel nesmie obsahovať želanú odpoveď ani predurčiť výsledok.

## 3.4 DERIVATION_CONTEXT

Kontext určuje okolnosti a významové vzťahy, v ktorých sa odvodzovanie vykonáva. Môže zahŕňať:

```text
projektový alebo doménový kontext
vzťah k iným SUBJECT-om
relevantný proces alebo situáciu
verziu alebo stav ako okolnosť významu
```

## 3.5 DERIVATION_SCOPE

Rozsah určuje hranice toho, čo odvodzovanie zahŕňa a čo už nezahŕňa. Musí vedieť určiť:

```text
vecné hranice
časové hranice
priestorové hranice
procesné hranice
zahrnuté a vylúčené prejavy SUBJECT-u
```

Rovnaký údaj nesmie byť bez odôvodnenia evidovaný raz ako kontext a inokedy ako rozsah.

Do textu kandidáta patrí iba `REQUIRED_QUESTION_CONTEXT`, bez ktorého by sa menil alebo strácal význam otázky. Kontext konkrétneho budúceho použitia patrí až hodnotiacemu záznamu.

## 3.6 DOMAIN_TERM_REFERENCES

Definitívna ontologická identita doménového pojmu ešte nie je potvrdená. Algoritmus preto používa spätne citovateľné odkazy na určené významy pojmov.

Každý rozhodujúci odkaz musí určiť najmenej:

```text
TERM
DEFINED_MEANING
DOMAIN alebo PROJECT_SCOPE
SOURCE_OF_DEFINITION
VALIDITY alebo VERSION, ak sa význam môže meniť
```

Neznámy alebo viacznačný pojem sa nesmie doplniť odhadom.

## 3.7 ACTOR_REFERENCE

Každý odvodzovací úkon musí mať určiteľného `ACTOR-a`.

```text
ACTOR
=
SUBJECT, ktorý vykonáva odvodzovací úkon
```

## 3.8 AUTHORITY_CONTEXT

Kontext Autority zachytáva najmenej:

```text
na základe akého tvrdeného alebo potvrdeného oprávnenia ACTOR koná
na aký úkon a SUBJECT sa oprávnenie vzťahuje
v akom rozsahu a čase platí
aký je stav jeho Validácie
```

Autorita nemusí byť pri začatí úkonu potvrdená. Jej stav však nesmie byť domyslený ani vynechaný.

---

# 4. Výstupný kontrakt

Výsledok algoritmu musí mať významový tvar:

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

Každý `QUESTION_CANDIDATE` musí obsahovať alebo jednoznačne odkazovať najmenej na:

```text
QUESTION_TEXT
SOURCE_QUESTION_REFERENCE
UNIVERSAL_CONDITION
DERIVATION_SUBJECT_REFERENCE
SUBJECT_MANIFESTATION
PRIMARY_DIMENSION
SPECIFIC_CONDITION
MEANING_OF_1
MEANING_OF_0
REQUIRED_QUESTION_CONTEXT
DERIVATION_TRACE
STATE = CANDIDATE
```

`DERIVATION_TRACE` je auditná a provenienčná stopa pôvodu kandidáta. Nie je dôkazom budúcej odpovede na otázku.

Prípustné základné výsledky procesu sú pracovné:

```text
CANDIDATES_CREATED
STOPPED_INSUFFICIENT_INPUT
STOPPED_AMBIGUOUS_INPUT
STOPPED_NO_RELEVANT_MANIFESTATION
RETURNED_FOR_DECOMPOSITION
```

Až samostatné prijatie a Validácia môžu určiť, či sa kandidát stane otázkou prijatou na použitie.

---

# 5. Minimálny odvodzovací algoritmus

## Krok 1 — Založenie QUESTION_DERIVATION

Vytvoriť historicky zachytiteľný odvodzovací úkon s jednoznačným záznamom času, vstupov a počiatočného stavu.

```text
QUESTION_DERIVATION
≠ QUESTION_DERIVATION_RECORD
```

Budúca technická implementácia môže vytvoriť záznam úkonu, nesmie ho však zameniť s metodickým úkonom samotným.

## Krok 2 — Identifikácia ACTOR-a a kontextu Autority

Overiť, že je určený ACTOR a zachytený `AUTHORITY_CONTEXT`.

Samotné vykonanie úkonu nepotvrdzuje oprávnenosť. Ak Autorita nie je potvrdená, jej stav sa zachytí ako neurčený alebo neValidovaný; nesmie sa domyslieť.

## Krok 3 — Načítanie zdrojovej QUESTION

Načítať existujúcu otázku v úlohe zdroja, jej presné znenie, významový stav, univerzálnu podmienku, primárny rozmer, polohu v matici a autoritatívny pôvod.

Kontrola:

```text
Je zdrojová QUESTION a jej významový stav jednoznačne identifikovaný?
```

Ak nie, algoritmus sa zastaví.

## Krok 4 — Prijatie DERIVATION_SUBJECT-u

Overiť, že SUBJECT prešiel minimálnym testom určiteľnosti, rozlíšiteľnosti, rozsahu a výsledkovej neutrality.

Kontrola:

```text
Môže byť význam kandidáta omylom priradený inému predmetu, časti, verzii alebo rozsahu?
```

Ak áno, SUBJECT treba spresniť alebo rozdeliť.

## Krok 5 — Kontrola účelu, kontextu a rozsahu

Overiť:

```text
účel je určený a neutrálny
kontext zachytáva významové okolnosti
rozsah určuje zahrnuté a vylúčené hranice
kontext a rozsah sa bez odôvodnenia neprekrývajú
```

## Krok 6 — Extrakcia univerzálnej podmienky

Zo zdrojovej otázky oddeliť gramatickú formu od významu, ktorý má odpoveď `[1/0]` potvrdiť.

```text
UNIVERSAL_CONDITION
=
podmienka zachovaná vo všetkých prípustných špecifikáciách otázky
```

Algoritmus nesmie zachovať iba podobné slová a pritom zmeniť skúmanú podmienku.

## Krok 7 — Určenie relevantného prejavu DERIVATION_SUBJECT-u

Vyhľadať, ktorá časť, vlastnosť, vzťah, stav, proces alebo udalosť SUBJECT-u zodpovedá univerzálnej podmienke.

```text
DERIVATION_SUBJECT
→ mapovanie univerzálnej podmienky
→ SUBJECT_MANIFESTATION
```

Ak univerzálnej podmienke nezodpovedá žiadny prejav, algoritmus sa zastaví.

Ak jej zodpovedá viac samostatných prejavov, nesmú sa zlúčiť do jednej otázky. Vznikne viac kandidátov.

`SUBJECT_MANIFESTATION` nie je automaticky nový SUBJECT. Ak má mať samostatnú identitu, musí prejsť samostatným logickým zdôvodnením.

## Krok 8 — Doménové dosadenie

Nahradiť všeobecné výrazy konkrétnymi doménovo určenými významami bez zmeny logického významu.

Dosadenie nesmie:

```text
meniť primárny rozmer otázky
vytvoriť nový predpoklad
rozšíriť alebo zúžiť podmienku bez záznamu
nahradiť neznámy význam odhadom
```

## Krok 9 — Určenie jednej špecifickej podmienky

Zostaviť jednu podmienku, ktorej potvrdenie alebo nepotvrdenie možno jednoznačne rozlíšiť.

```text
SPECIFIC_CONDITION
```

Ak veta obsahuje dve podmienky, ktoré môžu mať rozdielne odpovede, musí sa rozdeliť.

## Krok 10 — Kontrola rozmeru

Overiť, čo sa mení medzi odpoveďou `1` a `0`:

```text
X — predmet, existencia, identita alebo hranica
Y — spôsob, stav, mechanizmus alebo priebeh
Z — hodnota, miera, primeranosť alebo význam
T — čas, trvanie, poradie, platnosť alebo priorita
```

Špecifikácia nesmie zmeniť primárny rozmer bez toho, aby vznikla nová otázka s novým odôvodnením.

## Krok 11 — Zostavenie vety kandidáta

Vytvoriť jazykovo prirodzenú vetu, ktorá:

```text
skúma jednu podmienku
vzťahuje sa na určený predmet alebo jeho prejav
zachováva univerzálnu podmienku
nepredurčuje výsledok
umožňuje odpoveď [1/0]
obsahuje iba nevyhnutný REQUIRED_QUESTION_CONTEXT
```

Gramatický tvar nie je rozhodujúci. Rozhodujúci je význam odpovede.

## Krok 12 — Určenie významu odpovede 1

```text
MEANING_OF_1
=
špecifická podmienka je v určenom význame, rozsahu a nevyhnutnom kontexte potvrdená
```

## Krok 13 — Určenie významu odpovede 0

```text
MEANING_OF_0
=
špecifická podmienka v určenom význame nebola potvrdená
```

Hodnota `0` nesmie automaticky znamenať absolútnu neexistenciu SUBJECT-u. Podľa kontextu môže byť potrebné odlíšiť:

```text
podmienka neplatí
prejav nie je prítomný
chýba dôkaz
predmet je nepresne určený
ešte nenastal alebo už uplynul príslušný čas
otázka sa musí ďalej rozložiť
Validácia nebola vykonaná alebo neuspela
```

Tieto stavy sa bez ďalšej metodiky nesmú zlievať do jedného tvrdenia o realite.

## Krok 14 — Kontrola elementárnosti

Položiť kontrolnú otázku:

```text
Môže byť niektorá časť vety potvrdená a iná nepotvrdená?
```

Ak áno, kandidát nie je elementárny a musí sa rozdeliť.

## Krok 15 — Kontrola spätnej odvoditeľnosti

Musí byť možné bez domýšľania odpovedať:

```text
Ktorý ACTOR úkon vykonal?
V akom kontexte Autority konal?
Z ktorej QUESTION kandidát vznikol?
Ktorú univerzálnu podmienku zachoval?
Ktorý DERIVATION_SUBJECT a jeho prejav konkretizoval?
Ktoré doménové významy boli použité?
Aký kontext a rozsah boli rozhodujúce?
Prečo zostal v rovnakom primárnom rozmere?
Ktoré kontroly prešli alebo neprešli?
```

Ak niektorý krok nemožno doložiť, kandidát sa odmietne alebo vráti na revíziu.

## Krok 16 — Kontrola neutrality

Otázka nesmie obsahovať hodnotiace alebo presviedčacie výrazy, ktoré vopred určujú odpoveď.

Neprípustné:

```text
Funguje už správne navrhnuté načítanie TEMP?
```

Prípustné:

```text
Spustí kliknutie na mapu požiadavku na načítanie TEMP pre zvolené súradnice?
```

## Krok 17 — Vytvorenie kandidátov a DERIVATION_TRACE

Ak všetky kontroly prešli, vytvoriť:

```text
QUESTION_CANDIDATE alebo viac QUESTION_CANDIDATE
DERIVATION_TRACE
```

Kandidát nesmie byť automaticky zaradený medzi prijaté projektové otázky bez samostatného metodického prijatia a Validácie.

---

# 6. Zastavovacie podmienky

Algoritmus sa musí zastaviť najmenej pri týchto stavoch:

```text
SOURCE_QUESTION_NOT_IDENTIFIED
SUBJECT_NOT_JUSTIFIED
PURPOSE_NOT_DEFINED
PURPOSE_NOT_NEUTRAL
CONTEXT_AMBIGUOUS
SCOPE_AMBIGUOUS
DOMAIN_TERM_AMBIGUOUS
ACTOR_NOT_IDENTIFIED
AUTHORITY_CONTEXT_MISSING
NO_RELEVANT_MANIFESTATION
MULTIPLE_CONDITIONS_REQUIRE_DECOMPOSITION
PRIMARY_DIMENSION_CHANGED
ANSWER_MEANING_UNDEFINED
RESULT_NOT_NEUTRAL
DERIVATION_NOT_TRACEABLE
```

Zastavenie nie je chybou systému. Je metodickým výsledkom, že vstupy alebo podmienky zatiaľ nestačia na bezpečné odvodenie otázky.

---

# 7. Pracovný pseudokód

```text
FUNCTION derive_specific_questions(input: DERIVATION_INPUT):

    REQUIRE identified(input.actor)
    REQUIRE authority_context_recorded(input.authority_context)
    REQUIRE identified(input.source_question)
    REQUIRE justified(input.derivation_subject)
    REQUIRE defined(input.purpose)
    REQUIRE neutral(input.purpose)
    REQUIRE context_distinct_from_scope(input.context, input.scope)
    REQUIRE scope_unambiguous(input.scope)
    REQUIRE domain_terms_defined(input.domain_terms)

    derivation = begin_question_derivation(
        actor = input.actor,
        authority_context = input.authority_context,
        source_question = input.source_question,
        derivation_subject = input.derivation_subject,
        purpose = input.purpose,
        context = input.context,
        scope = input.scope,
        domain_terms = input.domain_terms
    )

    universal_condition = extract_condition(input.source_question)

    manifestation_set = map_condition_to_subject(
        universal_condition,
        input.derivation_subject,
        input.context,
        input.scope
    )

    IF manifestation_set is empty:
        RETURN DERIVATION_RESULT(
            derivation = derivation,
            candidates = [],
            trace = record_derivation_trace(),
            state = STOPPED_NO_RELEVANT_MANIFESTATION,
            stop_reason = NO_RELEVANT_MANIFESTATION
        )

    candidates = []

    FOR manifestation IN manifestation_set:
        specific_condition = specialize(
            universal_condition,
            manifestation,
            input.domain_terms,
            input.context,
            input.scope
        )

        IF contains_multiple_conditions(specific_condition):
            specific_conditions = decompose(specific_condition)
        ELSE:
            specific_conditions = [specific_condition]

        FOR condition IN specific_conditions:
            required_context = determine_required_question_context(
                condition,
                input.context,
                input.scope
            )

            candidate_text = formulate_question(
                condition,
                manifestation,
                required_context
            )

            REQUIRE same_primary_dimension(
                input.source_question,
                candidate_text
            )
            REQUIRE elementary(candidate_text)
            REQUIRE result_neutral(candidate_text)
            REQUIRE meaning_of_1_defined(condition)
            REQUIRE meaning_of_0_defined(condition)

            trace = record_derivation_trace(
                derivation = derivation,
                manifestation = manifestation,
                condition = condition,
                required_context = required_context,
                controls = current_controls()
            )

            REQUIRE derivation_traceable(trace)

            candidates.append(
                QUESTION_CANDIDATE(
                    question_text = candidate_text,
                    source_question = input.source_question,
                    universal_condition = universal_condition,
                    derivation_subject = input.derivation_subject,
                    subject_manifestation = manifestation,
                    primary_dimension = input.source_question.primary_dimension,
                    specific_condition = condition,
                    meaning_1 = define_1(condition),
                    meaning_0 = define_0(condition),
                    required_question_context = required_context,
                    derivation_trace = trace,
                    state = CANDIDATE
                )
            )

    RETURN DERIVATION_RESULT(
        derivation = derivation,
        candidates = candidates,
        trace = record_derivation_trace(),
        state = CANDIDATES_CREATED
    )
```

---

# 8. Prvý skúšobný príklad

## SOURCE_QUESTION_REFERENCE

```text
QUESTION:
MM — Má skúmaný jav určiteľný pôvod, myšlienku, zámer alebo dôvod svojej existencie?

UNIVERSAL_CONDITION:
prejav má určiteľný pôvod alebo dôvod vzniku
```

## DERIVATION_SUBJECT

```text
proces automatického načítania TEMP
po kliknutí na mapu
v určenej verzii TermikaXC
```

## DERIVATION_PURPOSE

```text
zistiť, či technický proces vzniká z určiteľného spúšťacieho podnetu
```

## DERIVATION_CONTEXT

```text
projekt TermikaXC
proces komunikácie mapy s načítaním TEMP
```

## DERIVATION_SCOPE

```text
zahrnuté:
vznik požiadavky po kliknutí na mapu pre zvolené súradnice

vylúčené:
odpoveď servera
správnosť údajov
zobrazenie TEMP
použiteľnosť výsledku
```

## DOMAIN_TERM_REFERENCES

```text
kliknutie na mapu
zvolené súradnice
požiadavka na načítanie TEMP
```

Ich význam a pôvod musia byť spätne citovateľné v projektovej doméne.

## ACTOR_REFERENCE

```text
ACTOR vykonávajúci odvodzovací úkon musí byť jednoznačne zachytený v zázname úkonu.
```

## AUTHORITY_CONTEXT

```text
Musí byť zachytené, na základe akého oprávnenia ACTOR odvodzuje projektovú otázku, pre aký projekt, SUBJECT, rozsah a čas a v akom stave je Validácia tohto oprávnenia.
```

Tento pracovný príklad neurčuje konkrétnu osobu ani nepredstiera potvrdenú Autoritu.

## SUBJECT_MANIFESTATION

```text
vznik požiadavky na načítanie TEMP
```

## Doménové dosadenie

```text
pôvod prejavu
→ kliknutie na mapu so zvolenými súradnicami

konkrétny prejav
→ požiadavka na načítanie TEMP
```

## QUESTION_CANDIDATE

```text
Spustí kliknutie na mapu požiadavku na načítanie TEMP pre zvolené súradnice?
```

## Význam odpovedí

```text
1 = po kliknutí na mapu vznikla pre zvolené súradnice preukázateľná požiadavka na načítanie TEMP

0 = vznik takejto požiadavky po kliknutí na mapu nebol v určenom význame potvrdený
```

## Poznámka k príkladu

Kandidát skúma iba bezprostredný spúšťací pôvod technického prejavu. Nehodnotí:

```text
či server odpovedal
či boli údaje správne
či sa TEMP zobrazil
či bol výsledok použiteľný
```

Tieto podmienky musia byť odvodené ako samostatné otázky.

Príklad je neúplným behom algoritmu, kým nie sú konkrétne určené a zaznamenané `ACTOR_REFERENCE`, `AUTHORITY_CONTEXT` a autoritatívne odkazy použitých doménových významov.

---

# 9. Minimálna Validácia kandidáta otázky

Pred prijatím kandidáta sa musí overiť najmenej:

```text
1. Je zdrojová QUESTION a jej významový stav jednoznačne určený?
2. Je DERIVATION_SUBJECT dostatočne vymedzený?
3. Je ACTOR jednoznačne určený?
4. Je zachytený kontext Autority ACTOR-a bez domyslenia jej platnosti?
5. Je účel odvodzovania určený a neutrálny?
6. Sú DERIVATION_CONTEXT a DERIVATION_SCOPE odlíšené?
7. Zachovala sa univerzálna podmienka?
8. Skúma kandidát iba jednu podmienku?
9. Zostal zachovaný správny primárny rozmer?
10. Je význam odpovede 1 jednoznačný?
11. Je význam odpovede 0 jednoznačný?
12. Je kandidát neutrálny voči výsledku?
13. Sú doménové významy určené bez domýšľania a spätne citovateľné?
14. Je REQUIRED_QUESTION_CONTEXT obmedzený iba na nevyhnutný význam otázky?
15. Je celý postup odvodenia spätne citovateľný v DERIVATION_TRACE?
16. Je DERIVATION_TRACE odlíšený od EVIDENCE budúceho hodnotenia?
17. Je kandidát odlíšený od prijatej otázky a budúceho hodnotenia?
```

Pracovný výsledok Validácie môže byť:

```text
ACCEPTED_FOR_USE
RETURNED_FOR_REVISION
REJECTED
NOT_VERIFIABLE
```

Tieto označenia zatiaľ netvoria potvrdený číselník METODIKY.

---

# 10. Hranica prvého algoritmu

Tento algoritmus zatiaľ nerieši:

```text
automatický výber najrelevantnejších univerzálnych otázok
úplnosť celého prieskumu SUBJECT-u
poradie odvodených otázok
definitívnu identitu DOMAIN_TERM
Autoritu prijatia kandidáta
úplnú Validáciu kandidáta
rozsah opakovateľnej použiteľnosti prijatej otázky
odvodzovanie otázok Z a T do zloženého vzťahu S
dedukciu odpovedí
výber dôkazov
vykonanie hodnotenia
Autoritu Validácie hodnotenia
technickú implementáciu
```

Najbližším logickým krokom je spoločné metodické preskúmanie tohto algoritmu a ontológie ako jedného významového celku. Až po ich prijatí možno odvodiť aplikačný kontrakt prvého doménového algoritmu v CodeIgniteri.