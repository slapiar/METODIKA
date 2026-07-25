# INICIALIZÁCIA — Kontrola predchádzajúcich úkonov a obnova z administrátorskej autority

## Stav brány

```text
GATE=CLOSED
STAV_ZÁZNAMU=PREKONANÝ_NEÚPLNÝM_VZDIALENÝM_STAVOM
BLOKUJÚCI_BOD=0. Povinné nové načítanie vzdialeného autoritatívneho repozitára
CHÝBAJÚCI_DÔKAZ=pri prvom čítaní neboli zachytené commity 4a87250b6bfa69305f465b0129dde311bf0771fd a ccbea1ac3ba9c67a0b7f46da4567189f63f79df7
POVOLENÝ_ĎALŠÍ_ÚKON=iba nová úplná inicializácia metodického úkonu nad aktuálnym vzdialeným HEAD
```

## Predmet metodického úkonu

Doplniť do `postupy/Inicializácia práce.md` záväzné pravidlo, podľa ktorého sa pred vykonaním každého bodu nového projektového kroku najprv overí, či už nebol vykonaný a doložený v niektorom predchádzajúcom kroku toho istého projektu. Platný existujúci výsledok sa neopakuje; znovu sa načíta, overí sa jeho vecná použiteľnosť a kontinuita voči aktuálnemu stavu a následne sa použije ako východisko.

Ak existujúci dôkaz alebo výsledok nie je priamo dostupný, najprv sa vykoná bezpečný pokus o jeho obnovenie alebo sprístupnenie prostredníctvom vlastnej preukázanej administrátorskej autority a dostupných projektových nástrojov. Administrátorská autorita sa nesmie zameniť za oprávnenie domýšľať výsledok, obchádzať bezpečnostné hranice alebo meniť produkciu bez osobitnej otvorenej brány.

## Pôvodne zaznamenaný vzdialený stav

```text
projekt=METODIKA
repozitár=slapiar/METODIKA
autoritatívna_vetva=main
NESPRÁVNE_NEÚPLNÝ_HEAD_PRED_INI=d418e72c162bde324af7546c937af979bd75182e
HEAD_PO_VYTVORENÍ_INI=06fff1593e9839604b220ca3a651c52c46fa9934
```

## STOP

```text
STOP
PORUŠENÝ_BOD=0. Povinné nové načítanie vzdialeného autoritatívneho repozitára a 3. Vetva a HEAD overené
ČO_BOLO_VYKONANÉ_PREDČASNE=vytvorenie tohto INI a jeho následné označenie GATE=OPEN
KTORÁ_BRÁNA_MALA_CHYBE_ZABRÁNIŤ=úplné nové čítanie aktuálnej vzdialenej histórie a bezprostredná kontrola HEAD
PREČO_NEZABRÁNILA=vyhľadanie commitov v prvom pokuse vrátilo ako najnovší d418e72c..., hoci pred vytvorením INI už na main pribudli 4a87250b... a ccbea1ac...
STAV_VZNIKNUTÝCH_ARTEFAKTOV=tento INI je historický dôkaz neúplnej inicializácie; metodika, register ani changelog ešte neboli týmto úkonom zmenené
ROLLBACK_ALEBO_NÁPRAVA=zachovať tento záznam ako STOP; vytvoriť novú úplnú inicializáciu nad aktuálnym HEAD a až po jej otvorení vykonať metodickú zmenu
```

## Zistený následný vzdialený stav

```text
4a87250b6bfa69305f465b0129dde311bf0771fd  Supervízor GATE po páde internetu
ccbea1ac3ba9c67a0b7f46da4567189f63f79df7  Krok 11: konsolidácia pôvodného INI na aktuálnom HEAD
06fff1593e9839604b220ca3a651c52c46fa9934  Inicializácia metodického pravidla kontinuity vykonaných úkonov
5f1c4590f55c422087e8255fb676b3e7bf1105e4  Otvorenie brány metodického pravidla kontinuity
07aaca31350be695032209c6f947fad6a7198eb3  Register: konsolidácia pôvodného INI Kroku 11
```

Pôvodné hodnotenie deviatich bodov a pôvodné `GATE=OPEN` sa nepoužívajú ako oprávnenie na implementáciu.

## Tabuľka stavu čítania a vykonávania metodických pokynov

| ID | Metodický pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Povinné nové načítanie vzdialeného autoritatívneho repozitára | 1 | 0 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 0 |
| 4 | Praktické overenie prístupov | 1 | 1 |
| 5 | Obnova projektového kontextu | 1 | 0 |
| 6 | Overenie skutočného stavu | 1 | 0 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 0 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 1 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 1 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |
