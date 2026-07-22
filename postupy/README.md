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
| `2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md` | PRACOVNÝ | Prvý minimálny významový algoritmus od univerzálnej otázky ku kandidátovi špecifickej otázky. Čaká na metodické preskúmanie a potvrdenie. |
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
