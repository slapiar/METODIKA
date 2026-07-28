# Aplikačný kontrakt prvého doménového algoritmu odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument odvodzuje aplikačný kontrakt z pracovnej ontológie, opraveného algoritmu a spoločnej reValidácie:

```text
postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md
+
postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md
+
postupy/2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md
```

Neurčuje PHP triedy, databázové tabuľky, HTTP route ani používateľské rozhranie. Určuje významovú hranicu aplikačnej operácie, topológiu odozvy jedného behu a povinné väzby, ktoré musí budúca služba zachovať.

---

# 1. Predmet kontraktu

```text
DERIVATION_APPLICATION_INPUT
→ QUESTION_DERIVATION
→ DERIVATION_RUN_RESULT
```

Každý pokus musí byť historicky zachytený. Spoločné vstupy sa musia overiť pred vetvením. Každá kandidátska vetva musí mať vlastný výsledok a auditnú stopu. Výsledok celého behu sa musí dať bez domýšľania spojiť so zdrojovou požiadavkou a cieľom návratu.

---

# 2. Režim behu

```text
RUN_MODE
=
PARTIAL_RUN_WITH_ATOMIC_GATE
```

```text
ATOMIC_INPUT_GATE
=
spoločné podmienky, bez ktorých nesmie vzniknúť žiadna kandidátska vetva
```

```text
PARTIAL_CANDIDATE_PROCESSING
=
po prechode bránou sa každá samostatná kandidátska vetva spracúva a ukončuje osobitne
```

Platí:

```text
zlyhanie spoločného vstupu
→ celý beh sa vráti k zdroju bez kandidátov
```

```text
zlyhanie jednej nezávislej vetvy
→ vetva sa vráti s vlastným výsledkom
→ úplne vytvorené kandidáty zostávajú zachované
→ ostatné nezávislé vetvy môžu pokračovať
```

---

# 3. Zdroj, požiadavka a cieľ návratu

```text
REQUEST_SOURCE
≠ ACTOR
≠ AUTHORITY
```

`REQUEST_SOURCE` je technický alebo aplikačný zdroj operácie. Môže ním byť používateľské rozhranie, API, CLI alebo nadradený proces. Metodický ACTOR sa určuje osobitne.

Každý vstup musí obsahovať:

```text
REQUEST_REFERENCE
=
jednoznačný odkaz na konkrétnu predloženú aplikačnú požiadavku
```

```text
RESPONSE_TARGET_REFERENCE
=
jednoznačný odkaz na zdroj alebo nadradený proces, ktorému sa má vrátiť výsledok
```

Musí byť zachovaná korelačná cesta:

```text
REQUEST_REFERENCE
→ QUESTION_DERIVATION
→ DERIVATION_RUN_RESULT
→ RESPONSE_TARGET_REFERENCE
```

`REQUEST_REFERENCE` nepreukazuje identitu ACTOR-a ani jeho Autoritu. Slúži iba na jednoznačné priradenie požiadavky, behu a odpovede.

---

# 4. Atómová vstupná brána

Pred vytvorením vetiev sa overuje najmenej:

```text
ACTOR_REFERENCE
AUTHORITY_CONTEXT
SOURCE_QUESTION_REFERENCE
DERIVATION_SUBJECT_REFERENCE
DERIVATION_PURPOSE
DERIVATION_CONTEXT
DERIVATION_SCOPE
DOMAIN_TERM_REFERENCES
UNIVERSAL_CONDITION
REQUEST_REFERENCE
RESPONSE_TARGET_REFERENCE
```

Ak zlyhá ktorákoľvek kontrola:

```text
run_state = STOPPED_AT_GATE
candidates = []
branch_results = []
```

Výsledok musí obsahovať `derivation`, `request_reference`, `response_target_reference`, `run_state`, `stop_reason`, `failed_control` a `trace`.

Atómová brána nepotvrdzuje Autoritu ako platnú. Potvrdzuje iba, že jej kontext a stav sú zachytené.

---

# 5. Kandidátska vetva

Po úspešnom prechode bránou vzniká pre každý samostatný `SUBJECT_MANIFESTATION` jedna vetva:

```text
CANDIDATE_BRANCH
{
    branch_reference,
    derivation_reference,
    subject_manifestation,
    dependencies: list<BRANCH_DEPENDENCY>,
    branch_state,
    branch_trace
}
```

Vetva vykonáva najmenej:

```text
špecializáciu univerzálnej podmienky
→ kontrolu elementárnosti
→ kontrolu primárneho rozmeru
→ formuláciu kandidáta
→ význam odpovedí 1 a 0
→ REQUIRED_QUESTION_CONTEXT
→ INTENDED_APPLICABILITY_SCOPE
→ kontrolu neutrality a spätnej odvoditeľnosti
```

Každá vetva sa ukončí osobitným výsledkom.

---

# 6. Vetvová závislosť

Závislosť nesmie vzniknúť voľným technickým odhadom. Musí byť významovo určená a spätne citovateľná.

```text
BRANCH_DEPENDENCY
{
    dependency_reference,
    dependent_branch_reference,
    prerequisite_reference,
    dependency_type,
    justification,
    determined_by_reference,
    validation_control_reference,
    trace
}
```

Význam polí:

```text
dependent_branch_reference
= vetva, ktorej pokračovanie závisí od podmienky

prerequisite_reference
= konkrétna vetva, výsledok, podmienka alebo rozklad, ktorý musí byť úspešne dokončený

dependency_type
= významová, poradová alebo metodická závislosť

justification
= dôvod, prečo bez predpokladu nemožno bezpečne pokračovať

determined_by_reference
= ACTOR alebo metodický úkon, ktorý závislosť určil

validation_control_reference
= kontrola, podľa ktorej sa závislosť potvrdila alebo vetva zablokovala
```

Technická závislosť infraštruktúry sama osebe nie je `BRANCH_DEPENDENCY`, pokiaľ nemení význam alebo prípustnosť metodickej vetvy.

Vetva sa označí:

```text
BLOCKED_BY_DEPENDENCY
```

iba ak existuje citovateľný `BRANCH_DEPENDENCY` a jeho `prerequisite_reference` neskončil stavom, ktorý povoľuje pokračovanie.

---

# 7. Pokračovanie medzi vetvami

```text
nezávislá ďalšia vetva
→ pokračovať
```

```text
vetva so splnenými predpokladmi
→ pokračovať
```

```text
vetva s nesplneným citovateľným predpokladom
→ nepokračovať
→ CANDIDATE_BRANCH_RESULT.state = BLOCKED_BY_DEPENDENCY
```

Zlyhanie jednej vetvy nesmie automaticky vytvoriť závislosť ostatných vetiev.

---

# 8. Vstupný kontrakt

```text
DERIVATION_APPLICATION_INPUT
{
    request_reference,
    request_source,
    response_target_reference,
    source_question: SOURCE_QUESTION_REFERENCE,
    derivation_subject: DERIVATION_SUBJECT_REFERENCE,
    purpose: DERIVATION_PURPOSE,
    context: DERIVATION_CONTEXT,
    scope: DERIVATION_SCOPE,
    domain_terms: set<DOMAIN_TERM_REFERENCE>,
    actor: ACTOR_REFERENCE,
    authority_context: AUTHORITY_CONTEXT,
    run_mode: PARTIAL_RUN_WITH_ATOMIC_GATE
}
```

`run_mode` je pevne určený. Nie je používateľskou voľbou ani voľným technickým nastavením.

---

# 9. Výstupný kontrakt

```text
DERIVATION_RUN_RESULT
{
    request_reference,
    response_target_reference,
    derivation: QUESTION_DERIVATION,
    run_mode: PARTIAL_RUN_WITH_ATOMIC_GATE,
    run_state,
    candidates: list<QUESTION_CANDIDATE>,
    branch_results: list<CANDIDATE_BRANCH_RESULT>,
    trace: DERIVATION_TRACE,
    stop_reason?,
    failed_control?,
    summary
}
```

```text
CANDIDATE_BRANCH_RESULT
{
    branch_reference,
    subject_manifestation,
    state,
    candidate?,
    stop_reason?,
    failed_control?,
    dependencies: list<BRANCH_DEPENDENCY>,
    trace
}
```

Vetvové stavy sú pracovné:

```text
CANDIDATE_CREATED
BRANCH_STOPPED
RETURNED_FOR_DECOMPOSITION
BLOCKED_BY_DEPENDENCY
```

---

# 10. Deterministická agregácia run_state

Nadradený stav sa odvodzuje iba z výsledku brány a úplnej množiny vetvových výsledkov.

## 10.1 Priorita brány

```text
ak vstupná brána zlyhala
→ run_state = STOPPED_AT_GATE
```

Vetvové stavy sa v tomto prípade nevytvárajú.

## 10.2 Agregácia po úspešnej bráne

Nech:

```text
C = počet CANDIDATE_CREATED
S = počet BRANCH_STOPPED
D = počet RETURNED_FOR_DECOMPOSITION
B = počet BLOCKED_BY_DEPENDENCY
N = celkový počet vetiev
```

Potom platí presne v tomto poradí:

```text
ak N = 0
→ run_state = COMPLETED_NO_BRANCHES
```

```text
ak C = N
→ run_state = COMPLETED_ALL_BRANCHES
```

```text
ak C > 0 a (S + D + B) > 0
→ run_state = COMPLETED_WITH_BRANCH_LIMITATIONS
```

```text
ak C = 0 a D > 0 a S = 0 a B = 0
→ run_state = RETURNED_FOR_DECOMPOSITION
```

```text
ak C = 0 a B > 0 a S = 0 a D = 0
→ run_state = BLOCKED_BY_DEPENDENCIES
```

```text
ak C = 0 a S > 0 a D = 0 a B = 0
→ run_state = COMPLETED_WITH_BRANCH_FAILURES
```

```text
ak C = 0 a najmenej dva z množín S, D, B sú nenulové
→ run_state = COMPLETED_WITH_MIXED_BRANCH_RESULTS
```

Každý beh musí dostať práve jeden nadradený stav. Súhrnný stav nesmie vymazať ani nahradiť jednotlivé vetvové výsledky.

---

# 11. Zachovanie výsledkov

Pri zlyhaní jednej vetvy sa zachová:

```text
historický záznam behu
úspešne vytvorené kandidáty
všetky ukončené vetvové výsledky
auditná stopa zlyhanej vetvy
vetvové závislosti a ich odôvodnenie
väzba na REQUEST_REFERENCE a RESPONSE_TARGET_REFERENCE
```

Neúplná, zlyhaná alebo zablokovaná vetva sa nesmie uložiť ako `QUESTION_CANDIDATE`.

```text
zlyhanie vetvy
≠ odpoveď 0
≠ neexistencia SUBJECT-u
≠ neplatnosť ostatných kandidátov
```

---

# 12. Sumárna odozva zdroju

Zdroj operácie musí dostať výsledok, ktorý rozlišuje:

```text
ktorá požiadavka bola spracovaná
kam sa výsledok vracia
čo prešlo vstupnou bránou
čo bolo odmietnuté
koľko vetiev vzniklo
ktoré vetvy vytvorili kandidáta
ktoré zlyhali alebo sa majú rozložiť
ktoré boli zablokované a na čom záviseli
čo môže pokračovať
aký je deterministický stav celého behu
```

Kontrakt nevracia iba technické `success/error`. Vracia topológiu metodického výsledku.

---

# 13. Hranica aplikačnej služby

Služba smie prijať vstup, založiť historický pokus, koordinovať bránu a vetvy, vyhodnotiť závislosti podľa kontraktu, agregovať `run_state` a vrátiť `DERIVATION_RUN_RESULT`.

Nesmie automaticky prijať kandidáta ako platnú otázku, vykonať hodnotenie, dedukovať odpoveď, domyslieť Autoritu, zameniť neúspech vetvy za odpoveď ani meniť režim behu bez nového metodického rozhodnutia.

---

# 14. Metodická a databázová transakcia

```text
metodická transakčná hranica
≠ databázová transakcia
```

Budúca implementácia môže používať krátke technické transakcie pre založenie behu, ukončenie vetvy, zápis kandidáta s auditnou stopou a finálny súhrn. Databázový rollback nesmie vymazať historicky platné výsledky už ukončených nezávislých vetiev pre zlyhanie neskoršej vetvy.

---

# 15. Kritériá pripravenosti

Bez domýšľania musí byť určiteľné:

```text
čo tvorí jeden beh
čo je atómová vstupná brána
ako sa koreluje požiadavka, beh, výsledok a cieľ návratu
kedy vzniká vetva
ako vzniká a Validuje sa vetvová závislosť
kedy môže pokračovať ďalšia vetva
čo sa zachová pri zlyhaní
čo nesmie byť kandidátom
ako sa deterministicky odvodí run_state
ako sa odlišuje metodická a databázová transakcia
```

---

# 16. Otvorené technické rozhodnutia

Kontrakt ešte neurčuje názvy PHP tried a metód, DTO, repository rozhrania, SQL schému, migrácie, HTTP route, status kódy, JSON formát, databázovú transakčnú implementáciu, asynchrónnosť ani paralelizáciu.

Tieto rozhodnutia nesmú meniť jeho významové väzby ani topológiu odozvy.

---

# 17. Nasledujúci logický krok

```text
vykonať reValidáciu aplikačného kontraktu
→ pri VALID alebo VALID_WITH_LIMITATIONS odvodiť technický návrh aplikačnej služby
→ až potom navrhnúť repository, migrácie, controller a API odpoveď
```
