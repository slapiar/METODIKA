# INICIALIZÁCIA NÁPRAVY — Odstránenie predčasnej vetvy `tmp-do-not-use`

## Stav brány

```text
GATE=CLOSED
```

## Účel kroku

Bez zásahu do autoritatívnej vetvy `main` overiť a odstrániť iba predčasne vytvorenú pomocnú vetvu `tmp-do-not-use`, evidovanú v STOP zázname:

`postupy/WORK/INI/2026-07-25_08-06_INI_STOP_predcasne_vytvorenie_vetvy.md`

## Inicializačná tabuľka

| Bod | Stav | Dôkaz / čo treba dokončiť |
|---|---|---|
| 1. Metodika načítaná | ÁNO | celý aktuálny obsah `postupy/Inicializácia práce.md`, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79` |
| 2. Projekt a autoritatívny zdroj | ÁNO | `slapiar/METODIKA`, autoritatívna vetva `main` |
| 3. Vetva a HEAD | ÁNO | HEAD `main` po STOP zázname: `29985b8da5392a6847516d58aa79ce52a24453db` |
| 4. Potrebné prístupy | NEOVERENÉ | treba prakticky potvrdiť čítanie referencie a oprávnenie odstrániť neautoritatívnu vetvu |
| 5. Prostredie | ÁNO | pre tento krok je potrebné iba GitHub úložisko; runtime ani DB nie sú predmetom kroku |
| 6. Závislosti | NEOVERENÉ | treba potvrdiť existenciu `refs/heads/tmp-do-not-use` a že ukazuje na commit odvodený z `main` |
| 7. Predmet a hranice | ÁNO | iba odstránenie `tmp-do-not-use`; žiadna zmena obsahu ani HEAD vetvy `main` |
| 8. Kritérium úspechu | ÁNO | referencia `tmp-do-not-use` neexistuje; `main` a jej obsah zostanú nezmenené |
| 9. Rollback | ÁNO | ak by bolo odstránenie chybné, vetvu možno obnoviť na pôvodný overený SHA; `main` sa nemení |

```text
GATE=CLOSED
BLOKUJÚCI_BOD=4 a 6 — praktické overenie prístupu a presnej referencie
CHÝBAJÚCI_DÔKAZ=existencia a SHA vetvy tmp-do-not-use a funkčné oprávnenie na odstránenie tejto referencie
POVOLENÝ_ĎALŠÍ_ÚKON=iba načítať referenciu tmp-do-not-use, porovnať ju s main a po doložení bodov 4 a 6 odstrániť výlučne túto vetvu
```

## Dôkaz prečítania a vykonania metodických pokynov

```text
#ID=0  R=1 W=1
#ID=1  R=1 W=1
#ID=2  R=1 W=1
#ID=3  R=1 W=1
#ID=4  R=1 W=0
#ID=5  R=1 W=1
#ID=6  R=1 W=0
#ID=7  R=1 W=1
#ID=8  R=1 W=0
#ID=9  R=1 W=0
#ID=10 R=1 W=0
#ID=11 R=1 W=0
#ID=12 R=1 W=0
#ID=13 R=1 W=1
#ID=14 R=1 W=0
```
