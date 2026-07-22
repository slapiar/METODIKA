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

## Aktuálny register

| Dokument | Stav | Autoritatívny cieľ alebo poznámka |
|---|---|---|
| `Inicializácia práce.md` | POTVRDENÝ-NA-PRENESENIE | Potvrdená syntéza existujúcich pravidiel pre povinnú inicializáciu každej práce. Cieľom prípadného prenesenia sú `README.md` a `CHECKLISTY/StartProjektu.md`. |
| `2026-07-22_09-38_CodeIgniter.md` | PRACOVNÝ | Pracovné architektonické pochopenie CodeIgniter 4.7.4 ako technického nosiča METODIKY. Čaká na preskúmanie a potvrdenie pred implementáciou aplikačnej štruktúry. |
| `2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md` | PRACOVNÝ | Tri blokujúce body boli opravené: doplnený `INTENDED_APPLICABILITY_SCOPE`, historický záznam pokusu vzniká pred kontrolami a každé zastavenie vracia jednotný `DERIVATION_RESULT`. Spoločná reValidácia skončila `VALID_WITH_LIMITATIONS`. |
| `2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Revidovaná pracovná ontológia vstupov. Spoločná reValidácia s opraveným algoritmom skončila `VALID_WITH_LIMITATIONS`; možno odvodiť aplikačný kontrakt pri zachovaní uvedeného obmedzenia. |
| `2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md` | PRACOVNÝ | Historická spoločná Validácia s výsledkom `CONDITIONALLY_VALID`; zachováva tri vtedy zistené blokujúce opravy. |
| `2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md` | PRACOVNÝ | Nová spoločná reValidácia po opravách. Výsledok `VALID_WITH_LIMITATIONS`; aplikačný kontrakt musí určiť atómové alebo čiastočné spracovanie jedného behu. |
| `2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Prvý aplikačný kontrakt s topológiou `PARTIAL_RUN_WITH_ATOMIC_GATE`. Validácia skončila `CONDITIONALLY_VALID`; treba doplniť koreláciu zdroja a výsledku, význam vetvovej závislosti a deterministickú agregáciu `run_state`. |
| `2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Validácia aplikačného kontraktu proti ontológii, algoritmu a reValidácii. Výsledok `CONDITIONALLY_VALID`; vnútorná atómová brána a izolácia vetiev sú správne, tri chýbajúce väzby blokujú technický návrh služby. |
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
