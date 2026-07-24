# INICIALIZÁCIA KROKU 9 — Audit a test bariéry, `load()` a timeoutu

## Stav

```text
GATE=OPEN
```

## 1. Metodika načítaná: ÁNO

- Overené: aktuálny dokument `postupy/Inicializácia práce.md`, stav `POTVRDENÝ-NA-PRENESENIE`, blob `201bcaef882e21e99c484b2c9acd61489e1e9914`.
- Úkon: nové čítanie z autoritatívnej vetvy `main`.
- Výsledok: potvrdená povinnosť deväťbodovej dôkazovej brány, oddelenia analýzy od návrhu a praktického overenia prostredia.
- Dôkaz: `postupy/Inicializácia práce.md` na `main`.
- Neoverené: nič, čo by blokovalo tento krok.

## 2. Projekt a autoritatívny zdroj overený: ÁNO

- Overené: projekt `METODIKA`, repozitár `github.com/slapiar/metodika`, autoritatívna vetva `main`, technický koreň `/codei` s CodeIgniter 4.7.4.
- Úkon: nové čítanie `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`.
- Výsledok: ide o pokračovanie vývoja diagnostického súbežného overenia; CodeIgniter je technický nosič, nie významová autorita.
- Dôkaz: `PROJEKTY/ZoznamProjektov.md`.
- Neoverené: žiadny iný projekt alebo repozitár nie je súčasťou zásahu.

## 3. Vetva a HEAD overené: ÁNO

- Overené: autoritatívna vetva `main`; aktuálny vzdialený HEAD pred otvorením Kroku 9 je `a2d0aa03c08d80b22dac6301e6109e664dae758e`.
- Úkon: vyhľadanie najnovších commitov repozitára a porovnanie s používateľovým potvrdením synchronizácie.
- Výsledok: posledná zmena odstránila omylom obnovený jednorazový workflow Kroku 8; Krok 8 zostáva uzavretý commitom `6bcfd98ac03fa1153546ba5cc2ea05c24298d9aa`.
- Dôkaz: história commitov `main`.
- Neoverené: lokálna pracovná vetva používateľa nie je autoritatívnym zdrojom; používateľ však výslovne potvrdil synchronizáciu.

## 4. Potrebné prístupy prakticky overené: ÁNO

- Overené: čítanie aktuálnych zdrojov a zápis nového INI súboru do `main` cez GitHub contents API.
- Úkon: úspešné čítanie metodiky, projektu, plánu a `DiagnosticsConcurrencyRunStore.php`; tento INI súbor je praktickým bezpečným zápisom.
- Výsledok: prístup potrebný na audit, zápis testov a dokumentácie je funkčný.
- Dôkaz: commit vytvorenia tohto súboru.
- Neoverené: produkčný hosting ani produkčná databáza nie sú pre Krok 9 potrebné a nebudú použité.

## 5. Prostredie prakticky overené: ÁNO

- Overené: izolované GitHub Actions prostredie s PHP 8.4, Composerom a testovacím file-store bolo v bezprostredne predchádzajúcom Kroku 8 prakticky spustené; patch a syntax prešli, diagnostické unit testy prešli `6/6` a session testy odhalili reprodukovateľný stavový rozpor `EXECUTING` verzus `BARRIER_OPEN`.
- Úkon: vyhodnotenie runov `30094146624` a úspešnej presnej Validácie, ktorá viedla ku commitu `6bcfd98...`.
- Výsledok: prostredie potrebné pre file-locking, session testy a dvojprocesový test je dostupné mimo produkcie; aktuálny Krok 9 vytvorí vlastnú izolovanú Validáciu.
- Dôkaz: GitHub Actions logy Kroku 8 a commit `6bcfd98...`.
- Neoverené: skutočná paralelnosť nového dvojprocesového scenára bude predmetom Kroku 9, nie predpokladom jeho výsledku.

## 6. Závislosti kroku dostupné: ÁNO

- Overené: `DiagnosticsConcurrencyRunStore::load()`, `mutate()`, `exposeOpenedBarrierToWaitingRequest()`, `isPartnerTimeoutAttempt()`, kontrolérové metódy `awaitBarrierOrTimeout()` a `claimFinalizationForTimeout()`, existujúce run-store a session testy.
- Úkon: priame čítanie `codei/app/Services/DiagnosticsConcurrencyRunStore.php`, blob `dfac24e9f7d452637a5eb02a4e9ea25f0bec9a1f`, a výsledkov session testov.
- Výsledok: všetky komponenty potrebné na audit a test sú v repozitári.
- Dôkaz: uvedené zdrojové súbory a log troch zlyhaní z runu `30094146624`.
- Neoverené: či je zmena `load()` nevyhnutná; to je rozhodovacia otázka Kroku 9.

## 7. Predmet a hranice zásahu určené: ÁNO

- Predmet:
  1. rozlíšiť uložený stav od interpretovanej kópie vracanej `load()`,
  2. auditovať `exposeOpenedBarrierToWaitingRequest()`,
  3. preskúmať pretek medzi otvorením bariéry a timeout mutáciou,
  4. overiť, že po otvorení bariéry nesmie byť zapísaný falošný `PARTNER_TIMEOUT`,
  5. vykonať skutočný dvojprocesový alebo paralelný test.
- Hranice: nemení sa repository vrstva first acceptance, databázová schéma, UI, produkcia ani funkčná koreňová oprava Kroku 10.
- Dotknuté oblasti: primárne run store, kontrolérový timeout tok a testy; zmena `load()` iba ak ju dôkazy preukážu ako nevyhnutnú.
- Dôkaz: záväzný plán Kroku 9 a stav registra, podľa ktorého sú Kroky 1–8 splnené.
- Neoverené: konečný rozsah kódovej zmeny vznikne až po analýze.

## 8. Kritérium úspechu určené: ÁNO

Krok 9 sa uzavrie iba vtedy, keď:

- je zdokumentovaný rozdiel medzi uloženým dokumentom a návratovou kópiou `load()`,
- je reprodukované a vysvetlené okno `BARRIER_OPEN / EXECUTING / timeout`,
- skutočný paralelný test potvrdí alebo vyvráti falošný timeout,
- existujúce tri zlyhania z Kroku 8 sú presne klasifikované bez ich maskovania,
- je rozhodnuté a zdôvodnené, či `load()` treba meniť,
- ak zmena nie je nevyhnutná, `load()` zostane bez zmeny,
- všetky dotknuté testy prejdú v izolovanom PHP 8.4 prostredí,
- výsledok je zapísaný v `postupy/WORK/`, `postupy/README.md` a `CHANGELOG.md`.

## 9. Rollback určený: ÁNO

- Testy a prípadný technický zásah budú oddelené od funkčnej opravy Kroku 10.
- Rollback: vrátenie samostatného commitu Kroku 9; odstránenie dočasných testovacích súborov a workflowu; file-store test používa vlastný dočasný adresár s cleanupom.
- Produkčné prostredie ani databáza sa nemenia.

## Brána

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

Statický audit presnej stavovej cesty a vytvorenie minimálnych testov Kroku 9. Funkčná oprava koreňovej príčiny rezervácie patrí až do Kroku 10.