# Plán vývoja webovej stránky METODIKA

Dátum: 2026-07-27

## Účel plánovanej webovej stránky

Webová stránka METODIKA má byť pracovným rozhraním medzi človekom, projektom a metodickou analýzou.

Jej úlohou nie je iba zobraziť dokumenty z repozitára. Má umožniť, aby sa s projektom dalo pracovať ako so živým predmetom skúmania: vybrať ho, načítať jeho známy stav, spustiť nad ním pripravené analýzy, zaznamenať priebeh a bezpečne rozhodnúť, či je pripravený na ďalší pohyb.

Stránka má človeku ušetriť opakované vysvetľovanie, ručné dohľadávanie súvislostí a chaotické prepínanie medzi dokumentmi, repozitárom, AI rozhovorom a databázou. Má vytvoriť jedno miesto, kde bude vidieť, čo sa skúma, prečo sa to skúma, čo už je potvrdené, čo je otvorené a aký je nasledujúci rozumný krok.

## Technické východisko

Aktuálna stránka už beží na:

```text
Framework: CodeIgniter 4
Databáza: MariaDB 11.8.8-MariaDB-log
PHP: 8.4
Web server: Apache
Repozitár: slapiar/METODIKA
Technický koreň: /codei
Zdroj projektov: /PROJEKTY/ZoznamProjektov.md
```

Existujúce diagnostické a GATE mechanizmy sa nebudú zahadzovať. Nová webová stránka ich má postupne premeniť z technickej diagnostiky na použiteľné pracovné rozhranie pre metodickú analýzu projektov.

## Hlavná predstava používateľského toku

Používateľ príde na stránku a najprv uvidí zoznam dostupných projektov.

Zoznam sa nebude písať ručne do view. Bude sa odvádzať z projektového registra v `/PROJEKTY/ZoznamProjektov.md`. Každý projekt bude mať minimálne názov, stav, dostupné zdroje, technické alebo metodické väzby a informáciu, či je pripravený na analýzu.

Po výbere projektu stránka zobrazí jeho pracovnú kartu. Tá má človeku rýchlo povedať:

- čo je to za projekt,
- kde má svoje zdroje,
- aký je jeho aktuálny stav,
- ktoré metodické súbory s ním súvisia,
- ktoré analýzy možno spustiť,
- ktoré výsledky už existujú,
- a či sú otvorené nejaké riziká alebo otázky.

Následne používateľ spustí analýzu. Analýza nesmie byť čierna skrinka. Každý jej významný priechod sa musí zapísať do registra brán, krokov a dôkazov.

## Základné časti webovej stránky

### 1. Katalóg projektov

Katalóg projektov bude čítacia vrstva nad `/PROJEKTY/ZoznamProjektov.md`.

Jeho úlohou je vytvoriť z textového registra použiteľný zoznam pre web:

```text
projektový slug
názov projektu
repozitár
produkcia alebo stage, ak existujú
aktuálny stav
hlavné moduly
metodické súbory
technické súbory
poznámky
stav pripravenosti na analýzu
```

Na začiatku stačí bezpečný parser existujúcej štruktúry. Neskôr môže byť projektový register doplnený o samostatné projektové manifesty, napríklad `/PROJEKTY/METODIKA/project.yaml`.

### 2. Projektová karta

Projektová karta bude hlavné miesto, kde sa človek po výbere projektu zorientuje.

Má obsahovať:

- názov a stručný popis projektu,
- prehľad zdrojov,
- aktuálny metodický stav,
- technický stav, ak je známy,
- dostupné analýzy,
- posledné výsledky,
- otvorené otázky,
- tlačidlo na spustenie novej analýzy.

Projektová karta nemá byť zahltená internými detailmi. Detailné dôkazy majú byť dostupné cez rozkliknutie, nie natlačené do prvého pohľadu.

### 3. Analytický beh

Analytický beh je konkrétne spustenie metodickej analýzy nad jedným projektom.

Každý beh musí mať vlastnú identitu:

```text
analysisRunId
projectSlug
startedAt
startedBy
analysisProfile
state
currentGateState
finishedAt
summaryResult
```

Analýza môže mať viac vrstiev. Na začiatok stačí pripraviť rámec pre tieto základné typy:

```text
PROJECT_OVERVIEW
SOURCE_COMPLETENESS
METHODOLOGY_ALIGNMENT
GATE_READINESS
OPEN_RISKS
NEXT_STEP_RECOMMENDATION
```

Dôležité je, že stránka nemusí hneď vedieť všetko dokonale. Musí však od začiatku správne zapisovať, čo urobila, z čoho vychádzala a čo označila za výsledok.

### 4. Register brán

Register brán bude srdce celej stránky.

Jeho úlohou nie je brzdiť tvorbu, ale chrániť ju pred stratou súvislosti. Každý analytický beh má mať jasne viditeľné, či je pripravený pokračovať, alebo či mu chýba podmienka.

Pre každý beh sa budú zapisovať minimálne tieto osi:

```text
GATE_PROJECT_LOADED
GATE_SOURCE_AVAILABLE
GATE_METHOD_FILES_AVAILABLE
GATE_ANALYSIS_PROFILE_SELECTED
GATE_EVIDENCE_ATTACHED
GATE_RESULT_WRITTEN
GATE_NEXT_STEP_DEFINED
```

Každá brána má mať stav:

```text
OPEN
CLOSED
BLOCKED
PASSED
FAILED
SKIPPED_WITH_REASON
```

A pri každej bráne musí byť možné uložiť dôkaz alebo dôvod:

```text
gateId
analysisRunId
gateCode
state
reason
sourceReference
evidenceId
createdAt
updatedAt
```

### 5. Evidencia dôkazov

Výsledok analýzy nesmie stáť iba na vete „AI si myslí“. Preto musí mať stránka samostatnú evidenciu dôkazov.

Dôkaz môže byť:

- cesta k súboru v repozitári,
- konkrétny riadok alebo rozsah riadkov,
- commit,
- databázový záznam,
- výstup testu,
- rozhodnutie Autority,
- poznámka používateľa,
- alebo výsledok predchádzajúceho behu.

Každý dôkaz musí niesť informáciu, k čomu patrí a akú váhu má.

### 6. Výsledok analýzy

Výsledok analýzy má byť čitateľný pre človeka.

Nemá to byť iba JSON. Stránka má zobraziť krátke zhrnutie:

```text
čo bolo skúmané
čo bolo potvrdené
čo chýba
čo je riziko
čo je odporúčaný ďalší krok
```

Pod tým môže byť technický detail pre vývojára a AI agenta.

## Prvá vývojová verzia

Prvá praktická verzia webovej stránky má byť malá, ale použiteľná.

Má obsahovať:

1. route pre zoznam projektov,
2. service na načítanie `/PROJEKTY/ZoznamProjektov.md`,
3. jednoduchý parser projektového registra,
4. stránku so zoznamom projektov,
5. detail vybraného projektu,
6. tlačidlo „Spustiť analýzu“,
7. založenie analytického behu v databáze,
8. zápis úvodných GATE stavov,
9. jednoduchý výsledok analýzy,
10. detail behu s bránami a dôkazmi.

Prvá verzia nemusí robiť hlbokú AI analýzu. Najprv musí správne prejsť tok:

```text
zoznam projektov
→ výber projektu
→ načítanie projektu
→ založenie analýzy
→ zápis brán
→ zobrazenie výsledku
```

Až potom má zmysel rozširovať samotnú hĺbku analýz.

## Navrhované technické moduly v CI4

### Controllers

```text
ProjectCatalogController
ProjectDetailController
ProjectAnalysisController
AnalysisRunController
GateRegisterController
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
analysis/run.php
analysis/result.php
gates/_gate_table.php
evidence/_evidence_list.php
```

### Routes

Predbežný návrh ciest:

```text
GET  /projects
GET  /projects/{slug}
POST /projects/{slug}/analysis/start
GET  /analysis/{runId}
GET  /analysis/{runId}/result
GET  /analysis/{runId}/gates
GET  /analysis/{runId}/evidence
```

Interné API môže vzniknúť až po potvrdení prvého stránkového toku. Najprv má fungovať človekom použiteľná stránka.

## Predbežný databázový model

Minimálny databázový základ:

```text
projects_snapshot
- id
- slug
- name
- repository
- production_url
- stage_url
- current_state
- source_path
- raw_block
- parsed_json
- created_at

analysis_runs
- id
- project_slug
- profile
- state
- summary
- started_at
- finished_at
- created_by

analysis_gates
- id
- analysis_run_id
- gate_code
- state
- reason
- source_reference
- evidence_id
- created_at
- updated_at

analysis_evidence
- id
- analysis_run_id
- evidence_type
- title
- source_reference
- content_excerpt
- weight
- created_at

analysis_results
- id
- analysis_run_id
- result_type
- human_summary
- technical_payload
- next_step
- created_at
```

## Vývojové poradie

Praktické poradie vývoja:

```text
1. načítať a zobraziť katalóg projektov
2. vytvoriť detail projektu
3. založiť databázový záznam analytického behu
4. zapísať prvé GATE stavy
5. zobraziť detail behu
6. doplniť evidenciu dôkazov
7. doplniť jednoduchý analytický výsledok
8. pripraviť demo stránku pre projekt METODIKA
9. pripraviť rovnaký tok pre ďalší projekt zo zoznamu
10. až potom rozšíriť hĺbku analýz
```

Pravidlo je jednoduché: najprv priechodný tok, potom inteligencia.

## Demo stránka

Demo stránka má ukázať celý pracovný tok na jednom bezpečnom projekte.

Najvhodnejší prvý demo projekt je `METODIKA`, pretože má najviac vlastných metodických a technických súborov priamo v tomto repozitári.

Demo má ukázať:

```text
výber projektu METODIKA
načítanie jeho registračného bloku
zobrazenie projektovej karty
spustenie analýzy
zápis GATE stavov
zobrazenie výsledku
odkaz na dôkazy
určenie ďalšieho kroku
```

Demo nesmie byť iba maketa. Musí používať rovnaké služby a modely ako budúci ostrý tok.

## Hranice prvej verzie

Do prvej verzie nepatrí:

- veľká vizuálna nadstavba,
- automatické prepisovanie projektových dokumentov,
- autonómne rozhodovanie AI bez potvrdenia človeka,
- produkčné zmeny mimo webovej analytickej vrstvy,
- napájanie na cudzie repozitáre bez samostatného bezpečného čítacieho plánu.

Do prvej verzie patrí:

- čitateľný katalóg,
- spoľahlivé načítanie projektového registra,
- základný analytický beh,
- registračný zápis brán,
- evidencia dôkazov,
- ľudsky zrozumiteľný výsledok,
- demo nad projektom METODIKA.

## Kritérium úspechu prvej verzie

Prvá verzia je úspešná, keď človek vie v prehliadači urobiť toto:

```text
otvoriť /projects
vybrať METODIKA
vidieť projektovú kartu
spustiť analýzu
vidieť vzniknutý analysisRunId
vidieť tabuľku GATE stavov
vidieť zoznam dôkazov
vidieť krátke odporúčanie ďalšieho kroku
```

Ak toto funguje, máme základ živého metodického pracoviska. Nie dokonalý systém, ale pevný most medzi repozitárom, databázou, človekom a AI.

## Záverečné rozhodnutie plánu

Tento plán otvára vývoj webovej stránky METODIKA ako samostatnej aplikačnej vrstvy nad existujúcim CodeIgniter jadrom.

Prvým praktickým cieľom je stránka katalógu projektov a demo analýza projektu METODIKA.

```text
NEXT_STEP=IMPLEMENTOVAŤ_KATALÓG_PROJEKTOV_A_DEMO_ANALÝZU_METODIKA
```
