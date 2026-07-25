# INICIALIZÁCIA — Kontrola predchádzajúcich úkonov a obnova z administrátorskej autority

## Stav brány

```text
GATE=OPEN
```

## Predmet metodického úkonu

Doplniť do `postupy/Inicializácia práce.md` záväzné pravidlo, podľa ktorého sa pred vykonaním každého bodu nového projektového kroku najprv overí, či už nebol vykonaný a doložený v niektorom predchádzajúcom kroku toho istého projektu. Platný existujúci výsledok sa neopakuje; znovu sa načíta, overí sa jeho vecná použiteľnosť a kontinuita voči aktuálnemu stavu a následne sa použije ako východisko.

Ak existujúci dôkaz alebo výsledok nie je priamo dostupný, najprv sa vykoná bezpečný pokus o jeho obnovenie alebo sprístupnenie prostredníctvom vlastnej preukázanej administrátorskej autority a dostupných projektových nástrojov. Administrátorská autorita sa nesmie zameniť za oprávnenie domýšľať výsledok, obchádzať bezpečnostné hranice alebo meniť produkciu bez osobitnej otvorenej brány.

## Aktuálny vzdialený stav

```text
projekt=METODIKA
repozitár=slapiar/METODIKA
autoritatívna_vetva=main
HEAD_PRED_INI=d418e72c162bde324af7546c937af979bd75182e
HEAD_PO_VYTVORENÍ_INI=06fff1593e9839604b220ca3a651c52c46fa9934
technický_koreň=/codei
čas_overenia=2026-07-25 12:16 Europe/Bratislava
```

## Povinne načítané zdroje

- `postupy/Inicializácia práce.md`, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79`, načítaný celý rozsah 403 riadkov,
- `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`,
- `postupy/PLAN/2026-07-25_05-18_Plan_prace.md`, blob `cc121bddaf480c53474477a43d4eda3dcf11d623`, načítaný celý rozsah,
- `postupy/WORK/INI/2026-07-25_10-33_INI_Krok_11_po_zmene_HEAD.md`, blob `bc517edeec8f85d3b3248c835a9e333ba28d0aad`,
- `postupy/README.md`, blob `9595efc9e62687ef1e81842e83ca40fe18ed8724`,
- `CHANGELOG.md`, blob `e4feda4d6732b17afcc40fa52586956eb7db5c58`,
- aktuálna história vetvy `main` po Krok 11 vrátane commitov `aa8af878...`, `51ea6b49...`, `39f054bd...`, `52707743...`, `b389443b...`, `b925e9d9...` a `d418e72c...`.

## Inicializačná tabuľka

| Bod | Stav | Čo bolo overené | Konkrétny výsledok a dôkaz | Zostáva neoverené |
|---|---|---|---|---|
| 1. Metodika načítaná | ÁNO | Úplný aktuálny obsah inicializačnej metodiky | blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79`, 403 riadkov | nič |
| 2. Projekt a autoritatívny zdroj overený | ÁNO | Projekt METODIKA a jeho register | `PROJEKTY/ZoznamProjektov.md`; `slapiar/METODIKA` | nič |
| 3. Vetva a HEAD overené | ÁNO | Autoritatívna vetva `main`, aktuálna história a zápis prvého INI artefaktu | `d418e72c... → 06fff159...` | HEAD znovu skontrolovať bezprostredne pred implementačným zápisom |
| 4. Potrebné prístupy prakticky overené | ÁNO | Čítanie, administrátorské oprávnenia, zápis a vzdialený read-back | `admin=true`, `push=true`; commit `06fff1593e9839604b220ca3a651c52c46fa9934`; read-back blob `ed6212b9669b06254d10c3071d0d7bf756a11145` | nič |
| 5. Prostredie prakticky overené | ÁNO | Pre tento úkon je potrebné iba vzdialené GitHub prostredie; databáza, runtime a produkcia nie sú predmetom zásahu | vzdialené čítanie, zápis a read-back sú funkčné | nič |
| 6. Závislosti kroku dostupné | ÁNO | Cieľový dokument, register a changelog sú dostupné v aktuálnych bloboch | vyššie uvedené súbory boli načítané | nič |
| 7. Predmet a hranice zásahu určené | ÁNO | Iba metodické doplnenie, register, changelog a pracovný záznam | bez zásahu do vykonateľného kódu, DB, workflowov, produkcie a bez uzatvárania Kroku 11 v tomto úkone | nič |
| 8. Kritérium úspechu určené | ÁNO | Nové pravidlo je jednoznačné, neumožňuje domnienku a zachováva dôkazovú kontinuitu | zápis v metodike, register a changelog; read-back každého zápisu | praktické vykonanie po otvorení brány |
| 9. Rollback určený | ÁNO | Vrátenie iba commitov tohto metodického úkonu | história sa zachová novou opravnou udalosťou, nie tichým prepísaním | nič |

## Vyhodnotenie brány

```text
1=ÁNO
2=ÁNO
3=ÁNO
4=ÁNO
5=ÁNO
6=ÁNO
7=ÁNO
8=ÁNO
9=ÁNO
GATE=OPEN
```

## Jediný povolený nasledujúci úkon

```text
Doplniť najmenšie záväzné pravidlo do postupy/Inicializácia práce.md, spätne načítať zápis a v tom istom pracovnom kroku aktualizovať postupy/README.md, CHANGELOG.md a pracovný záznam.
```

## Tabuľka stavu čítania a vykonávania metodických pokynov

| ID | Metodický pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Povinné nové načítanie vzdialeného autoritatívneho repozitára | 1 | 1 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 1 |
| 4 | Praktické overenie prístupov | 1 | 1 |
| 5 | Obnova projektového kontextu | 1 | 1 |
| 6 | Overenie skutočného stavu | 1 | 1 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 0 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 1 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 0 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |
