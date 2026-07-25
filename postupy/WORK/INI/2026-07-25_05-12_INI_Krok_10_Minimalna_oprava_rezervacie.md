# INICIALIZÁCIA KROKU 10 — Najmenšia funkčná oprava rezervácie

## Stav

```text
GATE=CLOSED
```

## 1. Metodika načítaná: ÁNO

- Overené nové úplné čítanie `postupy/Inicializácia práce.md`.
- Blob: `201bcaef882e21e99c484b2c9acd61489e1e9914`.
- Výsledok: Krok 10 smie začať iba po deviatich doložených hodnotách `ÁNO`.
- Dôkaz: autoritatívna vetva `main`.
- Neoverené: nič blokujúce v metodike.

## 2. Projekt a autoritatívny zdroj overený: ÁNO

- Projekt: `METODIKA`.
- Repozitár: `slapiar/METODIKA`.
- Autoritatívna vetva: `main`.
- Technický koreň: `/codei`, CodeIgniter 4.7.4.
- Dôkaz: `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`.
- Neoverené: nič.

## 3. Vetva a HEAD overené: ÁNO

- Vzdialený HEAD pred týmto INI: `6d6dfbd361256c74fde2604816876e924ed8fde4`.
- Posledný uzavretý pracovný krok: Krok 9.
- Jediný nasledujúci krok podľa dnešného plánu: Krok 10.
- Dôkaz: história `main` a `postupy/PLAN/2026-07-25_08-00_Predbezny_plan_Kroky_10-15.md`.
- Neoverené: lokálny HEAD v používateľovom Codespace.

## 4. Potrebné prístupy prakticky overené: ČIASTOČNE / NEOVERENÉ

- Čítanie autoritatívneho repozitára bolo prakticky overené načítaním metodiky, projektu, plánu a zdrojových súborov.
- Zápis do `main` je prakticky overený vytvorením tohto povoleného INI záznamu.
- Prístup k runtime Codespace, testovacej MariaDB a jej neprodukčnému spojeniu v tomto pracovnom kontexte prakticky overený nebol.
- Produkčný hosting ani produkčná databáza nie sú pre Krok 10 povolené ani potrebné.

## 5. Prostredie prakticky overené: NEOVERENÉ

- Zo zdrojov je potvrdené technické prostredie CodeIgniter 4.7.4 v `/codei`.
- Prakticky nebola v aktuálnom pracovnom bloku overená verzia PHP, Composer, aktívne prostredie aplikácie, testovacia MariaDB, migrácie, izolácia, oprávnenia, cleanup ani rollback testovacích dát.
- Predchádzajúce úspešné testy Kroku 9 nenahrádzajú nové overenie prostredia Kroku 10.

## 6. Závislosti kroku dostupné: ČIASTOČNE / NEOVERENÉ

- Dostupný je dnešný plán Krokov 10–15 a uzavretý záznam Kroku 9.
- Načítané aktuálne zdroje:
  - `codei/app/Application/QuestionDerivation/FirstAcceptanceService.php`, blob `ab3ea26bd65db3df29c2daca27117cca7b882947`,
  - `codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php`, blob `e290f30c5f784d9c9292d60bd82a0ffb45c25faa`.
- Praktická dostupnosť databázového spojenia a schémy zostáva neoverená.

## 7. Predmet a hranice zásahu určené: ÁNO

- Predmet: odstrániť potvrdené chybné pokračovanie druhého súbežného toku ako `CREATED`, keď insert rezervácie pri `DBDebug=false` zlyhá bez výnimky.
- Hranica: iba repository logika rezervácie a jej bezprostredné testy.
- Mimo rozsahu: diagnostika, UI, run-store, `load()`, timeout, databázová schéma, release a produkcia.
- Aktuálne potvrdený dotknutý komponent: `RequestReferenceRepository::reserveFirstAcceptance()`.

## 8. Kritérium úspechu určené: ÁNO

```text
prvý tok = CREATED
paralelný druhý tok = ALREADY_EXISTS
žiadny tok bez presnej rezervácie REQUEST_REFERENCE + derivation_reference
nepokračuje do CREATE_INITIAL_HISTORY_RUN
```

- Povinné je potvrdenie izolovaným databázovým a skutočným súbežným testom.

## 9. Rollback určený: ÁNO

- Rollback budúcej opravy: vrátenie jediného funkčného commitu Kroku 10.
- Schéma databázy sa meniť nesmie.
- Testovacie dáta musia byť odstrániteľné explicitným cleanupom alebo transakčným rollbackom.

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
BLOKUJÚCI_BOD=4, 5 a 6 — runtime, testovacia databáza a závislosti prostredia
CHÝBAJÚCI_DÔKAZ=praktické overenie PHP, Composer, neprodukčnej MariaDB, migrácií, izolácie a cleanupu v aktuálnom Codespace
POVOLENÝ_ĎALŠÍ_ÚKON=iba praktické overenie uvedených predpokladov bez návrhu alebo zmeny produkčného kódu
```
