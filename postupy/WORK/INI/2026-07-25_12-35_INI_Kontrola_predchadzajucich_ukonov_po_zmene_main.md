# INICIALIZÁCIA — Kontrola predchádzajúcich úkonov po zmene `main`

## Stav brány

```text
GATE=CLOSED
BLOKUJÚCI_BOD=3. Vetva a HEAD overené
CHÝBAJÚCI_DÔKAZ=commit vytvorenia a vzdialený read-back tohto INI
POVOLENÝ_ĎALŠÍ_ÚKON=iba spätné načítanie tohto INI a dokončenie bodu 3
```

## Nadväznosť

Predchádzajúce dva pokusy sú zachované ako historické STOP záznamy:

- `postupy/WORK/INI/2026-07-25_12-16_INI_Kontrola_predchadzajucich_ukonov_a_obnova_autority.md`,
- `postupy/WORK/INI/2026-07-25_12-23_INI_Obnova_metodickeho_pravidla_na_aktualnom_HEAD.md`.

Druhý pokus správne zastavila bezprostredná kontrola HEAD pred implementáciou. Nový autoritatívny stav bol úplne načítaný až po `main=1d7d7938754d1b167a50c9cbfc717862c35ab0b6` a následnom STOP commite `af6c72c94f723527775942fea078557f4d8a427d`.

## Predmet metodického úkonu

Doplniť do `postupy/Inicializácia práce.md` záväzné pravidlo, že pred vykonaním každého bodu nového projektového kroku sa najprv preverí jeho predchádzajúce vykonanie v tom istom projekte. Existujúci výsledok sa použije iba po novom načítaní, overení presnej zhody a kontinuity. Ak nie je dostupný, vykoná sa najprv bezpečný pokus o obnovu alebo sprístupnenie z vlastnej preukázanej administrátorskej autority.

Administrátorská autorita nie je oprávnením domýšľať výsledok, obchádzať bezpečnostné hranice ani meniť produkciu bez osobitnej otvorenej brány.

## Aktuálny vzdialený stav

```text
projekt=METODIKA
repozitár=slapiar/METODIKA
autoritatívna_vetva=main
HEAD_PRED_INI=af6c72c94f723527775942fea078557f4d8a427d
technický_koreň=/codei
čas_overenia=2026-07-25 12:35 Europe/Bratislava
```

## Načítané dôkazy

- celý `postupy/Inicializácia práce.md`, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79`, 403 riadkov,
- celý záväzný plán `postupy/PLAN/2026-07-25_05-18_Plan_prace.md`, blob `cc121bddaf480c53474477a43d4eda3dcf11d623`,
- `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`,
- autoritatívny INI Kroku 11 `postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md`, blob `3914ae9980c08f05cb6b0c28248bb583dade4a83`,
- register `postupy/README.md`, blob `1a9aee8d63ef80dec5937e3be6e90bfc1e9eea83`,
- `CHANGELOG.md` na novom autoritatívnom základe, blob `243ce42e9373479e5a8f160802cb6a19d26610b2`,
- `codei/app/Views/partials/header.php`, blob `09edd5d7e9017a17c77a7a6b997919b970953726`,
- porovnanie `5929519d... → 1d7d7938...`: päť commitov, zmenené `CHANGELOG.md` a `header.php`,
- porovnanie `af6c72c... → main`: `identical`.

Zmena `header.php` je mimo predmetu tohto úkonu. Bola načítaná iba preto, aby bol vzdialený stav úplný; nebude sa meniť ani hodnotiť.

## Inicializačná tabuľka

| Bod | Stav | Čo bolo overené | Konkrétny výsledok a dôkaz | Zostáva neoverené |
|---|---|---|---|---|
| 1. Metodika načítaná | ÁNO | Celý aktuálny dokument, body 0–14, STOP a základné pravidlo | blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79` | nič |
| 2. Projekt a autoritatívny zdroj overený | ÁNO | METODIKA, `slapiar/METODIKA`, `main`, `/codei` | projektový register a vzdialené metadata | nič |
| 3. Vetva a HEAD overené | NEOVERENÉ | Aktuálny HEAD pred INI a úplný rozdiel boli potvrdené | `HEAD_PRED_INI=af6c72c94f723527775942fea078557f4d8a427d`; `af6c72c... → main=identical` | commit vytvorenia a read-back |
| 4. Potrebné prístupy prakticky overené | ÁNO | Administrátorské čítanie, zápis a read-back | predchádzajúce bezpečné zápisy a read-backy; `admin=true`, `push=true` | nič |
| 5. Prostredie prakticky overené | ÁNO | Pre tento dokumentačný úkon je potrebné iba vzdialené GitHub prostredie | čítanie, zápis a read-back fungujú | nič |
| 6. Závislosti kroku dostupné | ÁNO | Metodika, register, changelog, WORK a INI priestor | všetky cieľové súbory sú dostupné | nič |
| 7. Predmet a hranice zásahu určené | ÁNO | Iba metodika a jej povinná evidencia | bez zmeny vykonateľného kódu, databázy, workflowu, produkcie a bez uzavretia Kroku 11 | nič |
| 8. Kritérium úspechu určené | ÁNO | Jednoznačné pravidlo, read-back, register, changelog a WORK záznam | existujúci dôkaz sa nesmie vyhlásiť za platný bez kontroly kontinuity | vykonanie po otvorení brány |
| 9. Rollback určený | ÁNO | Vrátiť iba commity tohto metodického úkonu | história sa opravuje novou udalosťou | nič |

## Tabuľka stavu čítania a vykonávania metodických pokynov

| ID | Metodický pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Povinné nové načítanie vzdialeného autoritatívneho repozitára | 1 | 1 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 0 |
| 4 | Praktické overenie prístupov | 1 | 1 |
| 5 | Obnova projektového kontextu | 1 | 1 |
| 6 | Overenie skutočného stavu | 1 | 1 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 0 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 0 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 0 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |
