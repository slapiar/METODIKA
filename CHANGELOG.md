# Changelog

Tento súbor zaznamenáva **čo sa zmenilo, kedy sa to zmenilo a kde je uložená platná definícia alebo aktuálny pracovný stav**.

CHANGELOG nie je samostatným autoritatívnym zdrojom definícií. Pri rozpore rozhoduje dokument uvedený v odkaze a jeho stav podľa príslušného registra.

---

## 2026-07-24

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
- vykonateľný kód, testy ani produkčné prostredie sa nemenili.

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

### STOP a oprava záväzného plánu

- používateľ nariadil `STOP`, pretože pôvodný plán umožnil ponechať A1 otvorené počas čakania na externý produkčný dôkaz a tým znemožnil systematické lineárne pokračovanie,
- záväzný plán [`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`](postupy/PLAN/2026-07-24_08-04_Plán_práce.md) bol opravený na jediný lineárny rad 15 krokov; v jednom čase môže byť otvorený presne jeden krok,
- každý krok sa musí pred otvorením ďalšieho uzavrieť ako `SPLNENÉ`, `UZAVRETÉ S OBMEDZENÍM` alebo `ZASTAVENÉ ROZHODOVACOU BRÁNOU`,
- externý produkčný dôkaz bol oddelený do samostatného Kroku 5, ktorého nedostupnosť sa uzavrie s obmedzením namiesto ponechania práce v blokovanom medzistave,
- vznikol pracovný záznam [`postupy/WORK/2026-07-24_09-12_STOP_Oprava_záväzného_plánu.md`](postupy/WORK/2026-07-24_09-12_STOP_Oprava_záväzného_plánu.md),
- pôvodné A1 záznamy boli v [`postupy/README.md`](postupy/README.md) označené ako `PREKONANÉ`; aktuálnym miestom práce je Krok 1 opravenej verzie plánu.

### Plán pokračovania webového súbežného overenia

- vytvorený úplný pracovný plán [`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`](postupy/PLAN/2026-07-24_08-04_Plán_práce.md), ktorý nahrádza neúplné pracovné návrhy jedným riadeným postupom s rozhodovacími bránami, dôkazmi, rollbackmi a kritériami Validácie,
- plán zahŕňa dokončenie auditu checklistu a matice `M01 – M26`, získanie presnej príčiny `FAILED_RUNTIME_ERROR`, samostatnú opravu diagnostického rozlíšenia, audit `load()`, bariéry a timeout poistky, najmenšiu funkčnú opravu, úplné testy, release, jeden čistý produkčný run, tombstone, sweep a povinný záznam,
- pracovný vstup používateľa [`postupy/2026-07-24/07_44-Dnešný plán tvorba štruktúry`](postupy/2026-07-24/07_44-Dnešný%20plán%20tvorba%20%C5%A1trukt%C3%BAry) bol zaevidovaný ako `PRACOVNÝ`,
- používateľ udelil plánu prívlastok **ZÁVÄZNÝ** až do úplného naplnenia alebo metodicky korektného zastavenia; povinnosť je zapísaná v [`README.md`](README.md) a v registri [`postupy/README.md`](postupy/README.md),
- začala Fáza A1 a vznikol záznam [`postupy/PLAN/2026-07-24_A1_Východiskový_stav.md`](postupy/PLAN/2026-07-24_A1_Východiskový_stav.md), ktorý zmrazil release `1.1.9`, technický commit `3b91c4e...`, metodický HEAD pri začatí A1 `5fbe06a...`, posledný verejný produkčný výsledok a chýbajúce produkčné dôkazy,
- nevznikla zmena vykonateľného kódu ani produkčného prostredia.

---

## 2026-07-23

### Databázová implementácia

- na Hostingeri bola úspešne vykonaná praktická diagnostika produkčnej databázy,
- CodeIgniter CLI rozpoznal všetkých osem nevykonaných migrácií M1 až M8,
- príkaz `php spark migrate` vykonal všetkých osem migrácií bez chyby,
- následný `php spark migrate:status` potvrdil všetkých osem migrácií v skupine `default`, s časom `2026-07-23 08:36:20 UTC` a v batchi `1`,
- v release `1.0.9` bol nasadený čítací príkaz [`codei/app/Commands/VerifyQuestionDerivationSchema.php`](codei/app/Commands/VerifyQuestionDerivationSchema.php),
- príkaz `php spark metodika:verify-question-schema` potvrdil 8 z 8 tabuliek s InnoDB a `utf8mb4_bin` a 10 z 10 cudzích kľúčov s `DELETE RESTRICT` a `UPDATE RESTRICT`,
- loader [`codei/app/Config/ExternalEnvironment.php`](codei/app/Config/ExternalEnvironment.php) bol v release `1.0.11` opravený tak, aby prechádzal všetkých rodičov až po filesystem root namiesto pevnej hĺbky,
- po nasadení release `1.0.11` bolo znovu potvrdené načítanie externého prostredia a fyzická schéma 8/8 tabuliek a 10/10 reštriktívnych cudzích kľúčov,
- implementačný stav je evidovaný v [`TECHNICKE-NAVRHY/2026-07-22_IMPLEMENTACIA-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md`](TECHNICKE-NAVRHY/2026-07-22_IMPLEMENTACIA-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md),
- praktická reValidácia schémy je v [`TECHNICKE-NAVRHY/2026-07-23_REVALIDACIA-VYKONANIA-MIGRACII-M1-M8.md`](TECHNICKE-NAVRHY/2026-07-23_REVALIDACIA-VYKONANIA-MIGRACII-M1-M8.md).

### Repository adaptéry

- vytvorený port [`codei/app/Application/QuestionDerivation/Contracts/RequestReferenceRepositoryPort.php`](codei/app/Application/QuestionDerivation/Contracts/RequestReferenceRepositoryPort.php),
- vytvorené dátové objekty rezervácie a výsledku rezervácie v [`codei/app/Application/QuestionDerivation/Data/`](codei/app/Application/QuestionDerivation/Data),
- implementovaný MySQLi adaptér [`codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php`](codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php),
- doplnené `DerivationHistoryPort`, `TransactionBoundaryPort`, ich databázové adaptéry a `FirstAcceptanceService`,
- továreň prvého prijatia zostavuje všetky databázové komponenty nad jednou `BaseConnection`,
- syntaktická kontrola všetkých súborov atómového prvého prijatia prešla bez chyby,
- unit test [`codei/tests/unit/FirstAcceptanceServiceTest.php`](codei/tests/unit/FirstAcceptanceServiceTest.php) bol spustený v Codespaces nad PHP `8.4.15` a skončil `2/2`, so 4 assertions,
- scenár `RESERVATION_CREATED` založil historický beh v tej istej transakčnej hranici a `ALREADY_EXISTS` ďalší historický beh nezaložil,
- `codei/composer.lock` uzamyká vývojové závislosti a `codei/build/` je ignorovaný ako lokálny PHPUnit cache a výstupný adresár,
- aktuálny stav je v [`TECHNICKE-NAVRHY/2026-07-23_IMPLEMENTACIA-REQUEST-REFERENCE-REPOSITORY-ADAPTERA.md`](TECHNICKE-NAVRHY/2026-07-23_IMPLEMENTACIA-REQUEST-REFERENCE-REPOSITORY-ADAPTERA.md) a [`TECHNICKE-NAVRHY/2026-07-23_IMPLEMENTACIA-ATOMOVEHO-PRVEHO-PRIJATIA.md`](TECHNICKE-NAVRHY/2026-07-23_IMPLEMENTACIA-ATOMOVEHO-PRVEHO-PRIJATIA.md).

### Integračné overenie prvého prijatia

- vytvorený Spark príkaz [`codei/app/Commands/VerifyFirstAcceptanceTransaction.php`](codei/app/Commands/VerifyFirstAcceptanceTransaction.php),
- príkaz bol prakticky spustený v release `1.0.11` nad Hostinger MySQLi/InnoDB databázou,
- scenár A potvrdil spoločný vznik rezervácie, historického behu a dvoch zoradených doménových väzieb a následný úplný rollback testovacích dát,
- scenár B potvrdil, že úmyselná chyba po založení historického behu vráti späť rezerváciu, beh aj doménové väzby,
- po oboch scenároch nezostali v databáze žiadne testovacie riadky,
- výsledok `MYSQL_TRANSACTION_ATOMICITY_VALIDATED` a otvorené obmedzenie súbežného testu sú v [`TECHNICKE-NAVRHY/2026-07-23_INTEGRACNE-OVERENIE-ATOMOVEHO-PRVEHO-PRIJATIA.md`](TECHNICKE-NAVRHY/2026-07-23_INTEGRACNE-OVERENIE-ATOMOVEHO-PRVEHO-PRIJATIA.md).

### Súbežné overenie prvého prijatia

- vytvorený Spark príkaz [`codei/app/Commands/VerifyConcurrentFirstAcceptance.php`](codei/app/Commands/VerifyConcurrentFirstAcceptance.php),
- príkaz používa dve nesdielané MySQLi spojenia s rozdielnymi databázovými `thread_id`,
- spojenie A vytvorí celé prvé prijatie v otvorenej vonkajšej transakcii a spojenie B súbežne odošle asynchrónny `INSERT` rovnakej `REQUEST_REFERENCE`,
- po commite spojenia A musí spojenie B dostať unikátnu kolíziu `1062` a následné volanie služby musí vrátiť `ALREADY_EXISTS` s `derivation_reference` toku A,
- test kontroluje výsledné počty `1 rezervácia + 1 beh + 2 doménové väzby` a po cielenom čistení `0 + 0 + 0`,
- praktické runtime overenie ešte nebolo vykonané; stav a hranice testu sú v [`TECHNICKE-NAVRHY/2026-07-23_SUBEZNE-OVERENIE-PRVEHO-PRIJATIA.md`](TECHNICKE-NAVRHY/2026-07-23_SUBEZNE-OVERENIE-PRVEHO-PRIJATIA.md).

### Webové súbežné overenie - checklist a matica

- bol aktualizovaný aktívny pracovný dokument [`postupy/2026-07-23_12-27_Copilot-checklist a testovacia matica.md`](postupy/2026-07-23_12-27_Copilot-checklist%20a%20testovacia%20matica.md),
- v dokumente bola vetva zosúladená na `main`,
- do checklistu bol doplnený samostatný krok pre feature flag `METODIKA_CONCURRENCY_WEB_ENABLED` vrátane testovania,
- do testovacej matice boli doplnené scenáre pre stabilný `.lock` pri atomickom `rename`, nepovolené HIT stavy, expiráciu cez finalization claim, manuálny cleanup bez falošného `COMPLETED_SUCCESS`, redakciu tombstone, pád participantu počas `accept()`, autorizáciu všetkých nových endpointov a testovanie feature flagu,
- register [`postupy/README.md`](postupy/README.md) bol zosúladený tak, že dokument `2026-07-23_14-21_IMPLEMENTACNY-CHECKLIST-A-TESTOVACIA-MATICA-WEBOVEHO-SUBEZNEHO-OVERENIA.md` je označený ako `PREKONANÝ` a jediná aktívna verzia zostáva dokument z `2026-07-23_12-27`.

### Webové súbežné overenie - implementácia krokov 10 az 13

- v kontroleri [`codei/app/Controllers/DiagnosticsController.php`](codei/app/Controllers/DiagnosticsController.php) bola doplnená result vetva, tombstone redukcia, sweep po `deleteAfter` a CSP nonce podpora pre diagnostics UI script,
- routes boli rozšírené o GET result endpoint v [`codei/app/Config/Routes.php`](codei/app/Config/Routes.php),
- diagnostics UI na stránke [`codei/app/Views/diagnostics/database.php`](codei/app/Views/diagnostics/database.php) bolo doplnené o `Start`, paralelne hit A/B fetch volania, polling resultu a zobrazenie troch osi + overall,
- validator tombstone/completed pravidiel bol rozšírený v [`codei/app/Services/DiagnosticsConcurrencyRunDocumentValidator.php`](codei/app/Services/DiagnosticsConcurrencyRunDocumentValidator.php),
- session testy boli rozšírené o UI a integračný webový scenár v [`codei/tests/session/DiagnosticsControllerTest.php`](codei/tests/session/DiagnosticsControllerTest.php),
- unit testy boli rozšírené o stavový model a validator v [`codei/tests/unit/DiagnosticsConcurrencyRunStateTest.php`](codei/tests/unit/DiagnosticsConcurrencyRunStateTest.php) a [`codei/tests/unit/DiagnosticsConcurrencyRunDocumentValidatorTest.php`](codei/tests/unit/DiagnosticsConcurrencyRunDocumentValidatorTest.php),
- behy `vendor/bin/phpunit --filter DiagnosticsControllerTest` a `vendor/bin/phpunit tests/unit` prešli; neblokujúce upozornenie ostáva iba `No code coverage driver available`.

---

## 2026-07-22

### Bezpečnosť a prevádzka

- zabezpečený webový setup povinným serverovým tokenom `METODIKA_SETUP_TOKEN`, CSRF ochranou, bezpečnostnými HTTP hlavičkami, požiadavkou HTTPS a automatickým uzamknutím po vytvorení konfigurácie; pozri [`app/setup.php`](app/setup.php),
- vedomé prepísanie existujúcej konfigurácie je možné iba po serverovom nastavení `METODIKA_SETUP_ALLOW_OVERWRITE=1`,
- nezabezpečené HTTP možno použiť iba pri výslovne povolenom lokálnom vývoji cez `METODIKA_SETUP_ALLOW_HTTP=1`,
- z [`codei/app/Config/Database.php`](codei/app/Config/Database.php) boli odstránené commitnuté databázové prihlasovacie údaje; sledovaný súbor obsahuje iba neškodný konfiguračný tvar a testovaciu SQLite skupinu,
- vytvorená verejná šablóna [`codei/.env.example`](codei/.env.example) bez platných tajomstiev,
- používateľ potvrdil rotáciu pôvodného databázového hesla a vytvorenie externého súboru `private/metodika.env` mimo `/codei`,
- implementovaný [`codei/app/Config/ExternalEnvironment.php`](codei/app/Config/ExternalEnvironment.php), ktorý načítava externé prostredie pred webovým aj CLI bootstrapom; prednosť má `METODIKA_ENV_FILE`,
- bezpečnostný kontrakt bol aktualizovaný v [`TECHNICKE-NAVRHY/2026-07-22_BEZPECNA-DATABAZOVA-KONFIGURACIA.md`](TECHNICKE-NAVRHY/2026-07-22_BEZPECNA-DATABAZOVA-KONFIGURACIA.md),
- pôvodná Validácia sanitizácie zostáva historicky zachovaná v [`TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md`](TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md),
- nová reValidácia po rotácii a externom env je v [`TECHNICKE-NAVRHY/2026-07-22_REVALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md`](TECHNICKE-NAVRHY/2026-07-22_REVALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md) s výsledkom `VALID_WITH_LIMITATIONS`; praktické serverové pripojenie zostáva nepotvrdené.

### Štruktúra a stav dokumentov

- projekt METODIKA bol doplnený do centrálneho registra [`PROJEKTY/ZoznamProjektov.md`](PROJEKTY/ZoznamProjektov.md) s autoritatívnym repozitárom, vetvou, aktuálnym stavom, významovými oblasťami a technickým prostredím,
- strom v [`README.md`](README.md) bol zosúladený s aktuálne evidovaným obsahom vetvy `main`,
- zavedený záväzný register stavov pracovných postupov v [`postupy/README.md`](postupy/README.md),
- zavedený záväzný register stavov pracovných poznámok v [`poznámky/README.md`](poznámky/README.md),
- zavedený osobitný koreň technickej architektúry [`TECHNICKE-NAVRHY/`](TECHNICKE-NAVRHY) s vlastným registrom [`TECHNICKE-NAVRHY/README.md`](TECHNICKE-NAVRHY/README.md),
- každý existujúci dokument v `postupy/` a `poznámky/` dostal explicitný stav,
- dokument `postupy/2026-07-21_13-52_LOG-MODEL-METODIC.md` zostáva označený ako `NEPLATNÝ`; ostatné existujúce metodické postupy zostávajú `PRACOVNÉ`, kým nie sú potvrdené a prenesené do autoritatívnych dokumentov,
- historický technický dokument [`postupy/2026-07-22_09-38_CodeIgniter.md`](postupy/2026-07-22_09-38_CodeIgniter.md) bol v registri označený `PREKONANÝ`; aktívna technická náhrada je v [`TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md`](TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md),
- vytvorený potvrdený pracovný postup [`postupy/Inicializácia práce.md`](postupy/Inicializácia%20práce.md), ktorého stav `POTVRDENÝ-NA-PRENESENIE` eviduje [`postupy/README.md`](postupy/README.md).

### Algoritmy otázok

- vytvorený prvý pracovný významový algoritmus [`postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md`](postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md),
- vytvorená a revidovaná pracovná ontológia vstupov [`postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md`](postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md),
- vytvorená pôvodná spoločná Validácia [`postupy/2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md`](postupy/2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md) s historickým výsledkom `CONDITIONALLY_VALID`,
- v odvodzovacom algoritme boli opravené tri blokujúce body: doplnený `INTENDED_APPLICABILITY_SCOPE`, pokus o `QUESTION_DERIVATION` sa zaznamenáva pred kontrolami a všetky zastavenia používajú jednotný `DERIVATION_RESULT` s auditnou stopou,
- vytvorená spoločná reValidácia [`postupy/2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md`](postupy/2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md) s výsledkom `VALID_WITH_LIMITATIONS`,
- vytvorený prvý aplikačný kontrakt [`postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md`](postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md) s topológiou `PARTIAL_RUN_WITH_ATOMIC_GATE`,
- vytvorená pôvodná Validácia kontraktu [`postupy/2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md`](postupy/2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md) s historickým výsledkom `CONDITIONALLY_VALID`,
- aplikačný kontrakt bol opravený o koreláciu `REQUEST_REFERENCE → QUESTION_DERIVATION → DERIVATION_RUN_RESULT → RESPONSE_TARGET_REFERENCE`, významový model `BRANCH_DEPENDENCY` a úplné deterministické pravidlo agregácie nadradeného `run_state`,
- vytvorená reValidácia [`postupy/2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md`](postupy/2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md) s výsledkom `VALID`,
- aplikačný kontrakt, jeho pôvodná Validácia aj reValidácia zostávajú `PRACOVNÉ`; stav eviduje [`postupy/README.md`](postupy/README.md),
- autoritatívnym východiskom zostávajú pravidlá otázok v [`OTAZKY/README.md`](OTAZKY/README.md), univerzálna matica v [`OTAZKY/UNIVERZALNE/Objektivita-XY.md`](OTAZKY/UNIVERZALNE/Objektivita-XY.md) a platné definície v [`POJMY-A-DEFINICIE.md`](POJMY-A-DEFINICIE.md).

### Technické návrhy

- vytvorený aktívny technický základ [`TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md`](TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md),
- vytvorený návrh [`TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md`](TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md) pre `QuestionDerivationApplicationService`,
- vytvorená replay politika a jej Validácia s rozhodnutím `IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE`,
- vytvorený repository kontrakt `REQUEST_REFERENCE` a spoločná Validácia s výsledkom `VALID`,
- vytvorený technický model uloženia a jeho Validácia s výsledkom `VALID`,
- vytvorený databázový návrh ôsmich tabuliek a jeho Validácia s výsledkom `VALID`,
- implementovaný bezpečný diagnostický príkaz [`codei/app/Commands/VerifyDatabaseCapabilities.php`](codei/app/Commands/VerifyDatabaseCapabilities.php), ktorý bez výpisu tajomstiev overuje verziu servera, InnoDB, `utf8mb4_bin` a `DATETIME(6)`,
- vytvorené CodeIgniter migrácie M1 až M8 v [`codei/app/Database/Migrations/`](codei/app/Database/Migrations), zatiaľ bez spustenia,
- implementačný stav je zaznamenaný v [`TECHNICKE-NAVRHY/2026-07-22_IMPLEMENTACIA-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md`](TECHNICKE-NAVRHY/2026-07-22_IMPLEMENTACIA-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md),
- statická Validácia implementácie je v [`TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-IMPLEMENTACIE-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md`](TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-IMPLEMENTACIE-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md) s výsledkom `VALID_WITH_LIMITATIONS`; server a vykonaná schéma zostávajú nepotvrdené,
- aktuálny stav technických dokumentov eviduje [`TECHNICKE-NAVRHY/README.md`](TECHNICKE-NAVRHY/README.md).

### Riadenie zmien

- CHANGELOG bol skrátený na záznam zmien s odkazmi na platné definície a registre stavov,
- do [`README.md`](README.md) bolo doplnené pravidlo, že zmena dokumentu, jeho stavu a záznamu v CHANGELOG tvoria jeden pracovný úkon.

### Integrácia CodeIgniter a hosting

- overený SHA256 a rozbalený balík `codei-git-ready.zip`; do repozitára bol pridaný runtime strom [`codei/`](codei),
- pre CodeIgniter boli doplnené šablóny Apache + PHP-FPM v [`codei/deploy/apache/README.md`](codei/deploy/apache/README.md) a konfigoch v `codei/deploy/apache/`,
- pre Hostinger Business boli doplnené fallback postupy a šablóny v [`codei/deploy/hostinger/README.md`](codei/deploy/hostinger/README.md),
- pre nasadenie bez zmeny website root boli pridané shim súbory [`codei/index.php`](codei/index.php) a [`codei/.htaccess`](codei/.htaccess), ktoré smerujú požiadavky do `public/`,
- base URL bola zosúladená v [`codei/app/Config/App.php`](codei/app/Config/App.php),
- pôvodný pracovný architektonický záznam v `postupy/` zostáva zachovaný historicky.

### Skripty spustenia a release

- pôvodná release logika bola presunutá do nového skriptu [`release.sh`](release.sh),
- skript [`startApp.sh`](startApp.sh) bol zmenený na lokálny launcher CodeIgniter servera,
- do hostinger dokumentácie boli doplnené voliteľné no-redirect šablóny,
- doplnená explicitná Codespaces konfigurácia [`.devcontainer/devcontainer.json`](.devcontainer/devcontainer.json) s `postCreateCommand` na technické zabezpečenie PHP runtime,
- vytvorený idempotentný skript [`.devcontainer/setup-php-extensions.sh`](.devcontainer/setup-php-extensions.sh), ktorý pre aktívny `/usr/local/php` rebuildne rovnakú verziu PHP s `intl` a `mysqli` a prepne symlink `current` bez prechodu na systémové `/usr/bin/php`,
- technický záznam je v [`TECHNICKE-NAVRHY/2026-07-22_CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md`](TECHNICKE-NAVRHY/2026-07-22_CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md),
- Validácia výsledku je v [`TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md`](TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md).
- doplnený bezpečný webový diagnostický endpoint databázy cez [`codei/app/Controllers/DiagnosticsController.php`](codei/app/Controllers/DiagnosticsController.php) a views v `codei/app/Views/diagnostics/`,
- diagnostika je gated serverovými premennými `METODIKA_DIAGNOSTICS_ENABLED` a `METODIKA_DIAGNOSTICS_TOKEN`, používa POST token overenie s `hash_equals`, krátkodobú session autorizáciu, CSRF filter a no-cache/noindex hlavičky,
- SQL capability logika je zdieľaná v [`codei/app/Services/DatabaseCapabilityInspector.php`](codei/app/Services/DatabaseCapabilityInspector.php) a používaná súčasne CLI príkazom [`codei/app/Commands/VerifyDatabaseCapabilities.php`](codei/app/Commands/VerifyDatabaseCapabilities.php) aj webovým controllerom,
- explicitné routes sú v [`codei/app/Config/Routes.php`](codei/app/Config/Routes.php) s vypnutým auto-routingom,
- do [`codei/.env.example`](codei/.env.example) boli doplnené bezpečné placeholdery pre diagnostics gate bez commitu reálneho tokenu,
- minimálne testy boli doplnené v [`codei/tests/session/DiagnosticsControllerTest.php`](codei/tests/session/DiagnosticsControllerTest.php) a [`codei/tests/unit/DatabaseCapabilityInspectorTest.php`](codei/tests/unit/DatabaseCapabilityInspectorTest.php),
- technický záznam je v [`TECHNICKE-NAVRHY/2026-07-22_WEBOVA-DIAGNOSTIKA-PRODUKCNEJ-DATABAZY.md`](TECHNICKE-NAVRHY/2026-07-22_WEBOVA-DIAGNOSTIKA-PRODUKCNEJ-DATABAZY.md),
- Validácia je v [`TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-WEBOVEJ-DIAGNOSTIKY-PRODUKCNEJ-DATABAZY.md`](TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-WEBOVEJ-DIAGNOSTIKY-PRODUKCNEJ-DATABAZY.md) s výsledkom `VALID_WITH_LIMITATIONS`.

---

## 2026-07-21

### Základ metodiky

- vytvorený autoritatívny koreň pojmov a rozmerov v [`POJMY-A-DEFINICIE.md`](POJMY-A-DEFINICIE.md),
- potvrdené rozlíšenie `X × Y`, `Z`, `T`, subjektivity `(Z, T)` a vzťahu `S`,
- potvrdený pracovný vzorec Autority a zaradenie Disciplíny pod Zodpovednosť,
- oddelené otázky od hodnotiacich záznamov,
- univerzálna objektívna matica presunutá do [`OTAZKY/UNIVERZALNE/Objektivita-XY.md`](OTAZKY/UNIVERZALNE/Objektivita-XY.md),
- otázky Disciplíny presunuté do [`OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md`](OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md),
- vytvorený metodický koreň siedmej plochy v [`OTAZKY/SIEDMA-PLOCHA-S.md`](OTAZKY/SIEDMA-PLOCHA-S.md),
- koreňové súbory [`uQestions.md`](uQestions.md) a [`DISCIPLINA.md`](DISCIPLINA.md) zostali ako rozcestníky.

### Pracovné modely a poznámky

- vznikli pracovné dokumenty v `postupy/` a pracovná poznámka v `poznámky/`,
- pôvodný dokument `postupy/2026-07-21_13-52_LOG-MODEL-METODIC.md` bol vyradený ako neplatný,
- potvrdené zostáva pravidlo, že databázová schéma ani implementácia nesmú predbehnúť elementárnu logiku.

### Obslužný softvér

- vytvorený adresár `app/` a prvá verzia `app/setup.php`,
- zavedené lokálne konfigurácie databáz METODIKY a MAPMET,
- pridaný [`.gitignore`](.gitignore) pre lokálne konfigurácie a prevádzkové súbory.

---

## Pravidlo ďalších zápisov

Každý nový záznam má obsahovať iba:

```text
dátum
× stručnú zmenu
× dotknutý súbor alebo register
× odkaz na miesto platnej definície
× prípadnú zmenu stavu dokumentu
```

Definícia sa v CHANGELOG neopakuje. Zapisuje sa iba odkaz na dokument, ktorý ju nesie.
