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
8. migračný návrh nesmie závisieť od neoverenej konkrétnej verzie servera.
```

Pre prenositeľnosť v rámci MySQL/MariaDB hostingu sa návrh nespolieha na:

```text
vynucovanie významu iba cez CHECK constraint,
generované stĺpce,
triggerové skladanie run_state,
serverovú JSON schému,
kaskádové fyzické mazanie.
```

---

# 2. Spoločné konvencie

## 2.1 Interné identity

Každá tabuľka používa interný primárny kľúč:

```text
id BIGINT UNSIGNED AUTO_INCREMENT
```

Interné `id` nie je metodickou referenciou a nesmie sa vracať ako náhrada za:

```text
request_reference
derivation_reference
branch_reference
dependency_reference
candidate_reference
result_reference
trace_reference
```

## 2.2 Referenčné stĺpce

Pracovný fyzický typ:

```text
VARCHAR(191)
CHARACTER SET utf8mb4
COLLATE utf8mb4_bin
```

Binárna kolácia je potrebná, aby sa technické referencie neporovnávali bez rozlíšenia veľkosti písmen alebo jazykovou normalizáciou.

## 2.3 Odtlačok požiadavky

```text
payload_fingerprint CHAR(64) ASCII
```

Predpokladaný technický obraz je hexadecimálny SHA-256 odtlačok. Algoritmus kanonizácie a výpočtu patrí `RequestReplayGuard`, nie databáze.

## 2.4 Čas

```text
DATETIME(6)
```

Všetky časové hodnoty sa ukladajú v UTC. Databázový server nesmie svojvoľne určovať metodický čas; aplikačná vrstva odovzdáva čas z `ClockPort`.

## 2.5 Štruktúrované snapshoty

Nasledujúce hodnoty môžu byť vnorené alebo štruktúrované:

```text
request_source
purpose
context
scope
authority_context
subject_manifestation
candidate_content
meaning_of_one
meaning_of_zero
required_question_context
intended_applicability_scope
summary
event_payload
```

Pracovný fyzický typ:

```text
LONGTEXT
```

Aplikačná vrstva ich ukladá v kanonickom UTF-8 formáte. Ak sa neskôr potvrdí konkrétna verzia databázového servera a JSON kompatibilita, možno samostatne Validovať zmenu niektorých stĺpcov na `JSON`. Táto zmena nesmie ovplyvniť význam ani fingerprint už uložených požiadaviek.

---

# 3. Tabuľka `question_derivation_request_reservations`

Predstavuje:

```text
REQUEST_REFERENCE_RESERVATION_RECORD
```

Navrhované stĺpce:

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `request_reference` | VARCHAR(191) utf8mb4_bin | nie | identita konkrétnej požiadavky |
| `payload_fingerprint` | CHAR(64) ASCII | nie | nemenný odtlačok kontraktového obsahu |
| `derivation_reference` | VARCHAR(191) utf8mb4_bin | nie | jediný beh priradený požiadavke |
| `reservation_state` | VARCHAR(32) ASCII | nie | `RESERVED`, `RUNNING`, `COMPLETED` |
| `reserved_at` | DATETIME(6) | nie | čas rezervácie |
| `updated_at` | DATETIME(6) | nie | čas poslednej technickej zmeny stavu |

Povinné obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (request_reference)
UNIQUE (derivation_reference)
```

Databáza nesmie pri konflikte unikátneho kľúča automaticky prepísať existujúci záznam.

Zakázané:

```text
ON DUPLICATE KEY UPDATE payload_fingerprint
ON DUPLICATE KEY UPDATE derivation_reference
REPLACE INTO
```

Konflikt rezervácie sa musí vrátiť aplikačnej vrstve ako technický výsledok `ALREADY_EXISTS` alebo chyba invariantu podľa skutočného stavu.

---

# 4. Tabuľka `question_derivation_runs`

Predstavuje:

```text
DERIVATION_RUN_RECORD
```

Navrhované stĺpce:

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `derivation_reference` | VARCHAR(191) utf8mb4_bin | nie | metodická referencia behu |
| `request_reference` | VARCHAR(191) utf8mb4_bin | nie | korelácia na požiadavku |
| `response_target_reference` | VARCHAR(191) utf8mb4_bin | nie | cieľ návratu výsledku |
| `request_source_snapshot` | LONGTEXT | nie | verný technický snapshot zdroja |
| `source_question_reference` | VARCHAR(191) utf8mb4_bin | nie | zdrojová otázka |
| `derivation_subject_reference` | VARCHAR(191) utf8mb4_bin | nie | predmet odvodenia |
| `purpose_snapshot` | LONGTEXT | nie | účel odvodenia |
| `context_snapshot` | LONGTEXT | nie | kontext odvodenia |
| `scope_snapshot` | LONGTEXT | nie | rozsah odvodenia |
| `actor_reference` | VARCHAR(191) utf8mb4_bin | nie | ACTOR bez tvrdenia o Autorite |
| `authority_context_snapshot` | LONGTEXT | nie | zachytený kontext Autority |
| `run_mode` | VARCHAR(64) ASCII | nie | musí reprezentovať `PARTIAL_RUN_WITH_ATOMIC_GATE` |
| `gate_state` | VARCHAR(64) ASCII | áno | technický obraz výsledku brány |
| `run_state` | VARCHAR(64) ASCII | áno | finálny metodický stav po agregácii |
| `stop_reason_snapshot` | LONGTEXT | áno | dôvod zastavenia |
| `failed_control_reference` | VARCHAR(191) utf8mb4_bin | áno | zlyhaná kontrola |
| `started_at` | DATETIME(6) | nie | začiatok historického pokusu |
| `completed_at` | DATETIME(6) | áno | ukončenie behu |

Povinné obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (derivation_reference)
UNIQUE (request_reference)
FOREIGN KEY request_reference
→ question_derivation_request_reservations.request_reference
```

Väzba rezervácie a behu je obojsmerná významom, ale fyzicky sa vynucuje:

```text
rovnakou request_reference
+
rovnakou derivation_reference overenou aplikačnou transakciou
```

Samotný cudzí kľúč nevie v prenositeľnom profile bezpečne vynútiť zhodu oboch stĺpcov bez ďalšieho kompozitného obmedzenia. Migračná implementácia preto musí použiť buď:

```text
A. kompozitný UNIQUE(request_reference, derivation_reference)
   na oboch stranách a kompozitný FOREIGN KEY,

alebo

B. jeden interný reservation_id ako povinný cudzí kľúč
   + aplikačný invariant nemennosti oboch referencií.
```

Pre prvú migráciu sa odporúča variant B, pretože minimalizuje duplicitu fyzických indexov a udržiava interné väzby oddelené od verejných referencií:

```text
reservation_id BIGINT UNSIGNED NOT NULL UNIQUE
FOREIGN KEY (reservation_id)
→ question_derivation_request_reservations(id)
ON DELETE RESTRICT
```

`request_reference` a `derivation_reference` zostávajú uložené aj v behu pre auditnú rekonštrukciu a korelačné čítanie.

---

# 5. Tabuľka `question_derivation_run_domain_terms`

Predstavuje množinu:

```text
domain_term_references
```

Navrhované stĺpce:

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `run_id` | BIGINT UNSIGNED | nie | interná väzba na beh |
| `domain_term_reference` | VARCHAR(191) utf8mb4_bin | nie | jeden použitý doménový termín |
| `canonical_order` | INT UNSIGNED | nie | stabilné poradie kanonického snapshotu |

Povinné obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (run_id, domain_term_reference)
UNIQUE (run_id, canonical_order)
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
```

`canonical_order` nesmie závisieť od poradia, v ktorom klient termíny odoslal. Vzniká z rovnakej kanonizácie, ktorá sa používa pre fingerprint.

---

# 6. Tabuľka `question_derivation_branches`

Predstavuje:

```text
DERIVATION_BRANCH_RECORD
```

Navrhované stĺpce:

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `branch_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia vetvy |
| `run_id` | BIGINT UNSIGNED | nie | beh, ktorému vetva patrí |
| `subject_manifestation_snapshot` | LONGTEXT | nie | prejav SUBJECT-u |
| `branch_state` | VARCHAR(64) ASCII | nie | konečný vetvový stav |
| `stop_reason_snapshot` | LONGTEXT | áno | dôvod zastavenia |
| `failed_control_reference` | VARCHAR(191) utf8mb4_bin | áno | zlyhaná kontrola |
| `created_at` | DATETIME(6) | nie | vznik vetvy |
| `completed_at` | DATETIME(6) | áno | ukončenie vetvy |

Povinné obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (branch_reference)
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
INDEX (run_id, branch_state)
```

Databáza nesmie sama meniť alebo dopočítavať `branch_state`.

Pri `STOPPED_AT_GATE` musí počet záznamov v tejto tabuľke pre daný beh zostať nula. Tento medzi-tabuľkový invariant sa vynucuje aplikačnou transakciou a integračným testom, nie triggerom.

---

# 7. Tabuľka `question_derivation_branch_dependencies`

Predstavuje:

```text
BRANCH_DEPENDENCY_RECORD
```

Navrhované stĺpce:

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `dependency_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia závislosti |
| `dependent_branch_id` | BIGINT UNSIGNED | nie | závislá vetva |
| `prerequisite_reference` | VARCHAR(191) utf8mb4_bin | nie | citovateľný predpoklad |
| `dependency_type` | VARCHAR(64) ASCII | nie | významový typ závislosti |
| `justification_snapshot` | LONGTEXT | nie | zdôvodnenie |
| `determined_by_reference` | VARCHAR(191) utf8mb4_bin | nie | zdroj určenia |
| `validation_control_reference` | VARCHAR(191) utf8mb4_bin | nie | použitá kontrola |
| `created_at` | DATETIME(6) | nie | čas zaznamenania |

Povinné obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (dependency_reference)
FOREIGN KEY (dependent_branch_id)
→ question_derivation_branches(id)
ON DELETE RESTRICT
INDEX (dependent_branch_id)
```

Invariant:

```text
branch_state = BLOCKED_BY_DEPENDENCY
→ najmenej jeden súvisiaci dependency záznam
```

sa vynucuje v jednej krátkej vetvovej transakcii a integračným testom. Databázový trigger sa nepoužíva.

---

# 8. Tabuľka `question_derivation_candidates`

Predstavuje:

```text
QUESTION_CANDIDATE_RECORD
```

Navrhované stĺpce:

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
| `required_question_context_snapshot` | LONGTEXT | nie | povinný kontext otázky |
| `intended_applicability_scope_snapshot` | LONGTEXT | nie | rozsah použiteľnosti |
| `created_at` | DATETIME(6) | nie | čas vzniku |

Povinné obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (candidate_reference)
UNIQUE (branch_id)
FOREIGN KEY (branch_id)
→ question_derivation_branches(id)
ON DELETE RESTRICT
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
INDEX (run_id)
```

Aplikačná transakcia musí pred potvrdením overiť:

```text
branch.run_id = candidate.run_id
branch.branch_state = CANDIDATE_CREATED
```

Databáza sama nemá meniť vetvu na `CANDIDATE_CREATED` po vložení kandidáta.

---

# 9. Tabuľka `question_derivation_run_results`

Predstavuje:

```text
DERIVATION_RUN_RESULT_RECORD
```

Navrhované stĺpce:

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `result_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia výsledku |
| `run_id` | BIGINT UNSIGNED | nie | ukončený beh |
| `request_reference` | VARCHAR(191) utf8mb4_bin | nie | korelácia požiadavky |
| `response_target_reference` | VARCHAR(191) utf8mb4_bin | nie | cieľ návratu |
| `run_mode` | VARCHAR(64) ASCII | nie | režim behu |
| `run_state` | VARCHAR(64) ASCII | nie | výsledok doménovej agregácie |
| `candidate_count` | INT UNSIGNED | nie | počet úspešných vetiev |
| `stopped_branch_count` | INT UNSIGNED | nie | počet zastavených vetiev |
| `decomposition_branch_count` | INT UNSIGNED | nie | počet vrátených vetiev |
| `blocked_branch_count` | INT UNSIGNED | nie | počet blokovaných vetiev |
| `total_branch_count` | INT UNSIGNED | nie | počet všetkých vetiev |
| `stop_reason_snapshot` | LONGTEXT | áno | dôvod zastavenia behu |
| `failed_control_reference` | VARCHAR(191) utf8mb4_bin | áno | zlyhaná kontrola |
| `summary_snapshot` | LONGTEXT | nie | súhrn výsledku |
| `completed_at` | DATETIME(6) | nie | čas ukončenia |

Povinné obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (result_reference)
UNIQUE (run_id)
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
INDEX (request_reference)
```

Aplikačná vrstva musí v ukončovacej transakcii overiť:

```text
request_reference = request_reference príslušného behu
response_target_reference = response_target_reference príslušného behu
run_mode = PARTIAL_RUN_WITH_ATOMIC_GATE
candidate_count + stopped_branch_count + decomposition_branch_count + blocked_branch_count
= total_branch_count
```

Pri `STOPPED_AT_GATE` musia byť všetky vetvové počty nula.

Databáza ani trigger nesmú odvodiť `run_state` z počtov. Uložený `run_state` vzniká iba doménovou agregáciou.

---

# 10. Tabuľka `question_derivation_traces`

Predstavuje:

```text
DERIVATION_TRACE_RECORD
```

Navrhované stĺpce:

| Stĺpec | Typ | Null | Význam |
|---|---|---:|---|
| `id` | BIGINT UNSIGNED | nie | interná identita |
| `trace_reference` | VARCHAR(191) utf8mb4_bin | nie | referencia udalosti |
| `run_id` | BIGINT UNSIGNED | nie | beh udalosti |
| `branch_id` | BIGINT UNSIGNED | áno | voliteľná vetva |
| `dependency_id` | BIGINT UNSIGNED | áno | voliteľná závislosť |
| `event_type` | VARCHAR(96) ASCII | nie | technický alebo metodický typ udalosti |
| `event_payload_snapshot` | LONGTEXT | nie | nemenný obsah udalosti |
| `occurred_at` | DATETIME(6) | nie | čas udalosti |
| `sequence_number` | BIGINT UNSIGNED | nie | stabilné poradie v rámci behu |

Povinné obmedzenia:

```text
PRIMARY KEY (id)
UNIQUE (trace_reference)
UNIQUE (run_id, sequence_number)
FOREIGN KEY (run_id)
→ question_derivation_runs(id)
ON DELETE RESTRICT
FOREIGN KEY (branch_id)
→ question_derivation_branches(id)
ON DELETE RESTRICT
FOREIGN KEY (dependency_id)
→ question_derivation_branch_dependencies(id)
ON DELETE RESTRICT
INDEX (run_id, occurred_at)
```

`sequence_number` prideľuje aplikačná alebo infraštruktúrna zodpovednosť atómovo v rámci behu. Samotná časová pečiatka nie je dostatočná na stabilné poradie.

Auditná stopa zostáva:

```text
≠ dôkaz
≠ Validácia
≠ automaticky metodická skutočnosť
```

---

# 11. Prehľad fyzických väzieb

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

Migrácie sa musia vytvoriť a spúšťať v tomto poradí:

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

Rollback poradie je presne opačné:

```text
M8 → M7 → M6 → M5 → M4 → M3 → M2 → M1
```

Rollback migračnej schémy je vývojový zásah do prázdnej alebo riadenej databázy. Nie je oprávnením mazať produkčnú historickú stopu.

Produkčný rollback s existujúcimi historickými údajmi musí byť samostatne riadený a nesmie použiť nekontrolované `DROP TABLE`.

---

# 13. Migračné obmedzenia

Každá migrácia musí:

```text
1. používať InnoDB,
2. používať utf8mb4,
3. vytvoriť primárne kľúče pred cudzími kľúčmi,
4. vytvoriť unikátne korelačné obmedzenia,
5. používať ON DELETE RESTRICT alebo NO ACTION,
6. nepoužívať ON DELETE CASCADE,
7. nepoužívať ON UPDATE CASCADE pre nemenné referencie,
8. nepoužívať trigger na run_state alebo branch_state,
9. nevkladať produkčné seed údaje,
10. nevytvárať defaulty, ktoré predstierajú metodický výsledok,
11. nevytvárať automatickú expiráciu alebo mazanie,
12. zachovať mikrosekundové časové hodnoty, ak ich server podporuje.
```

Ak server nepodporuje `DATETIME(6)`, migrácia sa nesmie ticho zmeniť na nižšiu presnosť. Najprv sa musí vytvoriť technické rozhodnutie o časovej presnosti a dopade na stabilné poradie udalostí.

---

# 14. Invarianty, ktoré vynucuje databáza

Databázové obmedzenia môžu priamo vynútiť:

```text
D1 — jedinečnosť request_reference,
D2 — jedinečnosť derivation_reference,
D3 — najviac jeden beh na rezerváciu,
D4 — každá vetva patrí existujúcemu behu,
D5 — najviac jeden kandidát na vetvu,
D6 — najviac jeden finálny výsledok na beh,
D7 — každá závislosť patrí existujúcej vetve,
D8 — každá auditná udalosť patrí existujúcemu behu,
D9 — stabilné jedinečné poradie trace udalostí v rámci behu,
D10 — zákaz kaskádového fyzického mazania cez referenčné obmedzenia.
```

---

# 15. Invarianty, ktoré musí vynútiť aplikácia

Nasledujúce významové invarianty sa nesmú presunúť do databázových triggerov:

```text
A1 — rezervácia a historický beh vzniknú v jednej prvej transakcii,
A2 — iba FIRST_ACCEPTANCE založí nový beh,
A3 — rovnaký fingerprint znamená replay a odlišný konflikt,
A4 — STOPPED_AT_GATE nevytvorí vetvy ani kandidátov,
A5 — CANDIDATE_CREATED vznikne spolu s úplným kandidátom,
A6 — ostatné vetvové stavy nemajú kandidáta,
A7 — BLOCKED_BY_DEPENDENCY má citovateľnú závislosť,
A8 — kandidát patrí tej istej vetve a behu,
A9 — run_state vzniká iba doménovou agregáciou,
A10 — počty výsledku zodpovedajú uloženým vetvám,
A11 — ukončené vetvové a nadradené výsledky sa neprepisujú,
A12 — auditná stopa sa nezamieňa s dôkazom alebo Validáciou.
```

---

# 16. Transakčné hranice fyzického zápisu

## 16.1 Prvé prijatie

```text
INSERT request reservation
+
INSERT derivation run
+
INSERT initial trace
```

Jedna krátka transakcia. Pri akomkoľvek zlyhaní sa neuloží nič.

## 16.2 Zlyhanie brány

```text
UPDATE derivation run
+
INSERT final run result
+
UPDATE request reservation to COMPLETED
+
INSERT trace
```

Jedna krátka transakcia. Nevznikne vetva.

## 16.3 Výsledok jednej vetvy

```text
INSERT branch
+
INSERT 0..N dependencies
+
INSERT 0..1 candidate
+
INSERT trace
```

Jedna krátka transakcia. Potvrdená vetva sa už pri neskoršom zlyhaní inej vetvy nerollbackuje.

## 16.4 Ukončenie behu

```text
INSERT final run result
+
UPDATE derivation run
+
UPDATE request reservation to COMPLETED
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
triggerové prepisovanie reservation_state na základe domnienky
jedna transakcia celého PARTIAL RUN
ukladanie všetkých vetiev do jedného prepisovaného JSON dokumentu
automatická expirácia ukončených behov
fyzické mazanie ako bežná oprava
```

---

# 18. Otvorené rozhodnutia pred produkčnou implementáciou

Databázový návrh vedome neurčuje:

```text
konkrétnu verziu MySQL alebo MariaDB servera,
šifrovanie a správu kľúčov pre citlivé snapshoty,
retenčnú, archivačnú a anonymizačnú politiku,
obnovu prerušeného behu,
model opravných udalostí,
formálny číselník event_type,
vzťahy retry_of, supersedes a follows,
produkčnú stratégiu záloh a obnovy.
```

Tieto body nebránia vytvoriť prvé migrácie pre vývojové prostredie, ale produkčné nasadenie citlivých vstupov nesmie predbehnúť rozhodnutie o ich ochrane.

---

# 19. Testovací kontrakt databázového návrhu

Minimálne databázové integračné testy musia overiť:

```text
1. druhá request_reference sa nedá vložiť,
2. druhá derivation_reference sa nedá priradiť jednej rezervácii,
3. beh bez rezervácie sa nedá potvrdiť,
4. vetva bez behu sa nedá vložiť,
5. druhý kandidát tej istej vetvy sa nedá vložiť,
6. druhý finálny výsledok toho istého behu sa nedá vložiť,
7. závislosť bez vetvy sa nedá vložiť,
8. duplicitné sequence_number v jednom behu sa nedá vložiť,
9. zmazanie rodiča s historickými deťmi je odmietnuté,
10. rollback jednej vetvy nevymaže už potvrdenú inú vetvu,
11. aplikačná transakcia nevytvorí CANDIDATE_CREATED bez kandidáta,
12. aplikácia nevytvorí vetvy pri STOPPED_AT_GATE,
13. uložené počty finálneho výsledku zodpovedajú vetvám,
14. databáza sama nevytvorí ani nezmení run_state,
15. migračný rollback sa nespustí nekontrolovane nad produkčnými údajmi.
```

---

# 20. Nasledujúci logický krok

```text
Validovať databázový návrh a migračné obmedzenia
→ overiť konkrétnu verziu databázového servera vo vývojovom prostredí
→ vytvoriť CodeIgniter migrácie M1 až M8
→ vytvoriť repository adaptéry
→ vykonať databázové integračné testy
→ Validovať implementovaný stav
```
