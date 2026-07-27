# Register stavov dokumentov v `postupy/`

## Účel

Tento súbor je záväzným registrom stavu každého dokumentu uloženého v adresári `postupy/`.

Dokument v tomto adresári nie je autoritatívnou definíciou iba preto, že obsahuje ucelený návrh alebo záver. Jeho platnosť určuje stav uvedený v tomto registri a prípadný odkaz na autoritatívny dokument.

## Povolené stavy

```text
PRACOVNÝ
POTVRDENÝ-NA-PRENESENIE
ČIASTOČNE-PREVZATÝ
PREKONANÝ
NEPLATNÝ
ARCHIVOVANÝ
```

Význam:

- `PRACOVNÝ` — otvorený pracovný dokument bez autority platnej definície.
- `POTVRDENÝ-NA-PRENESENIE` — obsah bol potvrdený, ale ešte musí byť prenesený do autoritatívneho dokumentu.
- `ČIASTOČNE-PREVZATÝ` — potvrdená časť už bola prenesená; zvyšok ostáva pracovný alebo neplatný.
- `PREKONANÝ` — nahradený novším pracovným alebo autoritatívnym riešením.
- `NEPLATNÝ` — nesmie byť použitý ako východisko návrhu ani implementácie.
- `ARCHIVOVANÝ` — zachovaný iba ako historický záznam.

Prívlastok `ZÁVÄZNÝ` nemení stav dokumentu na autoritatívnu definíciu. Určuje však povinnosť striktne dodržiavať jeho operačné poradie, rozsah a rozhodovacie brány až do výslovne zaznamenaného naplnenia, metodicky korektného zastavenia alebo nahradenia.

## Aktuálny register

| Dokument | Stav | Autoritatívny cieľ alebo poznámka |
|---|---|---|
| `WORK/INI/2026-07-27_11-46_INI_Krok_12_Jeden_release_a_uplny_audit_balika.md` | PRACOVNÝ | Samostatná deväťbodová inicializačná brána Kroku 12. Všetky body sú doložené, `GATE=OPEN`; checkpointy potvrdzujú Fázy 12.A–12.D ako splnené. Release `1.1.16` je v commite `fb243698...`, SHA-256 `04742c8b...`; úplný audit aj audit po prenose potvrdili 813/813 súborov, nulové rozdiely a zakázané artefakty. Podľa pokynu Autority sa čaká pred Fázou 12.E. |
| `PLAN/2026-07-27_11-51_Plan_Kroku_12_release_a_audit_balika.md` | PRACOVNÝ — **ZÁVÄZNÝ PRE KROK 12** | Podrobný vykonávací plán Fáz 12.A–12.F. Fázy 12.A–12.D sú splnené; nový release `1.1.16` vznikol raz a auditný run `30260322371` aj opätovný audit po prenose skončili `PASS`. Konzolový výstup jediného spustenia nebol zachovaný a je vedený ako pravdivá odchýlka bez opakovania release. Nasleduje Fáza 12.E, pred ktorou sa čaká na výslovný pokyn. |
| `WORK/2026-07-27_11-53_Inicializacia_analyza_a_plan_Kroku_12.md` | PRACOVNÝ | Záznam inicializácie, analýzy, vytvorenia záväzného plánu a vykonania Fáz 12.A–12.D. Dokladá zmrazený `/codei`, jediný release commit `fb243698...`, ZIP blob `24b49768...`, SHA-256 `04742c8b...`, 813/813 zhodných súborov a nulové zakázané artefakty. Produkcia zostala bez zásahu; Fáza 12.E nebola vykonaná. |
| `WORK/INI/2026-07-27_11-28_INI_naprava_evidencneho_uzavretia_Kroku_11.md` | PRACOVNÝ | Otvorená a splnená brána výhradne pre dokumentačnú nápravu rozporného evidenčného uzavretia Kroku 11; technický Krok 11 neopakuje ani znovu neotvára. |
| `WORK/2026-07-27_11-28_Naprava_evidencneho_uzavretia_Kroku_11.md` | PRACOVNÝ | Príčina, najmenšia náprava, read-after-write kritériá a rollback odstránenia rozporu `GATE=CLOSED` verzus `KROK_11=SPLNENÉ`. |
| `WORK/2026-07-27_10-20_Krok_11_Klasifikacia_a_testovacia_specifikacia.md` | PRACOVNÝ | Úplná klasifikácia, implementačný diff, testovacia špecifikácia a záverečná evidencia Fáz 11.B–11.F. Krok 11 je `SPLNENÉ`; finálny validačný head `bc85d18...`, run `30252640028`, merge `4cb2fe0a...`, cleanup `true`, produkcia bez zásahu, release `1.1.15` bez zmeny. |
| `WORK/INI/2026-07-27_08-47_INI_nacitanie-aktualneho-stavu-a-pokracovanie-overenia.md` | PREKONANÝ | Neúplný pokus vytvorený bez načítania obsahu autoritatívneho plánu a pôvodného INI. Priamo v súbore je označený ako prekonaný; jeho `GATE=CLOSED` nie je aktuálnou bránou projektu. |
| `WORK/INI/2026-07-26_07-37_INI_obnova-inicializacnej-metodiky-v2.md` | PRACOVNÝ | Inicializačný dôkaz opravy po chybnom merge; brána bola otvorená výlučne pre presnú obnovu potvrdeného blobu v2.0 a povinnú evidenciu. |
| `WORK/2026-07-26_07-42_Obnova_inicializacnej_metodiky_v2_po_merge.md` | PRACOVNÝ | Záznam príčiny merge hybridu, presnej obnovy 94-riadkovej verzie v2.0, read-after-write, Validácie a rollbacku. Výsledný blob `44729126508a0c9151fb2358badcb1445a425bd6`. |
| `WORK/INI/2026-07-26_06-44_INI_refaktorizacia-inicializacnej-metodiky-v2.md` | PRACOVNÝ | Inicializačný dôkaz refaktorizácie metodiky na v2.0; deväť bodov bolo doložených a brána otvorená výlučne pre presnú náhradu súboru a povinnú evidenciu. |
| `WORK/2026-07-26_06-52_Refaktorizacia_inicializacnej_metodiky_v2.md` | PRACOVNÝ | Záznam úplnej náhrady 432-riadkového predpisu štandardizovanou 94-riadkovou verziou v2.0, read-after-write, Validácie, rollbacku a evidencie. Výsledok `SPLNENÉ`. |
| `PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md` | PRACOVNÝ — **ZÁVÄZNÝ** | Aktuálny rámcový plán Krokov 11 až 15. Kroky 1 až 11 a Fázy 12.A–12.D sú `SPLNENÉ`; Krok 12 má `GATE=OPEN`, release `1.1.16` je úplne auditovaný a podľa podrobného plánu `PLAN/2026-07-27_11-51_Plan_Kroku_12_release_a_audit_balika.md` sa čaká pred Fázou 12.E. Nasadenie a Krok 13 zostávajú zatvorené. |
| `WORK/INI/2026-07-26_05-51_INI_dokoncenie-denneho-planu.md` | PRACOVNÝ | Inicializačný záznam dnešného plánovacieho úkonu. Po úplnom načítaní metodiky, projektu, histórie, aktívnych plánov, záznamov a zmenených technických závislostí otvoril bránu výlučne pre vytvorenie a evidenciu záväzného plánu; `/codei`, testy, release a produkcia sú mimo rozsahu. |
| `WORK/2026-07-26_06-11_Vytvorenie_zavazneho_planu_dokoncenia_testovacej_sustavy.md` | PRACOVNÝ | Záznam analýzy, vytvorenia a úplného vzdialeného read-backu nového 638-riadkového plánu. Výsledok `SPLNENÉ`; jediným nasledujúcim povoleným úkonom je Fáza 11.A v pôvodnom INI Kroku 11. |
| `WORK/INI/2026-07-25_18-11_INI_STOP_predbezny-plan-testov-naraz.md` | PRACOVNÝ | Povinný STOP po predčasných čiastkových diagnostických zásahoch. Určil, že celá testovacia sústava sa musí najprv pripraviť naraz; predčasné artefakty zostali `na rozhodnutie`. Je priamym vstupom aktuálneho plánu z 2026-07-26. |
| `WORK/INI/2026-07-25_17-33_INI_testovaci-krok-session-1.md` | PRACOVNÝ | Predčasný čiastkový INI pre produkčný test kroku session 1. Aktuálny plán ho drží v stave `NA_ROZHODNUTIE`; nie je samostatným oprávnením pokračovať a jeho kódové aj dátové následky sa musia klasifikovať vo Fáze 11.B. |
| `WORK/INI/2026-07-25_17-46_INI_diagnostika-evidence-kroku-1.md` | PRACOVNÝ | Predčasný čiastkový INI pre Evidence krok 1. Aktuálny plán ho drží v stave `NA_ROZHODNUTIE`; nie je samostatným oprávnením pokračovať a jeho kódové aj dátové následky sa musia klasifikovať vo Fáze 11.B. |
| `WORK/INI/2026-07-25_12-16_INI_Kontrola_predchadzajucich_ukonov_a_obnova_autority.md` | PREKONANÝ | Historický STOP záznam prvého pokusu, ktorého počiatočné čítanie nezachytilo úplný vzdialený stav. Nepoužíva sa ako platná brána ani ako východisko implementácie. |
| `WORK/INI/2026-07-25_12-23_INI_Obnova_metodickeho_pravidla_na_aktualnom_HEAD.md` | PREKONANÝ | Historický STOP záznam druhého pokusu. Brána bola korektne zastavená bezprostrednou kontrolou pred implementáciou, keď sa autoritatívny `main` zmenil o päť commitov. |
| `WORK/INI/2026-07-25_12-35_INI_Kontrola_predchadzajucich_ukonov_po_zmene_main.md` | PRACOVNÝ | Autoritatívny inicializačný záznam metodického doplnenia kontroly predchádzajúcich úkonov a bezpečnej obnovy dôkazov z preukázanej administrátorskej autority; deväť bodov `ÁNO`, `GATE=OPEN`. |
| `WORK/2026-07-25_12-42_Doplnenie_kontroly_predchadzajucich_ukonov.md` | PRACOVNÝ | Pracovný záznam vykonania, read-backu, hraníc a rollbacku nového metodického pravidla; nemení technický stav ani uzavretie Kroku 11. |
| `PLAN/2026-07-25_05-18_Plan_prace.md` | PREKONANÝ | Pôvodný záväzný plán Krokov 10 až 15. Kroky 1 až 10 zostávajú `SPLNENÉ`, ale spôsob pokračovania od Kroku 11 bol po predčasných zmenách nahradený plánom `PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md`. Zmena stavu: `PRACOVNÝ — ZÁVÄZNÝ → PREKONANÝ`, dôvod: 71 nových commitov, zmena vykonateľného kódu bez zmeny testov a STOP čiastkového postupu; dátum 2026-07-26. |
| `WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md` | PRACOVNÝ | Jediný autoritatívny inicializačný, pokračovací a záverečný záznam Kroku 11. Na začiatku aj na konci uvádza aktuálne `GATE=OPEN`, Fázy 11.A–11.F `SPLNENÉ`, merge `4cb2fe0a...`; starý zatvorený stav je iba označený historický dôkaz. |
| `WORK/INI/2026-07-25_10-33_INI_Krok_11_po_zmene_HEAD.md` | PREKONANÝ | Historický záznam medzistavu po súbežnej zmene HEAD. Autoritatívne pokračovanie bolo na výslovný pokyn používateľa konsolidované späť do pôvodného INI `2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md`; tento súbor sa nepoužíva ako samostatná brána ani ako východisko ďalšej práce. |
| `WORK/INI/2026-07-25_09-18_INI_Doplnenie_tabulky_metodickych_ukonov.md` | PRACOVNÝ | Nápravný inicializačný záznam po zistení, že pri uzavretí Kroku 10 nebola na obrazovke ani na konci INI použitá povinná tabuľka metodických úkonov. Otvoril výlučne dokumentačnú nápravu. |
| `WORK/INI/2026-07-25_08-14_INI_Dokoncenie_Kroku_10_po_STOP.md` | PRACOVNÝ | Dôkazový záznam Kroku 10: napravil chybné stotožnenie pomocného sandboxu s projektovým prostredím, prakticky overil PHP, Composer, MariaDB, migrácie, izoláciu, rollback a cleanup, otvoril bránu `GATE=OPEN` a po náprave obsahuje na úplnom konci povinnú tabuľku metodických úkonov ID 0–14. |
| `WORK/2026-07-25_09-01_Krok_10_Najmensia_funkcna_oprava_rezervacie.md` | PRACOVNÝ | Uzavretý záznam Kroku 10 s výsledkom `SPLNENÉ`: kontrola `insert()` pri `DBDebug=false`, presná korelácia rezervácie, druhý tok `ALREADY_EXISTS`, jediný history run a potvrdený cleanup. |
| `2026-07-25_09-05_CHECKPOINT-KROK-10-PRESNA-REZERVACIA.md` | PRACOVNÝ | Checkpoint po uzavretí Kroku 10. Funkčný commit `c90ae562...`; jediným nasledujúcim povoleným krokom je Krok 11 s novou samostatnou inicializačnou bránou. |
| `WORK/INI/2026-07-25_07-17_INI_Spresnenie_vyznamu_Gate_closed.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre metodické spresnenie, že `GATE=CLOSED` aktuálneho kroku neblokuje jeho vlastné overovacie úkony. |
| `WORK/INI/2026-07-25_05-17_INI_Zavazny_plan_2026-07-25.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre vytvorenie riadneho záväzného plánu na 2026-07-25. |
| `WORK/2026-07-25_05-20_Vytvorenie_zavazneho_planu.md` | PRACOVNÝ | Pracovný záznam vytvorenia, spätného načítania a evidenčného uzatvárania plánu z 2026-07-25; jeho plán bol 2026-07-26 prekonaný novým plánom pokračovania. |
| `WORK/INI/2026-07-25_05-33_INI_Uzavretie_planu_a_otvorenie_prace.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre odstránenie predbežného plánu, dokončenie evidencie a metodické otvorenie práce podľa plánu z 2026-07-25. |
| `WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md` | PRACOVNÝ | Uzavretý záznam Kroku 9 s výsledkom `SPLNENÉ`: dvojprocesový test rozlíšil raw stav od projekcie `load()`, potvrdil ochranu pred falošným timeoutom a zdôvodnil ponechanie `load()` bez zmeny. |
| `WORK/2026-07-24_13-45_Krok_8_Oprava_diagnostickeho_rozlisenia.md` | PRACOVNÝ | Uzavretý záznam Kroku 8 s výsledkom `SPLNENÉ`: diagnostické zlyhania sú rozlíšené podľa fázy a triedy chyby; interný log nesie úplný kontext a verejný dokument iba bezpečný kód. |
| `WORK/2026-07-24_13-25_Krok_7_Reprodukcia_korenovej_priciny_mimo_produkcie.md` | PRACOVNÝ | Uzavretý záznam Kroku 7 s výsledkom `SPLNENÉ`: koreňová príčina bola reprodukovaná v izolovanej MariaDB 11.4 cez reálnu MySQLi/InnoDB cestu, s potvrdeným rollbackom a cleanupom. |
| `WORK/INI/2026-07-24_13-34_INI_Zosuladenie_Kroku_7_a_Krok_8.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre zosúladenie Kroku 7 a samostatnú diagnostickú opravu Kroku 8. |
| `Inicializácia práce.md` | POTVRDENÝ-NA-PRENESENIE | Hlavný inicializačný predpis bol 2026-07-26 úplne refaktorovaný na štandardizovanú verziu v2.0. Po chybnom merge bol 2026-07-26 obnovený presne na potvrdených 94 riadkov a blob `44729126508a0c9151fb2358badcb1445a425bd6`; chybný hybridný blob `4c69dbd23fccb5c01250ec5be86dec63dadb67ca` je iba historickým dôkazom opravy. |
| `WORK/INI/2026-07-24_12-42_INI_Uprava_inicializacnej_brany.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre úpravu dokumentu `Inicializácia práce.md`; obsahuje deväť overených bodov, rozsah, kritérium úspechu a rollback. |
| `PLAN/2026-07-24_08-04_Plán_práce.md` | PREKONANÝ | Historický záväzný plán pred pokračovaním 2026-07-25. Kroky 1 až 9 boli `SPLNENÉ`; postup pokračovania bol neskôr nahradený plánom 2026-07-25 a následne aktuálnym plánom 2026-07-26. |
| `WORK/2026-07-24_11-42_Krok_6_Staticka_lokalizacia_chybovych_faz.md` | PRACOVNÝ | Uzavretý záznam Kroku 6 s výsledkom `SPLNENÉ`: produkčná výnimka je lokalizovaná do `CREATE_INITIAL_HISTORY_RUN`; statická príčinná cesta vedie cez nekontrolovaný neúspešný insert rezervácie a nepresný postcheck iba podľa `REQUEST_REFERENCE`. |
| `WORK/2026-07-24_11-05_Krok_5_Historicky_produkcny_dokaz.md` | PRACOVNÝ | Uzavretý záznam Kroku 5 s výsledkom `SPLNENÉ — dôkaz získaný`: potvrdené flagy, run/tombstone, neuskutočnený sweep a presná `RuntimeException` pri historickom behu bez rezervácie `REQUEST_REFERENCE`. |
| `WORK/2026-07-24_10-17_Krok_4_Audit_routovania_START_vetiev_a_UI.md` | PRACOVNÝ | Uzavretý záznam Kroku 4 s výsledkom `SPLNENÉ`: produkčný START kontrolér je určený, duplicitná vetva je mŕtva a UI chyba pri prezentácii `COMPLETED_FAILED` je potvrdená. |
| `WORK/2026-07-24_10-24_Krok_3_Audit_testovacej_matice_M01-M26.md` | PRACOVNÝ | Uzavretý záznam Kroku 3 s výsledkom `SPLNENÉ`: 5 scenárov `HOTOVÉ`, 15 `ČIASTOČNE`, 6 `CHYBNÉ`; obsahuje úplnú mapu M01–M26 a záväzný register testovacích dier. |
| `WORK/2026-07-24_09-55_Krok_2_Úplný_audit_checklistu_1-14.md` | PRACOVNÝ | Uzavretý záznam Kroku 2 s výsledkom `SPLNENÉ`: 1 bod `HOTOVÉ`, 9 bodov `ČIASTOČNE`, 4 body `CHYBNÉ`; najzávažnejšie nedostatky sú v bariére/timeout, `accept()`, integračnom teste a produkčnej Validácii. |
| `WORK/2026-07-24_09-31_Krok_1_Zmrazenie_repozitárového_východiska.md` | PRACOVNÝ | Uzavretý záznam Kroku 1 s výsledkom `SPLNENÉ`: auditovaný HEAD `fe562e1...`, release `1.1.9`, produkčný commit `3b91c4e...`, 24 commitov vpredu a úplný diff bez zmeny vykonateľného kódu. |
| `WORK/2026-07-24_09-12_STOP_Oprava_záväzného_plánu.md` | PRACOVNÝ | Záznam používateľom nariadeného STOP, identifikácie chyby pôvodného plánu a jeho opravy na jediný lineárny rad 15 krokov. |
| `WORK/2026-07-24_A1_Východiskový_stav.md` | PREKONANÝ | Historický záznam pôvodného A1. Jeho miešanie repozitárových a externých produkčných dôkazov bolo prekonané opraveným záväzným plánom 2026-07-24. |
| `WORK/2026-07-24_08-57_A1_Pokračovanie_a_produkčná_blokácia.md` | PREKONANÝ | Historický záznam blokácie, ktorá odhalila chybu plánu. Nahradený lineárnym pravidlom, podľa ktorého sa externý dôkaz rieši v samostatnom uzatvárateľnom Kroku 5. |
| `2026-07-24/07_44-Dnešný plán tvorba štruktúry` | PRACOVNÝ | Pracovný záznam používateľa obsahujúci predbežný audit, prvý plán, doplnenie a druhý plán; slúži ako historický vstup pre záväzný plán. |
| `2026-07-23_17-50_CHECKPOINT-WEBOVE-SUBEZNE-OVERENIE-PO-NEUSPESNEJ-PRODUKCNEJ-VALIDACII.md` | PRACOVNÝ | Záväzný checkpoint pre pokračovanie: produkčný run skončil `COMPLETED_FAILED`; presná príčina `FAILED_RUNTIME_ERROR` bola získaná v Kroku 5 a staticky lokalizovaná v Kroku 6. |
| `2026-07-23_12-27_Copilot-checklist a testovacia matica.md` | PRACOVNÝ | Aktívny checklist a matica boli po Kroku 11 aktualizované: checklist 1–13 je overený v izolovanom prostredí, M01–M26 sú `PASS_ALEBO_DÔKAZNE_VYRIEŠENÉ`; produkčný bod 14 zostáva úmyselne mimo Kroku 11. Finálnym dôkazom je run `30252640028`. |
| `2026-07-23_14-21_IMPLEMENTACNY-CHECKLIST-A-TESTOVACIA-MATICA-WEBOVEHO-SUBEZNEHO-OVERENIA.md` | PREKONANÝ | Nahradený dokumentom `2026-07-23_12-27_Copilot-checklist a testovacia matica.md`. |
| `2026-07-22_09-38_CodeIgniter.md` | PREKONANÝ | Historický technický podklad. Aktívna technická náhrada je v `TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md`. |
| `2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md` | PRACOVNÝ | Tri blokujúce body boli opravené; spoločná reValidácia skončila `VALID_WITH_LIMITATIONS`. |
| `2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Revidovaná pracovná ontológia vstupov; spoločná reValidácia skončila `VALID_WITH_LIMITATIONS`. |
| `2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md` | PRACOVNÝ | Historická spoločná Validácia s výsledkom `CONDITIONALLY_VALID`. |
| `2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md` | PRACOVNÝ | Nová spoločná reValidácia po opravách s výsledkom `VALID_WITH_LIMITATIONS`. |
| `2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Opravený aplikačný kontrakt s topológiou `PARTIAL_RUN_WITH_ATOMIC_GATE`; reValidácia skončila `VALID`. |
| `2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Historická Validácia kontraktu s výsledkom `CONDITIONALLY_VALID`. |
| `2026-07-22_TECHNICKY-PLAN-IMPLEMENTACIE-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Technický plán implementácie v CodeIgniter 4.7.4. |
| `2026-07-22_VALIDACIA-TECHNICKEHO-PLANU-IMPLEMENTACIE.md` | PRACOVNÝ | Validácia technického plánu s výsledkom `VALID`. |
| `2026-07-22_IMPLEMENTACIA-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Implementačný záznam aplikačného a infraštruktúrneho jadra. |
| `2026-07-21_AKTOR-A-AUTORITA.md` | PRACOVNÝ | Súvisí s `AUTORITA.md`; platí iba prenesený obsah. |
| `2026-07-21_AUTORITA-IDENTITY-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie a prípadné prenesenie. |
| `2026-07-21_DOKAZ-TVRDENIE-A-PRAVDA.md` | PRACOVNÝ | Čaká na potvrdenie a prípadné prenesenie. |
| `2026-07-21_IDENTITA-A-IDENTIFIKATORY.md` | PRACOVNÝ | Čaká na potvrdenie pravidiel identity a identifikátorov. |
| `2026-07-21_KONTINUITA-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie pravidiel kontinuity subjektu. |
| `2026-07-21_KRITERIA-IDENTITY-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie kritérií identity subjektu. |
| `2026-07-21_LOGICKE-ZDOVODNENIE-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie operatívneho testu subjektu. |
| `2026-07-21_METODICKE-UKONY.md` | PRACOVNÝ | Čaká na potvrdenie metodických úkonov. |
| `2026-07-21_MINIMALNY-LOGICKY-MODEL.md` | PRACOVNÝ | Významový model pred SQL, nie databázová autorita. |
| `2026-07-21_NASLEDKY-METODICKYCH-UKONOV.md` | PRACOVNÝ | Čaká na potvrdenie následkov metodických úkonov. |
| `2026-07-21_PLATNOST-A-UCINNOST.md` | PRACOVNÝ | Čaká na potvrdenie rozlíšenia platnosti a účinnosti. |
| `2026-07-21_POSTULAT-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie postulátu subjektu. |
| `2026-07-21_SKUTOCNOST-MERANIE-A-TVRDENIE.md` | PRACOVNÝ | Čaká na potvrdenie rozlíšenia skutočnosti, merania a tvrdenia. |
| `2026-07-21_VALIDACIA.md` | PRACOVNÝ | Súvisí s autoritatívnymi dokumentmi; platí iba prenesené jadro. |

## Pravidlo aktualizácie

Pri vytvorení, premenovaní, presunutí alebo významovej zmene dokumentu v `postupy/` sa musí v tom istom pracovnom kroku aktualizovať aj tento register a `CHANGELOG.md`.

Zmena stavu musí uvádzať:

```text
pôvodný stav
→ nový stav
→ dôvod
→ autoritatívny cieľ alebo náhradu
→ dátum zmeny
```
