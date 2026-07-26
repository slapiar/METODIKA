# INICIALIZÁCIA KROKU: Načítanie aktuálneho pracovného stavu a plánu

## Kontrolná matica (Dôkaz je povinný, inak = NEOVERENÉ)
1. Metodika načítaná: ÁNO | Dôkaz: `postupy/Inicializácia práce.md`, blob `44729126508a0c9151fb2358badcb1445a425bd6`
2. Projekt a autoritatívny zdroj: ÁNO | Dôkaz: repozitár `slapiar/METODIKA`, cesta `postupy/Inicializácia práce.md`, vetva `main`
3. Vetva a HEAD: NEOVERENÉ | Dôkaz: chýba hash aktuálneho HEAD vetvy `main`
4. Prístupy (read/write): NEOVERENÉ | Dôkaz: čítanie overené načítaním metodiky; zápis zatiaľ neoverený výsledkom vytvorenia tohto súboru
5. Prostredie a runtime: NEOVERENÉ | Dôkaz: chýba aktuálne vzdialené overenie
6. Závislosti kroku: NEOVERENÉ | Dôkaz: chýba aktuálne vzdialené overenie
7. Predmet a hranice zásahu: NEOVERENÉ | Dôkaz: záväzný pracovný stav a povolený krok ešte neboli načítané z plánu
8. Kritérium úspechu: NEOVERENÉ | Dôkaz: chýba aktuálny denný plán a akceptačné kritériá
9. Rollback plán: ÁNO | Dôkaz: pred otvorením brány sa vykonáva iba čítanie a tento jediný INI artefakt; rollback je odstránenie tohto INI súboru podľa jeho aktuálneho blob SHA

## Stav Brány
GATE = CLOSED
BLOKUJÚCI_BOD = 3, 4, 5, 6, 7, 8
POVOLENÝ_ĎALŠÍ_ÚKON = Iba overenie chýbajúcich bodov 3, 4, 5, 6, 7 a 8

## Matica plnenia pokynov
| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---|---|
| ID-01 až ID-14 | 1 | 1 |
