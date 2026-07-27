# INICIALIZÁCIA KROKU 10 — Obnova úplného vzdialeného stavu

## Stav

```text
GATE=CLOSED
```

## 1. Metodika načítaná: ÁNO

- Čo bolo overené: úplný aktuálny obsah `postupy/Inicializácia práce.md`.
- Konkrétny úkon: nové načítanie celého dokumentu z autoritatívnej vetvy `main` v dvoch nadväzujúcich častiach.
- Výsledok: metodika vyžaduje pred každým krokom úplné nové načítanie vzdialeného stavu; register, pamäť ani starší blob ho nenahrádzajú.
- Dôkaz: `postupy/Inicializácia práce.md`, blob `8e3d73b1253f403d86f979d1270726b5abf70524`.
- Neoverené: nič v tomto bode.

## 2. Projekt a autoritatívny zdroj overený: ÁNO

- Čo bolo overené: projekt, repozitár, vetva a technický koreň.
- Konkrétny úkon: úplné nové načítanie `PROJEKTY/ZoznamProjektov.md`.
- Výsledok: projekt `METODIKA`, repozitár `slapiar/METODIKA`, autoritatívna vetva `main`, technický koreň `/codei`, CodeIgniter 4.7.4.
- Dôkaz: `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`.
- Neoverené: nič v tomto bode.

## 3. Vetva a HEAD overené: ÁNO

- Čo bolo overené: aktuálny vzdialený HEAD, posledný uzavretý krok a zmeny od jeho uzavretia.
- Konkrétny úkon: nové načítanie histórie `main` a porovnanie commitu uzavretia Kroku 9 `6d12f1e6dd7b6e45f4442b1a863c4143764390fd` s aktuálnym HEAD.
- Výsledok: aktuálny vzdialený HEAD pred týmto INI je `4c9ce2b99ee75ead3db8c7c8c1556cf9408ba09e`; od Kroku 9 sa nezmenil žiadny súbor v `/codei`, iba metodika, plán, evidencia a používateľom vykonaný cleanup pracovných INI a starých release ZIP-ov.
- Dôkaz: história `main` a porovnanie `6d12f1e6...4c9ce2b9`.
- Neoverené: lokálny HEAD a čistota pracovného stromu používateľovho aktuálneho Codespace nie sú z GitHub konektora viditeľné.

## 4. Potrebné prístupy prakticky overené: NEOVERENÉ

- Čo bolo overené: čítanie a zápis do vzdialeného autoritatívneho repozitára.
- Konkrétny úkon: úplné čítanie metodiky, plánu, projektu, histórie, nadväzujúcich WORK/INI záznamov a dotknutých zdrojov; zápis tohto jediného povoleného INI záznamu.
- Výsledok: prístup k `main` na čítanie a zápis je funkčný.
- Dôkaz: tento INI záznam a jeho commit.
- Neoverené: prístup k vykonávaciemu runtime, Composeru, test runneru a izolovanej MariaDB použiteľnej pre Krok 10.

## 5. Prostredie prakticky overené: NEOVERENÉ

- Čo bolo overené zo vzdialených zdrojov:
  - `codei/composer.json`, blob `ab34d89fd260456cc4aa6485c09c2e4b2463bdd4`, požaduje PHP `^8.2` a obsahuje PHPUnit,
  - `codei/app/Config/Database.php`, blob `055b7ec29af4dfc1e8c3216bccc39ad84e2c227c`, určuje MySQLi, `DBDebug=false`, `utf8mb4_bin` a prázdne nesekretové pripojenie,
  - Krok 7 historicky prešiel v izolovanom GitHub Actions prostredí s PHP 8.4, Composerom 2 a MariaDB 11.4.
- Výsledok: zdroje a historický dôkaz prostredie opisujú, ale nepreukazujú jeho aktuálnu praktickú dostupnosť pre tento krok.
- Dôkaz: uvedené bloby a `postupy/WORK/2026-07-24_13-25_Krok_7_Reprodukcia_korenovej_priciny_mimo_produkcie.md`, blob `b222929de161a4da363d27c48dca09140f95f8f7`.
- Neoverené: aktuálne spustiteľné PHP a Composer, nainštalované závislosti, MySQLi, izolovaná MariaDB, migrácie M1–M8, dve spojenia alebo procesy, počiatočný stav dát, cleanup a rollback.

## 6. Závislosti kroku dostupné: NEOVERENÉ

- Čo bolo overené: úplný aktuálny obsah bezprostredne dotknutých zdrojov a testu.
- Konkrétny úkon: nové načítanie:
  - `codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php`, blob `e290f30c5f784d9c9292d60bd82a0ffb45c25faa`,
  - `codei/app/Application/QuestionDerivation/FirstAcceptanceService.php`, blob `ab3ea26bd65db3df29c2daca27117cca7b882947`,
  - `codei/tests/unit/FirstAcceptanceServiceTest.php`, blob `4d2de22093708ac126bc3ce3ed0348e3f1438350`,
  - aktuálny celý denný plán, blob `7571da852936f5ad0021b1dd9b6dff15f0116bb1`,
  - uzavretý Krok 9, blob `8d0f74ae3cc129111a5bbd22429daefc5e97d55a`.
- Výsledok: zdrojový predmet Kroku 10 je dostupný a technicky nezmenený od Kroku 9.
- Neoverené: vykonateľná databázová a súbežná testovacia závislosť v aktuálnom pracovnom prostredí.

## 7. Predmet a hranice zásahu určené: ÁNO

- Predmet: odstrániť potvrdené chybné pokračovanie druhého toku ako `CREATED`, keď `insert()` pri `DBDebug=false` zlyhá bez výnimky a následný lookup iba podľa `REQUEST_REFERENCE` načíta rezerváciu prvého toku.
- Dotknutý komponent: `RequestReferenceRepository::reserveFirstAcceptance()` a jeho bezprostredný regresný test.
- Mimo rozsahu: databázová schéma, diagnostika, UI, `DiagnosticsConcurrencyRunStore::load()`, timeoutová poistka, release a produkcia.
- Neoverené: konkrétny návrh opravy nevzniká, kým je brána zatvorená.

## 8. Kritérium úspechu určené: ÁNO

```text
prvý tok = CREATED
paralelný druhý tok = ALREADY_EXISTS
žiadny tok bez presnej rezervácie REQUEST_REFERENCE + derivation_reference
nepokračuje do CREATE_INITIAL_HISTORY_RUN
```

- Existujúce jednotkové testy musia prejsť.
- Musí pribudnúť alebo byť upravený test presne reprodukujúci pôvodnú chybu.
- Nesmie vzniknúť duplicitný počiatočný history run ani zvyškové testovacie dáta.

## 9. Rollback určený: ÁNO

- Funkčný rollback: vrátenie jediného budúceho funkčného commitu Kroku 10.
- Databázová schéma sa meniť nesmie.
- Testovacie dáta musia byť odstránené potvrdeným cleanupom alebo rollbackom.
- Tento zatvorený pokus nevykonal funkčnú zmenu; rollback nie je potrebný.

## Brána

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
BLOKUJÚCI_BOD=4, 5 a 6 — praktické prístupy, aktuálne vykonávacie prostredie a testovacie závislosti Kroku 10
CHÝBAJÚCI_DÔKAZ=aktuálny praktický výstup z vykonávacieho prostredia potvrdzujúci PHP, Composer, nainštalované závislosti, MySQLi, izolovanú MariaDB, migrácie M1–M8, dve spojenia alebo procesy, počiatočný stav dát, cleanup a rollback
POVOLENÝ_ĎALŠÍ_ÚKON=iba praktické overenie uvedených chýbajúcich predpokladov bez návrhu opravy, zmeny vykonateľného kódu, predmetového testu alebo zásahu do databázy
```