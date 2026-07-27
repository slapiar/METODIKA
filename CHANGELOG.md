# Changelog

Tento súbor zaznamenáva **čo sa zmenilo, kedy sa to zmenilo a kde je uložená platná definícia alebo aktuálny pracovný stav**.

CHANGELOG nie je samostatným autoritatívnym zdrojom definícií. Pri rozpore rozhoduje dokument uvedený v odkaze a jeho stav podľa príslušného registra.

---

## 2026-07-27

### Inicializácia, analýza a záväzný plán Kroku 12

- samostatný INI [`postupy/WORK/INI/2026-07-27_11-46_INI_Krok_12_Jeden_release_a_uplny_audit_balika.md`](postupy/WORK/INI/2026-07-27_11-46_INI_Krok_12_Jeden_release_a_uplny_audit_balika.md) doložil všetkých deväť bodov a otvoril `GATE=OPEN` pre jeden release a úplný audit balíka bez nasadenia,
- aktuálny strom `/codei` `4ec1d840...` je zhodný s finálne validovaným technickým headom `bc85d18...`; od neho sa na `main` zmenila iba dokumentácia,
- audit posledného zdrojovo preukázaného produkčného release `1.1.9` potvrdil úplnú manifestovú a obsahovú zhodu ZIP-u s commitom `3b91c4e...`; SHA-256 je `5aa4d4bd458c9ae4a1a003594de101aba6a2d4e010d24c5bcc94e9b26a0b5e72`,
- audit historického ZIP-u `1.1.15` potvrdil zhodný manifest, ale odhalil obsahový rozdiel v `codei/app/Views/diagnostics/database.php`; balík preto zostáva nedotknutým historickým artefaktom a nie je označený ako overený rollback,
- vznikol záväzný plán [`postupy/PLAN/2026-07-27_11-51_Plan_Kroku_12_release_a_audit_balika.md`](postupy/PLAN/2026-07-27_11-51_Plan_Kroku_12_release_a_audit_balika.md) pre Fázy 12.A–12.F: zmrazenie základne, izolované release prostredie, jediný release `1.1.16`, úplný audit, rollback a uzavretie,
- pracovná evidencia je v [`postupy/WORK/2026-07-27_11-53_Inicializacia_analyza_a_plan_Kroku_12.md`](postupy/WORK/2026-07-27_11-53_Inicializacia_analyza_a_plan_Kroku_12.md); všeobecný plán a register boli zosúladené na aktívny Krok 12,
- `/codei`, testy, workflow, `RELEASE_VERSION=1.1.15`, existujúce ZIP-y, databáza a produkcia zostali bez zmeny; nový ZIP zatiaľ nevznikol a Krok 13 zostáva zatvorený.

### Náprava evidenčného uzavretia Kroku 11

- používateľ po synchronizácii správne zistil rozpor: pôvodný autoritatívny INI začínal starým `GATE=CLOSED`, hoci jeho koniec, plán a register tvrdili `KROK_11=SPLNENÉ`,
- príčinou bolo pripísanie otvorenia a uzavretia iba na koniec súboru bez nahradenia jeho úvodného aktuálneho stavu; aktívny plán navyše uprostred zachoval starú vetu o zatvorenej bráne,
- opravný INI [`postupy/WORK/INI/2026-07-27_11-28_INI_naprava_evidencneho_uzavretia_Kroku_11.md`](postupy/WORK/INI/2026-07-27_11-28_INI_naprava_evidencneho_uzavretia_Kroku_11.md) otvoril bránu iba pre dokumentačnú nápravu, nie pre opakovanie technického Kroku 11,
- pôvodný INI teraz na začiatku aj na konci uvádza `KROK_11=SPLNENÉ`, `GATE=OPEN`, finálny validačný head `bc85d18...`, run `30252640028` a merge `4cb2fe0a...`; starý zatvorený stav zostal iba ako označený historický dôkaz,
- neúplný INI z 08:47 je označený ako `PREKONANÝ` priamo vo svojom obsahu a plán, pracovný záznam, checklist a register boli zosúladené,
- náprava nemení `/codei`, testy, workflow, databázu, release `1.1.15`, ZIP balíky ani produkciu.

### Dokončenie Kroku 11 — úplná lokálna a integračná Validácia

- v jednej implementačnej dávke bola diagnostika GATE uzavretá za spoločnú autorizáciu, zápisy dostali CSRF, validáciu, idempotenciu a bezpečné chybové kódy a čítanie stavu zostalo bez mutácie,
- doplnená bola migrácia M9 s GATE tabuľkami, väzbami a unikátnymi indexmi, interný cleanup expirovaných concurrency runov, detail session a bezpečné dashboard routes,
- odstránené boli klasifikované pracovné snapshoty, bundle, writable výstupy, neobsluhované kópie JavaScriptu, placeholder a nulové zvuky s koncovou medzerou,
- GitHub Actions run `30252080061`, job `89932151438`, na technickom HEAD `d55dcc2d7ff0d9eedb5327b94e757f42cce66bca` skončil `success` s PHP `8.4.23`, Composerom `2.10.2`, MariaDB `11.4.12` a migráciami M1–M9,
- unit, session a integration suite spolu vykonali 69 testov a 380 asercií; reálny HTTP tok potvrdil login, CSRF, START, paralelný HIT A/B, RESULT, databázovú jedinečnosť, `CREATED+ALREADY_EXISTS`, tombstone a úplný cleanup,
- po Validácii boli testovacie DB riadky aj run-store/temp súbory nulové; M01–M26 sú `PASS_ALEBO_DÔKAZNE_VYRIEŠENÉ`, G01–G12 a P01–P06 sú `PASS`,
- úplný výsledok je v [`postupy/WORK/2026-07-27_10-20_Krok_11_Klasifikacia_a_testovacia_specifikacia.md`](postupy/WORK/2026-07-27_10-20_Krok_11_Klasifikacia_a_testovacia_specifikacia.md) a v pôvodnom INI [`postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md`](postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md),
- neúplný INI z 2026-07-27 08:47 je v registri označený `PREKONANÝ`; aktívny plán a checklist boli zosúladené na `KROK_11=SPLNENÉ`,
- produkcia zostala bez zásahu, release zostáva `1.1.15`, nový ZIP nevznikol a Krok 12 sa môže začať iba vlastným novým INI a bránou.

---

## 2026-07-26

### Obnova inicializačnej metodiky v2.0 po chybnom merge

- merge commit `1cd574cdd715b35fe0797cb597d00efdec3903ea` vytvoril v [`postupy/Inicializácia práce.md`](postupy/Inicializácia%20práce.md) hybrid starého úvodu a štandardizovaného tela v2.0,
- chybný blob `4c69dbd23fccb5c01250ec5be86dec63dadb67ca` bol nahradený presným potvrdeným 94-riadkovým obsahom v2.0,
- commit opravy je `0c794628db7842635b10162a8f89a2b37a3900f9` a výsledný blob je znovu `44729126508a0c9151fb2358badcb1445a425bd6`,
- inicializačný dôkaz opravy je v [`postupy/WORK/INI/2026-07-26_07-37_INI_obnova-inicializacnej-metodiky-v2.md`](postupy/WORK/INI/2026-07-26_07-37_INI_obnova-inicializacnej-metodiky-v2.md),
- pracovný záznam príčiny, read-after-write, Validácie a rollbacku je v [`postupy/WORK/2026-07-26_07-42_Obnova_inicializacnej_metodiky_v2_po_merge.md`](postupy/WORK/2026-07-26_07-42_Obnova_inicializacnej_metodiky_v2_po_merge.md),
- register [`postupy/README.md`](postupy/README.md) bol zosúladený; `/codei`, testy, migrácie, databáza, release balíky ani produkcia sa nemenili.

### Refaktorizácia inicializačnej metodiky na v2.0

- hlavný predpis [`postupy/Inicializácia práce.md`](postupy/Inicializácia%20práce.md) bol úplne nahradený používateľom dodanou štandardizovanou verziou v2.0,
- pôvodný rozsah 432 riadkov bol skrátený na 94 riadkov bez pridávania vlastných pravidiel; nový dokument obsahuje GATE šablónu, GATE logiku, fyzický pracovný postup a STOP protokol,
- inicializačný dôkaz je v [`postupy/WORK/INI/2026-07-26_06-44_INI_refaktorizacia-inicializacnej-metodiky-v2.md`](postupy/WORK/INI/2026-07-26_06-44_INI_refaktorizacia-inicializacnej-metodiky-v2.md),
- pracovný výsledok, read-after-write a rollback sú v [`postupy/WORK/2026-07-26_06-52_Refaktorizacia_inicializacnej_metodiky_v2.md`](postupy/WORK/2026-07-26_06-52_Refaktorizacia_inicializacnej_metodiky_v2.md),
- register [`postupy/README.md`](postupy/README.md) bol zosúladený; stav dokumentu zostáva `POTVRDENÝ-NA-PRENESENIE`,
- commit náhrady metodiky je `6e566cc0e6df6a2b9c0cfdaaa4fb7827d8a6b4df`, výsledný blob `44729126508a0c9151fb2358badcb1445a425bd6`,
- `/codei`, testy, migrácie, databáza, release verzia, balíky a produkcia sa týmto úkonom nemenili.

### Záväzný plán dokončenia testovacej sústavy

- po úplnom novom načítaní metodiky, projektu, aktuálneho plánu, INI Kroku 11, včerajšieho STOP, checklistu 1–14, matice M01–M26, histórie a zmenených technických zdrojov vznikla plánovacia brána [`postupy/WORK/INI/2026-07-26_05-51_INI_dokoncenie-denneho-planu.md`](postupy/WORK/INI/2026-07-26_05-51_INI_dokoncenie-denneho-planu.md),
- porovnanie technického základu Kroku 11 `d418e72c162bde324af7546c937af979bd75182e` s aktuálnym stavom pred plánom potvrdilo 71 nových commitov a 45 zmenených súborov; vykonateľný kód, konfigurácia, routes, kontrollery, model, views, JavaScript, release verzia a balíky sa zmenili, kým testovacie súbory sa nezmenili,
- aktuálna repozitárová verzia je `1.1.15`; pre aktuálny HEAD ani posledný technický commit nebol nájdený workflow run a produkčný release, feature flagy, testovacie riadky a cleanup zostávajú `NEZISTENÉ`, nie odhadnuté,
- vytvorený bol nový záväzný plán [`postupy/PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md`](postupy/PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md), ktorý zachováva Kroky 1 až 10 ako `SPLNENÉ` a riadi pokračovanie Krokov 11 až 15,
- Krok 11 pokračuje iba v pôvodnom INI a zostáva `GATE=CLOSED`; plán ho rozdeľuje na obnovu a zmrazenie stavu, klasifikáciu 71 commitov, návrh celej testovacej sústavy naraz, jednu implementačnú dávku, jednu úplnú lokálnu a integračnú Validáciu a uzavretie,
- až po úplnom úspechu Kroku 11 je dovolený jeden release, jedno nasadenie, jeden súvislý produkčný diagnostický priechod, úplný tombstone/sweep/cleanup a záverečná reValidácia,
- včerajšie čiastkové session/step/Evidence artefakty zostávajú `NA_ROZHODNUTIE`; nie sú automaticky platné ani automaticky určené na odstránenie,
- pracovný výsledok a read-back 638-riadkového plánu sú v [`postupy/WORK/2026-07-26_06-11_Vytvorenie_zavazneho_planu_dokoncenia_testovacej_sustavy.md`](postupy/WORK/2026-07-26_06-11_Vytvorenie_zavazneho_planu_dokoncenia_testovacej_sustavy.md),
- register [`postupy/README.md`](postupy/README.md) označuje plán z 2026-07-26 ako aktuálny `PRACOVNÝ — ZÁVÄZNÝ`, plán z 2026-07-25 ako `PREKONANÝ` a pôvodný INI Kroku 11 ako jediný pokračovací záznam so zatvorenou bránou,
- týmto plánovacím úkonom sa nemenil `/codei`, testy, workflowy, migrácie, `RELEASE_VERSION`, ZIP balíky, databáza ani produkcia,
- jediným nasledujúcim povoleným pracovným úkonom je Fáza 11.A — obnova a zmrazenie skutočného stavu v pôvodnom INI Kroku 11.

---

## 2026-07-25

### Povinná kontrola predchádzajúcich úkonov a obnova dôkazov

- do [`postupy/Inicializácia práce.md`](postupy/Inicializácia%20práce.md) bol doplnený záväzný príkaz preveriť pred vykonaním každého bodu nového kroku jeho možné predchádzajúce vykonanie v tom istom projekte,
- existujúci dôkaz sa neopakuje ani automaticky nepreberá; použije sa až po novom načítaní, overení presnej zhody a kontinuity voči aktuálnemu HEAD, prostrediu, konfigurácii a rozsahu,
- ak dôkaz nie je priamo dostupný, najprv sa vykoná bezpečný pokus o jeho obnovenie alebo sprístupnenie prostredníctvom preukázanej administrátorskej autority; autorita neumožňuje domýšľanie výsledku, obchádzanie hraníc ani neoprávnenú zmenu produkcie,
- autoritatívna brána úkonu je v [`postupy/WORK/INI/2026-07-25_12-35_INI_Kontrola_predchadzajucich_ukonov_po_zmene_main.md`](postupy/WORK/INI/2026-07-25_12-35_INI_Kontrola_predchadzajucich_ukonov_po_zmene_main.md); dva predchádzajúce pokusy zostávajú zachované ako metodické STOP záznamy,
- úplný pracovný výsledok je v [`postupy/WORK/2026-07-25_12-42_Doplnenie_kontroly_predchadzajucich_ukonov.md`](postupy/WORK/2026-07-25_12-42_Doplnenie_kontroly_predchadzajucich_ukonov.md), metodický commit je `06eac77e998f8ac164566717c86ca50e8ccef3c2` a výsledný blob metodiky `1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19`,
- register [`postupy/README.md`](postupy/README.md) bol zosúladený; súbežné zmeny vykonateľných súborov mimo predmetu tohto úkonu neboli prepisované ani zahrnuté do jeho výsledku,
- týmto úkonom sa nemenil vykonateľný kód, databáza, workflow, produkcia ani technické uzavretie Kroku 11; jeho správne dokončenie zostáva samostatným nasledujúcim krokom.

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

- vonkajšie spracovanie v `DiagnosticsController::executeAcceptIfReady()` rozlišuje fázy `BUILD_INITIAL_RUN`, `LOAD_PAYLOAD_FINGERPRINT`, `CREATE_ACCEPTANCE_RUNNER`, `APPLICATION_ACCEPT` a `WRITE_PARTICIPANT_RESULT`,
- bezpečný kód nesie súčin `fáza × trieda chyby`; raw text výnimky sa zapisuje iba do serverového logu spolu s `runId` a participantom,
- verejný run dokument a UI dostávajú iba bezpečný kód,
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
- záväzný plán [`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`](postupy/PLAN/2026-07-24_08-04_Plán-práce.md) bol opravený na jediný lineárny rad 15 krokov; v jednom čase môže byť otvorený presne jeden krok,
- každý krok sa musí pred otvorením ďalšieho uzavrieť ako `SPLNENÉ`, `UZAVRETÉ S OBMEDZENÍM` alebo `ZASTAVENÉ ROZHODOVACOU BRÁNOU`,
- externý produkčný dôkaz bol oddelený do samostatného Kroku 5, ktorého nedostupnosť sa uzavrie s obmedzením namiesto ponechania práce v blokovanom medzistave,
- vznikol pracovný záznam [`postupy/WORK/2026-07-24_09-12_STOP_Oprava_záväzného_plánu.md`](postupy/WORK/2026-07-24_09-12_STOP_Oprava_záväzného-plánu.md),
- pôvodné A1 záznamy boli v [`postupy/README.md`](postupy/README.md) označené ako `PREKONANÉ`; aktuálnym miestom práce je Krok 1 opravenej verzie plánu.

### Plán pokračovania webového súbežného overenia

- vytvorený úplný pracovný plán [`postupy/PLAN/2026-07-24_08-04_Plán-práce.md`](postupy/PLAN/2026-07-24_08-04_Plán-práce.md), ktorý nahrádza neúplné pracovné návrhy jedným riadeným postupom s rozhodovacími bránami, dôkazmi, rollbackmi a kritériami Validácie,
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
