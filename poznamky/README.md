# Register stavov dokumentov v `poznámky/`

## Účel

Tento súbor je záväzným registrom stavu každého dokumentu uloženého v adresári `poznámky/`.

Poznámka zachytáva pracovnú myšlienku, pozorovanie, hypotézu alebo surový záznam. Sama osebe nemá Autoritu platnej definície ani pracovného postupu.

## Povolené stavy

```text
PRACOVNÁ-POZNÁMKA
PRENESENÁ
ČIASTOČNE-PRENESENÁ
PREKONANÁ
NEPLATNÁ
ARCHIVOVANÁ
```

## Aktuálny register

| Dokument | Stav | Autoritatívny cieľ alebo poznámka |
|---|---|---|
| `2026-07-21_08-06_DB-OTAZKY-ALG.md` | PRACOVNÁ-POZNÁMKA | Zachováva počiatočný pracovný smer. Databázový ani algoritmický model z nej nesmie byť implementovaný bez potvrdenia v metodike a postupe. |

## Pravidlo aktualizácie

Pri vytvorení, premenovaní, presunutí alebo významovej zmene dokumentu v `poznámky/` sa musí v tom istom pracovnom kroku aktualizovať aj tento register a `CHANGELOG.md`.

Ak sa obsah poznámky prenesie do pracovného postupu alebo autoritatívneho dokumentu, register musí uviesť presný cieľ a rozsah prenesenia. Pôvodná poznámka sa neprepisuje tak, aby sa stratila história jej vzniku.
