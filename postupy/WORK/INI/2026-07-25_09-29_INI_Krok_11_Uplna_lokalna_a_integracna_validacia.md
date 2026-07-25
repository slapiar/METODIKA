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

## Aktuálny vzdialený stav

```text
repozitár=slapiar/METODIKA
vetva=main
HEAD_PRED_INI=7ba45337653a132901739291d6c57ceab11a5ef2
HEAD_PO_VYTVORENÍ_INI=eebd6d5cacd5696972adcb2841a7e3aa178b0f83
technický_koreň=/codei
```

Rozdiel medzi oboma HEAD tvorí iba tento povinný INI záznam. Relevantná história od funkčného commitu Kroku 10 obsahuje uzatváracie a nápravné metodické zápisy; posledný funkčný commit zostáva `c90ae562a859de9fe0b2174f39e924c7f7bc6a4e`.

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

## Výsledok praktického overovania bodov 4 až 6

### Bod 4 — potrebné prístupy

Prakticky potvrdené:

- autentifikovaný GitHub účet je vlastník `slapiar`,
- repozitár hlási oprávnenia `admin`, `maintain`, `pull`, `push` a `triage`,
- vytvorenie tohto INI na `main` prešlo,
- následné vzdialené spätné načítanie tohto INI prešlo,
- čítanie a zápis obsahu repozitára sú preto prakticky potvrdené.

Zostáva neoverené:

- nový bezpečný Actions úkon, ktorým sa bez zmeny workflowu a bez spustenia predmetu Validácie prakticky potvrdí právo spustiť izolovaný environment-only job pre Krok 11.

Existujúci environment-only workflow `.github/workflows/krok-10-environment-verification.yml` nemá `workflow_dispatch`; spúšťa sa iba pushom, ktorý mení samotný workflow. Jeho zmena by pri `GATE=CLOSED` bola zakázanou zmenou konfigurácie. Dostupné commit-run a status rozhrania nevrátili push-triggered run ani job identifikátor. Preto sa oprávnenie Actions pre nový environment-only beh neoznačuje ako overené.

### Bod 5 — prostredie

Aktuálne načítaná konfigurácia environment-only workflowu vyžaduje:

- Ubuntu 24.04,
- PHP 8.4,
- `intl`, `mbstring`, `mysqli`,
- Composer 2,
- uzamknuté závislosti,
- MariaDB 11.4 a InnoDB,
- izolovanú databázu,
- migrácie M1–M8,
- prázdny počiatočný stav,
- dve nezávislé spojenia,
- rollback a finálny cleanup.

Tento zoznam je iba aktuálna konfigurácia. Nový praktický beh pre Krok 11 nebol vykonaný, a preto nie sú aktuálne verzie, služby, migrácie, izolácia ani cleanup označené ako overené.

### Bod 6 — závislosti a mapa deviatich blokov

| Blok | Aktuálne zdroje a testy | Izolácia / cleanup | Stav mapovania |
|---:|---|---|---|
| 1. Run store a validator | `DiagnosticsConcurrencyRunStore.php`, `DiagnosticsConcurrencyRunDocumentValidator.php`, `DiagnosticsConcurrencyRunStoreTest.php`, `DiagnosticsConcurrencyRunDocumentValidatorTest.php`, `DiagnosticsConcurrencyBarrierProcessTest.php` | náhodný adresár `WRITEPATH/tests/*`; teardown odstraňuje JSON, lock aj temp súbory; store má idempotentný cleanup | zdroje načítané; praktická vykonateľnosť `pcntl_fork` čerstvo neoverená |
| 2. `FirstAcceptanceService` | `FirstAcceptanceServiceTest.php`; regresný príkaz `ReproduceFirstAcceptanceRootCause.php` | jednotkový test bez DB; regresný príkaz má DB cleanup | zdroje načítané; praktický beh v Kroku 11 nevykonaný |
| 3. Diagnostické chybové fázy | `DiagnosticsControllerTest.php`, `DiagnosticsConcurrencyAcceptanceRunner.php` | mockované výnimky; verejný výsledok nesie bezpečný fázový kód, raw správa nesmie byť v run dokumente ani odpovedi | zdroje načítané; praktický beh nevykonaný |
| 4. Session a security endpointy | `DiagnosticsControllerTest.php`, `Routes.php` | test teardown odstraňuje env hodnoty a resetuje služby; CSRF, token, session a feature flag sú súčasťou testu | zdroje načítané; praktický beh nevykonaný |
| 5. Integračný DB rollback | príkaz `metodika:verify-first-acceptance-transaction` | nadradený rollback, rollback po úmyselnej chybe a núdzový cleanup podľa `request_reference` | zdroje načítané; aktuálna MariaDB nevykonaná |
| 6. Skutočný súbeh | `DiagnosticsConcurrencyBarrierProcessTest.php`; príkaz `metodika:verify-concurrent-first-acceptance` | procesový test maže vlastný adresár; DB príkaz používa dve MySQLi spojenia, `MYSQLI_ASYNC` a cleanup na `0 + 0 + 0` | zdroje načítané; `pcntl_fork` a `MYSQLI_ASYNC` čerstvo neoverené |
| 7. `START → HIT A/B → RESULT` | `DiagnosticsControllerTest::testConcurrencyWebIntegrationStartHitResultEndToEnd()` a routes | náhodný run-store adresár a jeho odstránenie | test je výslovne jednovláknový a participanta A predpripraví; sám nenahrádza skutočný paralelný HTTP tok |
| 8. Tombstone a sweep | session testy read-once/redakcie a expirovaného sweepu; `DiagnosticsConcurrencyRunStore::cleanup()` | kontrola zániku `.json`, `.lock` a temp súborov | zdroje načítané; praktický beh nevykonaný |
| 9. Login/database/logout | login, neoprávnený prístup, token, databázová stránka a citlivé údaje sú v `DiagnosticsControllerTest.php`; logout route existuje v `Routes.php` | teardown session testu resetuje env a služby | v aktuálnom testovom súbore ani vyhľadávaní nebola nájdená samostatná regresia logout; pokrytie bloku nie je úplné |

Ďalšie zistené závislosti:

- `composer.json`: PHP `^8.2`, `ext-intl`, `ext-mbstring`, PHPUnit `^10.5.16 || ^11.2`,
- MySQL integračné bloky vyžadujú `mysqli`,
- dvojprocesový test vyžaduje `pcntl_fork`,
- DB súbežný príkaz vyžaduje `MYSQLI_ASYNC`,
- `phpunit.dist.xml` má `failOnWarning=true`; pri runneri bez coverage drivera treba použiť už potvrdený režim `--no-coverage`,
- workflow `.github/workflows/krok-7-root-cause-reproduction.yml` má `workflow_dispatch`, ale vykonáva predmet regresie prvého prijatia; pri zatvorenej bráne sa nesmie použiť ako náhrada environment-only overenia.

## Inicializačná tabuľka

| Bod | Stav | Čo bolo overené | Konkrétny úkon a výsledok | Dôkaz | Zostáva neoverené |
|---|---|---|---|---|---|
| 1. Metodika načítaná | ÁNO | Úplný aktuálny obsah `postupy/Inicializácia práce.md`, body 0–14, STOP a základné pravidlo | Súbor bol znovu načítaný z `main`; blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79` | aktuálny vzdialený súbor | nič |
| 2. Projekt a autoritatívny zdroj overený | ÁNO | Projekt METODIKA a jeho register | Znovu načítaný `PROJEKTY/ZoznamProjektov.md`; potvrdil `github.com/slapiar/metodika`, vetvu `main`, CodeIgniter 4.7.4 a koreň `/codei` | blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d` | nič |
| 3. Vetva a HEAD overené | ÁNO | Autoritatívna vetva `main`; HEAD pred a po jedinom povolenom artefakte | História potvrdila `7ba4533... → eebd6d5...`; rozdiel je iba tento INI | GitHub história commitov | HEAD sa musí znovu skontrolovať bezprostredne pred prvým testom |
| 4. Potrebné prístupy prakticky overené | NEOVERENÉ | GitHub čítanie, zápis, read-back a administrátorské oprávnenie sú potvrdené | INI bol vytvorený a spätne načítaný; metadata repozitára hlásia `admin=true` | commit `eebd6d5...`, blob `5a405be...`, metadata repozitára | praktický bezpečný Actions environment-only beh pre Krok 11 |
| 5. Prostredie prakticky overené | NEOVERENÉ | Aktuálny kontrakt izolovaného prostredia je načítaný | Konfigurácia workflowu bola prečítaná; nový beh sa nevykonal | `.github/workflows/krok-10-environment-verification.yml`, blob `4bd6bd7...` | aktuálne PHP, Composer, rozšírenia, MariaDB, migrácie, počiatočné počty, izolácia, rollback a cleanup |
| 6. Závislosti kroku dostupné | NEOVERENÉ | Načítaná bola presná mapa deviatich blokov, testov, príkazov a cleanupov | Úplné zdroje rozhodujúcich testov a služieb boli prečítané | tabuľka mapy vyššie a uvedené bloby | praktická vykonateľnosť `pcntl_fork`, `MYSQLI_ASYNC`, všetkých testov; úplné pokrytie logout; reálny paralelný HTTP variant E2E |
| 7. Predmet a hranice zásahu určené | ÁNO | Iba úplná lokálna a integračná Validácia Kroku 11 | Načítaný celý záväzný plán; zakázaná je príprava release, produkčný run, zmena produkcie a otvorenie Kroku 12 pred úspešným uzavretím Kroku 11 | `postupy/PLAN/2026-07-25_05-18_Plan_prace.md` | nič |
| 8. Kritérium úspechu určené | ÁNO | Deväť povinných blokov a spoločné kritérium `DB_UNIQUENESS + OUTCOMES + CLEANUP + COMPLETED_SUCCESS` | Kritériá boli prevzaté bez dopĺňania z plánu | Krok 11 v záväznom pláne | nič |
| 9. Rollback určený | ÁNO | Odstrániť iba testovacie dáta a dočasné artefakty podľa vopred potvrdeného cleanupu | Funkčný commit Kroku 10 sa automaticky nevracia; pri neúspechu sa release zakáže a vznikne záznam príčiny | Krok 11 v záväznom pláne a cleanup kontrakty v načítaných zdrojoch | konkrétny súhrnný cleanup sa potvrdí pred otvorením brány |

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
CHÝBAJÚCI_DÔKAZ=nový bezpečný environment-only Actions beh; aktuálne runtime a databázové prostredie; praktická vykonateľnosť pcntl_fork a MYSQLI_ASYNC; úplné pokrytie logout a presné rozhodnutie o reálnom paralelnom HTTP E2E variante
POVOLENÝ_ĎALŠÍ_ÚKON=iba doplnenie uvedených dôkazov bodov 4 až 6 bez zmeny workflowu, konfigurácie alebo vykonateľného kódu a bez spustenia predmetu Validácie Kroku 11
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
