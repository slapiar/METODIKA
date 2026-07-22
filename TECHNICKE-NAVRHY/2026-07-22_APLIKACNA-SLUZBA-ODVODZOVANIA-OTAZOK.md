# Technický návrh aplikačnej služby odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument technicky odvodzuje aplikačnú službu z Validovaného aplikačného kontraktu a z Validovanej politiky opakovanej požiadavky:

```text
postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
+
TECHNICKE-NAVRHY/2026-07-22_POLITIKA-OPAKOVANEJ-REQUEST-REFERENCE.md
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
IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE
```

Neurčuje ešte SQL schému, migrácie, CodeIgniter Model, controller, HTTP route ani JSON odpoveď.

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

Jediný verejný prípad použitia:

```php
public function derive(
    DerivationApplicationInput $input
): DerivationRunResult;
```

Názvy sú pracovné technické označenia. Nemenia identitu metodického úkonu `QUESTION_DERIVATION`.

---

# 2. Zodpovednosť služby

Služba koordinuje:

```text
prijatie aplikačného vstupu
→ kontrolu replay identity požiadavky
→ pri FIRST_ACCEPTANCE založenie historického pokusu
→ atómovú vstupnú bránu
→ vytvorenie kandidátskych vetiev
→ spracovanie vetiev a ich závislostí
→ zachovanie čiastkových výsledkov
→ deterministickú agregáciu run_state
→ vrátenie DERIVATION_RUN_RESULT
```

Služba smie:

```text
koordinovať poradie krokov
volať RequestReplayGuard
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
prepisovať výsledky ukončených nezávislých vetiev
meniť run_mode podľa konfigurácie
založiť nový QUESTION_DERIVATION pri REPLAY_EXISTING_RUN
preložiť REQUEST_REFERENCE_CONFLICT na metodický stav
```

---

# 3. Technické dátové objekty

```text
DerivationApplicationInput
DerivationRunResult
CandidateBranchResult
BranchDependencyData
RequestReplayDecision
```

## 3.1 DerivationApplicationInput

Musí reprezentovať najmenej:

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

Objekt môže overiť iba technickú úplnosť a typovú konzistenciu. Nevykonáva metodickú Validáciu.

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

Musí zachovať celý Validovaný význam `BRANCH_DEPENDENCY`, nie iba dvojicu identifikátorov.

## 3.5 RequestReplayDecision

```text
REQUEST_REPLAY_DECISION
{
    request_reference,
    payload_fingerprint,
    decision,
    derivation_reference?,
    existing_run_state?,
    conflict_reason?
}
```

Pracovné rozhodnutia:

```text
FIRST_ACCEPTANCE
REPLAY_EXISTING_RUN
REQUEST_REFERENCE_CONFLICT
```

Nie sú metodickými stavmi.

---

# 4. Spolupracujúce zodpovednosti

## 4.1 QuestionDerivationAlgorithm

Doménový spolupracovník vykonáva jedinú implementáciu významových pravidiel:

```text
validateAtomicGate(input)
identifySubjectManifestations(input)
evaluateBranchDependencies(branch, completedBranchResults)
deriveCandidateBranch(run, manifestation, dependencies)
aggregateRunState(gateResult, branchResults)
```

Aplikačná služba, controller ani repository nesmú algoritmus alebo agregáciu kopírovať.

## 4.2 RequestReplayGuard

Technický spolupracovník vykonáva:

```text
kanonizáciu kontraktového vstupu
výpočet REQUEST_PAYLOAD_FINGERPRINT
rezerváciu REQUEST_REFERENCE
vyhľadanie existujúcej väzby
rozlíšenie FIRST_ACCEPTANCE, REPLAY_EXISTING_RUN a CONFLICT
```

Nesmie vykonávať doménový algoritmus, určovať Autoritu ani zakladať nový pokus pri replay.

---

# 5. Technické porty

## 5.1 RequestReferenceRepositoryPort

Zodpovednosť:

```text
atómovo rezervovať REQUEST_REFERENCE
uložiť payload_fingerprint a derivation_reference
nájsť rezerváciu podľa REQUEST_REFERENCE
načítať stav existujúceho behu
načítať existujúci DERIVATION_RUN_RESULT, ak je ukončený
rozlíšiť úspešnú rezerváciu od už existujúcej rezervácie
```

Repository nesmie rozhodovať, či vzniká nový metodický pokus. Toto rozhodnutie určuje Validovaná replay politika.

## 5.2 DerivationHistoryPort

```text
založiť pokus pred vstupnou bránou
uložiť výsledok brány
uložiť výsledok každej vetvy
uložiť finálny súhrn behu
zachovať auditnú stopu
```

## 5.3 QuestionCandidatePort

Uloží iba úplný `QUESTION_CANDIDATE` spolu s väzbou na vetvu a beh.

## 5.4 TransactionBoundaryPort

Zabezpečí krátke technické transakcie a nesmie jedným rollbackom vymazať výsledky ukončených nezávislých vetiev.

## 5.5 ReferenceGeneratorPort

Generuje `derivation_reference`, `branch_reference` a `dependency_reference`. Nesmie nahrádzať vstupnú `request_reference`.

## 5.6 ClockPort

Poskytuje čas záznamov bez priamej závislosti domény od globálnych systémových funkcií.

---

# 6. Poradie vykonania služby

```text
1. prijať DerivationApplicationInput
2. overiť technickú zostaviteľnosť vstupu
3. cez RequestReplayGuard vypočítať fingerprint a získať replay decision
4. ak REQUEST_REFERENCE_CONFLICT → ukončiť technickým konfliktom bez metodického výsledku
5. ak REPLAY_EXISTING_RUN a beh je ukončený → vrátiť existujúci DERIVATION_RUN_RESULT
6. ak REPLAY_EXISTING_RUN a beh je rozpracovaný → priradiť sa k existujúcej derivation_reference; nezaložiť nový beh
7. iba pri FIRST_ACCEPTANCE vytvoriť derivation_reference a čas začatia
8. v jednej krátkej transakcii rezervovať referenciu a založiť historický QUESTION_DERIVATION
9. až potom vykonať ATOMIC_INPUT_GATE
10. pri zlyhaní brány uložiť STOPPED_AT_GATE a vrátiť výsledok bez vetiev
11. pri úspechu identifikovať SUBJECT_MANIFESTATION vetvy
12. pre každú vetvu určiť citovateľné BRANCH_DEPENDENCY
13. zablokovanú vetvu ukončiť ako BLOCKED_BY_DEPENDENCY
14. nezablokovanú vetvu odovzdať doménovému algoritmu
15. výsledok každej vetvy uložiť v samostatnej krátkej transakcii
16. uložiť iba úplné QUESTION_CANDIDATE
17. po ukončení vetiev zavolať jedinú agregáciu run_state
18. uložiť finálny súhrn
19. vrátiť DerivationRunResult s pôvodnou koreláciou
```

Kroky 3 až 8 tvoria technickú ochranu identity požiadavky. Replay nie je ďalší metodický pokus a preto nevytvára nový historický záznam `QUESTION_DERIVATION`.

---

# 7. Transakčné hranice

```text
TR
= rezervácia REQUEST_REFERENCE + fingerprint + derivation_reference + založenie historického pokusu

TG
= ukončenie na vstupnej bráne

TB[n]
= výsledok jednej vetvy a prípadný kandidát

TF
= finálny súhrn behu
```

`TR` musí byť atómová voči súbežnému prvému prijatiu. Iba jeden technický tok smie vytvoriť väzbu jednej `REQUEST_REFERENCE` na jednu `derivation_reference`.

```text
TR ≠ transakcia celého PARTIAL RUN
TB[1] ≠ TB[2] ≠ ... ≠ TB[n]
```

---

# 8. Chybové kanály

## 8.1 Metodické výsledky

```text
STOPPED_AT_GATE
CANDIDATE_CREATED
BRANCH_STOPPED
RETURNED_FOR_DECOMPOSITION
BLOCKED_BY_DEPENDENCY
nadradené run_state
```

## 8.2 Replay výsledky

```text
FIRST_ACCEPTANCE
REPLAY_EXISTING_RUN
```

Sú technickým riadením korelácie, nie metodickým výsledkom.

## 8.3 Konflikt identity

```text
REQUEST_REFERENCE_CONFLICT
```

Je technické porušenie invariantu. Nesmie sa preložiť na odpoveď `0`, `STOPPED_AT_GATE` ani nový beh.

## 8.4 Infraštruktúrna chyba

Nedostupné úložisko, chyba transakcie alebo generovania referencie sa oznamujú samostatným technickým kanálom. Už uložené vetvy zostávajú zachované.

---

# 9. Nezávislosť od vstupného kanála

Rovnakú službu používa web, API, CLI, automatický test aj budúci AI agent. Žiadny kanál nesmie implementovať vlastný replay guard, doménový algoritmus ani agregáciu `run_state`.

---

# 10. Odporúčané technické členenie

```text
codei/app/Application/QuestionDerivation/
├── QuestionDerivationApplicationService.php
├── RequestReplayGuard.php
├── Data/
│   ├── DerivationApplicationInput.php
│   ├── DerivationRunResult.php
│   ├── CandidateBranchResult.php
│   ├── BranchDependencyData.php
│   └── RequestReplayDecision.php
└── Contracts/
    ├── RequestReferenceRepositoryPort.php
    ├── DerivationHistoryPort.php
    ├── QuestionCandidatePort.php
    ├── TransactionBoundaryPort.php
    ├── ReferenceGeneratorPort.php
    └── ClockPort.php

codei/app/Domain/QuestionDerivation/
└── QuestionDerivationAlgorithm.php
```

Toto je návrh zodpovedností, nie pokyn na okamžitú implementáciu súborov.

---

# 11. Testovací kontrakt služby

```text
1. prvé prijatie rezervuje referenciu a založí práve jeden beh,
2. historický pokus vznikne pred vstupnou bránou,
3. replay rovnakého obsahu nezaloží nový pokus,
4. replay ukončeného behu vráti existujúci výsledok,
5. replay rozpracovaného behu nevytvorí paralelný beh,
6. rovnaká referencia s odlišným obsahom skončí konfliktom,
7. súbežné prvé prijatie nevytvorí dve derivation_reference,
8. zlyhaná brána nevytvorí vetvu ani kandidáta,
9. úspešná vetva zostane zachovaná po zlyhaní inej vetvy,
10. zlyhaná vetva sa neuloží ako QUESTION_CANDIDATE,
11. BLOCKED_BY_DEPENDENCY vyžaduje citovateľnú závislosť,
12. agregácia každej kombinácie vetvových stavov vráti práve jeden run_state,
13. technický replay, konflikt ani chyba sa nezmenia na metodickú odpoveď 0,
14. službu možno testovať bez controllera a UI.
```

---

# 12. Čo návrh vedome neurčuje

```text
konkrétne databázové tabuľky
primárne a cudzie kľúče
unikátny index
CodeIgniter Models
repository adaptér
HTTP route a status kódy
JSON formát
obnovu prerušeného behu
vzťah medzi rozdielnymi REQUEST_REFERENCE
```

---

# 13. Kontrola zachovania zdrojov

```text
K1 — Validovaný aplikačný kontrakt zostal nezmenený = 1
K2 — replay politika IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE je premietnutá = 1
K3 — iba FIRST_ACCEPTANCE zakladá nový QUESTION_DERIVATION = 1
K4 — replay a konflikt sú oddelené od metodických stavov = 1
K5 — historický pokus vzniká pred bránou = 1
K6 — PARTIAL RUN a vetvové transakcie zostali zachované = 1
K7 — návrh nepredbieha SQL, adaptéry ani API = 1
```

```text
TECHNICAL_DERIVATION_CHECK
=
PASSED
```

---

# 14. Nasledujúci logický krok

```text
Validovať aktualizovanú technickú hranicu služby spolu s repository kontraktom
→ až po úspešnej Validácii odvodiť technický model uloženia
→ následne navrhnúť migrácie a repository adaptér
→ až potom controller a API replay správanie
```
