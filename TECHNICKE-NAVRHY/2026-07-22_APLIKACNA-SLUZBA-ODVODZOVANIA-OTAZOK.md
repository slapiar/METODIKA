# Technický návrh aplikačnej služby odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument technicky odvodzuje aplikačnú službu z Validovaného kontraktu:

```text
postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
```

Aktuálna reValidácia kontraktu:

```text
postupy/2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md
VALIDATION_RESULT = VALID
```

Technický návrh nesmie meniť:

```text
PARTIAL_RUN_WITH_ATOMIC_GATE
REQUEST_REFERENCE
RESPONSE_TARGET_REFERENCE
BRANCH_DEPENDENCY
deterministickú agregáciu run_state
význam QUESTION_CANDIDATE
význam metodického zastavenia
```

Neurčuje ešte SQL schému, migrácie, controller, HTTP route ani JSON odpoveď.

---

# 1. Predmet technického návrhu

Navrhovaná aplikačná služba:

```text
QuestionDerivationApplicationService
```

Odporúčané umiestnenie:

```text
codei/app/Application/QuestionDerivation/
```

Jej jediný verejný prípad použitia:

```php
public function derive(
    DerivationApplicationInput $input
): DerivationRunResult;
```

Názvy sú technické a pracovné. Nemenia identitu metodického úkonu `QUESTION_DERIVATION`.

---

# 2. Zodpovednosť služby

Služba koordinuje jeden beh odvodzovania:

```text
prijatie aplikačného vstupu
→ založenie historického pokusu
→ atómová vstupná brána
→ vytvorenie kandidátskych vetiev
→ spracovanie vetiev a ich závislostí
→ zachovanie čiastkových výsledkov
→ deterministická agregácia run_state
→ vrátenie DERIVATION_RUN_RESULT
```

Služba smie:

```text
koordinovať poradie krokov
volať doménový algoritmus
volať technické porty
udržiavať koreláciu požiadavky a výsledku
riadiť krátke technické transakcie
zostaviť technický obraz Validovaného výsledku
```

Služba nesmie:

```text
meniť doménové pravidlá
vytvoriť kandidáta bez doménového algoritmu
určovať Autoritu
zameniť technickú chybu za metodické zastavenie
zameniť zlyhanie vetvy za odpoveď 0
prepisovať výsledky už ukončených nezávislých vetiev
meniť run_mode podľa používateľskej konfigurácie
```

---

# 3. Technické dátové objekty

Odporúčané nemenné aplikačné objekty:

```text
DerivationApplicationInput
DerivationRunResult
CandidateBranchResult
BranchDependencyData
```

## 3.1 DerivationApplicationInput

Musí technicky reprezentovať najmenej:

```text
request_reference
request_source
response_target_reference
source_question_reference
derivation_subject_reference
purpose
context
scope
domain_term_references
actor_reference
authority_context
run_mode = PARTIAL_RUN_WITH_ATOMIC_GATE
```

Objekt nevykonáva metodickú Validáciu. Môže overiť iba svoju technickú úplnosť a typovú konzistenciu.

## 3.2 DerivationRunResult

Musí niesť najmenej:

```text
request_reference
response_target_reference
derivation_reference
run_mode
run_state
candidates
branch_results
trace
stop_reason?
failed_control?
summary
```

## 3.3 CandidateBranchResult

Musí niesť najmenej:

```text
branch_reference
subject_manifestation
state
candidate?
stop_reason?
failed_control?
dependencies
trace
```

## 3.4 BranchDependencyData

Musí zachovať celý Validovaný význam:

```text
dependency_reference
dependent_branch_reference
prerequisite_reference
dependency_type
justification
determined_by_reference
validation_control_reference
trace
```

Technický objekt nesmie redukovať závislosť iba na dvojicu databázových identifikátorov.

---

# 4. Doménový spolupracovník

Aplikačná služba nevykonáva vlastnú kópiu algoritmu. Používa jeden doménový spolupracovník:

```text
QuestionDerivationAlgorithm
```

Odporúčané rozhranie zodpovedností:

```text
validateAtomicGate(input)
identifySubjectManifestations(input)
deriveCandidateBranch(run, manifestation, dependencies)
evaluateBranchDependencies(branch, completedBranchResults)
aggregateRunState(gateResult, branchResults)
```

Presné PHP metódy sa môžu pri implementácii upraviť, ale technická realizácia musí zachovať tieto oddelené zodpovednosti.

`aggregateRunState` musí byť jedinou implementáciou Validovaného agregačného pravidla. Controller, repository ani prezentačná vrstva ho nesmú skladať znovu.

---

# 5. Technické porty

Služba smie závisieť iba od rozhraní aplikačnej alebo doménovej vrstvy.

## 5.1 DerivationHistoryPort

Zodpovednosť:

```text
založiť pokus pred vstupnými kontrolami
uložiť výsledok vstupnej brány
uložiť výsledok každej vetvy
uložiť finálny súhrn behu
zachovať auditnú stopu
```

## 5.2 QuestionCandidatePort

Zodpovednosť:

```text
uložiť iba úplný QUESTION_CANDIDATE
zachovať väzbu na vetvu a derivation_reference
neuložiť BRANCH_STOPPED, RETURNED_FOR_DECOMPOSITION ani BLOCKED_BY_DEPENDENCY ako kandidáta
```

## 5.3 TransactionBoundaryPort

Zodpovednosť:

```text
vykonať krátky konzistentný technický zápis
nepoužiť jednu transakciu pre celý PARTIAL RUN
nevrátiť rollbackom už potvrdené nezávislé vetvy
```

## 5.4 ReferenceGeneratorPort

Generuje technicky jednoznačné referencie pre:

```text
derivation_reference
branch_reference
dependency_reference
```

`request_reference` a `response_target_reference` preberá zo vstupu. Generátor ich nesmie svojvoľne nahradiť.

## 5.5 ClockPort

Poskytuje čas metodických a technických záznamov bez priamej závislosti domény od systémových globálnych funkcií.

---

# 6. Poradie vykonania služby

```text
1. prijať DerivationApplicationInput
2. overiť technickú zostaviteľnosť vstupného objektu
3. vytvoriť derivation_reference a čas začatia
4. cez DerivationHistoryPort založiť historický pokus
5. zavolať doménovú kontrolu ATOMIC_INPUT_GATE
6. pri zlyhaní brány uložiť STOPPED_AT_GATE a vrátiť výsledok bez vetiev
7. pri úspechu brány identifikovať SUBJECT_MANIFESTATION vetvy
8. pre každú vetvu určiť citovateľné BRANCH_DEPENDENCY
9. zablokovanú vetvu ukončiť ako BLOCKED_BY_DEPENDENCY
10. nezablokovanú vetvu odovzdať doménovému algoritmu
11. uložiť výsledok každej vetvy v samostatnej krátkej transakcii
12. uložiť iba úplné QUESTION_CANDIDATE
13. po ukončení všetkých vetiev zavolať jedinú agregáciu run_state
14. uložiť finálny súhrn behu
15. vrátiť DerivationRunResult s pôvodnou koreláciou požiadavky a cieľa návratu
```

Krok 4 musí predchádzať kroku 5. Bez historicky založeného pokusu sa vstupná kontrola nesmie začať.

---

# 7. Transakčná stratégia

Odporúčaná technická hranica:

```text
T1
= založenie historického pokusu

T2
= ukončenie na vstupnej bráne, ak brána zlyhala

TB[n]
= výsledok jednej kandidátskej vetvy a prípadný kandidát

TF
= finálny súhrn behu
```

Platí:

```text
T1 ≠ jedna transakcia celého behu
TB[1] ≠ TB[2] ≠ ... ≠ TB[n]
```

Ak neskoršia vetva zlyhá, už potvrdená `TB[n-1]` sa nesmie rollbackom vymazať.

Technická transakcia však musí zabezpečiť, aby sa kandidát neuložil bez svojho vetvového výsledku a auditnej stopy.

---

# 8. Chybové kanály

## 8.1 Metodický výsledok

Nasledujúce stavy sú riadnym návratovým výsledkom služby:

```text
STOPPED_AT_GATE
CANDIDATE_CREATED
BRANCH_STOPPED
RETURNED_FOR_DECOMPOSITION
BLOCKED_BY_DEPENDENCY
nadradené run_state podľa Validovaného agregačného pravidla
```

## 8.2 Technická chyba infraštruktúry

Príklady:

```text
nedostupné úložisko
chyba transakcie
porucha generovania referencie
chyba zápisu auditu
```

Technická chyba sa nesmie preložiť na metodický stav ani odpoveď `0`.

Služba ju oznamuje osobitným technickým chybovým kanálom, napríklad výnimkou aplikačnej vrstvy. Ak už boli niektoré vetvy uložené, ich história zostáva zachovaná.

## 8.3 Porušenie invariantu

Ak doménový spolupracovník vráti výsledok odporujúci kontraktu, napríklad kandidáta v stave `BRANCH_STOPPED`, služba musí operáciu označiť ako technické porušenie invariantu. Nesmie taký výsledok opravovať domýšľaním.

---

# 9. Korelácia a opakovanie požiadavky

```text
REQUEST_REFERENCE
= korelačný odkaz
≠ automaticky idempotency key
```

Tento návrh neurčuje, či opakované predloženie rovnakého `REQUEST_REFERENCE` vráti pôvodný beh, založí nový pokus alebo bude odmietnuté.

To je samostatné technické rozhodnutie, ktoré musí byť odvodené pred návrhom repository a API. Nesmie sa potichu vyriešiť databázovým unikátnym indexom.

---

# 10. Nezávislosť od vstupného kanála

Rovnakú službu musí byť možné zavolať z:

```text
webového controllera
API controllera
CLI príkazu
automatického testu
budúceho AI agenta
```

Žiadny vstupný kanál nesmie mať vlastnú paralelnú implementáciu doménového algoritmu alebo agregácie `run_state`.

---

# 11. Odporúčané technické členenie

```text
codei/app/Application/QuestionDerivation/
├── QuestionDerivationApplicationService.php
├── Data/
│   ├── DerivationApplicationInput.php
│   ├── DerivationRunResult.php
│   ├── CandidateBranchResult.php
│   └── BranchDependencyData.php
└── Contracts/
    ├── DerivationHistoryPort.php
    ├── QuestionCandidatePort.php
    ├── TransactionBoundaryPort.php
    ├── ReferenceGeneratorPort.php
    └── ClockPort.php

codei/app/Domain/QuestionDerivation/
└── QuestionDerivationAlgorithm.php
```

Toto je technické členenie zodpovedností, nie pokyn na okamžité vytvorenie všetkých súborov.

---

# 12. Testovací kontrakt služby

Minimálne unit a aplikačné testy musia overiť:

```text
1. historický pokus vznikne pred vstupnou kontrolou
2. zlyhaná brána nevytvorí vetvu ani kandidáta
3. úspešná vetva zostane zachovaná po zlyhaní inej vetvy
4. zlyhaná vetva sa neuloží ako QUESTION_CANDIDATE
5. BLOCKED_BY_DEPENDENCY vyžaduje citovateľnú závislosť
6. zlyhanie jednej vetvy nevytvorí automaticky závislosť ostatných
7. agregácia každej kombinácie vetvových stavov vráti práve jeden run_state
8. výsledok zachová REQUEST_REFERENCE a RESPONSE_TARGET_REFERENCE
9. technická chyba sa nezmení na odpoveď 0 ani metodický stav
10. controller alebo CLI nie sú potrebné na vykonanie testu služby
```

---

# 13. Čo tento návrh vedome neurčuje

```text
konkrétne databázové tabuľky
primárne a cudzie kľúče
CodeIgniter Models
konkrétne repository adaptéry
HTTP route
HTTP status kódy
JSON formát
UI formulár
asynchrónne fronty
paralelné vykonávanie vetiev
idempotenciu opakovanej požiadavky
```

Tieto rozhodnutia sa musia odvodiť z Validovaného kontraktu a tohto návrhu v samostatných technických krokoch.

---

# 14. Kontrola zachovania Validovaného kontraktu

```text
K1 — run_mode zostal PARTIAL_RUN_WITH_ATOMIC_GATE = 1
K2 — historický pokus vzniká pred bránou = 1
K3 — korelácia požiadavky a cieľa návratu je zachovaná = 1
K4 — vetvové závislosti sa nedomýšľajú technickou vrstvou = 1
K5 — každá vetva má vlastný výsledok a transakčnú hranicu = 1
K6 — úspešné nezávislé vetvy sa pri neskoršom zlyhaní nemažú = 1
K7 — neúspešná vetva sa neukladá ako kandidát = 1
K8 — run_state agreguje jediný doménový spolupracovník = 1
K9 — technická chyba je oddelená od metodického výsledku = 1
K10 — návrh nepredbieha repository, SQL, controller ani API = 1
```

Výsledok pracovnej kontroly:

```text
TECHNICAL_DERIVATION_CHECK
=
PASSED
```

Tento výsledok nie je samostatnou autoritatívnou Validáciou implementácie. Potvrdzuje iba, že technický návrh pri svojom odvodení nezmenil Validovaný aplikačný kontrakt.

---

# 15. Nasledujúci logický krok

```text
formálne Validovať technický návrh aplikačnej služby
→ rozhodnúť technickú politiku opakovanej REQUEST_REFERENCE
→ odvodiť repository kontrakty a model technického uloženia
→ až potom navrhnúť migrácie, adaptéry, controller a API
```