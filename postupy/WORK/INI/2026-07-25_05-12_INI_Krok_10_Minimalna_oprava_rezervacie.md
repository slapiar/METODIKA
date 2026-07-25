# INICIALIZÁCIA KROKU 10 — Najmenšia funkčná oprava rezervácie

## Stav

```text
GATE=CLOSED
```

## 1. Metodika načítaná: ÁNO

- Overené novým čítaním aktuálneho `postupy/Inicializácia práce.md`.
- Blob: `201bcaef882e21e99c484b2c9acd61489e1e9914`.
- Výsledok: Krok 10 smie začať iba po deviatich doložených hodnotách `ÁNO`; pri jedinom bode `NIE` alebo `NEOVERENÉ` zostáva jediným dovoleným artefaktom tento INI záznam.
- Dôkaz: autoritatívna vetva `main`.
- Neoverené: nič blokujúce v metodike.

## 2. Projekt a autoritatívny zdroj overený: ÁNO

- Projekt: `METODIKA`.
- Repozitár: `slapiar/METODIKA`.
- Autoritatívna vetva: `main`.
- Technický koreň: `/codei`, CodeIgniter 4.7.4.
- Dôkaz: `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`.
- Neoverené: nič.

## 3. Vetva a HEAD overené: ČIASTOČNE / NEOVERENÉ

- Aktuálny vzdialený HEAD vetvy `main` bol novým čítaním histórie potvrdený ako `a9fa1ff7ad7d7fb38a87e51aa065317d02bd5841`.
- Posledný uzavretý pracovný krok: Krok 9.
- Dnešný záväzný plán: `postupy/PLAN/2026-07-25_05-18_Plan_prace.md`.
- Jediný nasledujúci povolený krok: Krok 10.
- Dôkaz: história `main`, register `postupy/README.md` a uzavretý WORK záznam dnešného plánu.
- Neoverené: lokálna vetva, lokálny HEAD a čistota pracovného stromu v používateľovom aktuálnom Codespace.

## 4. Potrebné prístupy prakticky overené: ČIASTOČNE / NEOVERENÉ

- Čítanie autoritatívneho repozitára bolo prakticky overené načítaním metodiky, plánu, registra, histórie a zdrojových súborov.
- Zápis do `main` bol prakticky overený aktualizáciou tohto povoleného INI záznamu.
- Pokus o lokálne overenie prostredia z tohto pracovného kontextu zistil, že príkaz `gh` nie je dostupný.
- Samostatný pokus o `git ls-remote` zlyhal na nedostupnom DNS prístupe k `github.com`; tento vykonávací kontajner preto nie je používateľovým Codespace ani použiteľným náhradným testovacím prostredím.
- Prístup k používateľovmu aktuálnemu Codespace, jeho terminálu, runtime a testovacej MariaDB nebol prakticky overený.
- Produkčný hosting ani produkčná databáza nie sú pre Krok 10 povolené ani potrebné.

## 5. Prostredie prakticky overené: NEOVERENÉ

- Zo zdrojov je potvrdené technické prostredie CodeIgniter 4.7.4 v `/codei`; existencia zdrojov však nedokazuje pripravenosť aktuálneho runtime.
- V používateľovom aktuálnom Codespace neboli prakticky overené:
  - lokálna vetva a HEAD,
  - čistota pracovného stromu,
  - verzia a vykonateľnosť PHP,
  - Composer a nainštalované závislosti,
  - rozšírenia `mysqli` a ďalšie potrebné rozšírenia,
  - aktívne aplikačné prostredie,
  - neprodukčný MariaDB server a konkrétne spojenie,
  - migrácie M1–M8,
  - izolácia od produkcie,
  - oprávnenia,
  - stav testovacích riadkov a dočasných súborov,
  - praktická vykonateľnosť cleanupu a rollbacku.
- Predchádzajúce úspešné testy Krokov 7–9 nenahrádzajú nové overenie aktuálneho prostredia Kroku 10.

## 6. Závislosti kroku dostupné: ČIASTOČNE / NEOVERENÉ

- Dostupný je dnešný záväzný plán a uzavreté dôkazy Krokov 7–9.
- Načítané aktuálne zdroje:
  - `codei/app/Application/QuestionDerivation/FirstAcceptanceService.php`, blob `ab3ea26bd65db3df29c2daca27117cca7b882947`,
  - `codei/app/Infrastructure/Persistence/QuestionDerivation/RequestReferenceRepository.php`, blob `e290f30c5f784d9c9292d60bd82a0ffb45c25faa`.
- Staticky je potvrdený dotknutý komponent `RequestReferenceRepository::reserveFirstAcceptance()`.
- Praktická dostupnosť databázového spojenia, schémy, test runnera a súbežného testovacieho prostredia zostáva neoverená.

## 7. Predmet a hranice zásahu určené: ÁNO

- Predmet: odstrániť potvrdené chybné pokračovanie druhého súbežného toku ako `CREATED`, keď insert rezervácie pri `DBDebug=false` zlyhá bez výnimky.
- Hranica: iba repository logika rezervácie a jej bezprostredné testy.
- Mimo rozsahu: diagnostika, UI, run-store, `load()`, timeout, databázová schéma, release a produkcia.
- Potvrdený dotknutý komponent: `RequestReferenceRepository::reserveFirstAcceptance()`.
- Kým brána zostáva zatvorená, nevzniká návrh konkrétnej opravy ani zmena vykonateľného kódu.

## 8. Kritérium úspechu určené: ÁNO

```text
prvý tok = CREATED
paralelný druhý tok = ALREADY_EXISTS
žiadny tok bez presnej rezervácie REQUEST_REFERENCE + derivation_reference
nepokračuje do CREATE_INITIAL_HISTORY_RUN
```

- Povinné je potvrdenie izolovaným databázovým testom a skutočným súbežným testom.
- Nesmie vzniknúť duplicitný počiatočný history run ani zvyškové testovacie dáta.

## 9. Rollback určený: ÁNO

- Rollback budúcej opravy: vrátenie jediného funkčného commitu Kroku 10.
- Schéma databázy sa meniť nesmie.
- Testovacie dáta musia byť odstrániteľné explicitným cleanupom alebo transakčným rollbackom.
- Pred implementáciou musí byť prakticky potvrdená vykonateľnosť cleanupu v izolovanom prostredí.

## Brána

```text
1=ÁNO
2=ÁNO
3=NEOVERENÉ
4=NEOVERENÉ
5=NEOVERENÉ
6=NEOVERENÉ
7=ÁNO
8=ÁNO
9=ÁNO
GATE=CLOSED
```

```text
BLOKUJÚCI_BOD=3, 4, 5 a 6 — lokálny stav Codespace, prístupy, runtime, testovacia databáza a vykonateľné závislosti
CHÝBAJÚCI_DÔKAZ=praktický výstup z aktuálneho používateľovho Codespace potvrdzujúci vetvu a HEAD, čistotu pracovného stromu, PHP, Composer, rozšírenia, neprodukčnú MariaDB, migrácie M1–M8, izoláciu, počiatočný stav dát, cleanup a rollback
POVOLENÝ_ĎALŠÍ_ÚKON=iba praktické overenie uvedených predpokladov v aktuálnom Codespace bez návrhu opravy, zmeny vykonateľného kódu, testu predmetu Kroku 10 alebo zásahu do databázy
```
