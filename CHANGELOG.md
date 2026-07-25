# Changelog

Tento súbor zaznamenáva **čo sa zmenilo, kedy sa to zmenilo a kde je uložená platná definícia alebo aktuálny pracovný stav**.

CHANGELOG nie je samostatným autoritatívnym zdrojom definícií. Pri rozpore rozhoduje dokument uvedený v odkaze a jeho stav podľa príslušného registra.

---

## 2026-07-25

### Krok 11 — konsolidácia po obnovení pripojenia

- pokračovanie bolo na výslovný pokyn používateľa zapísané do pôvodného INI [`postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md`](postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md); nevznikol ďalší INI ani nový environmentálny test,
- aktuálny technický základ bol overený ako `d418e72c162bde324af7546c937af979bd75182e`; zmeny po poslednej korekcii patria samostatnému supervízorskému modulu a nemenia diagnostické zdroje, testy, migrácie, Composer ani workflowy Kroku 11,
- PR `#3` je uzavretý bez merge a jeho výsledky nie sú autoritatívnou Validáciou aktuálneho `main`,
- medzistavový INI `2026-07-25_10-33_INI_Krok_11_po_zmene_HEAD.md` bol v registri označený `PREKONANÝ`,
- všetkých deväť bodov zostáva `ÁNO`; `GATE=OPEN`, technický základ `d418e72...`; nasleduje analýza deviatich validačných blokov,
- register [`postupy/README.md`](postupy/README.md) bol zosúladený.

### Krok 11 — otvorenie validačnej brány existujúcimi dôkazmi

- vznikol a bol dokončený samostatný inicializačný záznam [`postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md`](postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md),
- nová brána Kroku 11 nepreberá stav `GATE=OPEN` predchádzajúceho kroku; používa však už existujúce praktické dôkazy z Krokov 7 až 10 po overení, že ich technické predpoklady zostali nezmenené,
- porovnanie funkčného commitu `c90ae562a859de9fe0b2174f39e924c7f7bc6a4e` s HEAD pred otvorením brány `23cdcee06dd25427ecfab96cd5ea0ec4a0e4ec02` potvrdilo iba metodické a dokumentačné zmeny; vykonateľný kód, testy, migrácie, `composer.lock` a workflowy sa nezmenili,
- potrebné prístupy dokladajú administrátorské oprávnenia repozitára, úspešný zápis a read-back, opakovaný environment job `89652985988` a úspešná PR Validácia runom `30148480939`, jobom `89654680309`,
- prostredie dokladajú existujúce úspešné behy nad PHP 8.4, Composerom 2, `pcntl_fork`, MySQLi, izolovanou MariaDB 11.4, migráciami M1–M8, rollbackom a cleanupom vrátane runu Kroku 9 `30098298849`,
- dostupnosť závislostí bola doložená presnou mapou všetkých deviatich blokov Kroku 11; známe medzery, najmä samostatná logout regresia a skutočne paralelný HTTP variant, sú predmetom analýzy a Validácie Kroku 11, nie chýbajúcim predpokladom otvorenia,
- všetkých deväť inicializačných bodov je `ÁNO`; `GATE=OPEN`,
- povolená je iba analýza a následná úplná lokálna a integračná Validácia Kroku 11; release, produkčný run a otvorenie Kroku 12 zostávajú zakázané,
- register [`postupy/README.md`](postupy/README.md) bol zosúladený; vykonateľný kód, testy, databázová schéma, workflowy a produkčné prostredie sa týmto metodickým úkonom nemenili.

### Náprava chýbajúcej tabuľky metodických úkonov

- pri kontrole uzavretia Kroku 10 sa zistilo, že jeho INI záznam obsahoval iba netabuľkový blok `#ID / R / W`, hoci základné pravidlo [`postupy/Inicializácia práce.md`](postupy/Inicializácia%20práce.md) prikazuje vypísať na obrazovku tabuľku stavu čítania a vykonávania pokynov a uložiť ju na koniec INI záznamu,
- pred nápravou vznikol samostatný inicializačný záznam [`postupy/WORK/INI/2026-07-25_09-18_INI_Doplnenie_tabulky_metodickych_ukonov.md`](postupy/WORK/INI/2026-07-25_09-18_INI_Doplnenie_tabulky_metodickych_ukonov.md),
- na úplnom konci [`postupy/WORK/INI/2026-07-25_08-14_INI_Dokoncenie_Kroku_10_po_STOP.md`](postupy/WORK/INI/2026-07-25_08-14_INI_Dokoncenie_Kroku_10_po_STOP.md) bola doplnená povinná markdown tabuľka ID 0–14 s názvom metodického pokynu a hodnotami `R` a `W`,
- tabuľka bola zároveň vypísaná používateľovi na obrazovku; predchádzajúca záverečná odpoveď bez tabuľky bola neúplná,
- register [`postupy/README.md`](postupy/README.md) bol doplnený o nápravný INI záznam a o informáciu, že pôvodný INI Kroku 10 už obsahuje tabuľku na skutočnom konci súboru,
- funkčný kód, testy, databáza, workflow, stav Kroku 10, commit `c90ae562...` ani produkčné prostredie sa nemenili.

### Krok 10 — presná rezervácia prvého prijatia

- inicializačná brána je zdokumentovaná v [`postupy/WORK/INI/2026-07-25_08-14_INI_Dokoncenie_Kroku_10_po_STOP.md`](postupy/WORK/INI/2026-07-25_08-14_INI_Dokoncenie_Kroku_10_po_STOP.md); praktické opakovanie existujúceho izolovaného jobu `89652985988` potvrdilo PHP 8.4, Composer 2, MySQLi, MariaDB 11.4, migrácie M1–M8, izoláciu, rollback a cleanup,
- predchádzajúce tvrdenie o chýbajúcom Composer-i a DNS bolo označené ako neplatné pre projekt, pretože sa týkalo iba interného pomocného sandboxu, nie projektového ani autoritatívneho testovacieho prostredia,
- v [`codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php`](codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php) sa kontroluje výsledok `insert()` pri `DBDebug=false`, duplicitný kľúč `1062` vracia `ALREADY_EXISTS` a `CREATED` je povolené iba po presnej zhode `REQUEST_REFERENCE + payload_fingerprint + derivation_reference`,
- príkaz [`codei/app/Commands/ReproduceFirstAcceptanceRootCause.php`](codei/app/Commands/ReproduceFirstAcceptanceRootCause.php) bol zmenený na regresný dôkaz dvoch nezávislých MySQLi spojení, jediného vlastníka rezervácie, jediného počiatočného history runu a úplného cleanupu,
- workflow [`.github/workflows/krok-7-root-cause-reproduction.yml`](.github/workflows/krok-7-root-cause-reproduction.yml) teraz vykonáva trvalú PR/push regresnú Validáciu nad PHP 8.4, Composerom 2 a izolovanou MariaDB 11.4,
- PR `#2` bol validovaný runom `30148480939`, jobom `89654680309`; prešli syntax, 2 jednotkové testy so 4 tvrdeniami, migrácie M1–M8, schéma, druhý výsledok `ALREADY_EXISTS`, neprítomnosť duplicitného history runu a cleanup,
- funkčná zmena bola squash-mergeovaná do jediného commitu `c90ae562a859de9fe0b2174f39e924c7f7bc6a4e`,
- úplný pracovný výsledok je v [`postupy/WORK/2026-07-25_09-01_Krok_10_Najmensia_funkcna_oprava_rezervacie.md`](postupy/WORK/2026-07-25_09-01_Krok_10_Najmensia_funkcna_oprava_rezervacie.md) a checkpoint v [`postupy/2026-07-25_09-05_CHECKPOINT-KROK-10-PRESNA-REZERVACIA.md`](postupy/2026-07-25_09-05_CHECKPOINT-KROK-10-PRESNA-REZERVACIA.md),
- záväzný plán a register boli zosúladené: Kroky 1 až 10 sú `SPLNENÉ`; jediným nasledujúcim povoleným krokom je Krok 11, ktorý zostáva zatvorený do vlastnej novej inicializačnej brány,
- databázová schéma, diagnostické rozlíšenie, `DiagnosticsConcurrencyRunStore::load()`, timeoutová poistka, UI, release skripty a produkčné prostredie sa nemenili.

### Spresnenie významu `GATE=CLOSED`

- metodický úkon otvoril INI záznam [`postupy/WORK/INI/2026-07-25_07-17_INI_Spresnenie_vyznamu_Gate_closed.md`](postupy/WORK/INI/2026-07-25_07-17_INI_Spresnenie_vyznamu_Gate_closed.md),
- v [`postupy/Inicializácia práce.md`](postupy/Inicializácia%20práce.md) bolo výslovne oddelené `GATE=OPEN` predchádzajúceho kroku od `GATE=CLOSED` aktuálne vykonávaného kroku,
- `GATE=CLOSED` aktuálneho kroku neblokuje vykonanie a doloženie jeho vlastných bodov 1 až 9; blokuje až analýzu, návrh, implementáciu, test predmetu práce, zásah do prostredia alebo ďalší krok, ktoré vyžadujú otvorenú bránu,
- register [`postupy/README.md`](postupy/README.md) bol zosúladený,
- vykonateľný kód, testy, databáza, workflow ani produkcia sa nemenili.

### Záväzný plán dňa a metodické otvorenie práce

- po samostatnej inicializačnej bráne vznikol riadny záväzný plán [`postupy/PLAN/2026-07-25_05-18_Plan_prace.md`](postupy/PLAN/2026-07-25_05-18_Plan_prace.md),
- plán zachováva lineárne poradie Krokov 10 až 15; Kroky 1 až 9 zostávajú `SPLNENÉ` a jediným nasledujúcim povoleným krokom je Krok 10,
- predbežný plán `postupy/PLAN/2026-07-25_08-00_Predbezny_plan_Kroky_10-15.md` bol po výslovnom pokyne používateľa odstránený ako nahradený riadnym plánom,
- plánovací úkon otvoril INI záznam [`postupy/WORK/INI/2026-07-25_05-17_INI_Zavazny_plan_2026-07-25.md`](postupy/WORK/INI/2026-07-25_05-17_INI_Zavazny_plan_2026-07-25.md) a výsledok zaznamenáva [`postupy/WORK/2026-07-25_05-20_Vytvorenie_zavazneho_planu.md`](postupy/WORK/2026-07-25_05-20_Vytvorenie_zavazneho_planu.md),
- odstránenie predbežného plánu a dokončenie evidencie otvorila samostatná brána [`postupy/WORK/INI/2026-07-25_05-33_INI_Uzavretie_planu_a_otvorenie_prace.md`](postupy/WORK/INI/2026-07-25_05-33_INI_Uzavretie_planu_a_otvorenie_prace.md),
- register [`postupy/README.md`](postupy/README.md) bol zosúladený s riadnym plánom, jeho INI a WORK záznamami,
- dnešná práca je metodicky otvorená na úrovni záväzného plánu; samotný Krok 10 sa môže funkčne otvoriť iba vlastnou novou bránou po praktickom overení Codespace, PHP, Composeru, testovacej MariaDB, migrácií, izolácie, cleanupu a rollbacku,
- vykonateľný kód, databáza, release ani produkčné prostredie sa týmto úkonom nemenili.

---

## 2026-07-24

### Krok 9 — audit bariéry, `load()` a timeoutu

- GitHub Actions run `30098298849` na PHP 8.4.23 s `pcntl_fork` úspešne vykonal existujúce run-store testy aj nový skutočný dvojprocesový test,
- fyzicky uložený stav `EXECUTING` bol presne odlíšený od návratovej projekcie `BARRIER_OPEN`, ktorú `load()` poskytuje čakajúcemu requestu bez zmeny raw JSON,
- oneskorený pokus zapísať `PARTNER_TIMEOUT` po otvorení bariéry bol pod exkluzívnym zámkom odmietnutý; raw dokument zostal nezmenený,
- zmena `load()` nebola preukázaná ako nevyhnutná, preto produkčný run store, kontrolér a stavový automat zostali bez zmeny,
- výsledok je v [`postupy/WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md`](postupy/WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md),
- jediným ďalším povoleným krokom je Krok 10 — najmenšia funkčná oprava koreňovej príčiny.
### Krok 8 — oprava diagnostického rozlíšenia

- `DiagnosticsConcurrencyAcceptanceRunner` zachováva pôvodné bezpečné `accept()` a pre kontrolér poskytuje `acceptOrThrow()` na vonkajšie fázové rozlíšenie,
- testy bezpečných kódov všetkých fáz a session test neprítomnosti raw exception textu prešli na PHP 8.4,
- výsledok je v [`postupy/WORK/2026-07-24_13-45_Krok_8_Oprava_diagnostickeho_rozlisenia.md`](postupy/WORK/2026-07-24_13-45_Krok_8_Oprava_diagnostickeho_rozlisenia.md),
- funkčná koreňová chyba rezervácie sa v Kroku 8 nemenila; jediným ďalším povoleným krokom je Krok 9.

### Krok 7 — reprodukcia koreňovej príčiny mimo produkcie

- izolovaný GitHub Actions run `30089261354` nad PHP 8.4 a MariaDB 11.4 úspešne vykonal migrácie M1–M8, overenie schémy a reprodukciu cez nezmenenú aplikačnú cestu,
- potvrdená bola trieda príčiny `DBDebug=false + nekontrolovaný insert rezervácie + postcheck iba podľa REQUEST_REFERENCE`,
- druhý tok skončil presnou `RuntimeException` vo fáze `CREATE_INITIAL_HISTORY_RUN`, jeho transakcia bola vrátená späť a cleanup potvrdil počty `0 + 0 + 0`,
- výsledok je v [`postupy/WORK/2026-07-24_13-25_Krok_7_Reprodukcia_korenovej_priciny_mimo_produkcie.md`](postupy/WORK/2026-07-24_13-25_Krok_7_Reprodukcia_korenovej_priciny_mimo_produkcie.md).
### Sprísnenie povinnej inicializačnej brány

- pred úpravou vznikol dôkazový inicializačný záznam [`postupy/WORK/INI/2026-07-24_12-42_INI_Uprava_inicializacnej_brany.md`](postupy/WORK/INI/2026-07-24_12-42_INI_Uprava_inicializacnej_brany.md) s deviatimi doloženými hodnotami `ÁNO`, rozsahom, kritériom úspechu a rollbackom,
- [`postupy/Inicializácia práce.md`](postupy/Inicializácia%20práce.md) bol rozpracovaný na nepriechodnú vykonávaciu bránu: `prečítané ≠ splnené`, samotné `ÁNO` bez dôkazu znamená `NEOVERENÉ` a pri jedinom bode `NIE/NEOVERENÉ` nesmie vzniknúť návrh, implementácia, príkaz ani zmena prostredia,
- povinne sa rozlišuje existencia klienta, ovládača, rozšírenia, triedy, príkazu alebo konfigurácie od skutočnej pripravenosti služby a prostredia,
- doplnené boli pravidlá pre zmenu podstatného predpokladu po otvorení brány, spätné načítanie vzdialeného výsledku a povinný STOP záznam pri práci bez otvorenej brány,
- register [`postupy/README.md`](postupy/README.md) eviduje sprísnený dokument aj jeho INI záznam,
- vykonateľný kód ani produkčné prostredie sa nemenili.

### Krok 6 — statická lokalizácia chybových fáz

- Krok 6 záväzného plánu bol uzavretý ako `SPLNENÉ`; výsledok je v [`postupy/WORK/2026-07-24_11-42_Krok_6_Staticka_lokalizacia_chybovych_faz.md`](postupy/WORK/2026-07-24_11-42_Krok_6_Staticka_lokalizacia_chybovych_faz.md),
- presná produkčná výnimka bola lokalizovaná do fázy `CREATE_INITIAL_HISTORY_RUN` v `DerivationHistoryRepository::createInitialRun()`,
- staticky potvrdená príčinná cesta vedie cez nekontrolovaný neúspešný `insert()` pri `DBDebug=false`, nepresný postcheck iba podľa `REQUEST_REFERENCE` a následné zlyhanie presnej korelácie `REQUEST_REFERENCE + derivation_reference`,
- vznikla úplná mapa fáz od zostavenia `InitialDerivationRun` po zápis participant outcome vrátane hraníc catchov a verejných bezpečných kódov,
- register [`postupy/README.md`](postupy/README.md) určuje ako jediný nasledujúci povolený úkon Krok 7 — reprodukcia koreňovej príčiny mimo produkcie,
- vykonateľný kód, testy ani produkčné prostredie sa nemenili.

### Krok 5 — historický produkčný dôkaz

- Krok 5 záväzného plánu bol uzavretý ako `SPLNENÉ — dôkaz získaný`; výsledok je v [`postupy/WORK/2026-07-24_11-05_Krok_5_Historicky_produkcny_dokaz.md`](postupy/WORK/2026-07-24_11-05_Krok_5_Historicky_produkcny_dokaz.md),
- produkčné flagy `METODIKA_DIAGNOSTICS_ENABLED` a `METODIKA_CONCURRENCY_WEB_ENABLED` boli potvrdené ako zapnuté,
- tombstone runu `run-5c73222700d7863a1b05e135` potvrdil `COMPLETED_FAILED`, participant A `FAILED_RUNTIME_ERROR`, participant B `CREATED`, otvorenú bariéru, potvrdenú DB unikátnosť a cleanup, ale nepotvrdený aplikačný replay,
- serverový log určil presnú príčinu: `RuntimeException` — historický beh nemožno založiť bez presnej rezervácie `REQUEST_REFERENCE`,
- tombstone aj stabilný `.lock` zostali na serveri po `deleteAfter`, čím sa potvrdil chýbajúci účinný následný sweep,
- register [`postupy/README.md`](postupy/README.md) určuje ako jediný nasledujúci povolený úkon Krok 6 — statická lokalizácia chybových fáz,
- vykonateľný kód, testy ani produkčné prostredie sa nemenili.

### Krok 4 — audit routovania, START vetiev a UI

- Krok 4 záväzného plánu bol uzavretý ako `SPLNENÉ`; výsledok je v [`postupy/WORK/2026-07-24_10-17_Krok_4_Audit_routovania_START_vetiev_a_UI.md`](postupy/WORK/2026-07-24_10-17_Krok_4_Audit_routovania_START_vetiev_a_UI.md),
- produkčný START endpoint obsluhuje `DiagnosticsConcurrencyStartController::start()`, kým `DiagnosticsController::startConcurrencyRun()` je mŕtva neroutovaná duplicita s odlišným verejným a chybovým kontraktom,
- testy START volajú rovnakú route ako produkcia,
- UI oddeľuje HTTP transport od aplikačných osí, ale `COMPLETED_FAILED` môže stále pôsobiť ako úspech pre text `Hotovo` a zelený záverečný log,
- register [`postupy/README.md`](postupy/README.md) určuje ako jediný nasledujúci povolený úkon Krok 5 — pokus o získanie historického produkčného dôkazu,
- vykonateľný kód, testy ani produkčné prostredie sa nemenili.

### Krok 3 — audit testovacej matice M01–M26

- Krok 3 záväzného plánu bol uzavretý ako `SPLNENÉ`; výsledok je v [`postupy/WORK/2026-07-24_10-24_Krok_3_Audit_testovacej_matice_M01-M26.md`](postupy/WORK/2026-07-24_10-24_Krok_3_Audit_testovacej_matice_M01-M26.md),
- audit vyhodnotil 5 scenárov ako `HOTOVÉ`, 15 ako `ČIASTOČNE` a 6 ako `CHYBNÉ`,
- vznikol záväzný register testovacích dier pre skutočný dvojprocesový file-store test, paralelné HTTP požiadavky, reálnu aplikačnú službu a DB spojenia, presnú klasifikáciu chybovej fázy, expiráciu, manuálny cleanup, autorizáciu a feature flag,
- register [`postupy/README.md`](postupy/README.md) určuje ako jediný nasledujúci povolený úkon Krok 4 — audit routovania, START vetiev a UI,
- vykonateľný kód ani produkčné prostredie sa nemenili.

### Krok 2 — úplný audit checklistu 1–14

- Krok 2 záväzného plánu bol uzavretý ako `SPLNENÉ`; výsledok je v [`postupy/WORK/2026-07-24_09-55_Krok_2_Úplný_audit_checklistu_1-14.md`](postupy/WORK/2026-07-24_09-55_Krok_2_Úplný_audit_checklistu_1-14.md),
- audit vyhodnotil 1 bod ako `HOTOVÉ`, 9 bodov ako `ČIASTOČNE` a 4 body ako `CHYBNÉ`,
- potvrdené boli najmä nedostatky bariéry a timeoutu, vonkajšieho `try/catch` pri `accept()`, absencia skutočného dvojprocesového integračného testu a neúspešný produkčný výsledok,
- register [`postupy/README.md`](postupy/README.md) určuje ako jediný nasledujúci povolený úkon Krok 3 — audit testovacej matice `M01–M26`,
- vykonateľný kód ani produkčné prostredie sa nemenili.

### Krok 1 — zmrazenie repozitárového východiska

- Krok 1 záväzného plánu bol uzavretý ako `SPLNENÉ`; výsledok je v [`postupy/WORK/2026-07-24_09-31_Krok_1_Zmrazenie_repozitárového_východiska.md`](postupy/WORK/2026-07-24_09-31_Krok_1_Zmrazenie_repozitárového_východiska.md),
- auditovaný HEAD je `fe562e1...`, produkčný základ zostáva release `1.1.9` na commite `3b91c4e...`,
- úplný GitHub diff potvrdil 24 commitov vpredu, 0 pozadu a iba deväť dokumentačných alebo metodických súborov; vykonateľný kód ani produkčné prostredie sa nemenili,
- register [`postupy/README.md`](postupy/README.md) určuje ako jediný nasledujúci povolený úkon Krok 2 — úplný audit checklistu 1–14.
