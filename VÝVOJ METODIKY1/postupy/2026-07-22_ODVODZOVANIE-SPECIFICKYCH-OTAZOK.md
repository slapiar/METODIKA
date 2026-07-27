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
≠ INTENDED_APPLICABILITY_SCOPE
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

Univerzálna otázka nesie všeobecnú skúmanú podmienku. Kandidát špecifickej otázky túto podmienku zachováva, ale viaže ju na presne určený predmet odvodenia, jeho relevantný prejav, význam, kontext a rozsah.

---

# 3. Minimálny vstupný kontrakt

Algoritmus prijíma surový vstup, ktorý ešte nemusí byť platný. Pokus o odvodzovací úkon sa musí historicky zaznamenať skôr, než sa platnosť jednotlivých vstupov potvrdí.

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

Každý vstup musí byť po kontrole buď jednoznačne identifikovaným odkazom na existujúcu identitu, alebo explicitne určenou vlastnosťou konkrétneho odvodzovacieho úkonu.

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

Predmet, z ktorého významu sa otázka odvodila, nemusí byť automaticky totožný s každým budúcim predmetom hodnotenia.

## 3.3 DERIVATION_PURPOSE

Účel určuje, aké poznanie má odvodená otázka umožniť. Nesmie obsahovať želanú odpoveď ani predurčiť výsledok.

## 3.4 DERIVATION_CONTEXT

Kontext určuje okolnosti a významové vzťahy, v ktorých sa odvodzovanie vykonáva.

## 3.5 DERIVATION_SCOPE

Rozsah určuje hranice konkrétneho odvodzovacieho úkonu:

```text
vecné hranice
časové hranice
priestorové hranice
procesné hranice
zahrnuté a vylúčené prejavy SUBJECT-u
```

Do textu kandidáta patrí iba `REQUIRED_QUESTION_CONTEXT`, bez ktorého by sa menil alebo strácal význam otázky.

## 3.6 DOMAIN_TERM_REFERENCES

Algoritmus používa spätne citovateľné odkazy na určené významy doménových pojmov. Neznámy alebo viacznačný význam sa nesmie doplniť odhadom.

## 3.7 ACTOR_REFERENCE

Každý odvodzovací úkon musí mať určiteľného `ACTOR-a`.

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

Každý ukončený pokus o odvodzovanie musí vytvoriť jednotný výsledok:

```text
DERIVATION_RESULT
{
    derivation: QUESTION_DERIVATION,
    candidates: list<QUESTION_CANDIDATE>,
    trace: DERIVATION_TRACE,
    state,
    stop_reason?,
    failed_control?
}
```

Pri úspechu je `stop_reason` a `failed_control` prázdny. Pri zastavení musia presne pomenovať dôvod a kontrolu, ktorá neprešla.

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
INTENDED_APPLICABILITY_SCOPE
DERIVATION_TRACE
STATE = CANDIDATE
```

```text
DERIVATION_SCOPE
=
hranice konkrétneho odvodzovacieho úkonu
```

```text
INTENDED_APPLICABILITY_SCOPE
=
predbežný rozsah predmetov, situácií, verzií alebo vzťahov,
pre ktoré môže byť kandidát navrhnutý na prijatie
```

`INTENDED_APPLICABILITY_SCOPE` nesmie automaticky určovať konečný rozsah prijatej otázky. Ten vzniká až pri samostatnom metodickom prijatí a Validácii kandidáta.

`DERIVATION_TRACE` je auditná a provenienčná stopa pôvodu kandidáta a celého odvodzovacieho úkonu. Nie je dôkazom budúcej odpovede na otázku.

Prípustné základné výsledky procesu sú pracovné:

```text
CANDIDATES_CREATED
STOPPED_INSUFFICIENT_INPUT
STOPPED_AMBIGUOUS_INPUT
STOPPED_NO_RELEVANT_MANIFESTATION
RETURNED_FOR_DECOMPOSITION
```

---

# 5. Minimálny odvodzovací algoritmus

## Krok 1 — Založenie pokusu o QUESTION_DERIVATION

Bezprostredne po prijatí surového vstupu vytvoriť historicky zachytiteľný záznam pokusu o odvodzovací úkon s časom, dostupnými vstupmi a počiatočným stavom:

```text
DERIVATION_ATTEMPT_RECORDED
```

Založenie pokusu nepotvrdzuje správnosť ani úplnosť vstupov.

```text
QUESTION_DERIVATION
≠ QUESTION_DERIVATION_RECORD
```

## Krok 2 — Kontrola ACTOR-a a kontextu Autority

Overiť, že je určený ACTOR a zachytený `AUTHORITY_CONTEXT`. Pri neúspechu ukončiť úkon jednotným `DERIVATION_RESULT`.

## Krok 3 — Kontrola zdrojovej QUESTION

Overiť identitu, významový stav, univerzálnu podmienku, primárny rozmer a autoritatívny pôvod zdrojovej otázky.

## Krok 4 — Kontrola DERIVATION_SUBJECT-u

Overiť určiteľnosť, rozlíšiteľnosť, rozsah, kontinuitu identity a výsledkovú neutralitu SUBJECT-u.

## Krok 5 — Kontrola účelu, kontextu a rozsahu

Overiť:

```text
účel je určený a neutrálny
kontext zachytáva významové okolnosti
rozsah určuje zahrnuté a vylúčené hranice
kontext a rozsah sa bez odôvodnenia neprekrývajú
```

## Krok 6 — Kontrola doménových významov

Overiť, že každý rozhodujúci doménový význam je určený a spätne citovateľný.

## Krok 7 — Extrakcia univerzálnej podmienky

Oddeliť gramatickú formu otázky od významu, ktorý má odpoveď `[1/0]` potvrdiť.

## Krok 8 — Určenie relevantného prejavu DERIVATION_SUBJECT-u

Vyhľadať časť, vlastnosť, vzťah, stav, proces alebo udalosť SUBJECT-u zodpovedajúcu univerzálnej podmienke.

Ak žiadny prejav nezodpovedá, ukončiť úkon s `NO_RELEVANT_MANIFESTATION`.

Ak zodpovedá viac samostatných prejavov, vytvoriť viac kandidátov alebo vrátiť podmienku na rozklad.

## Krok 9 — Doménové dosadenie

Nahradiť všeobecné výrazy konkrétnymi doménovo určenými významami bez zmeny logického významu.

## Krok 10 — Určenie jednej špecifickej podmienky

Zostaviť jednu podmienku, ktorej potvrdenie alebo nepotvrdenie možno jednoznačne rozlíšiť.

## Krok 11 — Kontrola primárneho rozmeru

Špecifikácia nesmie zmeniť primárny rozmer bez vzniku novej otázky a nového odôvodnenia.

## Krok 12 — Zostavenie vety kandidáta

Veta musí:

```text
skúmať jednu podmienku
vzťahovať sa na určený predmet alebo jeho prejav
zachovávať univerzálnu podmienku
nepredurčovať výsledok
umožňovať odpoveď [1/0]
obsahovať iba nevyhnutný REQUIRED_QUESTION_CONTEXT
```

## Krok 13 — Určenie významu odpovedí 1 a 0

Význam `1` a `0` musí byť jednoznačne viazaný na jednu špecifickú podmienku. Hodnota `0` nesmie automaticky znamenať absolútnu neexistenciu SUBJECT-u.

## Krok 14 — Kontrola elementárnosti a neutrality

Ak možno rozdielne časti vety potvrdiť rozdielne, kandidát sa musí rozdeliť. Otázka nesmie predurčovať želaný výsledok.

## Krok 15 — Určenie INTENDED_APPLICABILITY_SCOPE

Pre každý kandidát určiť predbežný rozsah predmetov, situácií, verzií alebo vzťahov, pre ktoré môže byť navrhnutý na prijatie.

Tento rozsah musí byť odvodený z významu kandidáta, jeho SUBJECT-u, univerzálnej podmienky a nevyhnutného kontextu. Nesmie byť automaticky skopírovaný z `DERIVATION_SCOPE`.

## Krok 16 — Kontrola spätnej odvoditeľnosti

Musí byť možné bez domýšľania odpovedať:

```text
ktorý ACTOR úkon vykonal
v akom kontexte Autority konal
z ktorej QUESTION kandidát vznikol
ktorú univerzálnu podmienku zachoval
ktorý DERIVATION_SUBJECT a prejav konkretizoval
ktoré doménové významy použil
aký kontext a rozsah boli rozhodujúce
ako vznikol INTENDED_APPLICABILITY_SCOPE
prečo zostal kandidát v rovnakom primárnom rozmere
ktoré kontroly prešli alebo neprešli
```

## Krok 17 — Ukončenie úkonu

Každý beh sa musí ukončiť jednotným `DERIVATION_RESULT`:

```text
úspech
→ CANDIDATES_CREATED

zastavenie
→ príslušný STOPPED_* alebo RETURNED_FOR_DECOMPOSITION
```

Žiadne zastavenie nesmie zostať bez `DERIVATION_TRACE`, `stop_reason` a `failed_control`.

---

# 6. Zastavovacie podmienky

Algoritmus sa musí zastaviť najmenej pri stavoch:

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
APPLICABILITY_SCOPE_UNDEFINED
DERIVATION_NOT_TRACEABLE
```

Každé zastavenie je historicky zachytiteľný metodický výsledok, nie strata záznamu ani technická výnimka bez významu.

---

# 7. Jednotný mechanizmus zastavenia

```text
FUNCTION stop_derivation(
    derivation,
    state,
    stop_reason,
    failed_control,
    trace_context
):
    trace = record_derivation_trace(
        derivation = derivation,
        result = state,
        stop_reason = stop_reason,
        failed_control = failed_control,
        context = trace_context
    )

    RETURN DERIVATION_RESULT(
        derivation = derivation,
        candidates = [],
        trace = trace,
        state = state,
        stop_reason = stop_reason,
        failed_control = failed_control
    )
```

Všetky neúspešné kontroly musia použiť tento významovo jednotný mechanizmus alebo jeho ekvivalent.

---

# 8. Pracovný pseudokód

```text
FUNCTION derive_specific_questions(raw_input):

    derivation = record_question_derivation_attempt(
        raw_input = raw_input,
        state = DERIVATION_ATTEMPT_RECORDED,
        time = now()
    )

    IF NOT identified(raw_input.actor):
        RETURN stop_derivation(
            derivation,
            STOPPED_INSUFFICIENT_INPUT,
            ACTOR_NOT_IDENTIFIED,
            ACTOR_CONTROL,
            raw_input
        )

    IF NOT authority_context_recorded(raw_input.authority_context):
        RETURN stop_derivation(
            derivation,
            STOPPED_INSUFFICIENT_INPUT,
            AUTHORITY_CONTEXT_MISSING,
            AUTHORITY_CONTEXT_CONTROL,
            raw_input
        )

    IF NOT identified(raw_input.source_question):
        RETURN stop_derivation(
            derivation,
            STOPPED_INSUFFICIENT_INPUT,
            SOURCE_QUESTION_NOT_IDENTIFIED,
            SOURCE_QUESTION_CONTROL,
            raw_input
        )

    IF NOT justified(raw_input.derivation_subject):
        RETURN stop_derivation(
            derivation,
            STOPPED_INSUFFICIENT_INPUT,
            SUBJECT_NOT_JUSTIFIED,
            SUBJECT_CONTROL,
            raw_input
        )

    IF NOT defined(raw_input.purpose):
        RETURN stop_derivation(
            derivation,
            STOPPED_INSUFFICIENT_INPUT,
            PURPOSE_NOT_DEFINED,
            PURPOSE_CONTROL,
            raw_input
        )

    IF NOT neutral(raw_input.purpose):
        RETURN stop_derivation(
            derivation,
            STOPPED_AMBIGUOUS_INPUT,
            PURPOSE_NOT_NEUTRAL,
            PURPOSE_NEUTRALITY_CONTROL,
            raw_input
        )

    IF NOT context_distinct_from_scope(raw_input.context, raw_input.scope):
        RETURN stop_derivation(
            derivation,
            STOPPED_AMBIGUOUS_INPUT,
            CONTEXT_AMBIGUOUS,
            CONTEXT_SCOPE_CONTROL,
            raw_input
        )

    IF NOT scope_unambiguous(raw_input.scope):
        RETURN stop_derivation(
            derivation,
            STOPPED_AMBIGUOUS_INPUT,
            SCOPE_AMBIGUOUS,
            SCOPE_CONTROL,
            raw_input
        )

    IF NOT domain_terms_defined(raw_input.domain_terms):
        RETURN stop_derivation(
            derivation,
            STOPPED_AMBIGUOUS_INPUT,
            DOMAIN_TERM_AMBIGUOUS,
            DOMAIN_TERM_CONTROL,
            raw_input
        )

    input = accept_validated_derivation_input(raw_input)
    universal_condition = extract_condition(input.source_question)

    manifestation_set = map_condition_to_subject(
        universal_condition,
        input.derivation_subject,
        input.context,
        input.scope
    )

    IF manifestation_set is empty:
        RETURN stop_derivation(
            derivation,
            STOPPED_NO_RELEVANT_MANIFESTATION,
            NO_RELEVANT_MANIFESTATION,
            SUBJECT_MANIFESTATION_CONTROL,
            input
        )

    candidates = []

    FOR manifestation IN manifestation_set:
        condition_set = specialize_and_decompose(
            universal_condition,
            manifestation,
            input.domain_terms,
            input.context,
            input.scope
        )

        FOR condition IN condition_set:
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

            IF NOT same_primary_dimension(input.source_question, candidate_text):
                RETURN stop_derivation(
                    derivation,
                    STOPPED_AMBIGUOUS_INPUT,
                    PRIMARY_DIMENSION_CHANGED,
                    PRIMARY_DIMENSION_CONTROL,
                    current_state()
                )

            IF NOT elementary(candidate_text):
                RETURN stop_derivation(
                    derivation,
                    RETURNED_FOR_DECOMPOSITION,
                    MULTIPLE_CONDITIONS_REQUIRE_DECOMPOSITION,
                    ELEMENTARITY_CONTROL,
                    current_state()
                )

            IF NOT result_neutral(candidate_text):
                RETURN stop_derivation(
                    derivation,
                    STOPPED_AMBIGUOUS_INPUT,
                    RESULT_NOT_NEUTRAL,
                    NEUTRALITY_CONTROL,
                    current_state()
                )

            IF NOT meaning_of_1_defined(condition) OR NOT meaning_of_0_defined(condition):
                RETURN stop_derivation(
                    derivation,
                    STOPPED_INSUFFICIENT_INPUT,
                    ANSWER_MEANING_UNDEFINED,
                    ANSWER_MEANING_CONTROL,
                    current_state()
                )

            applicability_scope = determine_intended_applicability_scope(
                candidate_text,
                input.derivation_subject,
                manifestation,
                universal_condition,
                required_context
            )

            IF NOT applicability_scope_defined(applicability_scope):
                RETURN stop_derivation(
                    derivation,
                    STOPPED_INSUFFICIENT_INPUT,
                    APPLICABILITY_SCOPE_UNDEFINED,
                    APPLICABILITY_SCOPE_CONTROL,
                    current_state()
                )

            candidate_trace = record_derivation_trace(
                derivation = derivation,
                manifestation = manifestation,
                condition = condition,
                required_context = required_context,
                intended_applicability_scope = applicability_scope,
                controls = current_controls()
            )

            IF NOT derivation_traceable(candidate_trace):
                RETURN stop_derivation(
                    derivation,
                    STOPPED_INSUFFICIENT_INPUT,
                    DERIVATION_NOT_TRACEABLE,
                    TRACEABILITY_CONTROL,
                    current_state()
                )

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
                    intended_applicability_scope = applicability_scope,
                    derivation_trace = candidate_trace,
                    state = CANDIDATE
                )
            )

    final_trace = record_derivation_trace(
        derivation = derivation,
        candidates = candidates,
        result = CANDIDATES_CREATED,
        controls = all_controls()
    )

    RETURN DERIVATION_RESULT(
        derivation = derivation,
        candidates = candidates,
        trace = final_trace,
        state = CANDIDATES_CREATED,
        stop_reason = null,
        failed_control = null
    )
```

---

# 9. Skúšobný príklad TermikaXC

```text
SOURCE_QUESTION_REFERENCE:
MM — Má skúmaný jav určiteľný pôvod, myšlienku, zámer alebo dôvod svojej existencie?

UNIVERSAL_CONDITION:
prejav má určiteľný pôvod alebo dôvod vzniku

DERIVATION_SUBJECT:
proces automatického načítania TEMP po kliknutí na mapu v určenej verzii TermikaXC

DERIVATION_PURPOSE:
zistiť, či technický proces vzniká z určiteľného spúšťacieho podnetu

DERIVATION_CONTEXT:
projekt TermikaXC; proces komunikácie mapy s načítaním TEMP

DERIVATION_SCOPE:
zahrnutý vznik požiadavky po kliknutí na mapu;
vylúčená odpoveď servera, správnosť údajov, zobrazenie TEMP a použiteľnosť výsledku

SUBJECT_MANIFESTATION:
vznik požiadavky na načítanie TEMP

QUESTION_CANDIDATE:
Spustí kliknutie na mapu požiadavku na načítanie TEMP pre zvolené súradnice?

INTENDED_APPLICABILITY_SCOPE:
kandidát je predbežne navrhnutý pre hodnotenie totožného typu spúšťacieho vzťahu
v presne určených verziách alebo stavoch TermikaXC, v ktorých význam pojmov
„kliknutie na mapu“, „zvolené súradnice“ a „požiadavka na načítanie TEMP“ zostáva totožný
```

Príklad nie je úplným vykonaným behom, kým nie sú konkrétne určené a zaznamenané `ACTOR_REFERENCE`, `AUTHORITY_CONTEXT` a autoritatívne odkazy doménových významov.

---

# 10. Minimálna Validácia kandidáta otázky

Pred prijatím kandidáta sa musí overiť najmenej:

```text
1. Je zdrojová QUESTION a jej významový stav jednoznačne určený?
2. Je DERIVATION_SUBJECT dostatočne vymedzený?
3. Je ACTOR jednoznačne určený?
4. Je zachytený kontext Autority ACTOR-a?
5. Je účel odvodzovania určený a neutrálny?
6. Sú DERIVATION_CONTEXT a DERIVATION_SCOPE odlíšené?
7. Zachovala sa univerzálna podmienka?
8. Skúma kandidát iba jednu podmienku?
9. Zostal zachovaný správny primárny rozmer?
10. Je význam odpovede 1 jednoznačný?
11. Je význam odpovede 0 jednoznačný?
12. Je kandidát neutrálny voči výsledku?
13. Sú doménové významy spätne citovateľné?
14. Je REQUIRED_QUESTION_CONTEXT iba nevyhnutný?
15. Je INTENDED_APPLICABILITY_SCOPE určený a odlíšený od DERIVATION_SCOPE?
16. Je celý postup spätne citovateľný v DERIVATION_TRACE?
17. Je DERIVATION_TRACE odlíšený od EVIDENCE budúceho hodnotenia?
18. Je kandidát odlíšený od prijatej otázky a budúceho hodnotenia?
19. Vytvorilo každé zastavenie jednotný DERIVATION_RESULT?
```

---

# 11. Hranica prvého algoritmu

Tento algoritmus zatiaľ nerieši:

```text
automatický výber najrelevantnejších univerzálnych otázok
úplnosť celého prieskumu SUBJECT-u
poradie odvodených otázok
definitívnu identitu DOMAIN_TERM
Autoritu prijatia kandidáta
úplnú Validáciu kandidáta
konečný rozsah opakovateľnej použiteľnosti prijatej otázky
odvodzovanie otázok Z a T do zloženého vzťahu S
dedukciu odpovedí
výber dôkazov
vykonanie hodnotenia
Autoritu Validácie hodnotenia
technickú implementáciu
```

Najbližším logickým krokom je spoločná reValidácia tohto algoritmu a ontológie ako jedného významového celku. Až pri výsledku `VALID` alebo `VALID_WITH_LIMITATIONS` možno odvodiť aplikačný kontrakt prvého doménového algoritmu.