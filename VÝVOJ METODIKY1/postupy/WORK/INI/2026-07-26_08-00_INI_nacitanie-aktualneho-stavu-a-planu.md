# INICIALIZÁCIA KROKU: Načítanie aktuálneho vzdialeného stavu, záväzného plánu a nasledujúceho povoleného kroku

## Kontrolná matica (Dôkaz je povinný, inak = NEOVERENÉ)
1. Metodika načítaná: ÁNO | Dôkaz: `postupy/Inicializácia práce.md`, vetva `main`, blob `44729126508a0c9151fb2358badcb1445a425bd6`, úplný read-back 94 riadkov
2. Projekt a autoritatívny zdroj: ÁNO | Dôkaz: `slapiar/METODIKA`, predvolená a používateľom určená vetva `main`; vzdialený repozitár je dostupný a nearchivovaný
3. Vetva a HEAD: ÁNO | Dôkaz: vzdialený `main` pri otvorení brány = `e44433ee40bf2dff519e1ebf2a236a9ca39f9ddf`; compare voči `main` = `identical`; následný commit `f14c81932eccf04fa3c8927957b132628e030f52` pridal iba WORK záznam tohto orientačného úkonu
4. Prístupy (read/write): ÁNO | Dôkaz: čítanie potvrdené úplnými remote read-backmi; zápis potvrdený commitmi `db5f859ae9dd0d2b0e6fa774724594f6b7729be5`, `e44433ee40bf2dff519e1ebf2a236a9ca39f9ddf` a `f14c81932eccf04fa3c8927957b132628e030f52`; WORK read-back blob `827725b0621ea7b2b488a5c9527ba3b0f6f05b5d`
5. Prostredie a runtime: ÁNO | Dôkaz: rozsah je iba vzdialené čítanie a evidencia Markdown súborov cez GitHub; aplikačný runtime `/codei` je mimo predmetu tohto inicializačného kroku
6. Závislosti kroku: ÁNO | Dôkaz: aktuálny register `postupy/README.md`, blob `6d46e8c2377961817170e10fc5154ecd2ec55c11`; záväzný plán `postupy/PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md`, blob `49e579e4520e622532b22b2eb4627aec596c397e`, načítaný celý; jediný pokračovací INI Kroku 11, blob `08c718769c793ac0348dd82f7f3c0b929c96cec7`, načítaný celý
7. Predmet a hranice zásahu: ÁNO | Dôkaz: iba načítanie autoritatívneho vzdialeného stavu, určenie záväzného plánu a nasledujúceho povoleného kroku; bez zásahu do `/codei`, testov, databázy, release alebo produkcie
8. Kritérium úspechu: ÁNO | Dôkaz: aktuálny HEAD, aktívny plán, pracovný stav a jediný nasledujúci povolený krok sú doložené v `postupy/WORK/2026-07-26_08-05_Nacitanie_aktualneho_stavu_a_planu.md`
9. Rollback plán: ÁNO | Dôkaz: ak sa pred ďalším projektovým úkonom zmení `main`, znovu načítať autoritatívny stav; tento krok nemenil vykonateľný kód ani externé prostredie

## Stav Brány
GATE = OPEN
BLOKUJÚCI_BOD = Žiadny
POVOLENÝ_ĎALŠÍ_ÚKON = Pokračovať výhradne v pôvodnom INI Kroku 11 a dokončovať iba bezpečný čítací dôkaz Fázy 11.A

## Výsledok orientácie

```text
PLÁN=postupy/PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md
PLÁN_STAV=PRACOVNÝ — ZÁVÄZNÝ
KROKY_1_AŽ_10=SPLNENÉ
AKTÍVNY_KROK=KROK_11
FÁZA_11_A=ZAČATÁ_A_NEÚPLNÁ
GATE_KROKU_11=CLOSED
NOVÝ_INI_KROKU_11=false
NOVÝ_ENVIRONMENTÁLNY_TEST=false
NEXT_ALLOWED_ACTION=IBA_DOKONČENIE_BEZPEČNÉHO_ČÍTACIEHO_DÔKAZU_FÁZY_11_A
```

## Hranice

```text
CODEI=BEZ_ZMENY
TESTY=NESPUSTENÉ
DATABÁZA=BEZ_ZÁSAHU
RELEASE=BEZ_ZMENY
PRODUKCIA=BEZ_ZÁSAHU
```

## Matica plnenia pokynov
| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---|---|
| ID-01 až ID-14 | 1 | 1 |
