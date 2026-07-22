# Technický model uloženia odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument odvodzuje technický model uloženia z Validovaného spoločného základu:

```text
TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md
+
TECHNICKE-NAVRHY/2026-07-22_POLITIKA-OPAKOVANEJ-REQUEST-REFERENCE.md
+
TECHNICKE-NAVRHY/2026-07-22_REPOSITORY-KONTRAKT-REQUEST-REFERENCE.md
+
TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-REPOSITORY-KONTRAKTU-REQUEST-REFERENCE.md
```

Neurčuje ešte:

```text
SQL syntax
názvy databázových tabuliek
konkrétne dátové typy
indexy a cudzie kľúče
CodeIgniter Models
repository adaptéry
migrácie
HTTP alebo JSON rozhranie
```

Technický model nesmie meniť význam doménových objektov ani Validovaný režim `PARTIAL_RUN_WITH_ATOMIC_GATE`.

---

# 1. Účel modelu

Model musí umožniť zachovať:

```text
jednu konkrétnu aplikačnú požiadavku
→ najviac jeden QUESTION_DERIVATION
→ nula alebo viac kandidátskych vetiev
→ samostatný výsledok každej vetvy
→ nula alebo viac úplných QUESTION_CANDIDATE
→ práve jeden finálny DERIVATION_RUN_RESULT
```

Zároveň musí umožniť:

```text
replay ukončeného behu
rozpoznanie rozpracovaného behu
konflikt REQUEST_REFERENCE
čiastkové potvrdenie nezávislých vetiev
spätnú auditnú rekonštrukciu
```

---

# 2. Základné záznamové skupiny

Technický model používa tieto pracovné skupiny:

```text
REQUEST_REFERENCE_RESERVATION_RECORD
DERIVATION_RUN_RECORD
DERIVATION_BRANCH_RECORD
BRANCH_DEPENDENCY_RECORD
QUESTION_CANDIDATE_RECORD
DERIVATION_RUN_RESULT_RECORD
DERIVATION_TRACE_RECORD
```

Sú to technické záznamové tvary. Nie sú novou ontológiou METODIKY.

---

# 3. REQUEST_REFERENCE_RESERVATION_RECORD

```text
REQUEST_REFERENCE_RESERVATION_RECORD
{
    request_reference,
    payload_fingerprint,
    derivation_reference,
    reservation_state,
    reserved_at,
    updated_at
}
```

Invarianty:

```text
jedna request_reference
→ najviac jeden záznam rezervácie

jedna request_reference
→ práve jedna nemenná derivation_reference po potvrdení

payload_fingerprint
→ po potvrdení nemenný
```

`reservation_state` je technický stav korelácie:

```text
RESERVED
RUNNING
COMPLETED
```

Nie je to metodický `run_state`.

---

# 4. DERIVATION_RUN_RECORD

```text
DERIVATION_RUN_RECORD
{
    derivation_reference,
    request_reference,
    response_target_reference,
    request_source,
    source_question_reference,
    derivation_subject_reference,
    purpose,
    context,
    scope,
    domain_term_references,
    actor_reference,
    authority_context,
    run_mode,
    run_state?,
    gate_state?,
    stop_reason?,
    failed_control?,
    started_at,
    completed_at?
}
```

Tento záznam technicky reprezentuje jeden historický pokus `QUESTION_DERIVATION`.

Platí:

```text
REQUEST_REFERENCE_RESERVATION_RECORD
1 ↔ 1
DERIVATION_RUN_RECORD
```

v rozsahu politiky `IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE`.

`run_mode` musí byť:

```text
PARTIAL_RUN_WITH_ATOMIC_GATE
```

`gate_state` a `run_state` sa nesmú zlúčiť do jedného poľa významu. Atómová brána je krok behu; `run_state` je výsledný nadradený stav.

Doménové vstupy sa musia uchovať v rozsahu potrebnom na spätnú rekonštrukciu toho, čo bolo predložené. Technické uloženie nesmie spätne meniť ich obsah.

---

# 5. DERIVATION_BRANCH_RECORD

```text
DERIVATION_BRANCH_RECORD
{
    branch_reference,
    derivation_reference,
    subject_manifestation,
    branch_state,
    stop_reason?,
    failed_control?,
    created_at,
    completed_at?
}
```

Kardinalita:

```text
DERIVATION_RUN_RECORD
1 → 0..N
DERIVATION_BRANCH_RECORD
```

Pri `STOPPED_AT_GATE` platí:

```text
počet vetiev = 0
```

Každá vetva má práve jeden konečný vetvový stav z Validovaného kontraktu:

```text
CANDIDATE_CREATED
BRANCH_STOPPED
RETURNED_FOR_DECOMPOSITION
BLOCKED_BY_DEPENDENCY
```

Vetva sa nesmie technicky prepísať na kandidáta. Kandidát je samostatný záznam, ktorý môže vzniknúť iba pri `CANDIDATE_CREATED`.

---

# 6. BRANCH_DEPENDENCY_RECORD

```text
BRANCH_DEPENDENCY_RECORD
{
    dependency_reference,
    dependent_branch_reference,
    prerequisite_reference,
    dependency_type,
    justification,
    determined_by_reference,
    validation_control_reference,
    created_at
}
```

Kardinalita:

```text
DERIVATION_BRANCH_RECORD
1 → 0..N
BRANCH_DEPENDENCY_RECORD
```

Závislosť musí zostať spätne citovateľná. Technický model ju nesmie redukovať iba na dvojicu identifikátorov.

Platí:

```text
BLOCKED_BY_DEPENDENCY
→ najmenej jedna citovateľná BRANCH_DEPENDENCY_RECORD
```

Zlyhanie jednej vetvy samo osebe nesmie vytvoriť záznam závislosti pre inú vetvu.

---

# 7. QUESTION_CANDIDATE_RECORD

```text
QUESTION_CANDIDATE_RECORD
{
    candidate_reference,
    branch_reference,
    derivation_reference,
    candidate_content,
    primary_dimension,
    meaning_of_one,
    meaning_of_zero,
    required_question_context,
    intended_applicability_scope,
    created_at
}
```

Kardinalita:

```text
DERIVATION_BRANCH_RECORD
1 → 0..1
QUESTION_CANDIDATE_RECORD
```

Platí:

```text
branch_state = CANDIDATE_CREATED
↔
práve jeden úplný QUESTION_CANDIDATE_RECORD
```

A zároveň:

```text
BRANCH_STOPPED
RETURNED_FOR_DECOMPOSITION
BLOCKED_BY_DEPENDENCY
→ žiadny QUESTION_CANDIDATE_RECORD
```

Kandidát a vetvový výsledok musia byť potvrdené v jednej krátkej technickej transakcii. Nesmie vzniknúť kandidát bez svojej vetvy ani vetva `CANDIDATE_CREATED` bez úplného kandidáta.

`candidate_content` je technická reprezentácia formulovaného kandidáta. Neudeľuje mu autoritatívny stav ani Validáciu.

---

# 8. DERIVATION_RUN_RESULT_RECORD

```text
DERIVATION_RUN_RESULT_RECORD
{
    result_reference,
    derivation_reference,
    request_reference,
    response_target_reference,
    run_mode,
    run_state,
    candidate_count,
    stopped_branch_count,
    decomposition_branch_count,
    blocked_branch_count,
    total_branch_count,
    stop_reason?,
    failed_control?,
    summary,
    completed_at
}
```

Kardinalita:

```text
DERIVATION_RUN_RECORD
1 → 0..1 rozpracovaný beh
1 → práve 1 ukončený beh
DERIVATION_RUN_RESULT_RECORD
```

Finálny záznam výsledku vznikne až po deterministickej agregácii. Repository ani databázový mechanizmus nesmú `run_state` odvodiť samostatne.

Počty vetvových výsledkov sú uloženým technickým obrazom agregácie. Musia byť overiteľné voči aktuálnym vetvovým záznamom:

```text
candidate_count
= počet CANDIDATE_CREATED

stopped_branch_count
= počet BRANCH_STOPPED

decomposition_branch_count
= počet RETURNED_FOR_DECOMPOSITION

blocked_branch_count
= počet BLOCKED_BY_DEPENDENCY

total_branch_count
= súčet všetkých vetvových výsledkov
```

Pri `STOPPED_AT_GATE` musia byť všetky počty vetiev nulové.

---

# 9. DERIVATION_TRACE_RECORD

```text
DERIVATION_TRACE_RECORD
{
    trace_reference,
    derivation_reference,
    branch_reference?,
    dependency_reference?,
    event_type,
    event_payload,
    occurred_at,
    sequence_reference
}
```

Auditná stopa musí umožniť rekonštruovať poradie technických a metodických udalostí bez toho, aby sa zamieňala s dôkazom alebo Validáciou.

Platí:

```text
DERIVATION_TRACE_RECORD
≠ dôkaz
≠ Validácia
≠ automaticky metodická skutočnosť
```

`sequence_reference` musí umožniť stabilné poradie udalostí jedného behu aj pri rovnakom čase zápisu.

---

# 10. Väzby modelu

```text
REQUEST_REFERENCE_RESERVATION_RECORD
1 ↔ 1
DERIVATION_RUN_RECORD

DERIVATION_RUN_RECORD
1 → 0..N
DERIVATION_BRANCH_RECORD

DERIVATION_BRANCH_RECORD
1 → 0..N
BRANCH_DEPENDENCY_RECORD

DERIVATION_BRANCH_RECORD
1 → 0..1
QUESTION_CANDIDATE_RECORD

DERIVATION_RUN_RECORD
1 → 0..1
DERIVATION_RUN_RESULT_RECORD

DERIVATION_RUN_RECORD
1 → 1..N
DERIVATION_TRACE_RECORD
```

`1..N` pri auditnej stope znamená, že už prvé prijatie musí zanechať najmenej záznam vzniku historického pokusu.

---

# 11. Transakčné hranice

## 11.1 Prvé prijatie

```text
REQUEST_REFERENCE_RESERVATION_RECORD
+
DERIVATION_RUN_RECORD
+
počiatočný DERIVATION_TRACE_RECORD
```

Vzniknú spolu alebo nevznikne nič.

## 11.2 Zlyhanie atómovej brány

```text
aktualizácia DERIVATION_RUN_RECORD
+
DERIVATION_RUN_RESULT_RECORD
+
DERIVATION_TRACE_RECORD
```

Nevznikne žiadna vetva ani kandidát.

## 11.3 Jedna vetva

```text
DERIVATION_BRANCH_RECORD
+
0..N BRANCH_DEPENDENCY_RECORD
+
0..1 QUESTION_CANDIDATE_RECORD
+
DERIVATION_TRACE_RECORD
```

Tvoria jednu krátku konzistentnú hranicu výsledku vetvy.

## 11.4 Ukončenie behu

```text
DERIVATION_RUN_RESULT_RECORD
+
aktualizácia DERIVATION_RUN_RECORD
+
aktualizácia REQUEST_REFERENCE_RESERVATION_RECORD
+
DERIVATION_TRACE_RECORD
```

Vzniknú po jedinej doménovej agregácii `run_state`.

Tieto hranice nesmú byť zlúčené do jednej transakcie celého `PARTIAL RUN`.

---

# 12. Nemennosť a opravy

Po potvrdení sa nesmú prepisovať:

```text
request_reference
payload_fingerprint
derivation_reference
branch_reference
dependency_reference
candidate_reference
result_reference
ukončený vetvový výsledok
ukončený DERIVATION_RUN_RESULT
```

Technická oprava nesmie potichu meniť historický význam. Ak bude potrebná korekcia, musí vzniknúť osobitná zaznamenaná technická alebo metodická udalosť podľa povahy zmeny.

Tento dokument zatiaľ neurčuje konkrétny model opravných udalostí.

---

# 13. Mazanie a životnosť

Model neumožňuje odvodiť automatické mazanie iba z technickej potreby uvoľniť priestor.

Zatiaľ platí:

```text
žiadna automatická expirácia
žiadne kaskádové mazanie
žiadne fyzické odstránenie ukončeného behu
```

kým nebude samostatne určená politika uchovávania, archivácie a prípadnej anonymizácie.

To neznamená potvrdenie neobmedzenej právnej alebo prevádzkovej retencie. Znamená iba, že tento návrh ju nesmie domyslieť.

---

# 14. Kontrolné invarianty modelu

```text
M1 — jedna REQUEST_REFERENCE má najviac jednu derivation_reference,
M2 — rezervácia bez DERIVATION_RUN_RECORD sa nesmie potvrdiť,
M3 — STOPPED_AT_GATE nemá vetvy ani kandidátov,
M4 — každá vetva patrí práve jednému behu,
M5 — CANDIDATE_CREATED má práve jedného úplného kandidáta,
M6 — ostatné vetvové stavy nemajú kandidáta,
M7 — BLOCKED_BY_DEPENDENCY má citovateľnú závislosť,
M8 — výsledok behu patrí tej istej požiadavke a derivation_reference,
M9 — finálny run_state vzniká iba jednou doménovou agregáciou,
M10 — počty vo výsledku sú overiteľné voči vetvám,
M11 — úspešná vetva zostáva zachovaná pri zlyhaní inej vetvy,
M12 — technické stavy rezervácie sa nezamieňajú s metodickými stavmi,
M13 — auditná stopa sa nezamieňa s dôkazom alebo Validáciou,
M14 — ukončené výsledky sa neprepisujú,
M15 — model nepredpisuje SQL implementáciu.
```

---

# 15. Čo model vedome neurčuje

```text
fyzické tabuľky
SQL dátové typy
primárne a cudzie kľúče
unikátne a pomocné indexy
názvy constraintov
CodeIgniter entity a modely
konkrétne repository adaptéry
formát event_payload
šifrovanie citlivých vstupov
retenciu a anonymizáciu
obnovu prerušeného behu
vzťahy retry_of, supersedes alebo follows
```

Tieto rozhodnutia musia byť odvodené alebo explicitne určené pred implementáciou.

---

# 16. Nasledujúci logický krok

```text
Validovať technický model uloženia
→ odvodiť databázový návrh a migračné obmedzenia
→ Validovať databázový návrh
→ až potom vytvoriť migrácie a repository adaptéry
```
