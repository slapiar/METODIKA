# INICIALIZÁCIA — Obnova metodického pravidla na aktuálnom HEAD

## Stav brány

```text
GATE=OPEN
```

## Nadväznosť na STOP

Predchádzajúci pokus je zachovaný v:

`postupy/WORK/INI/2026-07-25_12-16_INI_Kontrola_predchadzajucich_ukonov_a_obnova_autority.md`

Jeho brána je `CLOSED`, pretože prvé čítanie nezachytilo úplný vzdialený stav. Tento nový záznam vznikol nad aktuálnym HEAD po úplnom načítaní následnosti až po commit `ee4f0b1766a25854623d92ded7f60a922a6338c1`.

## Predmet metodického úkonu

Doplniť do `postupy/Inicializácia práce.md` záväzné pravidlo:

1. pred vykonaním každého bodu nového projektového kroku najprv overiť, či už nebol vykonaný a doložený v niektorom predchádzajúcom kroku toho istého projektu,
2. platný existujúci výsledok neopakovať, ale znovu načítať, overiť jeho použiteľnosť a kontinuitu voči aktuálnemu stavu a použiť ho ako východisko,
3. ak výsledok alebo dôkaz nie je priamo dostupný, najprv sa ho pokúsiť bezpečne obnoviť alebo sprístupniť prostredníctvom vlastnej preukázanej administrátorskej autority a dostupných projektových nástrojov,
4. administrátorskú autoritu nepoužiť na domýšľanie výsledku, obchádzanie bezpečnostných hraníc ani neoprávnený zásah do produkcie.

## Aktuálny vzdialený stav

```text
projekt=METODIKA
repozitár=slapiar/METODIKA
autoritatívna_vetva=main
HEAD_PRED_INI=ee4f0b1766a25854623d92ded7f60a922a6338c1
HEAD_PO_VYTVORENÍ_INI=f056211ac5931b63c3f74c3b01b10e5f05192442
technický_koreň=/codei
čas_overenia=2026-07-25 12:23 Europe/Bratislava
```

## Povinne načítané zdroje

- celý `postupy/Inicializácia práce.md`, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79`,
- celý aktuálny plán `postupy/PLAN/2026-07-25_05-18_Plan_prace.md`, blob `cc121bddaf480c53474477a43d4eda3dcf11d623`,
- `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`,
- aktuálny autoritatívny INI Kroku 11 `postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md`, blob `3914ae9980c08f05cb6b0c28248bb583dade4a83`,
- historický INI Kroku 11 po zmene HEAD, blob `bc517edeec8f85d3b3248c835a9e333ba28d0aad`,
- aktuálny register `postupy/README.md` po commite `07aaca31350be695032209c6f947fad6a7198eb3`,
- aktuálny `CHANGELOG.md`,
- aktuálna história `4a87250b... → ccbea1ac... → 06fff159... → 5f1c4590... → 07aaca31... → ee4f0b17... → f056211a...`.

## Inicializačná tabuľka

| Bod | Stav | Čo bolo overené | Konkrétny výsledok a dôkaz | Zostáva neoverené |
|---|---|---|---|---|
| 1. Metodika načítaná | ÁNO | Úplný obsah bodov 0–14, STOP a základného pravidla | blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79` | nič |
| 2. Projekt a autoritatívny zdroj overený | ÁNO | METODIKA, `slapiar/METODIKA`, `main`, `/codei` | projektový register a metadata repozitára | nič |
| 3. Vetva a HEAD overené | ÁNO | HEAD pred INI, commit vytvorenia a vzdialený read-back | `ee4f0b17... → f056211a...`; read-back blob `23808bc75207faae9a4350b2d5c695fbc42959c5` | iba povinná kontrola HEAD bezprostredne pred prvým implementačným zápisom |
| 4. Potrebné prístupy prakticky overené | ÁNO | admin, push, čítanie, zápis a read-back | `admin=true`, `push=true`; commit STOP `ee4f0b17...`; commit INI `f056211a...`; vzdialené read-backy | nič |
| 5. Prostredie prakticky overené | ÁNO | Potrebné je iba vzdialené GitHub prostredie | čítanie, zápis a read-back sú funkčné | nič |
| 6. Závislosti kroku dostupné | ÁNO | Cieľová metodika, register, changelog a pracovný priestor | všetky dotknuté súbory sú dostupné | nič |
| 7. Predmet a hranice zásahu určené | ÁNO | Iba metodika, register, changelog a pracovný záznam | bez vykonateľného kódu, DB, workflowu, produkcie a bez uzavretia Kroku 11 | nič |
| 8. Kritérium úspechu určené | ÁNO | Jednoznačný príkaz, evidencia a read-back každého zápisu | pravidlo nesmie zamieňať starý dôkaz s automatickou platnosťou | vykonanie po otvorení brány |
| 9. Rollback určený | ÁNO | Vrátenie iba commitov tohto metodického úkonu | oprava novou historickou udalosťou | nič |

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
Bezprostredne overiť aktuálny HEAD, doplniť najmenšie záväzné pravidlo do postupy/Inicializácia práce.md, spätne načítať výsledok a v tom istom pracovnom kroku aktualizovať postupy/README.md, CHANGELOG.md a pracovný záznam.
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
