# INICIALIZÁCIA KROKU 10 — Najmenšia funkčná oprava koreňovej príčiny

## Stav projektovej brány

```text
GATE=CLOSED
```

## 1. Metodika načítaná: ÁNO

- Čo bolo overené: celý aktuálny obsah `postupy/Inicializácia práce.md` vrátane bodov 0 až 14, postupu pri STOP a základného pravidla.
- Konkrétny úkon: dokument bol z autoritatívnej vetvy `main` načítaný v úplnom rozsahu v dvoch nadväzujúcich častiach.
- Výsledok: metodická kontrola `#ID / R / W` sleduje čítanie a vykonávanie pokynov asistentkou; nemá vplyv na stav projektovej brány.
- Dôkaz: `postupy/Inicializácia práce.md`, blob `b1710485f08d77ac712f1962c826b26f2a5a2f93`.
- Neoverené: nič v tomto bode.

## 2. Projekt a autoritatívny zdroj overený: ÁNO

- Čo bolo overené: projekt, repozitár, autoritatívna vetva a technický koreň.
- Konkrétny úkon: úplné načítanie `PROJEKTY/ZoznamProjektov.md`.
- Výsledok: projekt `METODIKA`, repozitár `slapiar/METODIKA`, autoritatívna vetva `main`, technický koreň `/codei`, CodeIgniter 4.7.4.
- Dôkaz: `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`.
- Neoverené: nič v tomto bode.

## 3. Vetva a HEAD overené: ÁNO

- Čo bolo overené: aktuálny vzdialený HEAD a zmeny od uzavretia Kroku 9.
- Konkrétny úkon: načítanie histórie `main` a porovnanie `6d12f1e6dd7b6e45f4442b1a863c4143764390fd...91d71346a8bcf9597a7bc1bc4ae6b1e298d09749`.
- Výsledok: vzdialený HEAD pred týmto INI je `91d71346a8bcf9597a7bc1bc4ae6b1e298d09749`; od Kroku 9 sa produkčný kód v `/codei` nezmenil. Pribudol však predčasne vytvorený workflow `.github/workflows/krok-10-environment-verification.yml` a metodické/evidenčné zmeny.
- Dôkaz: história `main`, porovnanie commitov a workflow blob `4eee8d11defa62297d1c8d287040d461076fa2ae`.
- Neoverené: lokálny pracovný stav používateľovho Codespace nie je dostupný cez GitHub konektor.

## 4. Potrebné prístupy prakticky overené: NEOVERENÉ

- Čo bolo overené: čítanie a zápis do autoritatívneho vzdialeného repozitára.
- Konkrétny úkon: úplné čítanie metodiky, projektu, plánu, registra, changelogu, histórie a workflow; vytvorenie tohto INI záznamu.
- Výsledok: vzdialené čítanie a zápis do `main` fungujú.
- Dôkaz: tento INI záznam a jeho commit.
- Neoverené: praktický prístup k vykonávaciemu runtime, Composeru, test runneru a izolovanej MariaDB. Lokálny nástroj `gh` v dostupnom kontajneri nie je nainštalovaný.

## 5. Prostredie prakticky overené: NEOVERENÉ

- Čo bolo overené: existencia a úplný obsah workflow pre izolované prostredie Kroku 10.
- Konkrétny úkon: načítanie `.github/workflows/krok-10-environment-verification.yml` a pokus získať workflow run viazaný na commit `227b5c62db0801bef5653c2309283582cae8322c`.
- Výsledok: workflow deklaruje Ubuntu 24.04, PHP 8.4, Composer 2, MySQLi, MariaDB 11.4, migrácie, dve spojenia, rollback a cleanup; samotný deklarovaný workflow však nie je dôkazom úspešne vykonaného prostredia. Dostupné rozhranie nevrátilo žiadny workflow run.
- Dôkaz: workflow blob `4eee8d11defa62297d1c8d287040d461076fa2ae`; výsledok vyhľadania workflow runov: prázdny zoznam.
- Neoverené: úspešné vykonanie runtime, Composeru, MariaDB, migrácií M1–M8, dvoch spojení, rollbacku a cleanupu.

## 6. Závislosti kroku dostupné: NEOVERENÉ

- Čo bolo overené: aktuálny plán, uzavretý Krok 9, repository komponent, aplikačná služba, test a workflow prostredia.
- Konkrétny úkon: nové načítanie a kontrola aktuálnych vzdialených zdrojov a porovnanie zmien od Kroku 9.
- Výsledok: zdrojový predmet Kroku 10 je dostupný a od Kroku 9 nezmenený; vykonateľné databázové a testovacie závislosti zatiaľ nemajú dôkaz úspešného behu.
- Dôkaz:
  - `postupy/PLAN/2026-07-25_05-18_Plan_prace.md`, blob `7571da852936f5ad0021b1dd9b6dff15f0116bb1`,
  - `codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php`, blob `e290f30c5f784d9c9292d60bd82a0ffb45c25faa`,
  - `codei/app/Application/QuestionDerivation/FirstAcceptanceService.php`, blob `ab3ea26bd65db3df29c2daca27117cca7b882947`,
  - `codei/tests/unit/FirstAcceptanceServiceTest.php`, blob `4d2de22093708ac126bc3ce3ed0348e3f1438350`,
  - workflow blob `4eee8d11defa62297d1c8d287040d461076fa2ae`.
- Neoverené: vykonateľnosť týchto závislostí v úspešne overenom izolovanom prostredí.

## 7. Predmet a hranice zásahu určené: ÁNO

- Predmet: opraviť chybné pokračovanie druhého toku ako `CREATED`, keď `insert()` pri `DBDebug=false` zlyhá bez výnimky a postcheck iba podľa `REQUEST_REFERENCE` načíta rezerváciu prvého toku.
- Dotknutý komponent: `RequestReferenceRepository::reserveFirstAcceptance()` a jeho bezprostredný regresný test.
- Mimo rozsahu: databázová schéma, diagnostika, UI, `DiagnosticsConcurrencyRunStore::load()`, timeoutová poistka, release a produkcia.
- Existujúci workflow z commitu `227b5c62...` je vedený ako predčasne vytvorený artefakt; nie je funkčnou opravou ani dôkazom otvorenej brány.

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
- Predčasný workflow možno odstrániť samostatným commitom, ak sa ukáže ako neplatný alebo nepotrebný.

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
POVOLENÝ_ĎALŠÍ_ÚKON=iba získať a vyhodnotiť dôkaz už vytvoreného environment-verification workflow; bez návrhu opravy a bez zmeny vykonateľného kódu
```

## Dôkaz prečítania a vykonania metodických pokynov

Tento blok kontroluje postup asistentky. Nemá vplyv na vyššie uvedenú projektovú bránu.

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

Vysvetlenie hodnôt `W=0`: analýza, návrh, implementácia, spätné načítanie výsledku, Validácia a ukončenie Kroku 10 ešte neboli vykonané, pretože projektová brána zostáva zatvorená. Ich nevykonanie je zámerné naplnenie zákazu predbehnúť bránu, nie tvrdenie o stave projektovej brány.