# INICIALIZÁCIA KROKU: Načítanie aktuálneho vzdialeného stavu, záväzného plánu a nasledujúceho povoleného kroku

## Kontrolná matica (Dôkaz je povinný, inak = NEOVERENÉ)
1. Metodika načítaná: ÁNO | Dôkaz: `postupy/Inicializácia práce.md`, vetva `main`, blob `44729126508a0c9151fb2358badcb1445a425bd6`, úplný read-back 94 riadkov
2. Projekt a autoritatívny zdroj: ÁNO | Dôkaz: používateľ určil `slapiar/METODIKA`, vetva `main`; overené čítaním vzdialeného súboru
3. Vetva a HEAD: NEOVERENÉ | Dôkaz: aktuálny vzdialený HEAD sa overí po vytvorení tohto jediného povoleného artefaktu
4. Prístupy (read/write): NEOVERENÉ | Dôkaz: čítanie je potvrdené; zápis a read-back tohto INI sa ešte musia potvrdiť
5. Prostredie a runtime: ÁNO | Dôkaz: rozsah je iba vzdialené čítanie a evidencia Markdown súborov cez GitHub; aplikačný runtime `/codei` je mimo predmetu tohto inicializačného kroku
6. Závislosti kroku: NEOVERENÉ | Dôkaz: aktuálny register, záväzný plán, pokračovací INI a posledný pracovný záznam sa ešte musia načítať z aktuálneho `main`
7. Predmet a hranice zásahu: ÁNO | Dôkaz: iba načítanie autoritatívneho vzdialeného stavu, určenie záväzného plánu a nasledujúceho povoleného kroku; bez zásahu do `/codei`, testov, databázy, release alebo produkcie
8. Kritérium úspechu: ÁNO | Dôkaz: aktuálny HEAD, aktívny plán, pracovný stav a jediný nasledujúci povolený krok budú doložené presnými vzdialenými súbormi, commitmi a blobmi
9. Rollback plán: ÁNO | Dôkaz: ak sa zistí súbežná zmena `main` alebo nesprávny rozsah, krok zostane `GATE=CLOSED`; odstránenie či oprava tohto INI iba novým doloženým metodickým úkonom

## Stav Brány
GATE = CLOSED
BLOKUJÚCI_BOD = 3, 4, 6
POVOLENÝ_ĎALŠÍ_ÚKON = Iba overenie aktuálneho HEAD, zápisu/read-backu INI a načítanie aktuálnych autoritatívnych dokumentov stavu a plánu

## Matica plnenia pokynov
| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---|---|
| ID-01 až ID-14 | 1 | 1 |
