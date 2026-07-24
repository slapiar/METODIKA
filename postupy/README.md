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
| `Inicializácia práce.md` | POTVRDENÝ-NA-PRENESENIE | Potvrdená syntéza existujúcich pravidiel pre povinnú inicializáciu každej práce. Cieľom prípadného prenesenia sú `README.md` a `CHECKLISTY/StartProjektu.md`. |
| `PLAN/2026-07-24_08-04_Plán_práce.md` | PRACOVNÝ — **ZÁVÄZNÝ** | Opravená striktne lineárna verzia. Kroky 1 a 2 sú `SPLNENÉ`; jediným nasledujúcim povoleným krokom je Krok 3 — audit testovacej matice M01–M26. |
| `WORK/2026-07-24_09-55_Krok_2_Úplný_audit_checklistu_1-14.md` | PRACOVNÝ | Uzavretý záznam Kroku 2 s výsledkom `SPLNENÉ`: 1 bod `HOTOVÉ`, 9 bodov `ČIASTOČNE`, 4 body `CHYBNÉ`; najzávažnejšie nedostatky sú v bariére/timeout, `accept()`, integračnom teste a produkčnej Validácii. |
| `WORK/2026-07-24_09-31_Krok_1_Zmrazenie_repozitárového_východiska.md` | PRACOVNÝ | Uzavretý záznam Kroku 1 s výsledkom `SPLNENÉ`: auditovaný HEAD `fe562e1...`, release `1.1.9`, produkčný commit `3b91c4e...`, 24 commitov vpredu a úplný diff bez zmeny vykonateľného kódu. |
| `WORK/2026-07-24_09-12_STOP_Oprava_záväzného_plánu.md` | PRACOVNÝ | Záznam používateľom nariadeného STOP, identifikácie chyby pôvodného plánu a jeho opravy na jediný lineárny rad 15 krokov. |
| `WORK/2026-07-24_A1_Východiskový_stav.md` | PREKONANÝ | Historický záznam pôvodného A1. Jeho miešanie repozitárových a externých produkčných dôkazov bolo prekonané opraveným záväzným plánom 2026-07-24. |
| `WORK/2026-07-24_08-57_A1_Pokračovanie_a_produkčná_blokácia.md` | PREKONANÝ | Historický záznam blokácie, ktorá odhalila chybu plánu. Nahradený lineárnym pravidlom, podľa ktorého sa externý dôkaz rieši v samostatnom uzatvárateľnom Kroku 5. |
| `2026-07-24/07_44-Dnešný plán tvorba štruktúry` | PRACOVNÝ | Pracovný záznam používateľa obsahujúci predbežný audit, prvý plán, doplnenie a druhý plán; slúži ako historický vstup pre záväzný plán. |
| `2026-07-23_17-50_CHECKPOINT-WEBOVE-SUBEZNE-OVERENIE-PO-NEUSPESNEJ-PRODUKCNEJ-VALIDACII.md` | PRACOVNÝ | Záväzný checkpoint pre pokračovanie: produkčný run skončil `COMPLETED_FAILED`; potvrdené sú transport, otvorenie bariéry bez timeoutu, DB unikátnosť a cleanup, nepotvrdený zostáva aplikačný replay a presná príčina `FAILED_RUNTIME_ERROR`. |
| `2026-07-23_12-27_Copilot-checklist a testovacia matica.md` | PRACOVNÝ | Aktívny implementačný podklad. Krok 2 záväzného plánu znovu auditoval checklist 1–14; podrobný audit je v `WORK/2026-07-24_09-55_Krok_2_Úplný_audit_checklistu_1-14.md`. |
| `2026-07-23_14-21_IMPLEMENTACNY-CHECKLIST-A-TESTOVACIA-MATICA-WEBOVEHO-SUBEZNEHO-OVERENIA.md` | PREKONANÝ | Nahradený dokumentom `2026-07-23_12-27_Copilot-checklist a testovacia matica.md`. |
| `2026-07-22_09-38_CodeIgniter.md` | PREKONANÝ | Historický technický podklad. Aktívna technická náhrada je v `TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md`. |
| `2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md` | PRACOVNÝ | Tri blokujúce body boli opravené; spoločná reValidácia skončila `VALID_WITH_LIMITATIONS`. |
| `2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Revidovaná pracovná ontológia vstupov; spoločná reValidácia skončila `VALID_WITH_LIMITATIONS`. |
| `2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md` | PRACOVNÝ | Historická spoločná Validácia s výsledkom `CONDITIONALLY_VALID`. |
| `2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md` | PRACOVNÝ | Nová spoločná reValidácia po opravách s výsledkom `VALID_WITH_LIMITATIONS`. |
| `2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Opravený aplikačný kontrakt s topológiou `PARTIAL_RUN_WITH_ATOMIC_GATE`; reValidácia skončila `VALID`. |
| `2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Historická Validácia s výsledkom `CONDITIONALLY_VALID`. |
| `2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | ReValidácia opraveného aplikačného kontraktu; výsledok `VALID`. |
| `2026-07-21_10-23_DB-METODIKA-MAPMET.md` | PRACOVNÝ | Databázový návrh nesmie predbehnúť potvrdenú metodiku. |
| `2026-07-21_13-52_LOG-MODEL-METODIC.md` | NEPLATNÝ | Výslovne označený ako neplatný pracovný návrh určený na revíziu. |
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
