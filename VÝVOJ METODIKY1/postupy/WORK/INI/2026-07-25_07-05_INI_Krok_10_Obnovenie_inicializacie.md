# INICIALIZÁCIA KROKU 10 — Obnovenie inicializácie podľa aktuálnej metodiky

## Stav projektovej brány

```text
GATE=CLOSED
```

## Overenie brány predchádzajúceho kroku pred prvým zápisom

- Predchádzajúci krok: Krok 9 — Audit a test bariéry, `load()` a timeoutu.
- Stav: `SPLNENÉ`.
- Jeho inicializácia uvádza `GATE=OPEN`.
- Dôkaz: `postupy/WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md`, blob `8d0f74ae3cc129111a5bbd22429daefc5e97d55a`.

## 1. Metodika načítaná: ÁNO

- Čo bolo overené: celý aktuálny obsah `postupy/Inicializácia práce.md`, vrátane bodov 0–14, postupu pri STOP a základného pravidla.
- Konkrétny úkon: nové úplné načítanie z autoritatívnej vetvy `main` v dvoch nadväzujúcich častiach.
- Výsledok: metodika bola načítaná celá; aktuálne vyžaduje aj zobrazenie tabuľky `#ID/R/W` na obrazovke.
- Dôkaz: blob `c00743e3cbde1685e9bdfe69900e175bf21b59da`.
- Neoverené: nič.

## 2. Projekt a autoritatívny zdroj overený: ÁNO

- Čo bolo overené: projekt, repozitár, autoritatívna vetva a technický koreň.
- Konkrétny úkon: úplné načítanie `PROJEKTY/ZoznamProjektov.md`.
- Výsledok: projekt `METODIKA`, repozitár `slapiar/METODIKA`, vetva `main`, technický koreň `/codei`, CodeIgniter 4.7.4.
- Dôkaz: blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`.
- Neoverené: vlastník alebo zodpovedná osoba nie je v registri výslovne uvedená; vykonávacou autoritou v tomto pracovnom bloku je používateľ.

## 3. Vetva a HEAD overené: ÁNO

- Čo bolo overené: aktuálny vzdialený HEAD a relevantná história od uzavretia Kroku 9.
- Konkrétny úkon: nové načítanie histórie `main` a porovnanie `6d12f1e6dd7b6e45f4442b1a863c4143764390fd...2e761db8800a17d5055a1ac865385914071be2b4`.
- Výsledok: HEAD pred týmto INI je `2e761db8800a17d5055a1ac865385914071be2b4`; od Kroku 9 sa nemenil vykonateľný kód v `/codei`. Pribudli metodické, plánovacie a evidenčné zmeny a workflow overenia prostredia.
- Dôkaz: história `main` a uvedené porovnanie.
- Neoverené: lokálny pracovný strom používateľovho Codespace nie je cez GitHub konektor viditeľný.

## 4. Potrebné prístupy prakticky overené: NEOVERENÉ

- Čo bolo overené: vzdialené čítanie a zápis do `main`.
- Konkrétny úkon: nové čítanie metodiky, plánu, projektu, Kroku 9, existujúceho INI a dotknutých zdrojov; zápis tohto INI.
- Výsledok: prístup ku GitHub repozitáru na čítanie a zápis je funkčný.
- Dôkaz: tento INI záznam a jeho commit.
- Neoverené: praktický prístup k runtime, Composeru, test runneru a izolovanej MariaDB pre Krok 10.

## 5. Prostredie prakticky overené: NEOVERENÉ

- Čo bolo overené: existuje workflow `.github/workflows/krok-10-environment-verification.yml` deklarujúci izolované Ubuntu 24.04, PHP 8.4, Composer 2, MySQLi a MariaDB 11.4.
- Konkrétny úkon: kontrola workflow a kontrola stavov commitu, ktorý ho vytvoril.
- Výsledok: existencia workflow nie je dôkazom vykonaného prostredia; nebol dostupný žiadny úspešný workflow run ani status.
- Dôkaz: workflow blob `4eee8d11defa62297d1c8d287040d461076fa2ae`; commit `227b5c62db0801bef5653c2309283582cae8322c`; stavové kontroly boli prázdne.
- Neoverené: PHP, Composer, rozšírenia, MariaDB, migrácie M1–M8, dve spojenia, izolácia, počiatočný stav, rollback a cleanup v aktuálne vykonanom prostredí.

## 6. Závislosti kroku dostupné: NEOVERENÉ

- Čo bolo overené: celý aktuálny plán a bezprostredne dotknuté zdroje.
- Konkrétny úkon: úplné nové načítanie plánu a zdrojov.
- Výsledok: zdrojový predmet Kroku 10 je dostupný a od Kroku 9 nezmenený; vykonateľnosť databázových a testovacích závislostí nie je prakticky potvrdená.
- Dôkaz:
  - plán `postupy/PLAN/2026-07-25_05-18_Plan_prace.md`, blob `7571da852936f5ad0021b1dd9b6dff15f0116bb1`,
  - `RequestReferenceRepository.php`, blob `e290f30c5f784d9c9292d60bd82a0ffb45c25faa`,
  - `FirstAcceptanceService.php`, blob `ab3ea26bd65db3df29c2daca27117cca7b882947`.
- Neoverené: úspešne vykonané izolované DB a testovacie prostredie.

## 7. Predmet a hranice zásahu určené: ÁNO

- Predmet: opraviť iba potvrdené chybné pokračovanie druhého toku ako `CREATED`, keď `insert()` pri `DBDebug=false` zlyhá bez výnimky a postcheck iba podľa `REQUEST_REFERENCE` načíta rezerváciu prvého toku.
- Dotknutý komponent: `RequestReferenceRepository::reserveFirstAcceptance()` a bezprostredný regresný test.
- Mimo rozsahu: databázová schéma, diagnostika, UI, `DiagnosticsConcurrencyRunStore::load()`, timeout, release a produkcia.
- Podmienka STOP: ak oprava vyžaduje zmenu schémy, mieša iné oblasti alebo izolovaná DB nie je pripravená.

## 8. Kritérium úspechu určené: ÁNO

```text
prvý tok = CREATED
paralelný druhý tok = ALREADY_EXISTS
žiadny tok bez presnej rezervácie REQUEST_REFERENCE + derivation_reference
nepokračuje do CREATE_INITIAL_HISTORY_RUN
```

- Existujúce jednotkové testy musia prejsť.
- Regresný test musí presne reprodukovať pôvodnú chybu.
- Nesmie vzniknúť duplicitný počiatočný history run ani zvyškové testovacie dáta.

## 9. Rollback určený: ÁNO

- Funkčný rollback: vrátenie jediného budúceho funkčného commitu Kroku 10.
- Databázová schéma sa nesmie meniť.
- Testovacie dáta musia byť odstránené potvrdeným rollbackom alebo cleanupom.
- Tento INI záznam nemení vykonateľný kód ani databázu.

## Vyhodnotenie projektovej brány

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
BLOKUJÚCI_BOD=4, 5 a 6 — praktické prístupy, vykonávacie prostredie a vykonateľné závislosti
CHÝBAJÚCI_DÔKAZ=úspešný praktický výstup izolovaného prostredia potvrdzujúci PHP, Composer, MySQLi, MariaDB, migrácie M1–M8, dve spojenia, počiatočný stav, rollback a cleanup
POVOLENÝ_ĎALŠÍ_ÚKON=iba získať a vyhodnotiť praktický dôkaz prostredia bez návrhu opravy, zmeny vykonateľného kódu alebo predmetového testu
```

## Dôkaz prečítania a vykonania metodických pokynov

Tento blok kontroluje postup asistentky a nemá vplyv na projektovú bránu.

```text
#ID=0  R=1 W=1
#ID=1  R=1 W=1
#ID=2  R=1 W=1
#ID=3  R=1 W=1
#ID=4  R=1 W=1
#ID=5  R=1 W=1
#ID=6  R=1 W=1
#ID=7  R=1 W=1
#ID=8  R=1 W=0
#ID=9  R=1 W=0
#ID=10 R=1 W=0
#ID=11 R=1 W=0
#ID=12 R=1 W=0
#ID=13 R=1 W=1
#ID=14 R=1 W=0
```

Hodnoty `W=0` označujú úkony, ktoré sa nesmú vykonať pred otvorením projektovej brány alebo pred dokončením kroku.