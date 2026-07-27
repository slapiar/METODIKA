# INICIALIZÁCIA KROKU — STOP a predbežný plán testov naraz

Dátum a čas: 2026-07-25 18:11 Europe/Bratislava
Projekt: METODIKA
Predmet zamýšľaného kroku: zapísať na zajtra predbežný plán, podľa ktorého sa celá testovacia sada najprv navrhne a pripraví naraz, potom sa vykoná jediná implementácia, jedno nasadenie a jeden súvislý diagnostický priechod.

## STOP

```text
STOP
PORUŠENÝ_BOD=0 až 10 — nový krok sa začal bez nového úplného načítania vzdialeného stavu a bez otvorenej inicializačnej brány
ČO_BOLO_VYKONANÉ_PREDČASNE=boli navrhnuté a zapísané čiastkové diagnostické zásahy a commity pre jednotlivé tlačidlá a Evidence test
KTORÁ_BRÁNA_MALA_CHYBE_ZABRÁNIŤ=Všeobecne záväzná inicializačná brána a povinný postup 0 až 10
PREČO_NEZABRÁNILA=nesprávne sa pokračovalo z pamäte a priebežných výsledkov bez nového úplného overenia všetkých predpokladov samostatného pracovného kroku
STAV_VZNIKNUTÝCH_ARTEFAKTOV=na rozhodnutie; nesmú sa automaticky považovať za platný výsledok nového kroku
ROLLBACK_ALEBO_NÁPRAVA=zastaviť ďalšiu implementáciu; vytvoriť tento INI záznam; vykonať úplné overenie bodov 0 až 9; až po GATE=OPEN analyzovať a zapísať predbežný plán
```

## Stav inicializačnej brány

1. Metodika načítaná: ÁNO
   - Overené úplným novým načítaním `postupy/Inicializácia práce.md` z vetvy `main`.
   - Dôkaz: blob `1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19`.
   - Zostáva neoverené: ostatné autoritatívne metodické dokumenty požadované krokom 1.

2. Projekt a autoritatívny zdroj overený: NEOVERENÉ
   - Zostáva načítať `PROJEKTY/ZoznamProjektov.md` a dôkazy o autoritatívnom zdroji.

3. Vetva a HEAD overené: NEOVERENÉ
   - Vetva `main` je použitá na vytvorenie INI záznamu, ale aktuálny vzdialený HEAD a relevantná história ešte neboli úplne doložené.

4. Potrebné prístupy prakticky overené: ČIASTOČNE / NEOVERENÉ
   - Zápis do repozitára bol prakticky overený vytvorením tohto INI záznamu.
   - Prístupy potrebné pre plánovaný krok ešte neboli úplne určené a overené.

5. Prostredie prakticky overené: NEOVERENÉ
   - Produkcia, vývojové prostredie, databáza, runtime, schéma, izolácia a cleanup neboli pre tento nový krok úplne overené.

6. Závislosti kroku dostupné: NEOVERENÉ
   - Denný plán, nadväzujúce INI/WORK/checkpoint záznamy, zdroje testov a databázové dôkazy ešte neboli úplne načítané.

7. Predmet a hranice zásahu určené: ČIASTOČNE / NEOVERENÉ
   - Používateľ určil cieľ: neopakovať čiastkové release; pripraviť celý postup testov naraz.
   - Presný rozsah a dotknuté artefakty sa určia až z aktuálneho plánu a úplného vzdialeného stavu.

8. Kritérium úspechu určené: ČIASTOČNE / NEOVERENÉ
   - Predbežne: jeden ucelený plán celej testovacej sady, bez ďalšej implementácie dnes.
   - Úplné kritériá sa nesmú domyslieť pred načítaním plánu a závislostí.

9. Rollback určený: ČIASTOČNE / NEOVERENÉ
   - Pre samotný plán: revert plánovacieho commitu.
   - Stav predčasných diagnostických artefaktov a ich prípadná náprava sa musí najprv analyzovať po otvorení brány.

```text
GATE=CLOSED
BLOKUJÚCI_BOD=2, 3, 4, 5, 6, 7, 8, 9 — neúplne načítaný a nedoložený aktuálny vzdialený stav
CHÝBAJÚCI_DÔKAZ=aktuálny HEAD a história; celý dnešný plán; nadväzujúce INI, WORK a checkpoint záznamy; projektové a autoritatívne dokumenty; aktuálne dotknuté zdroje, konfigurácia, schéma a rozdiel produkcia/repozitár/release
POVOLENÝ_ĎALŠÍ_ÚKON=iba úplné načítanie a doloženie chýbajúcich predpokladov bodov 0 až 9 v tomto INI zázname
```

## Evidencia čítania a vykonania pokynov

| ID | Pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Nové načítanie vzdialeného autoritatívneho repozitára | 1 | 0 |
| 1 | Obnova univerzálnej metodiky | 1 | 0 |
| 2 | Identifikácia projektu | 1 | 0 |
| 3 | Určenie autoritatívneho zdroja | 1 | 0 |
| 4 | Praktické overenie prístupov | 1 | 0 |
| 5 | Obnova projektového kontextu | 1 | 0 |
| 6 | Overenie skutočného stavu | 1 | 0 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 0 |
| 8 | Analýza pred návrhom | 1 | 0 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 0 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 0 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |
| STOP | Povinný postup pri STOP | 1 | 1 |
