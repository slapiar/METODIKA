# Databázový návrh a migračné obmedzenia odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument odvodzuje fyzický databázový návrh z Validovaného technického modelu:

```text
TECHNICKE-NAVRHY/2026-07-22_TECHNICKY-MODEL-ULOZENIA-ODVODZOVANIA-OTAZOK.md
+
TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-TECHNICKEHO-MODELU-ULOZENIA-ODVODZOVANIA-OTAZOK.md
```

Technický cieľ:

```text
CodeIgniter 4.7.4
+
MySQLi
+
transakčný MySQL/InnoDB profil
+
utf8mb4
```

Tento návrh ešte nevytvára migračné PHP súbory, repository adaptéry ani aplikačný kód.

Databázová schéma smie vynucovať technické invarianty, ale nesmie určovať Autoritu, vykonávať doménový algoritmus ani odvodzovať metodický `run_state`.

---

# 1. Zásady fyzického návrhu

```text
1. každá technická záznamová skupina má vlastnú tabuľku,
2. interné databázové identity sú oddelené od metodických referencií,
3. verejné a korelačné referencie sú po potvrdení nemenné,
4. historické výsledky sa neprepisujú ani kaskádovo nemažú,
5. jedna transakcia celého PARTIAL RUN je zakázaná,
6. databáza neagreguje run_state,
7. citlivé a štruktúrované vstupy sa ukladajú ako verné snapshoty,
8. návrh nesmie závisieť od neoverenej konkrétnej verzie servera.
```

Pre prenositeľnosť sa návrh nespolieha na:

```text
vynucovanie významu iba cez CHECK constraint,
generované stĺpce,
triggerové skladanie run_state,
serverovú JSON schému,
kaskádové fyzické mazanie.
```

---

# 2. Spoločné fyzické konvencie

## 2.1 Interné identity

Každá tabuľka používa:

```text
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
```

Interné `id` nie je metodickou referenciou a nesmie nahrádzať:

```text
request_reference
derivation_reference
branch_reference
dependency_reference
candidate_reference
result_reference
trace_reference
```

## 2.2 Referencie

```text
VARCHAR(191)
CHARACTER SET utf8mb4
COLLATE utf8mb4_bin
```

Binárna kolácia zachováva presnú technickú identitu referencie.

## 2.3 Fingerprint

```text
payload_fingerprint CHAR(64) ASCII
```

Ide o hexadecimálny SHA-256 obraz. Kanonizácia a výpočet patria `RequestReplayGuard`, nie databáze.

## 2.4 Čas

```text
DATETIME(6)
```

Čas sa ukladá v UTC a dodáva ho aplikačná vrstva cez `ClockPort`.

## 2.5 Štruktúrované snapshoty

Vnorené hodnoty sa v prvom návrhu ukladajú ako:

```text
LONGTEXT
```

v kanonickom UTF-8 formáte. Zmena na databázový `JSON` vyžaduje overenie konkrétnej verzie servera a samostatnú technickú Validáciu. Nesmie zmeniť fingerprint ani význam historických údajov.

---

# 3. `question_derivation_request_reservations`

Predstavuje `REQUEST_REFERENCE_RESERVATION_RECORD`.

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `request_reference` | VARCHAR(191) utf8mb4_bin | nie | konkrétna požiadavka |
| `payload_fingerprint` | CHAR(64) ASCII | nie | nemenný odtlačok vstupu |
| `derivation_reference` | VARCHAR(191) utf8mb4_bin | nie | jediný priradený beh |
| `reservation_state` | VARCHAR(32) ASCII | nie | `RESERVED`, `RUNNING`, `COMPLETED` |
| `reserved_at` | DATETIME(6) | nie | čas rezervácie |
| `updated_at` | DATETIME(6) | nie | čas technickej zmeny |

Obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (request_reference)
UNIQUE (derivation_reference)
```

Zakázané:

```text
REPLACE INTO
ON DUPLICATE KEY UPDATE payload_fingerprint
ON DUPLICATE KEY UPDATE derivation_reference
```

Konflikt unikátneho kľúča sa vracia aplikačnej vrstve. Existujúca rezervácia sa neprepisuje.

---

# 4. `question_derivation_runs`

Predstavuje `DERIVATION_RUN_RECORD`.

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `reservation_id` | BIGINT UNSIGNED | nie | jediná interná väzba na rezerváciu |
| `derivation_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia behu |
| `request_reference` | VARCHAR(191) utf8mb4_bin | nie | auditná korelácia požiadavky |
| `response_target_reference` | VARCHAR(191) utf8mb4_bin | nie | cieľ návratu |
| `request_source_snapshot` | LONGTEXT | nie | snapshot zdroja |
| `source_question_reference` | VARCHAR(191) utf8mb4_bin | nie | zdrojová otázka |
| `derivation_subject_reference` | VARCHAR(191) utf8mb4_bin | nie | predmet odvodenia |
| `purpose_snapshot` | LONGTEXT | nie | účel |
| `context_snapshot` | LONGTEXT | nie | kontext |
| `scope_snapshot` | LONGTEXT | nie | rozsah |
| `actor_reference` | VARCHAR(191) utf8mb4_bin | nie | ACTOR |
| `authority_context_snapshot` | LONGTEXT | nie | zachytený kontext Autority |
| `run_mode` | VARCHAR(64) ASCII | nie | `PARTIAL_RUN_WITH_ATOMIC_GATE` |
| `gate_state` | VARCHAR(64) ASCII | áno | technický obraz brány |
| `run_state` | VARCHAR(64) ASCII | áno | výsledok doménovej agregácie |
| `stop_reason_snapshot` | LONGTEXT | áno | dôvod zastavenia |
| `failed_control_reference` | VARCHAR(191) utf8mb4_bin | áno | zlyhaná kontrola |
| `started_at` | DATETIME(6) | nie | začiatok pokusu |
| `completed_at` | DATETIME(6) | áno | ukončenie pokusu |

Obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (reservation_id)
UNIQUE (derivation_reference)
UNIQUE (request_reference)
FOREIGN KEY (reservation_id)
→ question_derivation_request_reservations(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
```

`request_reference` a `derivation_reference` sa v behu uchovávajú pre auditnú rekonštrukciu a korelačné čítanie. Aplikačná transakcia pri prvom prijatí musí overiť, že sa zhodujú s rezerváciou označenou `reservation_id`.

Databázový trigger túto zhodu nevyrába ani neopravuje.

---

# 5. `question_derivation_run_domain_terms`

Predstavuje množinu `domain_term_references`.

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `run_id` | BIGINT UNSIGNED | nie | beh |
| `domain_term_reference` | VARCHAR(191) utf8mb4_bin | nie | jeden termín |
| `canonical_order` | INT UNSIGNED | nie | stabilné kanonické poradie |

Obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (run_id, domain_term_reference)
UNIQUE (run_id, canonical_order)
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
```

`canonical_order` vzniká tou istou kanonizáciou, ktorá sa používa pre fingerprint; nie poradím klientského vstupu.

---

# 6. `question_derivation_branches`

Predstavuje `DERIVATION_BRANCH_RECORD`.

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `branch_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia vetvy |
| `run_id` | BIGINT UNSIGNED | nie | nadradený beh |
| `subject_manifestation_snapshot` | LONGTEXT | nie | prejav SUBJECT-u |
| `branch_state` | VARCHAR(64) ASCII | nie | konečný vetvový stav |
| `stop_reason_snapshot` | LONGTEXT | áno | dôvod zastavenia |
| `failed_control_reference` | VARCHAR(191) utf8mb4_bin | áno | zlyhaná kontrola |
| `created_at` | DATETIME(6) | nie | vznik vetvy |
| `completed_at` | DATETIME(6) | áno | ukončenie vetvy |

Obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (branch_reference)
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
INDEX (run_id, branch_state)
```

Pri `STOPPED_AT_GATE` musí počet vetiev zostať nula. Tento medzi-tabuľkový invariant vynucuje aplikačná transakcia a integračný test, nie trigger.

---

# 7. `question_derivation_branch_dependencies`

Predstavuje `BRANCH_DEPENDENCY_RECORD`.

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `dependency_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia závislosti |
| `dependent_branch_id` | BIGINT UNSIGNED | nie | závislá vetva |
| `prerequisite_reference` | VARCHAR(191) utf8mb4_bin | nie | citovateľný predpoklad |
| `dependency_type` | VARCHAR(64) ASCII | nie | typ závislosti |
| `justification_snapshot` | LONGTEXT | nie | zdôvodnenie |
| `determined_by_reference` | VARCHAR(191) utf8mb4_bin | nie | zdroj určenia |
| `validation_control_reference` | VARCHAR(191) utf8mb4_bin | nie | použitá kontrola |
| `created_at` | DATETIME(6) | nie | zaznamenanie |

Obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (dependency_reference)
FOREIGN KEY (dependent_branch_id)
→ question_derivation_branches(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
INDEX (dependent_branch_id)
```

Invariant:

```text
BLOCKED_BY_DEPENDENCY
→ najmenej jedna citovateľná závislosť
```

vynucuje krátka vetvová transakcia a integračný test.

---

# 8. `question_derivation_candidates`

Predstavuje `QUESTION_CANDIDATE_RECORD`.

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `candidate_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia kandidáta |
| `branch_id` | BIGINT UNSIGNED | nie | jediná vetva kandidáta |
| `run_id` | BIGINT UNSIGNED | nie | beh pre korelačné čítanie |
| `candidate_content_snapshot` | LONGTEXT | nie | úplný obsah kandidáta |
| `primary_dimension` | VARCHAR(32) ASCII | nie | primárny rozmer |
| `meaning_of_one_snapshot` | LONGTEXT | nie | význam odpovede 1 |
| `meaning_of_zero_snapshot` | LONGTEXT | nie | význam odpovede 0 |
| `required_question_context_snapshot` | LONGTEXT | nie | povinný kontext |
| `intended_applicability_scope_snapshot` | LONGTEXT | nie | rozsah použiteľnosti |
| `created_at` | DATETIME(6) | nie | vznik kandidáta |

Obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (candidate_reference)
UNIQUE (branch_id)
FOREIGN KEY (branch_id)
→ question_derivation_branches(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
INDEX (run_id)
```

Pred potvrdením vetvovej transakcie aplikácia overí:

```text
branch.run_id = candidate.run_id
branch.branch_state = CANDIDATE_CREATED
```

Databáza sama nemení stav vetvy po vložení kandidáta.

---

# 9. `question_derivation_run_results`

Predstavuje `DERIVATION_RUN_RESULT_RECORD`.

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `result_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia výsledku |
| `run_id` | BIGINT UNSIGNED | nie | ukončený beh |
| `request_reference` | VARCHAR(191) utf8mb4_bin | nie | korelácia požiadavky |
| `response_target_reference` | VARCHAR(191) utf8mb4_bin | nie | cieľ návratu |
| `run_mode` | VARCHAR(64) ASCII | nie | režim behu |
| `run_state` | VARCHAR(64) ASCII | nie | doménovo agregovaný stav |
| `candidate_count` | INT UNSIGNED | nie | úspešné vetvy |
| `stopped_branch_count` | INT UNSIGNED | nie | zastavené vetvy |
| `decomposition_branch_count` | INT UNSIGNED | nie | vrátené vetvy |
| `blocked_branch_count` | INT UNSIGNED | nie | blokované vetvy |
| `total_branch_count` | INT UNSIGNED | nie | všetky vetvy |
| `stop_reason_snapshot` | LONGTEXT | áno | dôvod zastavenia |
| `failed_control_reference` | VARCHAR(191) utf8mb4_bin | áno | zlyhaná kontrola |
| `summary_snapshot` | LONGTEXT | nie | súhrn výsledku |
| `completed_at` | DATETIME(6) | nie | ukončenie |

Obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (result_reference)
UNIQUE (run_id)
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
INDEX (request_reference)
```

Ukončovacia transakcia musí overiť:

```text
request_reference = request_reference behu
response_target_reference = response_target_reference behu
run_mode = PARTIAL_RUN_WITH_ATOMIC_GATE
candidate_count + stopped_branch_count + decomposition_branch_count + blocked_branch_count
= total_branch_count
```

Pri `STOPPED_AT_GATE` sú všetky vetvové počty nula.

Databáza ani trigger nesmú odvodiť `run_state` z počtov.

---

# 10. `question_derivation_traces`

Predstavuje `DERIVATION_TRACE_RECORD`.

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `trace_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia udalosti |
| `run_id` | BIGINT UNSIGNED | nie | beh udalosti |
| `branch_id` | BIGINT UNSIGNED | áno | voliteľná vetva |
| `dependency_id` | BIGINT UNSIGNED | áno | voliteľná závislosť |
| `event_type` | VARCHAR(96) ASCII | nie | typ udalosti |
| `event_payload_snapshot` | LONGTEXT | nie | nemenný obsah |
| `occurred_at` | DATETIME(6) | nie | čas udalosti |
| `sequence_number` | BIGINT UNSIGNED | nie | poradie v behu |

Obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (trace_reference)
UNIQUE (run_id, sequence_number)
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
FOREIGN KEY (branch_id)
→ question_derivation_branches(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
FOREIGN KEY (dependency_id)
→ question_derivation_branch_dependencies(id)
ON DELETE RESTRICT
ON UPDATE RESTRICT
INDEX (run_id, occurred_at)
```

`sequence_number` sa prideľuje atómovo v rámci behu. Časová pečiatka sama nestačí.

Auditná stopa zostáva:

```text
≠ dôkaz
≠ Validácia
≠ automaticky metodická skutočnosť
```

---

# 11. Fyzické väzby

```text
question_derivation_request_reservations
1 ↔ 1
question_derivation_runs

question_derivation_runs
1 → 0..N
question_derivation_run_domain_terms

question_derivation_runs
1 → 0..N
question_derivation_branches

question_derivation_branches
1 → 0..N
question_derivation_branch_dependencies

question_derivation_branches
1 → 0..1
question_derivation_candidates

question_derivation_runs
1 → 0..1
question_derivation_run_results

question_derivation_runs
1 → 1..N
question_derivation_traces
```

---

# 12. Migračné poradie

```text
M1  question_derivation_request_reservations
M2  question_derivation_runs
M3  question_derivation_run_domain_terms
M4  question_derivation_branches
M5  question_derivation_branch_dependencies
M6  question_derivation_candidates
M7  question_derivation_run_results
M8  question_derivation_traces
```

Rollback poradie:

```text
M8 → M7 → M6 → M5 → M4 → M3 → M2 → M1
```

Rollback schémy je vývojový zásah nad prázdnou alebo riadene spravovanou databázou. Nie je oprávnením mazať produkčnú históriu.

---

# 13. Migračné obmedzenia

Každá migrácia musí:

```text
1. používať InnoDB,
2. používať utf8mb4,
3. vytvoriť rodičovské kľúče pred cudzími kľúčmi,
4. vytvoriť unikátne korelačné obmedzenia,
5. používať ON DELETE RESTRICT alebo NO ACTION,
6. používať ON UPDATE RESTRICT pre nemenné väzby,
7. nepoužívať ON DELETE CASCADE,
8. nepoužívať trigger na run_state alebo branch_state,
9. nevkladať produkčné seed údaje,
10. nevytvárať defaulty predstierajúce metodický výsledok,
11. nevytvárať automatickú expiráciu alebo mazanie,
12. zachovať mikrosekundový čas, ak ho server podporuje.
```

Ak server nepodporuje `DATETIME(6)`, migrácia sa nesmie ticho zmeniť. Najprv musí vzniknúť technické rozhodnutie o presnosti času a dopade na poradie udalostí.

---

# 14. Databázou vynútiteľné invarianty

```text
D1 — jedinečnosť request_reference,
D2 — jedinečnosť derivation_reference,
D3 — najviac jeden beh na rezerváciu,
D4 — každá vetva patrí existujúcemu behu,
D5 — najviac jeden kandidát na vetvu,
D6 — najviac jeden finálny výsledok na beh,
D7 — každá závislosť patrí existujúcej vetve,
D8 — každá auditná udalosť patrí existujúcemu behu,
D9 — jedinečné stabilné poradie trace udalostí v behu,
D10 — zákaz kaskádového fyzického mazania.
```

---

# 15. Aplikáciou vynútiteľné invarianty

```text
A1 — rezervácia, beh a počiatočný trace vzniknú spolu,
A2 — iba FIRST_ACCEPTANCE založí nový beh,
A3 — fingerprint rozlíši replay a konflikt,
A4 — referencie behu sa zhodujú s jeho reservation_id,
A5 — STOPPED_AT_GATE nevytvorí vetvy ani kandidátov,
A6 — CANDIDATE_CREATED vznikne spolu s úplným kandidátom,
A7 — ostatné vetvové stavy nemajú kandidáta,
A8 — BLOCKED_BY_DEPENDENCY má citovateľnú závislosť,
A9 — kandidát patrí tej istej vetve a behu,
A10 — run_state vzniká iba doménovou agregáciou,
A11 — počty výsledku zodpovedajú vetvám,
A12 — ukončené výsledky sa neprepisujú,
A13 — auditná stopa sa nezamieňa s dôkazom alebo Validáciou.
```

---

# 16. Transakčné hranice

## 16.1 Prvé prijatie

```text
INSERT reservation
+
INSERT run s reservation_id
+
INSERT initial trace
```

Jedna krátka transakcia. Pri zlyhaní sa neuloží nič.

## 16.2 Zlyhanie brány

```text
UPDATE run
+
INSERT final result
+
UPDATE reservation to COMPLETED
+
INSERT trace
```

Jedna krátka transakcia. Nevznikne vetva.

## 16.3 Jedna vetva

```text
INSERT branch
+
INSERT 0..N dependencies
+
INSERT 0..1 candidate
+
INSERT trace
```

Jedna krátka transakcia. Potvrdená nezávislá vetva sa pri neskoršom zlyhaní inej vetvy nerollbackuje.

## 16.4 Ukončenie behu

```text
INSERT final result
+
UPDATE run
+
UPDATE reservation to COMPLETED
+
INSERT trace
```

Jedna krátka transakcia po doménovej agregácii.

---

# 17. Zakázané databázové skratky

```text
REPLACE INTO nad historickými záznamami
ON DUPLICATE KEY UPDATE nemenných referencií
ON DELETE CASCADE
triggerové vytváranie kandidáta
triggerová agregácia run_state
triggerové domýšľanie reservation_state
jedna transakcia celého PARTIAL RUN
ukladanie všetkých vetiev do jedného prepisovaného JSON dokumentu
automatická expirácia ukončených behov
fyzické mazanie ako bežná oprava
```

---

# 18. Otvorené rozhodnutia

```text
konkrétna verzia MySQL alebo MariaDB servera,
šifrovanie a správa kľúčov pre citlivé snapshoty,
retencia, archivácia a anonymizácia,
obnova prerušeného behu,
model opravných udalostí,
formálny číselník event_type,
vzťahy retry_of, supersedes a follows,
produkčná stratégia záloh a obnovy.
```

Tieto body nebránia vytvoriť vývojové migrácie, ale produkčné uloženie citlivých vstupov nesmie predbehnúť rozhodnutie o ich ochrane.

---

# 19. Testovací kontrakt

```text
1. duplicitnú request_reference nemožno vložiť,
2. jedna rezervácia nemôže mať dva behy,
3. beh bez reservation_id nemožno vložiť,
4. aplikácia odmietne nezhodu referencií behu a rezervácie,
5. vetvu bez behu nemožno vložiť,
6. druhého kandidáta tej istej vetvy nemožno vložiť,
7. druhý finálny výsledok toho istého behu nemožno vložiť,
8. závislosť bez vetvy nemožno vložiť,
9. duplicitné sequence_number v jednom behu nemožno vložiť,
10. zmazanie rodiča s históriou je odmietnuté,
11. rollback vetvy nevymaže inú potvrdenú vetvu,
12. CANDIDATE_CREATED nevznikne bez kandidáta,
13. STOPPED_AT_GATE nevytvorí vetvy,
14. uložené počty výsledku zodpovedajú vetvám,
15. databáza sama nevytvorí ani nezmení run_state.
```

---

# 20. Nasledujúci logický krok

```text
Validovať databázový návrh a migračné obmedzenia
→ overiť konkrétnu verziu databázového servera
→ vytvoriť CodeIgniter migrácie M1 až M8
→ vytvoriť repository adaptéry
→ vykonať databázové integračné testy
→ Validovať implementovaný stav
```
