# Plán vývoja webovej stránky METODIKA

Dátum a čas: 2026-07-27 16:11 Europe/Bratislava

Stav: PRACOVNÝ — ZÁVÄZNÝ PRE NADVÄZUJÚCI VÝVOJ WEBOVEJ STRÁNKY

---

## Východisko

Tento plán nezačína projekt nanovo.

Nadväzuje na ukončený plán Krokov 1 až 15, v ktorom bola pripravená a uzavretá testovacia sústava, produkčný stav bol potvrdený Autoritou a ďalším povoleným smerom sa stala tvorba webovej stránky s predpripravenými testami.

Existujúce východisko:

```text
REPOZITÁR=slapiar/METODIKA
VETVA=main
APLIKAČNÝ_KOREŇ=/codei
FRAMEWORK=CodeIgniter 4
DATABÁZA=MariaDB 11.8.8-MariaDB-log
PHP=8.4
SERVER=Apache
ZDROJ_PROJEKTOV=/PROJEKTY/ZoznamProjektov.md
DOKUMENTAČNÁ_ZLOŽKA=/DOC
PLÁNOVACIA_ZLOŽKA=/postupy/PLAN
```

Zložka `/DOC` slúži iba na vývoj dokumentácie a kapitol dokumentácie. Plány vývoja sa ukladajú výhradne do `/postupy/PLAN/` s názvom začínajúcim dátumom a časom.

---

## Účel webovej stránky

Webová stránka má byť pracovným rozhraním, cez ktoré sa bude dať metodicky analyzovať vybraný projekt.

Používateľ nemá ručne hľadať, ktorý projekt je dostupný, ktoré súbory s ním súvisia, aký má stav a ktoré analýzy možno spustiť. Stránka má tieto informácie načítať zo štruktúry repozitára a pripraviť ich do použiteľného pracovného pohľadu.

Základný účel:

```text
vybrať projekt
→ načítať jeho registrované údaje
→ zobraziť projektovú kartu
→ ponúknuť predpripravené testy a analýzy
→ spustiť analýzu
→ zapisovať priebeh do registrov brán
→ zobraziť výsledok a ďalší krok
```

Tento web nemá byť novou metodikou. Má byť aplikačným rozhraním už vytvorenej metodiky.

---

## Rozsah prvej etapy

Prvá etapa má postaviť priechodný a overiteľný tok nad existujúcim CodeIgniter jadrom.

Do prvej etapy patrí:

1. stránka so zoznamom projektov,
2. načítanie projektov zo súboru `/PROJEKTY/ZoznamProjektov.md`,
3. detail vybraného projektu,
4. výber predpripraveného testu alebo analýzy,
5. založenie analytického behu,
6. zápis GATE stavov,
7. evidencia dôkazov,
8. zobrazenie výsledku analýzy,
9. demo nad projektom `METODIKA`.

Do prvej etapy nepatrí:

- prepisovanie dokumentácie v `/DOC`,
- prepisovanie metodických dokumentov bez samostatného plánu,
- autonómne rozhodovanie AI bez potvrdenia Autority,
- napájanie na cudzie repozitáre bez osobitného čítacieho mechanizmu,
- veľká vizuálna nadstavba pred funkčným tokom.

---

## Zdroj projektov

Autoritatívnym zdrojom pre zoznam projektov je:

```text
/PROJEKTY/ZoznamProjektov.md
```

Prvá verzia nebude vyžadovať nové projektové manifesty. Bude čítať existujúci zoznam projektov a vytvorí z neho katalóg.

Minimálne údaje projektu:

```text
slug
názov
repozitár
produkcia
stage
aktuálny stav
hlavné moduly
metodické súbory
technické súbory
poznámky
```

Ak niektorý údaj v zdroji chýba, stránka ho neuhádne. Zobrazí ho ako nezadaný alebo neúplný.

---

## Predpripravené testy a analýzy

Predpripravené testy nie sú školské otázky. Sú to pracovné metodické kontroly, ktoré majú pomôcť rozhodnúť, či je projekt pripravený na ďalší pohyb.

Prvá sada analýz:

```text
PROJECT_OVERVIEW
SOURCE_COMPLETENESS
METHODOLOGY_ALIGNMENT
GATE_READINESS
OPEN_RISKS
NEXT_STEP_RECOMMENDATION
```

Význam:

- `PROJECT_OVERVIEW` — základná orientácia v projekte,
- `SOURCE_COMPLETENESS` — kontrola dostupnosti zdrojov,
- `METHODOLOGY_ALIGNMENT` — kontrola väzby na metodické súbory,
- `GATE_READINESS` — pripravenosť na ďalší pracovný krok,
- `OPEN_RISKS` — zachytenie otvorených rizík,
- `NEXT_STEP_RECOMMENDATION` — návrh najbližšieho vecného kroku.

---

## Register brán

Každý analytický beh musí zapisovať stav brán.

Minimálne brány prvej verzie:

```text
GATE_PROJECT_SELECTED
GATE_PROJECT_LOADED
GATE_SOURCE_AVAILABLE
GATE_ANALYSIS_SELECTED
GATE_EVIDENCE_REGISTERED
GATE_RESULT_WRITTEN
GATE_NEXT_STEP_DEFINED
```

Povolené stavy:

```text
OPEN
CLOSED
BLOCKED
PASSED
FAILED
SKIPPED_WITH_REASON
```

Každá brána musí mať dôvod alebo dôkaz. Ak dôkaz nie je dostupný, nesmie sa nahradiť domnienkou.

---

## Evidencia dôkazov

Analytická stránka musí vedieť uložiť dôkaz, z ktorého vychádza výsledok.

Dôkaz môže byť:

- cesta k súboru,
- časť projektového registra,
- odkaz na metodický dokument,
- databázový záznam,
- výsledok predchádzajúceho behu,
- rozhodnutie Autority,
- používateľská poznámka.

Každý dôkaz musí byť naviazaný na konkrétny analytický beh a podľa možnosti aj na konkrétnu bránu.

---

## Navrhované používateľské stránky

Prvá verzia má obsahovať tieto stránky:

```text
/projects
/projects/{slug}
/projects/{slug}/analysis
/analysis/{runId}
/analysis/{runId}/result
```

Význam:

- `/projects` — katalóg projektov,
- `/projects/{slug}` — karta projektu,
- `/projects/{slug}/analysis` — výber a spustenie analýzy,
- `/analysis/{runId}` — priebeh a brány behu,
- `/analysis/{runId}/result` — výsledok a ďalší krok.

---

## Navrhované technické časti v CodeIgniter

Technické názvy sú pracovné. Pred implementáciou sa majú zosúladiť s aktuálnou štruktúrou `/codei`.

### Controllers

```text
ProjectsController
ProjectAnalysisController
AnalysisRunController
```

### Services

```text
ProjectRegistryReader
ProjectRegistryParser
ProjectCatalogService
ProjectAnalysisService
GateRegisterService
EvidenceRegisterService
```

### Models

```text
ProjectSnapshotModel
AnalysisRunModel
AnalysisGateModel
AnalysisEvidenceModel
AnalysisResultModel
```

### Views

```text
projects/index.php
projects/show.php
projects/analysis.php
analysis/show.php
analysis/result.php
```

---

## Minimálny databázový model

Prvá verzia potrebuje minimálne tieto tabuľky:

```text
project_snapshots
analysis_runs
analysis_gates
analysis_evidence
analysis_results
```

### project_snapshots

```text
id
slug
name
repository
production_url
stage_url
current_state
source_path
raw_block
parsed_json
created_at
```

### analysis_runs

```text
id
project_slug
analysis_code
state
started_at
finished_at
created_by
summary
```

### analysis_gates

```text
id
analysis_run_id
gate_code
state
reason
evidence_id
created_at
updated_at
```

### analysis_evidence

```text
id
analysis_run_id
evidence_type
title
source_reference
content_excerpt
weight
created_at
```

### analysis_results

```text
id
analysis_run_id
result_type
human_summary
technical_payload
next_step
created_at
```

---

## Poradie vývoja

Praktické poradie:

```text
1. overiť aktuálnu štruktúru /codei
2. vytvoriť route /projects
3. vytvoriť ProjectRegistryReader
4. vytvoriť ProjectRegistryParser pre /PROJEKTY/ZoznamProjektov.md
5. zobraziť katalóg projektov
6. vytvoriť detail projektu
7. pripraviť databázové migrácie pre analytické behy
8. založiť prvý analysis_run
9. zapísať úvodné brány
10. zobraziť detail behu a brány
11. uložiť prvé dôkazy
12. zobraziť ľudský výsledok analýzy
13. pripraviť demo toku pre projekt METODIKA
```

---

## Kritérium úspechu prvej vývojovej etapy

Prvá etapa je splnená, keď používateľ vie v prehliadači urobiť tento tok:

```text
otvoriť /projects
→ vidieť zoznam projektov z /PROJEKTY/ZoznamProjektov.md
→ vybrať METODIKA
→ vidieť kartu projektu
→ vybrať predpripravenú analýzu
→ spustiť analytický beh
→ vidieť analysisRunId
→ vidieť GATE stavy
→ vidieť dôkazy
→ vidieť výsledok a ďalší krok
```

---

## Hranice a zákaz domýšľania

Ak pri implementácii nebude jasné, ako presne čítať niektorý projektový údaj, stránka ho nesmie vymyslieť.

Ak nebude jasné, ktorá tabuľka už existuje alebo aký model je správny, najprv sa načíta aktuálny stav `/codei`.

Ak bude treba meniť databázu, zmena sa urobí migráciou a až po potvrdení rozsahu.

---

## Najbližší povolený krok

```text
NEXT_ALLOWED_STEP=OVERIŤ_AKTUÁLNU_ŠTRUKTÚRU_CODEI_A_IMPLEMENTOVAŤ_ROUTE_PROJECTS
```
