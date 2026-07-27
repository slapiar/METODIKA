# INICIALIZÁCIA — Krok 11: Úplná lokálna a integračná Validácia

## Stav brány

```text
GATE=CLOSED
DÔVOD=FÁZA_11_A_ZASTAVENÁ_ROZHODOVACOU_BRÁNOU_AKTUÁLNY_PRODUKČNÝ_RUNTIME_A_STAV_MIGRÁCIÍ_NEOVERENÉ
```

## Záväzný pokyn používateľa

Pokračovanie Kroku 11 sa vedie výhradne v tomto pôvodnom súbore:

```text
postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md
```

Pre Krok 11 platí:

```text
NOVÝ_INI=false
NOVÝ_ENVIRONMENTÁLNY_TEST=false
```

Existujúce dôkazy z Krokov 7 až 10 sa znovu nevyrábajú. Môžu sa použiť po novom načítaní a overení ich kontinuity voči stabilnému aktuálnemu HEAD.

## Overenie predchádzajúceho kroku

```text
PREDCHÁDZAJÚCI_KROK=KROK_10
STAV=SPLNENÉ
FUNKČNÝ_COMMIT=c90ae562a859de9fe0b2174f39e924c7f7bc6a4e
CHECKPOINT=postupy/2026-07-25_09-05_CHECKPOINT-KROK-10-PRESNA-REZERVACIA.md
```

Dôkazy Krokov 7 až 10 zostávajú uložené a neboli zneplatnené samotným výpadkom internetového pripojenia.

## Zistený stav po obnovení pripojenia

Pôvodná konsolidácia tohto INI bola zapísaná commitom:

```text
ccbea1ac3ba9c67a0b7f46da4567189f63f79df7
```

Vtedy bol ako posledný skontrolovaný technický stav použitý `d418e72...`. Počas následnej kontroly však pokračoval súbežný vývoj supervízorského modulu a samostatného metodického pravidla.

Zachytená následnosť obsahovala okrem metodických commitov aj vykonateľné zmeny:

```text
4a87250b6bfa69305f465b0129dde311bf0771fd  Supervízor GATE po páde internetu
b925e9d9f7a2196f81079dc2ccc6ca0ed99c80c6  Supervisor modely init
d418e72c162bde324af7546c937af979bd75182e  Krok 8 Supervizora
a234cd224dea9674a20d6328b36f1815672eb359  Pridanie GATE do navig
4b99731f2150e4dfcb2bbe224955ce2137eafcc3  Oprava navig
0e423f400c02ae25987fea57c6e7d5ea996b3e54  Doplnenie route GateDashboard::index
```

Commit `0e423f4...` zmenil spoločný súbor:

```text
codei/app/Config/Routes.php
```

Doplnil samostatnú route supervízora a nemenil text existujúcich diagnostických routes. Napriek tomu ide o vykonateľný súbor zahrnutý v závislostiach Kroku 11, preto sa jeho kontinuita musí vyhodnotiť až nad stabilným HEAD.

Posledný vzdialený HEAD zachytený bezprostredne pred pôvodným STOP zápisom:

```text
91bed025c70d53f1413195c52cb9651dd50cc349
```

Počas pôvodnej kontroly sa `main` posúval opakovane. Preto vtedy nebolo možné pravdivo potvrdiť, že HEAD zostane stabilný medzi otvorením brány a prvým testom alebo funkčným zápisom.

## Súbežne vzniknuté INI záznamy

Počas samostatného metodického pracovného prúdu vznikli:

- `2026-07-25_12-16_INI_Kontrola_predchadzajucich_ukonov_a_obnova_autority.md`,
- `2026-07-25_12-23_INI_Obnova_metodickeho_pravidla_na_aktualnom_HEAD.md`,
- `2026-07-25_12-35_INI_Kontrola_predchadzajucich_ukonov_po_zmene_main.md`.

Tieto súbory patria samostatnému metodickému úkonu. Pre Krok 11 neotvárajú, nezatvárajú ani nenahrádzajú tento pôvodný INI.

Súbor:

```text
2026-07-25_10-33_INI_Krok_11_po_zmene_HEAD.md
```

zostáva pre Krok 11 prekonaným historickým medzistavom.

## Stav CHANGELOG

Prvý pokus o doplnenie `CHANGELOG.md` omylom nahradil iba načítanú časť. Neúplný zápis bol napravený obnovením celého pôvodného obsahu:

```text
OBNOVOVACÍ_COMMIT=ce0040a3799605e3c21565c290cb735078c6380a
OBNOVENÝ_BLOB=e4feda4d6732b17afcc40fa52586956eb7db5c58
STARŠIA_HISTÓRIA_ZACHOVANÁ=true
```

Pripravený nový konsolidačný obsah registra a changelogu nebol commitnutý, pretože sa autoritatívny `main` počas prípravy znovu zmenil. Tým sa zabránilo ďalšiemu neúplnému alebo zastaranému zápisu.

## Stav dôkazov bodov 4 až 6

### Bod 4 — prístupy

```text
BOD_4=ÁNO
```

Potvrdené zostávajú administrátorské oprávnenia, čítanie, zápis a read-back. Aktuálne metadata repozitára potvrdili `admin=true`, `push=true`, `pull=true`.

### Bod 5 — prostredie

```text
BOD_5=NEOVERENÉ
```

Historické praktické dôkazy naďalej potvrdzujú PHP 8.4, Composer 2, MariaDB 11.4/InnoDB, MySQLi, `pcntl_fork`, migrácie M1–M8, dve spojenia, rollback a cleanup. Nový environmentálny test sa podľa záväzného pokynu nevytvára.

Aktuálna verejná diagnostická URL je v repozitári zdokumentovaná, ale v dostupnom pracovnom spojení nebolo možné získať bezpečný aktuálny produkčný read-back. Preto zostávajú aktuálne praktické verzie produkčného PHP, databázy a stav migrácií `NEZISTENÉ`; historický dôkaz sa nevydáva za súčasný stav.

### Bod 6 — závislosti a aktuálny stav

```text
BOD_6=ÁNO
```

Kontinuita všetkých deviatich blokov bola dokončená nad stabilným HEAD `6f5adb84b4c9a45ccc30a9c2e4922d0a85c8dc9d`. Porovnanie `f7e67cab... → 6f5adb84...` obsahuje 15 commitov a iba metodické alebo evidenčné súbory; po predchádzajúcom technickom snapshote sa `/codei`, testy, migrácie, `RELEASE_VERSION` ani ZIP balíky nezmenili.

Všetkých deväť blokov má na aktuálnom HEAD čitateľné vykonateľné závislosti. Zistené testovacie medzery — najmä skutočne paralelný HTTP dôkaz a samostatná logout regresia — sú predmetom návrhu a Validácie Kroku 11, nie chýbajúcou dostupnosťou závislostí. Nový Gate/session/step/Evidence modul je dostupný, ale jeho zámer, bezpečnostné hranice, placeholdery a testové pokrytie zostávajú na klasifikáciu vo Fáze 11.B.

## Inicializačná tabuľka

| Bod | Stav | Dôkaz | Zostáva neoverené |
|---|---|---|---|
| 1. Metodika načítaná | ÁNO | `postupy/Inicializácia práce.md` v2.0, blob `44729126508a0c9151fb2358badcb1445a425bd6` | nič |
| 2. Projekt a autoritatívny zdroj | ÁNO | METODIKA, `slapiar/METODIKA`, `main`, `/codei` | nič |
| 3. Vetva a HEAD | ÁNO | vzdialený snapshot `6f5adb84b4c9a45ccc30a9c2e4922d0a85c8dc9d` bol stabilný počas celého čítacieho okna; následný zápis mení iba tento INI | nový read-back po tomto zápise |
| 4. Prístupy | ÁNO | admin/push/read/write/read-back | nič |
| 5. Prostredie | NEOVERENÉ | historické praktické dôkazy Krokov 7 až 10 zachované; aktuálny produkčný read-back nebol v dostupnom spojení získaný; nový test zakázaný | aktuálny praktický produkčný runtime a stav migrácií |
| 6. Závislosti | ÁNO | `c90ae562... → 6f5adb84...` = 127 commitov; `d418e72... → 6f5adb84...` = 105 commitov; `f7e67cab... → 6f5adb84...` = 15 commitov iba v dokumentácii; úplná mapa deviatich blokov je nižšie | funkčné výsledky patria až do Validácie po otvorení brány |
| 7. Rozsah | ÁNO | iba Fáza 11.A — čítanie, obnova dôkazov, zmrazenie stavu a aktualizácia tohto INI | nič |
| 8. Kritérium úspechu | ÁNO | každý stav doložený alebo označený `NEZISTENÉ`; Gate sa otvorí až po deviatich hodnotách ÁNO | praktické splnenie Kroku 11 |
| 9. Rollback | ÁNO | odstránenie iba vlastných metodických zápisov; žiadny zásah do kódu, dát ani produkcie | nič |

## Vyhodnotenie brány

```text
1=ÁNO
2=ÁNO
3=ÁNO
4=ÁNO
5=NEOVERENÉ
6=ÁNO
7=ÁNO
8=ÁNO
9=ÁNO
GATE=CLOSED
```

## STOP

```text
STOP
PORUŠENÝ_PREDPOKLAD=aktuálny praktický produkčný runtime a stav migrácií pred otvorením Kroku 11
ČO_NEBOLO_VYKONANÉ=nebol spustený environmentálny test, validačná matica, test, funkčná zmena, release ani produkčný run
ČO_BOLO_OCHRÁNENÉ=existujúce dôkazy Krokov 7 až 10, vykonateľný kód, databáza, release balíky a produkcia
DÔVOD_ZASTAVENIA=repozitárová a závislostná kontinuita je dokončená, ale aktuálny produkčný runtime a stav migrácií neboli v dostupnom spojení prakticky a bezpečne načítané
```

## Jediný povolený nasledujúci úkon

```text
1. získať bezpečný aktuálny čítací produkčný dôkaz bez zmeny produkcie,
2. potvrdiť z neho nasadenú verziu a zdrojový commit, ak sú dostupné,
3. potvrdiť aktuálny produkčný runtime, stav migrácií a runtime flagy,
4. bezpečne odlíšiť a načítať aktuálny stav testovacích session/step/Evidence, databázových riadkov a run-store JSON/lock/temp súborov,
5. nezistené hodnoty ponechať výslovne ako NEZISTENÉ, nie ako nulu,
6. aktualizovať tento istý INI,
7. až po deviatich hodnotách ÁNO otvoriť GATE a pokračovať Fázou 11.B.
```

Nevytvára sa nový INI Kroku 11 ani nový environmentálny test.

## Tabuľka stavu čítania a vykonávania metodických pokynov

| ID | Metodický pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Povinné nové načítanie vzdialeného autoritatívneho repozitára | 1 | 1 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 1 |
| 4 | Praktické overenie prístupov | 1 | 1 |
| 5 | Obnova projektového kontextu | 1 | 1 |
| 6 | Overenie skutočného stavu | 1 | 1 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 1 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 0 |
| 12 | Validácia výsledku Kroku 11 | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 1 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |

---

## Checkpoint Fázy 11.A — 2026-07-26 07:22 Europe/Bratislava

### Zmrazený stav repozitára pred zápisom

```text
REPOSITORY=slapiar/METODIKA
BRANCH=main
SNAPSHOT_HEAD=f7e67cab460b96d5fdcb3f606b6a2a3ecc2d4747
HEAD_STABILNÝ_POČAS_ČÍTACIEHO_OKNA=true
WORKFLOW_RUNS_PRE_SNAPSHOT_HEAD=0
```

### Rozdiely voči základom

```text
c90ae562a859de9fe0b2174f39e924c7f7bc6a4e → f7e67cab460b96d5fdcb3f606b6a2a3ecc2d4747
STATUS=ahead
COMMITS=112

 d418e72c162bde324af7546c937af979bd75182e → f7e67cab460b96d5fdcb3f606b6a2a3ecc2d4747
STATUS=ahead
COMMITS=90
```

V oboch porovnaniach sú zmenené vykonateľné a konfiguračné súbory. Patria medzi ne najmä Boot konfigurácia, `Routes.php`, diagnostické kontroléry, Gate Supervisor, modely, views, JavaScript, CSS a zvukové súbory. Porovnania nezaznamenali zmenu existujúcich testovacích súborov ani databázových migrácií; táto neprítomnosť zmeny však sama nepotvrdzuje funkčnú kontinuitu nových modulov.

### Stav releaseov v repozitári

```text
RELEASE_VERSION=1.1.15
RELEASE_VERSION_BLOB=645377eea8d0ff0cc974600d76e48ea516c4c8c0
RELEASE_1_1_9_BLOB=1e407b914e9be81b500612b69dd20492fbb63fa5
RELEASE_1_1_15_BLOB=aeaf80b31f299cbf824834590bd58435e216e99d
RELEASE_1_1_15_COMMIT=019a5c8fb741b640192dd89536be1091ef11e437
RELEASES_1_1_10_AŽ_1_1_15=PRÍTOMNÉ_V_REPOZITÁRI
```

Existencia čísla a ZIP súboru v repozitári nie je dôkazom nasadenia ani úspešnej produkčnej Validácie.

### Stav repozitárovej produkčnej konfigurácie

- `codei/app/Config/Boot/production.php` načítava `ExternalEnvironment`, vypína zobrazovanie chýb a nastavuje `CI_DEBUG=false`.
- Aktuálny `Routes.php` obsahuje pôvodné diagnostické routes aj Gate Dashboard, Gate Supervisor a nové session/step/Evidence routes.
- `DiagnosticsController` číta flagy `METODIKA_DIAGNOSTICS_ENABLED` a `METODIKA_CONCURRENCY_WEB_ENABLED` z runtime prostredia.
- Hodnoty runtime flagov sa zo zdrojového repozitára nedajú určiť.

### Oddelená dôkazová mapa

| Oblasť | Stav | Dôkaz |
|---|---|---|
| Repozitár | DOLOŽENÉ | stabilný snapshot `f7e67cab...`, dva úplné compare výsledky |
| Release balíky v repozitári | DOLOŽENÉ | `RELEASE_VERSION=1.1.15`, ZIP 1.1.9 a 1.1.15 majú blob SHA, 1.1.10–1.1.15 sú prítomné |
| Nasadená produkčná verzia | NEZISTENÉ | chýba bezpečný praktický produkčný dôkaz |
| Zdrojový commit nasadenia | NEZISTENÉ | chýba deployment manifest alebo produkčný read-back |
| Feature flagy | NEZISTENÉ | runtime hodnoty nie sú v repozitári |
| Produkčný runtime | NEZISTENÉ | historické dôkazy sú zachované, aktuálny praktický dôkaz chýba |
| Stav migrácií na produkcii | NEZISTENÉ | repozitár nepreukazuje vykonaný produkčný stav |
| Databázové testovacie riadky | NEZISTENÉ | chýba bezpečný read-only DB dôkaz |
| Session/step/Evidence dáta | NEZISTENÉ | chýba bezpečný read-only produkčný dôkaz |
| Run-store JSON/lock/temp | NEZISTENÉ | chýba bezpečný read-only súborový dôkaz |
| Produkčný cleanup | NEZISTENÉ | nezistené sa nepovažuje za nulu |

### Výsledok checkpointu

```text
FÁZA_11_A=ZAČATÁ_A_NEÚPLNÁ
GATE_KROKU_11=CLOSED
KROK_11_TESTY=NESPUSTENÉ
KÓD=BEZ_ZMENY
RELEASE=BEZ_ZMENY
PRODUKCIA=BEZ_ZÁSAHU
NEXT_ALLOWED_ACTION=IBA_DOKONČENIE_BEZPEČNÉHO_ČÍTACIEHO_DÔKAZU_FÁZY_11_A
```

---

## Checkpoint Fázy 11.A — 2026-07-26 08:24 Europe/Bratislava

### Aktuálny stabilný snapshot

```text
REPOSITORY=slapiar/METODIKA
BRANCH=main
SNAPSHOT_HEAD=6f5adb84b4c9a45ccc30a9c2e4922d0a85c8dc9d
HEAD_STABILNÝ_POČAS_ČÍTACIEHO_OKNA=true
PREDCHÁDZAJÚCI_TECHNICKÝ_SNAPSHOT=f7e67cab460b96d5fdcb3f606b6a2a3ecc2d4747
F7E67CAB_AŽ_6F5ADB84=15_COMMITOV_IBA_METODIKA_A_EVIDENCIA
CODEI_OD_F7E67CAB=BEZ_ZMENY
TESTY_OD_F7E67CAB=BEZ_ZMENY
MIGRÁCIE_OD_F7E67CAB=BEZ_ZMENY
RELEASE_OD_F7E67CAB=BEZ_ZMENY
```

### Úplné rozdiely voči technickým základom

```text
c90ae562a859de9fe0b2174f39e924c7f7bc6a4e → 6f5adb84b4c9a45ccc30a9c2e4922d0a85c8dc9d
STATUS=ahead
COMMITS=127

d418e72c162bde324af7546c937af979bd75182e → 6f5adb84b4c9a45ccc30a9c2e4922d0a85c8dc9d
STATUS=ahead
COMMITS=105
```

Zmenený technický súborový rozsah zostáva rovnaký ako pri predchádzajúcom snapshote. Zahŕňa Boot konfigurácie, databázovú konfiguráciu, `Routes.php`, diagnostické a Gate kontroléry, modely, views, JavaScript, CSS, zvuky, `RELEASE_VERSION` a ZIP balíky 1.1.10–1.1.15. Dva zvukové súbory majú v repozitárovej ceste koncové medzery. Existujúce testy ani migrácie sa v technickom rozdiele nezmenili.

### Kontinuitná mapa deviatich blokov Kroku 11

| Blok | Vykonateľná závislosť na HEAD | Existujúci dôkaz/test | Kontinuita | Otvorená medzera pre neskoršiu Validáciu |
|---:|---|---|---|---|
| 1. Run store a validator | `DiagnosticsConcurrencyRunStore`, `DiagnosticsConcurrencyRunDocumentValidator`, stavový model | unit testy store, validatora a stavov | POTVRDENÁ | testy v tomto kroku ešte neboli spustené |
| 2. `FirstAcceptanceService` | služba, porty, factory a databázové adaptéry | `FirstAcceptanceServiceTest` | POTVRDENÁ | praktická integračná Validácia patrí až po otvorení brány |
| 3. Diagnostické chybové fázy | `DiagnosticsConcurrencyFailureReporter`, vonkajšie fázy v `DiagnosticsController` | session test bezpečných kódov bez raw výnimky | POTVRDENÁ | úplná matica všetkých fáz sa ešte nespustila |
| 4. Session a security endpointy | login/database/logout, START, HIT, RESULT a diagnostické session/step/Evidence routes | existujúce diagnostics session/security testy | POTVRDENÁ S ROZŠÍRENÝM RIZIKOM | nové Gate API routes nemajú spoločnú explicitnú autorizáciu ani vlastné testy; niektoré nové chyby vracajú raw interné údaje |
| 5. Integračný DB rollback | `metodika:verify-first-acceptance-transaction` | dva reálne rollback scenáre a núdzový cleanup | POTVRDENÁ | aktuálne spustenie patrí až do Validácie |
| 6. Skutočná DB súbežnosť | `metodika:verify-concurrent-first-acceptance` | dve nezávislé MySQLi spojenia, async INSERT, kolízia 1062, cleanup | POTVRDENÁ | aktuálne spustenie patrí až do Validácie |
| 7. `START → HIT A/B → RESULT` | routovaný `DiagnosticsConcurrencyStartController`, HIT a RESULT v `DiagnosticsController`, UI | session E2E test existuje | POTVRDENÁ S TESTOVOU MEDZEROU | feature test ručne predpripraví participanta A; nejde o skutočne paralelný HTTP dôkaz M12/M17 |
| 8. Tombstone a sweep | redakcia tombstone, `readOnceConsumedAt`, `deleteAfter`, cleanup JSON/lock/temp | session a unit testy tombstone, resultu, sweepu a idempotentného cleanupu | POTVRDENÁ | úplný praktický nulový stav sa ešte neoveril |
| 9. Login/database/logout regresia | tri routes a session autentifikácia s TTL | login a database regresné testy existujú | POTVRDENÁ S TESTOVOU MEDZEROU | samostatný logout regresný test sa v repozitári nenašiel |

### Oddelenie dostupnosti od Validácie

```text
ZÁVISLOSTI_DEVÄŤ_BLOKOV=DOSTUPNÉ
KONTINUITA_VOČI_STABILNÉMU_HEAD=POTVRDENÁ
BOD_6=ÁNO
TESTY_AKTUÁLNE_NESPUSTENÉ=true
FUNKČNÁ_SPRÁVNOSŤ_NOVÉHO_GATE_MODULU=NEPOTVRDENÁ
ZÁMER_GATE_SUPERVISORA=NA_KLASIFIKÁCIU_V_11_B
```

Nový Gate modul obsahuje pevné testovacie identifikátory session a kroku, placeholder metódy, verejné API routes bez spoločného explicitného filtra a diagnostické chybové odpovede s internými údajmi. Samostatné testy `GateSupervisor`, `DiagnosticsGateStepController` ani `DiagnosticsGateEvidenceController` sa nenašli. Tieto zistenia sa iba evidujú; žiadny kód sa v zatvorenej bráne nemení.

### Aktualizovaná produkčná dôkazová mapa

| Oblasť | Aktuálny stav | Historický alebo repozitárový dôkaz | Chýbajúci aktuálny dôkaz |
|---|---|---|---|
| Repozitár | DOLOŽENÉ | stabilný HEAD `6f5adb84...`, úplné compare výsledky | nič |
| Release balíky v repozitári | DOLOŽENÉ | `RELEASE_VERSION=1.1.15`, balíky 1.1.10–1.1.15 prítomné | dôkaz nasadenia |
| Nasadená produkčná verzia | NEZISTENÉ | footer vie čítať release marker, ale produkčný read-back nebol získaný | bezpečný aktuálny HTTP alebo serverový read-back |
| Zdrojový commit nasadenia | NEZISTENÉ | v repozitári nie je aktuálny deployment manifest | produkčný manifest alebo zhodný súborový read-back |
| Feature flagy | NEZISTENÉ | zdrojový kód ich číta z runtime prostredia | aktuálne runtime hodnoty |
| Produkčný runtime | NEZISTENÉ | historicky PHP 8.4 a MariaDB 11.4; nejde o dnešný dôkaz | aktuálny praktický výpis bez zmeny prostredia |
| Stav migrácií na produkcii | NEZISTENÉ | historicky M1–M8 vykonané | aktuálny `migrate:status` alebo ekvivalentný čítací dôkaz |
| Databázové testovacie riadky | NEZISTENÉ | historické testy mali cleanup | aktuálne bezpečné počty jednoznačne testovacích riadkov |
| Session dáta | NEZISTENÉ | 2026-07-25 bola používateľom doložená session `id=1` | dnešný read-only stav |
| Step dáta | NEZISTENÉ | 2026-07-25 bol používateľom doložený krok `id=1` | dnešný read-only stav |
| Evidence dáta | NEZISTENÉ | historický INI ponechal zápis Evidence ako následný test | dnešný read-only stav |
| Run-store JSON/lock/temp | NEZISTENÉ | repozitár určuje adresár a formáty | dnešný bezpečný súborový výpis |
| Produkčný cleanup | NEZISTENÉ | nezistené sa nepovažuje za nulu | spoločný aktuálny DB a súborový postcheck |

### Výsledok druhého checkpointu

```text
FÁZA_11_A=ZASTAVENÁ_ROZHODOVACOU_BRÁNOU_PO_DOKONČENÍ_REPOZITÁROVEJ_A_KONTINUITNEJ_MAPY
BOD_5=NEOVERENÉ
BOD_6=ÁNO
GATE_KROKU_11=CLOSED
KROK_11_TESTY=NESPUSTENÉ
KÓD=BEZ_ZMENY
DATABÁZA=BEZ_ZÁSAHU
RELEASE=BEZ_ZMENY
PRODUKCIA=BEZ_ZÁSAHU
FÁZA_11_B=NEOTVORENÁ
NEXT_ALLOWED_ACTION=IBA_ZÍSKANIE_BEZPEČNÉHO_AKTUÁLNEHO_PRODUKČNÉHO_DÔKAZU_A_AKTUALIZÁCIA_TOHTO_INI
```

---

## Checkpoint Fázy 11.A — 2026-07-26 08:42 Europe/Bratislava

### Východisko pokusu

```text
REPOSITORY=slapiar/METODIKA
BRANCH=main
HEAD_PRED_POKUSOM=7f42d33d182ef7c2ae7e3684a3eb1175a82ee7c8
HEAD_STABILNÝ_PRED_ZÁPISOM=true
INI_BLOB_PRED_ZÁPISOM=67d218f33ed8a9bd5278c8181465e6607861e1fb
ROZSAH=IBA_BEZPEČNÉ_ČÍTANIE_PRODUKCIE_A_ZÁPIS_DÔKAZOV_DO_TOHTO_INI
```

### Vykonané bezpečné čítacie pokusy

1. Pokus o priame načítanie zdokumentovanej produkčnej domény a existujúcich GET ciest `/`, `/diagnostics/database`, `/diagnostics/database/login`, `/gate`, `/api/gate/sessions`, `/api/gate/session/1/steps` a `/api/gate/step/1/evidence` cez dostupnú webovú čítaciu cestu.
2. Pokus o HTTP načítanie rovnakých ciest z izolovaného pracovného runtime.
3. Pokus o verejné vyhľadanie a indexačné overenie produkčnej subdomény.
4. Opätovné načítanie release mechanizmu `release.sh`, ktorý potvrdzuje, že balík obsahuje `codei/RELEASE_VERSION` a `codei/deploy/RELEASE_VERSION.txt`, ale neobsahuje dôkaz, ktorý balík je práve nasadený.

### Výsledok pokusov

```text
PRIAMY_HTTP_READ_BACK=NEZÍSKANÝ
DNS_RESOLUTION_V_IZOLOVANOM_PRACOVNOM_RUNTIME=NEÚSPEŠNÁ
VEREJNÝ_INDEX_PRODUKČNEJ_SUBDOMÉNY=BEZ_VÝSLEDKU
PRODUKČNÁ_HTTP_ODPOVEĎ=NEZÍSKANÁ
PRODUKCIA_NEDOSTUPNÁ_GLOBÁLNE=NEPOTVRDENÉ
ZÁVER=NEDOSTUPNOSŤ_PRACOVNEJ_ČÍTACEJ_CESTY_NIE_JE_DÔKAZOM_VÝPADKU_PRODUKCIE
```

Žiadny z pokusov nevykonal POST, zápis do databázy, vytvorenie session, kroku, Evidence, run-store súboru, migráciu, release ani produkčný zásah.

### Produkčné hodnoty po pokuse

| Oblasť | Stav po pokuse |
|---|---|
| Nasadená verzia | NEZISTENÉ |
| Zdrojový commit nasadenia | NEZISTENÉ |
| Runtime flagy | NEZISTENÉ |
| PHP a databázový runtime | NEZISTENÉ |
| Stav migrácií | NEZISTENÉ |
| Databázové testovacie riadky | NEZISTENÉ |
| Session/step/Evidence | NEZISTENÉ |
| Run-store JSON/lock/temp | NEZISTENÉ |
| Produkčný cleanup | NEZISTENÉ |

### Rozhodnutie brány

```text
BOD_5=NEOVERENÉ
BOD_6=ÁNO
GATE_KROKU_11=CLOSED
FÁZA_11_A=ZASTAVENÁ_NA_NEDOSTUPNEJ_BEZPEČNEJ_PRODUKČNEJ_ČÍTACEJ_CESTE
FÁZA_11_B=NEOTVORENÁ
KROK_11_TESTY=NESPUSTENÉ
KÓD=BEZ_ZMENY
DATABÁZA=BEZ_ZÁSAHU
RELEASE=BEZ_ZMENY
PRODUKCIA=BEZ_ZÁSAHU
```

### Jediný nasledujúci povolený úkon

```text
1. použiť existujúci autorizovaný read-only prístup k produkcii alebo aktuálny read-only výstup získaný prevádzkovateľom z existujúcej diagnostiky či servera,
2. nevytvárať nový endpoint, environmentálny test ani produkčný zápis,
3. doložiť nasadenú verziu a zdrojový commit, ak ich produkcia sprístupňuje,
4. doložiť runtime, migrácie, flagy, testovacie DB riadky, session/step/Evidence a run-store JSON/lock/temp stav,
5. všetko nezistené ponechať ako NEZISTENÉ,
6. aktualizovať výhradne tento pôvodný INI,
7. GATE otvoriť až po deviatich hodnotách ÁNO.
```

---

## Checkpoint Fázy 11.A — 2026-07-27 09:35 Europe/Bratislava

### Nové načítanie autoritatívneho stavu

```text
REPOSITORY=slapiar/METODIKA
BRANCH=main
HEAD_PRED_ČÍTACÍM_POKUSOM=ba1061fd44d7426c928d3e824a69105101ce673a
INI_BLOB_PRED_ZÁPISOM=d70e67322e0479a4abb9e963ae51fd45ab040854
ROZSAH=IBA_EXISTUJÚCE_PRODUKČNÉ_GET_ROZHRANIA_A_ZÁPIS_DÔKAZU_DO_TOHTO_INI
```

### Vykonaný bezpečný čítací pokus

Boli požiadané iba existujúce GET adresy:

- `https://codei.dremont.in/`
- `https://codei.dremont.in/diagnostics/database`
- `https://codei.dremont.in/diagnostics/database/login`
- `https://codei.dremont.in/gate`
- `https://codei.dremont.in/api/gate/sessions`
- `https://codei.dremont.in/api/gate/session/1/steps`
- `https://codei.dremont.in/api/gate/step/1/evidence`

Pracovné webové prostredie odmietlo všetky adresy pred vykonaním HTTP požiadavky s výsledkom `URL is not safe to open (non-retryable error)`.

```text
HTTP_POŽIADAVKA_ODOSLANÁ=false
PRODUKČNÁ_ODPOVEĎ=NEZÍSKANÁ
PRODUKCIA_NEDOSTUPNÁ_GLOBÁLNE=NEPOTVRDENÉ
ZÁVER=OBMEDZENIE_PRACOVNÉHO_PROSTREDIA_NIE_JE_DÔKAZOM_VÝPADKU_PRODUKCIE
```

Nebol vykonaný POST, prihlásenie, vytvorenie session, kroku, Evidence, databázový zápis, migrácia, environmentálny test, release ani produkčný zásah.

### Stav brány po pokuse

```text
BOD_5=NEOVERENÉ
GATE_KROKU_11=CLOSED
FÁZA_11_A=ZASTAVENÁ_NA_NEDOSTUPNEJ_AUTORIZOVANEJ_PRODUKČNEJ_ČÍTACEJ_CESTE
FÁZA_11_B=NEOTVORENÁ
KROK_11_TESTY=NESPUSTENÉ
KÓD=BEZ_ZMENY
DATABÁZA=BEZ_ZÁSAHU
RELEASE=BEZ_ZMENY
PRODUKCIA=BEZ_ZÁSAHU
```

### Jediný povolený nasledujúci úkon

```text
1. sprístupniť existujúci autorizovaný read-only produkčný výstup alebo jeho aktuálny export,
2. nevytvárať nový endpoint, environmentálny test ani produkčný zápis,
3. doložiť dostupné hodnoty nasadenej verzie, zdrojového commitu, runtime, migrácií, flagov, testovacích DB riadkov, session/step/Evidence a run-store JSON/lock/temp,
4. nezistené hodnoty ponechať ako NEZISTENÉ,
5. aktualizovať výhradne tento pôvodný INI,
6. GATE otvoriť až po deviatich hodnotách ÁNO.
```
