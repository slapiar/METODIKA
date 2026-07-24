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
| `PLAN/2026-07-24_08-04_Plán_práce.md` | PRACOVNÝ — **ZÁVÄZNÝ** | Úplný plán práce pre pokračovanie po neúspešnej produkčnej Validácii. Je záväzný až do úplného naplnenia alebo metodicky korektného zastavenia podľa vlastných rozhodovacích brán. Pri každej inicializácii sa musí načítať a pokračovať presne podľa jeho aktuálnej fázy. Prívlastok udelený používateľom 2026-07-24. |
| `WORK/2026-07-24_A1_Východiskový_stav.md` | PRACOVNÝ | Záznam začatia Fázy A1 záväzného plánu, presunutý z `PLAN/` do `WORK/`: zmrazenie dostupného repozitárového a produkčného východiska, rozlíšenie potvrdených údajov a chýbajúcich produkčných dôkazov. |
| `WORK/2026-07-24_08-57_A1_Pokračovanie_a_produkčná_blokácia.md` | PRACOVNÝ | Časovaný záznam pokračovania A1. Potvrdzuje novú štruktúru `WORK/`, opravuje starú cestu v registri a eviduje, že A1 zostáva blokované chýbajúcim produkčným dôkazom; A2 sa zatiaľ nezačína. |
| `2026-07-24/07_44-Dnešný plán tvorba štruktúry` | PRACOVNÝ | Pracovný záznam používateľa obsahujúci predbežný audit, prvý plán, doplnenie a druhý plán; slúži ako historický vstup pre úplný plán v `PLAN/2026-07-24_08-04_Plán_práce.md`. |
| `2026-07-23_17-50_CHECKPOINT-WEBOVE-SUBEZNE-OVERENIE-PO-NEUSPESNEJ-PRODUKCNEJ-VALIDACII.md` | PRACOVNÝ | Záväzný checkpoint pre pokračovanie: produkčný run skončil `COMPLETED_FAILED`; potvrdené sú transport, otvorenie bariéry bez timeoutu, DB unikátnosť a cleanup, nepotvrdený zostáva aplikačný replay a presná príčina `FAILED_RUNTIME_ERROR`. Pred ďalším zápisom je povinný úplný audit podľa `Inicializácia práce.md`. |
| `2026-07-23_12-27_Copilot-checklist a testovacia matica.md` | PRACOVNÝ | Aktívny implementačný podklad pre krátko-žijúci webový diagnostický scenár súbežného prvého prijatia. Kroky 1 az 13 boli deklarované ako implementačne pokryté, ale po neúspešnej produkčnej Validácii a dnešných zásahoch musia byť znovu auditované; krok 14 je otvorený a produkčný výsledok je `COMPLETED_FAILED`. |
| `2026-07-23_14-21_IMPLEMENTACNY-CHECKLIST-A-TESTOVACIA-MATICA-WEBOVEHO-SUBEZNEHO-OVERENIA.md` | PREKONANÝ | Nahradený dokumentom `2026-07-23_12-27_Copilot-checklist a testovacia matica.md`, ktorý je vedený ako jediná aktívna pracovná verzia checklistu a testovacej matice webového súbežného overenia. |
| `2026-07-22_09-38_CodeIgniter.md` | PREKONANÝ | Historický technický podklad. Aktívne technické pravidlá boli oddelené do `TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md`; technické návrhy sa už nemajú ukladať medzi metodické postupy. Zmena 2026-07-22. |
| `2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md` | PRACOVNÝ | Tri blokujúce body boli opravené: doplnený `INTENDED_APPLICABILITY_SCOPE`, historický záznam pokusu vzniká pred kontrolami a každé zastavenie vracia jednotný `DERIVATION_RESULT`. Spoločná reValidácia skončila `VALID_WITH_LIMITATIONS`. |
| `2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Revidovaná pracovná ontológia vstupov. Spoločná reValidácia s opraveným algoritmom skončila `VALID_WITH_LIMITATIONS`; možno odvodiť aplikačný kontrakt pri zachovaní uvedeného obmedzenia. |
| `2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md` | PRACOVNÝ | Historická spoločná Validácia s výsledkom `CONDITIONALLY_VALID`; zachováva tri vtedy zistené blokujúce opravy. |
| `2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md` | PRACOVNÝ | Nová spoločná reValidácia po opravách. Výsledok `VALID_WITH_LIMITATIONS`; umožnila odvodiť aplikačný kontrakt. |
| `2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Opravený aplikačný kontrakt s topológiou `PARTIAL_RUN_WITH_ATOMIC_GATE`. Doplnil koreláciu požiadavky a výsledku, významový model vetvovej závislosti a deterministickú agregáciu `run_state`. ReValidácia skončila `VALID`. |
| `2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Historická Validácia aplikačného kontraktu s výsledkom `CONDITIONALLY_VALID`; zachováva tri vtedy zistené chýbajúce väzby. |
| `2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | ReValidácia opraveného aplikačného kontraktu. Výsledok `VALID`; všetkých pätnásť kritérií je splnených a možno odvodiť technický návrh aplikačnej služby. |
| `2026-07-21_10-23_DB-METODIKA-MAPMET.md` | PRACOVNÝ | Databázový návrh nesmie predbehnúť potvrdenú metodiku. |
| `2026-07-21_13-52_LOG-MODEL-METODIC.md` | NEPLATNÝ | V `CHANGELOG.md` bol výslovne označený ako neplatný pracovný návrh určený na revíziu. |
| `2026-07-21_AKTOR-A-AUTORITA.md` | PRACOVNÝ | Súvisí s `AUTORITA.md`; platí iba to, čo bolo prenesené do autoritatívneho dokumentu. |
| `2026-07-21_AUTORITA-IDENTITY-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie a prípadné prenesenie do autoritatívnych definícií. |
| `2026-07-21_DOKAZ-TVRDENIE-A-PRAVDA.md` | PRACOVNÝ | Čaká na potvrdenie a prípadné prenesenie do `POJMY-A-DEFINICIE.md` alebo osobitného autoritatívneho koreňa. |
| `2026-07-21_IDENTITA-A-IDENTIFIKATORY.md` | PRACOVNÝ | Čaká na potvrdenie pravidiel identity a identifikátorov. |
| `2026-07-21_KONTINUITA-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie pravidiel kontinuity subjektu. |
| `2026-07-21_KRITERIA-IDENTITY-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie kritérií identity subjektu. |
| `2026-07-21_LOGICKE-ZDOVODNENIE-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie operatívneho testu subjektu. |
| `2026-07-21_METODICKE-UKONY.md` | PRACOVNÝ | Čaká na potvrdenie metodických úkonov. |
| `2026-07-21_MINIMALNY-LOGICKY-MODEL.md` | PRACOVNÝ | Je významovým modelom pred SQL, nie databázovou autoritou. |
| `2026-07-21_NASLEDKY-METODICKYCH-UKONOV.md` | PRACOVNÝ | Čaká na potvrdenie následkov metodických úkonov. |
| `2026-07-21_PLATNOST-A-UCINNOST.md` | PRACOVNÝ | Čaká na potvrdenie rozlíšenia platnosti a účinnosti. |
| `2026-07-21_POSTULAT-SUBJEKTU.md` | PRACOVNÝ | Čaká na potvrdenie postulátu subjektu. |
| `2026-07-21_SKUTOCNOST-MERANIE-A-TVRDENIE.md` | PRACOVNÝ | Čaká na potvrdenie rozlíšenia skutočnosti, merania a tvrdenia. |
| `2026-07-21_VALIDACIA.md` | PRACOVNÝ | Súvisí s `AUTORITA.md` a `POJMY-A-DEFINICIE.md`; platí iba prenesené jadro. |

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
