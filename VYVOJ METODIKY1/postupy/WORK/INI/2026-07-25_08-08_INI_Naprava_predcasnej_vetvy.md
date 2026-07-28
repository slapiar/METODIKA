# INICIALIZÁCIA NÁPRAVY — Odstránenie predčasnej vetvy `tmp-do-not-use`

## Stav brány

```text
GATE=CLOSED
```

## Účel kroku

Bez zásahu do autoritatívnej vetvy `main` overiť a odstrániť iba predčasne vytvorenú pomocnú vetvu `tmp-do-not-use`, evidovanú v STOP zázname:

`postupy/WORK/INI/2026-07-25_08-06_INI_STOP_predcasne_vytvorenie_vetvy.md`

## Inicializačná tabuľka

| Bod | Stav | Dôkaz / výsledok |
|---|---|---|
| 1. Metodika načítaná | ÁNO | celý aktuálny obsah `postupy/Inicializácia práce.md`, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79` |
| 2. Projekt a autoritatívny zdroj | ÁNO | `slapiar/METODIKA`, autoritatívna vetva `main` |
| 3. Vetva a HEAD | ÁNO | aktuálny HEAD `main` pri porovnaní: `de8b02637c9b8cb9c789ebf3a3b4b00c2fab528c` |
| 4. Potrebné prístupy | NEOVERENÉ | čítanie referencií je prakticky potvrdené; konektor však neposkytuje operáciu odstránenia vetvy a lokálne prostredie nemá `gh` ani sieťový prístup |
| 5. Prostredie | ÁNO | pre tento krok je potrebné iba GitHub úložisko; runtime ani DB nie sú predmetom kroku |
| 6. Závislosti | ÁNO | `tmp-do-not-use` existuje a ukazuje na `44401bc7b41ab371b9220162be300874c1286789`; voči `main` je pozadu o dva commity, bez vlastných commitov a bez rozdielov v súboroch |
| 7. Predmet a hranice | ÁNO | iba odstránenie `tmp-do-not-use`; žiadna zmena obsahu ani HEAD vetvy `main` |
| 8. Kritérium úspechu | ÁNO | referencia `tmp-do-not-use` neexistuje; `main` a jej obsah zostanú nezmenené |
| 9. Rollback | ÁNO | ak by bolo odstránenie chybné, vetvu možno obnoviť na SHA `44401bc7b41ab371b9220162be300874c1286789`; `main` sa nemení |

## Praktický dôkaz referencie

```text
base=main
head=tmp-do-not-use
status=behind
ahead_by=0
behind_by=2
head_sha=44401bc7b41ab371b9220162be300874c1286789
files=[]
```

## Vyhodnotenie

```text
1=ÁNO
2=ÁNO
3=ÁNO
4=NEOVERENÉ
5=ÁNO
6=ÁNO
7=ÁNO
8=ÁNO
9=ÁNO
GATE=CLOSED
```

```text
BLOKUJÚCI_BOD=4 — praktické oprávnenie a dostupný nástroj na odstránenie Git referencie
CHÝBAJÚCI_DÔKAZ=úspešné odstránenie refs/heads/tmp-do-not-use a následné čítacie potvrdenie, že referencia už neexistuje
POVOLENÝ_ĎALŠÍ_ÚKON=iba odstrániť presne refs/heads/tmp-do-not-use nástrojom s oprávnením delete ref a následne overiť jej neprítomnosť; main sa nesmie meniť
```

## Dôkaz prečítania a vykonania metodických pokynov

```text
#ID=0  R=1 W=1
#ID=1  R=1 W=1
#ID=2  R=1 W=1
#ID=3  R=1 W=1
#ID=4  R=1 W=0
#ID=5  R=1 W=1
#ID=6  R=1 W=1
#ID=7  R=1 W=1
#ID=8  R=1 W=0
#ID=9  R=1 W=0
#ID=10 R=1 W=0
#ID=11 R=1 W=1
#ID=12 R=1 W=0
#ID=13 R=1 W=1
#ID=14 R=1 W=0
```
