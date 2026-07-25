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
| `PLAN/2026-07-25_05-18_Plan_prace.md` | PRACOVNÝ — **ZÁVÄZNÝ** | Riadny plán práce na 2026-07-25. Kroky 1 až 9 sú `SPLNENÉ`; jediným nasledujúcim povoleným krokom je Krok 10. Nahradil a po pokyne používateľa odstránil predbežný plán. |
| `WORK/INI/2026-07-25_07-17_INI_Spresnenie_vyznamu_Gate_closed.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre metodické spresnenie, že `GATE=CLOSED` aktuálneho kroku neblokuje jeho vlastné overovacie úkony. |
| `WORK/INI/2026-07-25_05-17_INI_Zavazny_plan_2026-07-25.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre vytvorenie riadneho záväzného plánu na 2026-07-25. |
| `WORK/2026-07-25_05-20_Vytvorenie_zavazneho_planu.md` | PRACOVNÝ | Pracovný záznam vytvorenia, spätného načítania a evidenčného uzatvárania dnešného plánu. |
| `WORK/INI/2026-07-25_05-33_INI_Uzavretie_planu_a_otvorenie_prace.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre odstránenie predbežného plánu, dokončenie evidencie a metodické otvorenie práce podľa dnešného plánu. |
| `WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md` | PRACOVNÝ | Uzavretý záznam Kroku 9 s výsledkom `SPLNENÉ`: dvojprocesový test rozlíšil raw stav od projekcie `load()`, potvrdil ochranu pred falošným timeoutom a zdôvodnil ponechanie `load()` bez zmeny. |
| `WORK/2026-07-24_13-45_Krok_8_Oprava_diagnostickeho_rozlisenia.md` | PRACOVNÝ | Uzavretý záznam Kroku 8 s výsledkom `SPLNENÉ`: diagnostické zlyhania sú rozlíšené podľa fázy a triedy chyby; interný log nesie úplný kontext a verejný dokument iba bezpečný kód. |
| `WORK/2026-07-24_13-25_Krok_7_Reprodukcia_korenovej_priciny_mimo_produkcie.md` | PRACOVNÝ | Uzavretý záznam Kroku 7 s výsledkom `SPLNENÉ`: koreňová príčina bola reprodukovaná v izolovanej MariaDB 11.4 cez reálnu MySQLi/InnoDB cestu, s potvrdeným rollbackom a cleanupom. |
| `WORK/INI/2026-07-24_13-34_INI_Zosuladenie_Kroku_7_a_Krok_8.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre zosúladenie Kroku 7 a samostatnú diagnostickú opravu Kroku 8. |
| `Inicializácia práce.md` | POTVRDENÝ-NA-PRENESENIE | Sprísnená záväzná inicializačná brána. Výslovne rozlišuje `GATE=OPEN` predchádzajúceho kroku od `GATE=CLOSED` aktuálneho kroku: zatvorená brána aktuálneho kroku neblokuje dokončenie jeho bodov 1 až 9, ale až analýzu, návrh, implementáciu a ďalšie úkony vyžadujúce otvorenú bránu. Cieľom prípadného prenesenia sú `README.md` a `CHECKLISTY/StartProjektu.md`. |
| `WORK/INI/2026-07-24_12-42_INI_Uprava_inicializacnej_brany.md` | PRACOVNÝ | Dôkazový záznam otvorenia brány pre úpravu dokumentu `Inicializácia práce.md`; obsahuje deväť overených bodov, rozsah, kritérium úspechu a rollback. |
| `PLAN/2026-07-24_08-04_Plán_práce.md` | PRACOVNÝ — **ZÁVÄZNÝ** | Opravená striktne lineárna verzia. Kroky 1 až 9 sú `SPLNENÉ`; jediným nasledujúcim povoleným krokom je Krok 10 — najmenšia funkčná oprava koreňovej príčiny. |
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
| `2026-07-23_12-27_Copilot-checklist a testovacia matica.md` | PRACOVNÝ | Aktívny implementačný podklad. Kroky 2 až 6 záväzného plánu auditovali checklist 1–14, maticu M01–M26, routovanie, START vetvy, UI, historický produkčný dôkaz a statickú chybovú cestu; podrobné výsledky sú v príslušných záznamoch `WORK/`. |
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
