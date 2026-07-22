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

Neurčuje ešte PHP triedy, databázové tabuľky, HTTP route ani používateľské rozhranie. Určuje významovú hranicu aplikačnej operácie, topológiu odozvy jedného behu a povinné vstupy a výstupy, ktoré musí budúca služba zachovať.

---

# 1. Predmet kontraktu

Aplikačná operácia vykonáva jeden metodický úkon odvodzovania:

```text
DERIVATION_INPUT
→ QUESTION_DERIVATION
→ DERIVATION_RESULT
```

Kontrakt musí zabezpečiť, aby:

```text
- každý pokus bol historicky zachytený,
- spoločné vstupné podmienky boli overené pred vetvením,
- každý kandidát vznikal vo vlastnej preskúmateľnej vetve,
- zlyhanie jednej vetvy nevymazalo platné výsledky iných vetiev,
- každý výsledok sa vrátil k zdroju spolu s dôvodom a auditnou stopou.
```

---

# 2. Rozhodnutie transakčnej hranice

Pre prvý doménový algoritmus sa prijíma:

```text
RUN_MODE
=
PARTIAL_RUN_WITH_ATOMIC_GATE
```

Ide o `PARTIAL RUN` s jednou spoločnou atómovou vstupnou bránou.

```text
ATOMIC INPUT GATE
=
spoločné podmienky, bez ktorých nesmie vzniknúť žiadna kandidátska vetva
```

```text
PARTIAL CANDIDATE PROCESSING
=
po úspešnom prechode vstupnou bránou sa každá samostatná kandidátska vetva spracúva a ukončuje osobitne
```

Toto rozhodnutie znamená:

```text
zlyhanie spoločného vstupu
→ celý beh sa vráti k zdroju bez kandidátov
```

```text
zlyhanie jednej kandidátskej vetvy
→ táto vetva sa vráti k zdroju s vlastným výsledkom
→ úplne vytvorené kandidáty zostávajú zachované
→ ostatné nezávislé vetvy môžu pokračovať
```

---

# 3. Dôvod voľby PARTIAL RUN

Jedna univerzálna podmienka môže zodpovedať viacerým samostatným prejavom `DERIVATION_SUBJECT-u`.

```text
jedna SOURCE QUESTION
→ viac SUBJECT_MANIFESTATION
→ viac nezávislých kandidátskych vetiev
```

Ak jedna vetva zlyhá pre neelementárnosť, nejasný rozsah použiteľnosti alebo zmenu primárneho rozmeru, nevzniká tým automaticky dôvod zneplatniť kandidáta odvodeného z iného samostatného prejavu.

Úplne atómový beh by spôsobil:

```text
jedna chybná vetva
→ strata všetkých už korektne odvodených kandidátov
```

To by miešalo výsledok jednej vetvy s výsledkom celého metodického úkonu a skrývalo by, ktorá časť skutočne zlyhala.

`PARTIAL_RUN_WITH_ATOMIC_GATE` zachováva:

```text
spoločnú integritu vstupov
+
nezávislosť kandidátskych vetiev
+
spätnú preskúmateľnosť každého výsledku
```

---

# 4. Topológia odozvy

## 4.1 Zdroj behu

Zdrojom aplikačnej operácie je ACTOR alebo nadradený aplikačný proces, ktorý predložil `DERIVATION_INPUT`.

```text
REQUEST_SOURCE
≠ automaticky ACTOR
```

Technický žiadateľ môže byť používateľské rozhranie, API, CLI alebo iný proces. Metodický ACTOR musí byť určený osobitne.

## 4.2 Spoločná odozva behu

Každý beh vracia jeden nadradený výsledok:

```text
DERIVATION_RUN_RESULT
```

Ten sa vždy vracia zdroju operácie a obsahuje stav celého behu.

## 4.3 Odozva kandidátskej vetvy

Každá vetva vracia:

```text
CANDIDATE_BRANCH_RESULT
```

Vetva môže skončiť:

```text
CANDIDATE_CREATED
BRANCH_STOPPED
RETURNED_FOR_DECOMPOSITION
```

Výsledok vetvy sa pripojí k nadradenému výsledku behu. Neúspešná vetva sa nepresúva automaticky na ďalšie spracovanie ako kandidát.

## 4.4 Pokračovanie k ďalšiemu kandidátovi

Po ukončení jednej vetvy platí:

```text
ak existuje ďalší nezávislý SUBJECT_MANIFESTATION
→ pokračovať ďalšou vetvou
```

```text
ak ďalšia vetva závisí od neúspešného výsledku predchádzajúcej vetvy
→ nepokračovať
→ vrátiť závislosť k zdroju ako BLOCKED_BY_DEPENDENCY
```

Tým sa odlišuje:

```text
nezávislý ďalší kandidát
≠ následný krok závislý od neúspešného kandidáta
```

---

# 5. Atómová vstupná brána

Pred vytvorením kandidátskych vetiev musí prejsť spoločná kontrola:

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
```

Ak zlyhá ktorákoľvek z týchto kontrol:

```text
RUN_STATE = STOPPED_AT_GATE
candidates = []
branch_results = []
```

Výsledok musí obsahovať:

```text
derivation
run_state
stop_reason
failed_control
trace
```

Atómová brána nepotvrdzuje Autoritu ako platnú. Potvrdzuje iba, že jej kontext a stav sú zachytené tak, aby bolo možné úkon preskúmať.

---

# 6. Kandidátska vetva

Po úspešnom prechode bránou vzniká pre každý samostatný `SUBJECT_MANIFESTATION` jedna vetva:

```text
CANDIDATE_BRANCH
{
    branch_id,
    derivation_reference,
    subject_manifestation,
    branch_dependencies,
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
→ určenie REQUIRED_QUESTION_CONTEXT
→ určenie INTENDED_APPLICABILITY_SCOPE
→ kontrolu neutrality a spätnej odvoditeľnosti
```

Každá vetva musí byť ukončená osobitným výsledkom bez ohľadu na úspech alebo neúspech.

---

# 7. Vstupný kontrakt

```text
DERIVATION_APPLICATION_INPUT
{
    request_source,
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

`run_mode` je v prvom kontrakte pevne určený. Nie je používateľskou voľbou ani voľným technickým nastavením.

Budúca zmena režimu musí byť novým metodickým rozhodnutím, nie zmenou konfigurácie bez Validácie.

---

# 8. Výstupný kontrakt

```text
DERIVATION_RUN_RESULT
{
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

Každý vetvový výsledok má tvar:

```text
CANDIDATE_BRANCH_RESULT
{
    branch_id,
    subject_manifestation,
    state,
    candidate?,
    stop_reason?,
    failed_control?,
    dependencies,
    trace
}
```

Nadradený `run_state` môže byť pracovný:

```text
STOPPED_AT_GATE
COMPLETED_ALL_BRANCHES
COMPLETED_WITH_BRANCH_FAILURES
RETURNED_FOR_DECOMPOSITION
```

Tieto označenia zatiaľ netvoria potvrdený globálny číselník METODIKY.

---

# 9. Pravidlá zachovania výsledkov

## 9.1 Zachová sa

Pri zlyhaní jednej kandidátskej vetvy sa zachová:

```text
- historický záznam celého behu,
- úspešne ukončené kandidáty,
- výsledky všetkých už ukončených vetiev,
- auditná stopa zlyhanej vetvy,
- dôvod zastavenia a neúspešná kontrola,
- väzba výsledku na zdrojový úkon.
```

## 9.2 Nezachová sa ako kandidát

Neúplná alebo zlyhaná vetva sa nesmie zaradiť medzi `QUESTION_CANDIDATE`.

Môže byť zachovaná iba ako:

```text
CANDIDATE_BRANCH_RESULT
```

so stavom a auditnou stopou.

## 9.3 Nesmie sa domyslieť

```text
zlyhanie vetvy
≠ odpoveď 0
≠ neexistencia SUBJECT-u
≠ neplatnosť ostatných kandidátov
```

---

# 10. Sumárna odozva zdroju

Zdroj operácie musí dostať výsledok, ktorý rozlišuje:

```text
čo bolo prijaté ako spoločný vstup
čo bolo odmietnuté na vstupnej bráne
koľko kandidátskych vetiev vzniklo
ktoré vetvy vytvorili kandidáta
ktoré vetvy zlyhali
prečo zlyhali
ktoré vetvy boli zablokované závislosťou
čo môže pokračovať
čo sa musí vrátiť na doplnenie alebo rozklad
```

Kontrakt teda nevracia iba technické `success/error`. Vracia topológiu výsledku metodického behu.

---

# 11. Hranica aplikačnej služby

Budúca aplikačná služba smie:

```text
- prijať aplikačný vstup,
- založiť historický pokus,
- koordinovať atómovú vstupnú bránu,
- vytvoriť a koordinovať nezávislé kandidátske vetvy,
- zhromaždiť výsledky vetiev,
- vrátiť DERIVATION_RUN_RESULT.
```

Nesmie:

```text
- automaticky prijať kandidáta ako platnú otázku,
- vykonať budúce hodnotenie otázky,
- dedukovať odpoveď 1 alebo 0,
- domyslieť Autoritu,
- zameniť neúspech vetvy za metodickú odpoveď,
- meniť režim PARTIAL RUN bez nového metodického rozhodnutia.
```

---

# 12. Hranica technickej transakcie

Metodický `PARTIAL RUN` neznamená, že databázové zápisy majú prebiehať bez technických transakcií.

Treba rozlíšiť:

```text
metodická transakčná hranica
≠ databázová transakcia
```

Budúca implementácia môže používať krátke technické transakcie pre konzistentný zápis:

```text
- založenia behu,
- ukončenia jednej vetvy,
- zápisu kandidáta spolu s jeho auditnou stopou,
- finálneho súhrnu behu.
```

Nesmie však jednou databázovou rollback operáciou vymazať historicky platné výsledky už ukončených nezávislých vetiev iba preto, že neskoršia vetva zlyhala.

---

# 13. Kritériá úspechu kontraktu

Aplikačný kontrakt je pripravený na technický návrh, ak možno bez domýšľania určiť:

```text
1. čo tvorí jeden beh,
2. čo je spoločná atómová vstupná brána,
3. kedy vzniká kandidátska vetva,
4. kedy sa celý beh vracia k zdroju,
5. kedy môže pokračovať ďalší nezávislý kandidát,
6. čo sa zachová pri zlyhaní jednej vetvy,
7. čo nesmie byť uložené ako kandidát,
8. aký nadradený výsledok sa vracia zdroju,
9. ako sa odlišuje metodická a databázová transakčná hranica.
```

---

# 14. Otvorené technické rozhodnutia

Tento kontrakt ešte neurčuje:

```text
názov PHP služby a jej metód
DTO alebo Value Object triedy
repository rozhrania
SQL schému a migrácie
HTTP route a status kódy
formát JSON odpovede
konkrétnu stratégiu databázových transakcií
asynchrónne spracovanie
paralelizáciu kandidátskych vetiev
```

Tieto rozhodnutia sa môžu odvodiť až z tohto kontraktu a nesmú meniť jeho topológiu odozvy.

---

# 15. Nasledujúci logický krok

```text
Validovať aplikačný kontrakt proti ontológii, algoritmu a reValidácii
→ po úspešnej Validácii odvodiť technický návrh aplikačnej služby
→ až potom navrhnúť repository, migrácie, controller a API odpoveď v CodeIgniteri
```
