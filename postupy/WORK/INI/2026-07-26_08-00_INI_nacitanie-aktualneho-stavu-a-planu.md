# INICIALIZÁCIA KROKU: Načítanie aktuálneho vzdialeného stavu, záväzného plánu a nasledujúceho povoleného kroku

## Kontrolná matica (Dôkaz je povinný, inak = NEOVERENÉ)
1. Metodika načítaná: ÁNO | Dôkaz: `postupy/Inicializácia práce.md`, vetva `main`, blob `44729126508a0c9151fb2358badcb1445a425bd6`, úplný read-back 94 riadkov
2. Projekt a autoritatívny zdroj: ÁNO | Dôkaz: `slapiar/METODIKA`, predvolená a používateľom určená vetva `main`; vzdialený repozitár je dostupný a nearchivovaný
3. Vetva a HEAD: ÁNO | Dôkaz: vzdialený `main` po vytvorení INI = `db5f859ae9dd0d2b0e6fa774724594f6b7729be5`; compare `db5f859... → main` = `identical`
4. Prístupy (read/write): ÁNO | Dôkaz: čítanie potvrdené úplnými remote read-backmi; zápis potvrdený commitom `db5f859ae9dd0d2b0e6fa774724594f6b7729be5` a read-backom INI blobu `f5dcd3fd375d018a0ad6b6639d2a4651079baeb2`
5. Prostredie a runtime: ÁNO | Dôkaz: rozsah je iba vzdialené čítanie a evidencia Markdown súborov cez GitHub; aplikačný runtime `/codei` je mimo predmetu tohto inicializačného kroku
6. Závislosti kroku: ÁNO | Dôkaz: aktuálny register `postupy/README.md`, blob `6d46e8c2377961817170e10fc5154ecd2ec55c11`; záväzný plán `postupy/PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md`, blob `49e579e4520e622532b22b2eb4627aec596c397e`, načítaný celý; jediný pokračovací INI Kroku 11, blob `08c718769c793ac0348dd82f7f3c0b929c96cec7`, načítaný celý
7. Predmet a hranice zásahu: ÁNO | Dôkaz: iba načítanie autoritatívneho vzdialeného stavu, určenie záväzného plánu a nasledujúceho povoleného kroku; bez zásahu do `/codei`, testov, databázy, release alebo produkcie
8. Kritérium úspechu: ÁNO | Dôkaz: aktuálny HEAD, aktívny plán, pracovný stav a jediný nasledujúci povolený krok sú doložené presnými vzdialenými súbormi, commitmi a blobmi
9. Rollback plán: ÁNO | Dôkaz: ak sa pred ďalším zápisom zmení `main`, zastaviť a znovu načítať; tento krok nemení vykonateľný kód ani externé prostredie

## Stav Brány
GATE = OPEN
BLOKUJÚCI_BOD = Žiadny
POVOLENÝ_ĎALŠÍ_ÚKON = Zaznamenať aktuálny pracovný stav a potom pokračovať iba v pôvodnom INI Kroku 11 bezpečným čítacím dokončovaním Fázy 11.A

## Matica plnenia pokynov
| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---|---|
| ID-01 až ID-14 | 1 | 1 |
