# INICIALIZÁCIA — Krok 11: Úplná lokálna a integračná Validácia

## Stav brány

```text
GATE=CLOSED
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

Dôkazy boli znovu načítané priamo z autoritatívnej vetvy `main`:

- `postupy/WORK/INI/2026-07-25_08-14_INI_Dokoncenie_Kroku_10_po_STOP.md`,
- `postupy/WORK/2026-07-25_09-01_Krok_10_Najmensia_funkcna_oprava_rezervacie.md`,
- `postupy/2026-07-25_09-05_CHECKPOINT-KROK-10-PRESNA-REZERVACIA.md`,
- `postupy/PLAN/2026-07-25_05-18_Plan_prace.md`.

## Aktuálny vzdialený stav pred vytvorením INI

```text
repozitár=slapiar/METODIKA
vetva=main
HEAD=7ba45337653a132901739291d6c57ceab11a5ef2
technický_koreň=/codei
čas_overenia=2026-07-25 09:29 Europe/Bratislava
```

Relevantná história od funkčného commitu Kroku 10 obsahuje iba uzatváracie a nápravné metodické zápisy; posledný funkčný commit zostáva `c90ae562a859de9fe0b2174f39e924c7f7bc6a4e`.

## Záväzný rozsah Kroku 11

Povinné testovacie bloky podľa plánu:

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

## Inicializačná tabuľka

| Bod | Stav | Čo bolo overené | Konkrétny úkon a výsledok | Dôkaz | Zostáva neoverené |
|---|---|---|---|---|---|
| 1. Metodika načítaná | ÁNO | Úplný aktuálny obsah `postupy/Inicializácia práce.md`, body 0–14, STOP a základné pravidlo | Súbor bol znovu načítaný z `main`; blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79` | aktuálny vzdialený súbor | nič |
| 2. Projekt a autoritatívny zdroj overený | ÁNO | Projekt METODIKA a jeho register | Znovu načítaný `PROJEKTY/ZoznamProjektov.md`; potvrdil `github.com/slapiar/metodika`, vetvu `main`, CodeIgniter 4.7.4 a koreň `/codei` | blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d` | nič |
| 3. Vetva a HEAD overené | ÁNO | Autoritatívna vetva `main` a vzdialený HEAD pred týmto INI | Načítaná aktuálna história commitov; HEAD `7ba45337653a132901739291d6c57ceab11a5ef2` | GitHub história commitov | HEAD sa musí znovu skontrolovať bezprostredne pred prvým testom |
| 4. Potrebné prístupy prakticky overené | NEOVERENÉ | Čítanie GitHubu je potvrdené | Úspešné čítanie repozitára; zápis a spätné načítanie tohto nového INI ešte v okamihu zostavenia záznamu neboli dokončené | tento INI po vytvorení | potvrdiť zápis a read-back; potvrdiť oprávnenie spúšťať potrebné izolované validačné joby |
| 5. Prostredie prakticky overené | NEOVERENÉ | Historický úspech Kroku 10 bol načítaný, ale neprenáša sa do novej brány | Nový praktický dôkaz aktuálneho PHP, Composeru, MariaDB, MySQLi, izolácie a cleanupu pre Krok 11 ešte nebol vykonaný | zatiaľ chýba | celé aktuálne validačné prostredie Kroku 11 |
| 6. Závislosti kroku dostupné | NEOVERENÉ | Plán vymenúva deväť povinných testovacích blokov | Ich aktuálne zdrojové súbory, testy, príkazy, workflowy, migrácie a cleanup kontrakty ešte neboli úplne načítané a prakticky overené | zatiaľ chýba úplná mapa | úplná mapa 9 blokov a ich vykonateľnosť v izolovanom prostredí |
| 7. Predmet a hranice zásahu určené | ÁNO | Iba úplná lokálna a integračná Validácia Kroku 11 | Načítaný celý záväzný plán; zakázaná je príprava release, produkčný run, zmena produkcie a otvorenie Kroku 12 pred úspešným uzavretím Kroku 11 | `postupy/PLAN/2026-07-25_05-18_Plan_prace.md` | nič |
| 8. Kritérium úspechu určené | ÁNO | Deväť povinných blokov a spoločné kritérium `DB_UNIQUENESS + OUTCOMES + CLEANUP + COMPLETED_SUCCESS` | Kritériá boli prevzaté bez dopĺňania z plánu | Krok 11 v záväznom pláne | nič |
| 9. Rollback určený | ÁNO | Odstrániť iba testovacie dáta a dočasné artefakty podľa vopred potvrdeného cleanupu | Funkčný commit Kroku 10 sa automaticky nevracia; pri neúspechu sa release zakáže a vznikne záznam príčiny | Krok 11 v záväznom pláne | konkrétne cleanup príkazy sa musia potvrdiť spolu so závislosťami pred otvorením brány |

## Vyhodnotenie brány

```text
1=ÁNO
2=ÁNO
3=ÁNO
4=NEOVERENÉ
5=NEOVERENÉ
6=NEOVERENÉ
7=ÁNO
8=ÁNO
9=ÁNO
GATE=CLOSED
```

```text
BLOKUJÚCI_BOD=4, 5 a 6
CHÝBAJÚCI_DÔKAZ=praktický zápis a read-back INI; oprávnenie na izolované joby; aktuálne validačné prostredie; úplná mapa a vykonateľnosť deviatich testovacích blokov
POVOLENÝ_ĎALŠÍ_ÚKON=iba dokončenie overovania bodov 4 až 6 bez spustenia predmetu Validácie Kroku 11
```

## Tabuľka stavu čítania a vykonávania metodických pokynov

| ID | Metodický pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Povinné nové načítanie vzdialeného autoritatívneho repozitára | 1 | 1 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 1 |
| 4 | Praktické overenie prístupov | 1 | 0 |
| 5 | Obnova projektového kontextu | 1 | 1 |
| 6 | Overenie skutočného stavu | 1 | 0 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 0 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 0 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 0 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |
