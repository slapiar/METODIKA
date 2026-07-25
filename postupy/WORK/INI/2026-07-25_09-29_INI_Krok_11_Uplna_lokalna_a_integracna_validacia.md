# INICIALIZÁCIA — Krok 11: Úplná lokálna a integračná Validácia

## Stav brány

```text
GATE=OPEN
```

## Účel kroku

Vykonať výhradne Krok 11 záväzného plánu: úplnú lokálnu a integračnú Validáciu po oprave Kroku 10, bez prípravy release, bez nasadenia a bez zásahu do produkcie.

## Overenie predchádzajúceho kroku

```text
PREDCHÁDZAJÚCI_KROK=KROK_10
STAV=SPLNENÉ
FUNKČNÝ_COMMIT=c90ae562a859de9fe0b2174f39e924c7f7bc6a4e
CHECKPOINT=postupy/2026-07-25_09-05_CHECKPOINT-KROK-10-PRESNA-REZERVACIA.md
```

Znovu načítané dôkazy:

- `postupy/WORK/INI/2026-07-25_08-14_INI_Dokoncenie_Kroku_10_po_STOP.md`,
- `postupy/WORK/2026-07-25_09-01_Krok_10_Najmensia_funkcna_oprava_rezervacie.md`,
- `postupy/2026-07-25_09-05_CHECKPOINT-KROK-10-PRESNA-REZERVACIA.md`,
- `postupy/WORK/2026-07-24_13-25_Krok_7_Reprodukcia_korenovej_priciny_mimo_produkcie.md`,
- `postupy/WORK/2026-07-24_13-45_Krok_8_Oprava_diagnostickeho_rozlisenia.md`,
- `postupy/WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md`,
- `postupy/PLAN/2026-07-25_05-18_Plan_prace.md`.

## Spresnenie použitia existujúcich dôkazov

Nová brána Kroku 11 nepreberá `GATE=OPEN` predchádzajúceho kroku. Existujúce technické dôkazy sa však nemusia vyrábať znova, ak sa pred ich použitím overí, že:

1. ich predpoklady zostali nezmenené,
2. dotknuté vykonateľné zdroje, testy, migrácie, závislosti a workflowy zostali nezmenené,
3. dôkaz presne zodpovedá tomu, čo sa ním v novej bráne dokladá.

Porovnanie:

```text
BASE=c90ae562a859de9fe0b2174f39e924c7f7bc6a4e
HEAD_PRED_OTVORENÍM=23cdcee06dd25427ecfab96cd5ea0ec4a0e4ec02
STATUS=ahead
AHEAD_BY=13
ZMENENÉ_SÚBORY=iba metodické dokumenty a CHANGELOG
VYKONATEĽNÝ_KÓD_ZMENENÝ=false
TESTY_ZMENENÉ=false
MIGRÁCIE_ZMENENÉ=false
COMPOSER_LOCK_ZMENENÝ=false
WORKFLOWY_ZMENENÉ=false
```

Preto zostávajú predchádzajúce technické dôkazy použiteľné pre otvorenie Kroku 11.

## Záväzný rozsah Kroku 11

Povinné testovacie bloky:

1. run store a validator,
2. `FirstAcceptanceService`,
3. diagnostické chybové fázy,
4. session a security endpointy,
5. integračný DB rollback,
6. skutočný súbežný test s dvoma procesmi alebo spojeniami,
7. end-to-end `START → HIT A/B → RESULT`,
8. tombstone a sweep,
9. regresia login/database/logout diagnostiky.

Povinné dôkazy pred a po testoch:

- presný testovaný HEAD,
- verzie PHP, Composeru a MariaDB,
- stav migrácií,
- počty databázových riadkov,
- run-store JSON a lock súbory,
- temp súbory,
- cleanup výsledok,
- izolácia od produkcie.

Povinné úspešné kritérium:

```text
DB_UNIQUENESS=true
AND OUTCOMES=CREATED+ALREADY_EXISTS
AND CLEANUP=true
AND STATE=COMPLETED_SUCCESS
```

## Dôkazy bodov 4 až 6

### Bod 4 — potrebné prístupy

Prakticky potvrdené:

- autentifikovaný účet je vlastník `slapiar`,
- repozitár hlási `admin=true`, `maintain=true`, `pull=true`, `push=true`, `triage=true`,
- čítanie, zápis a vzdialený read-back INI prešli,
- v Kroku 10 bolo prakticky prijaté a vykonané opakovanie Actions jobu `89652985988`,
- PR `#2` bol validovaný a zlúčený po úspešnom Actions rune `30148480939`, job `89654680309`.

Záver:

```text
BOD_4=ÁNO
```

### Bod 5 — prostredie

Použiteľné praktické dôkazy:

- opakovaný environment job `89652985988`: `success`,
- PHP 8.4, Composer 2, `intl`, `mbstring`, `mysqli`,
- izolovaná MariaDB 11.4 a InnoDB,
- uzamknuté závislosti,
- migrácie M1–M8,
- overenie schémy a prázdneho počiatočného stavu,
- dve nezávislé databázové spojenia,
- rollback a finálny cleanup,
- Krok 9 run `30098298849`: PHP 8.4.23 a funkčný `pcntl_fork`,
- Krok 10 run `30148480939`: dve nezávislé MySQLi spojenia, výsledky `CREATED + ALREADY_EXISTS`, jediný history run a cleanup.

Kontinuita týchto dôkazov je potvrdená nezmenenými workflowmi, závislosťami, testami a migráciami.

Záver:

```text
BOD_5=ÁNO
```

### Bod 6 — závislosti a mapa deviatich blokov

| Blok | Dostupné zdroje a dôkazy | Stav pre otvorenie |
|---:|---|---|
| 1. Run store a validator | `DiagnosticsConcurrencyRunStore.php`, validator, ich jednotkové testy a dvojprocesový test; Krok 9 potvrdil run-store testy aj `pcntl_fork` | ÁNO |
| 2. `FirstAcceptanceService` | `FirstAcceptanceServiceTest.php`; Krok 10 úspešne vykonal 2 testy/4 tvrdenia a DB regresiu | ÁNO |
| 3. Diagnostické chybové fázy | `DiagnosticsControllerTest.php`, acceptance runner; Krok 8 potvrdil fázové kódy a bezpečné verejné výstupy | ÁNO |
| 4. Session a security endpointy | feature testy, routes, CSRF, token, session a feature flag; testovací teardown resetuje prostredie | ÁNO |
| 5. Integračný DB rollback | `metodika:verify-first-acceptance-transaction`; predchádzajúci dôkaz potvrdil úspešnú vetvu aj úmyselnú chybu s nulovým zvyškom dát | ÁNO |
| 6. Skutočný súbeh | dvojprocesový test Kroku 9 a DB príkazy s dvoma nezávislými MySQLi spojeniami; cleanup na nulové počty | ÁNO |
| 7. `START → HIT A/B → RESULT` | existujúci feature E2E test a routy; známe obmedzenie jednovláknovej simulácie je predmetom Validácie Kroku 11, nie chýbajúcou závislosťou | ÁNO |
| 8. Tombstone a sweep | read-once, redakcia, `deleteAfter`, sweep a cleanup testy; historický produkčný dôkaz Kroku 5 | ÁNO |
| 9. Login/database/logout | login/database testy a logout route/controller sú dostupné; chýbajúca alebo nedostatočná logout regresia sa má analyzovať a podľa dôkazu doplniť v Kroku 11 | ÁNO |

Známe testovacie medzery nie sú dôvodom držať inicializačnú bránu zatvorenú. Sú predmetom samotnej analýzy a Validácie Kroku 11.

Záver:

```text
BOD_6=ÁNO
```

## Inicializačná tabuľka

| Bod | Stav | Čo bolo overené | Dôkaz |
|---|---|---|---|
| 1. Metodika načítaná | ÁNO | Úplný aktuálny obsah `postupy/Inicializácia práce.md`, body 0–14, STOP a základné pravidlo | aktuálny vzdialený súbor, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79` |
| 2. Projekt a autoritatívny zdroj overený | ÁNO | METODIKA, `slapiar/METODIKA`, `main`, `/codei` | `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d` |
| 3. Vetva a HEAD overené | ÁNO | Aktuálna história a kontinuita od validovaného funkčného commitu | porovnanie `c90ae562... → 23cdcee0...`; iba dokumentačné zmeny |
| 4. Potrebné prístupy prakticky overené | ÁNO | admin/push/read/write/read-back a Actions vykonanie | metadata repozitára; job `89652985988`; run `30148480939`, job `89654680309` |
| 5. Prostredie prakticky overené | ÁNO | PHP, Composer, MariaDB, MySQLi, pcntl, migrácie, izolácia, rollback a cleanup | runy `30098298849`, `30148480939`; joby `89652985988`, `89654680309` |
| 6. Závislosti kroku dostupné | ÁNO | Presná mapa všetkých deviatich blokov, testov, príkazov a cleanup kontraktov | načítané aktuálne zdroje a nezmenený technický stav od `c90ae562...` |
| 7. Predmet a hranice zásahu určené | ÁNO | Iba úplná lokálna a integračná Validácia; bez release a produkcie | záväzný plán Kroku 11 |
| 8. Kritérium úspechu určené | ÁNO | Deväť blokov a spoločné binárne kritérium | záväzný plán Kroku 11 |
| 9. Rollback určený | ÁNO | Odstrániť iba testovacie dáta a dočasné artefakty; Krok 10 sa automaticky nevracia | záväzný plán a existujúce cleanup kontrakty |

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
Vykonať analýzu presného pokrytia deviatich testovacích blokov na aktuálnom HEAD,
určiť existujúce dôkazy a skutočné medzery,
a až potom navrhnúť najmenšiu bezpečnú validačnú zostavu Kroku 11.
```

Zakázané zostávajú release, nasadenie, produkčný run a otvorenie Kroku 12.

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
| 11 | Spätné načítanie po zápise | 1 | 0 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 1 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |