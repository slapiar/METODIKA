# INICIALIZÁCIA KROKU: Krok 12 — Jeden release a úplný audit balíka

Dátum: 2026-07-27 11:46 Europe/Bratislava

## Kontrolná matica

1. Metodika načítaná: ÁNO
   Dôkaz: vzdialený `postupy/Inicializácia práce.md` na vetve `main`,
   blob `4b73ad567bbc78a30e7cc2066b3fcada73b0022d`.

2. Projekt a autoritatívny zdroj: ÁNO
   Dôkaz: repozitár `slapiar/METODIKA`, vetva `main`, technický koreň `/codei`;
   aktuálny záväzný plán
   `postupy/PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md`.

3. Vetva a HEAD: ÁNO
   Dôkaz: aktuálny vzdialený `main`
   `11a62f688e6d3d641e8aad7e4f0b04bbb4c988e3`; technický strom `/codei`
   má stromový objekt `4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c`, zhodný s finálne
   validovaným technickým headom
   `bc85d18fd0edc1a52fad81f8fac54c1ae66a7014`.

4. Prístupy (read/write): ÁNO
   Dôkaz: GitHub konektor potvrdil oprávnenia `admin`, `maintain`, `pull`,
   `push` a aktuálny vzdialený read-back; používateľ výslovne začal Krok 12
   inicializáciou, analýzou a vytvorením plánu.

5. Prostredie a runtime: ÁNO
   Dôkaz: finálny validačný run `30252640028` nad zhodným technickým stromom
   skončil `success` s PHP `8.4.23`, Composerom `2.10.2` a MariaDB `11.4.12`.
   Autoritatívne vykonávacie prostredie Kroku 12 bude projektový GitHub Actions
   runner `ubuntu-24.04` s PHP 8.4 a Composerom 2. Pomocný Work runtime má
   dostupné `git 2.51.1`, `zip 3.0`, `unzip 6.00` a `sha256sum 9.4`, ale nemá
   PHP ani Composer, preto sa v ňom výsledný release nevytvorí.

6. Závislosti kroku: ÁNO
   Dôkaz: autoritatívny `release.sh`, blob
   `e365e7ec087ed81c350a7998ab5ac79450aed1c1`, prešiel `bash -n`; dostupné sú
   zdrojový strom `/codei`, uzamknuté závislosti `codei/composer.lock`,
   nástroje `git`, `zip`, `unzip`, `sha256sum` a overený spôsob prípravy PHP 8.4
   s Composerom 2 v existujúcom validačnom workflowe.

7. Predmet a hranice zásahu: ÁNO
   Dôkaz: predmetom je iba analýza a vykonanie Kroku 12 podľa záväzného plánu:
   jeden bump `1.1.15 → 1.1.16`, jeden ZIP vytvorený `release.sh`, SHA-256,
   čisté rozbalenie, úplné porovnanie so zdrojovým technickým stromom a audit
   zakázaných artefaktov. V rozsahu sú `RELEASE_VERSION`, nový release ZIP,
   prípadný jednoúčelový workflow Kroku 12 a povinné záznamy v `postupy/`.
   Mimo rozsahu sú produkcia, nasadenie, produkčná databáza, feature flagy
   a otvorenie Kroku 13.

8. Kritérium úspechu: ÁNO
   Dôkaz: záväzný plán určuje
   `JEDEN_RELEASE=true`, `HASH=ZAZNAMENANÝ`,
   `BALÍK_ZODPOVEDÁ_HEAD=true`, `ZAKÁZANÉ_ARTEFAKTY=0` a
   `ROLLBACK_BALÍK=DOSTUPNÝ`. Navyše musí zostať zachovaná presná väzba
   `zdrojový HEAD → verzia → názov ZIP → SHA-256 → auditný výsledok`.

9. Rollback plán: ÁNO
   Dôkaz: pred nasadením možno vrátiť jedinú release dávku obnovením
   `RELEASE_VERSION=1.1.15` a odstránením iba nového, ešte nenasadeného ZIP-u
   `1.1.16`; existujúci historický balík `1.1.15`, blob
   `aeaf80b31f299cbf824834590bd58435e216e99d`, zostane nedotknutý. Produkčný
   rollback nie je predmetom Kroku 12.

## Stav brány

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
BLOKUJÚCI_BOD=ŽIADNY
POVOLENÝ_ĎALŠÍ_ÚKON=ČAKAŤ_NA_POKYN_PRED_FÁZOU_12_E
```

## Povinné východiská analýzy

```text
KROK_11=SPLNENÉ
VALIDOVANÝ_TECHNICKÝ_HEAD=bc85d18fd0edc1a52fad81f8fac54c1ae66a7014
VALIDOVANÝ_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
MAIN_PRI_INICIALIZÁCII=11a62f688e6d3d641e8aad7e4f0b04bbb4c988e3
CODEI_TREE_PRI_INICIALIZÁCII=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
VALIDAČNÝ_RUN=30252640028
RELEASE_VERSION=1.1.15
PRODUKČNE_POZOROVANÁ_VERZIA=1.1.15
ZDROJOVÝ_COMMIT_PRODUKČNÉHO_NASADENIA=NEZISTENÝ
NOVÝ_RELEASE_ZATIAĽ=false
NOVÝ_ZIP_ZATIAĽ=false
PRODUKCIA_BEZ_ZÁSAHU=true
```

Nezistený zdrojový commit produkčného nasadenia sa nesmie nahradiť odhadom.
Krok 12 preto najprv oddelí:

- doloženú produkčne zobrazenú verziu `1.1.15`,
- historický repozitárový release commit `019a5c8fb741b640192dd89536be1091ef11e437`,
- existujúci ZIP `releases/metodika-codei-hostinger-1.1.15.zip`,
- finálne validovaný technický strom Kroku 11.

Až z týchto dôkazov sa vytvorí presná auditná základňa. Existujúci ZIP
`1.1.15` sa bez auditu nesmie označiť za overený rollback balík.

## Matica plnenia pokynov

| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|:---:|:---:|
| ID-01 až ID-14 | 1 | 1 |

## Historický checkpoint po analýze a vytvorení plánu

```text
INICIALIZÁCIA=SPLNENÁ
ANALÝZA=SPLNENÁ
PLÁN_KROKU_12=postupy/PLAN/2026-07-27_11-51_Plan_Kroku_12_release_a_audit_balika.md
WORK_ZÁZNAM=postupy/WORK/2026-07-27_11-53_Inicializacia_analyza_a_plan_Kroku_12.md
GATE=OPEN
AKTÍVNA_FÁZA_V_TOMTO_CHECKPOINTE=12.A_ZMRAZENIE_AUDITNEJ_ZÁKLADNE
RELEASE_VERSION=1.1.15_BEZ_ZMENY
NOVÝ_ZIP=false
PRODUKCIA_BEZ_ZÁSAHU=true
KROK_13=ZATVORENÝ
```

Zistený rozpor historického ZIP-u `1.1.15` nie je blokáciou vytvorenia nového
release z úplne validovaného stromu. Je však dôvodom, prečo sa tento historický
ZIP nesmie označiť za overený rollback balík a nesmie sa potichu prepísať.

## Checkpoint Fázy 12.A — zmrazenie auditnej základne

Dátum vykonania: 2026-07-27 12:18 Europe/Bratislava

### Výsledok šiestich povinných úkonov

1. Vzdialený `main` znovu načítaný: ÁNO
   Dôkaz: pred kontrolou aj po kontrole
   `771b0c2b69e3f1e1d7b74604b672275823bc9f95`.

2. Strom `/codei` potvrdený: ÁNO
   Dôkaz: aktuálny aj validovaný strom
   `4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c`.

3. `RELEASE_VERSION` potvrdený: ÁNO
   Dôkaz: vzdialený `main` obsahuje `1.1.15`.

4. Blob `release.sh` potvrdený: ÁNO
   Dôkaz: `e365e7ec087ed81c350a7998ab5ac79450aed1c1`.

5. Dva porovnávacie pohľady zaznamenané: ÁNO
   Dôkaz:
   - `1.1.9 / 3b91c4e... → bc85d18...`: 42 zmenených ciest v `/codei`,
     z toho 28 pridaných a 14 zmenených;
   - historický ZIP `1.1.15 →` očakávaný manifest validovaného stromu:
     812 verzus 813 súborov, 6 chýbajúcich, 5 neočakávaných a 17 obsahových
     rozdielov.

6. Rollbackový ZIP `1.1.9` potvrdený: ÁNO
   Dôkaz:

   ```text
   ZIP_BLOB=1e407b914e9be81b500612b69dd20492fbb63fa5
   SHA256=5aa4d4bd458c9ae4a1a003594de101aba6a2d4e010d24c5bcc94e9b26a0b5e72
   OČAKÁVANÉ_SÚBORY=790
   SKUTOČNÉ_SÚBORY=790
   CHÝBAJÚCE=0
   NEOČAKÁVANÉ=0
   OBSAHOVÉ_ROZDIELY=0
   ```

Historický ZIP `1.1.15` bol týmto jednorazovo klasifikovaný a ďalej nie je
aktívnou auditnou ani rollbackovou základňou. Zostal bez zmeny.

### Záver Fázy 12.A

```text
FÁZA_12_A=SPLNENÁ
MAIN_STABILNÝ=true
CODEI_ZHODA=true
ROLLBACK_1.1.9_DOSTUPNÝ_A_ZDROJOVO_ZHODNÝ=true
STOP_DÔVOD=ŽIADNY
RELEASE_VERSION=1.1.15_BEZ_ZMENY
NOVÝ_RELEASE=false
NOVÝ_ZIP=false
PRODUKCIA_BEZ_ZÁSAHU=true
AKTÍVNA_FÁZA=12.B_PRÍPRAVA_IZOLOVANÉHO_RELEASE_PROSTREDIA
NEXT_ALLOWED_ACTION=FÁZA_12_B_PO_VZDIALENOM_READ_BACKU_TOHTO_CHECKPOINTU
KROK_13=ZATVORENÝ
```

## Checkpoint Fázy 12.B — príprava izolovaného release prostredia

Dátum vykonania: 2026-07-27 12:34 Europe/Bratislava

### Výsledok šiestich povinných úkonov

1. Projektový runner `ubuntu-24.04` použitý: ÁNO
   Dôkaz: GitHub Actions run `30258778406`, job `89953499069`,
   operačný systém Ubuntu `24.04.4 LTS`.

2. PHP 8.4 a Composer 2 pripravené rovnakým spôsobom ako v Kroku 11: ÁNO
   Dôkaz: `shivammathur/setup-php@v2`, PHP `8.4.23`, Composer `2.10.2`,
   rozšírenia `curl`, `intl`, `mbstring`, `mysqli`, `pcntl`, `xml`.

3. Povinné nástroje a ich verzie overené: ÁNO
   Dôkaz:

   ```text
   PHP=8.4.23
   COMPOSER=2.10.2
   GIT=2.54.0
   ZIP=3.0
   UNZIP=6.00
   SHA256SUM=GNU_coreutils_9.4
   ```

4. Presný zdrojový commit pracovnej vetvy načítaný: ÁNO
   Dôkaz: očakávaný aj skutočný checkout
   `97800c03180769fbb006856dc8ba91162d44d94e`.

5. Strom `/codei` pred balením zhodný s validovaným stromom: ÁNO
   Dôkaz:
   `4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c`,
   pred aj po príprave runtime.

6. Pracovný strom čistý: ÁNO
   Dôkaz: `WORKTREE_CLEAN=true` a `WORKTREE_CLEAN_AFTER_SETUP=true`.

### Hranice Fázy 12.B

```text
WORKFLOW=.github/workflows/krok-12-release-environment.yml
WORKFLOW_RUN=30258778406
WORKFLOW_JOB=89953499069
RUN_RESULT=success
RELEASE_SCRIPT_SPUSTENÝ=false
RELEASE_VERSION=1.1.15_BEZ_ZMENY
NOVÝ_RELEASE=false
NOVÝ_ZIP=false
/codei=BEZ_ZMENY
PRODUKCIA_BEZ_ZÁSAHU=true
KROK_13=ZATVORENÝ
```

### Záver Fázy 12.B

```text
FÁZA_12_B=SPLNENÁ
STOP_DÔVOD=ŽIADNY
RUNTIME_DOLOŽENÝ_PRIAMO_V_RUN_LOGU=true
CHECKOUT_EXACT=true
CODEI_ZHODA=true
WORKTREE_CLEAN=true
AKTÍVNA_FÁZA=12.C_JEDINÉ_VYTVORENIE_RELEASE
NEXT_ALLOWED_ACTION=FÁZA_12_C_PO_VZDIALENOM_READ_BACKU_TOHTO_CHECKPOINTU
```

## Checkpoint Fáz 12.C–12.D — jediný release a úplný audit ZIP-u

Dátum zápisu: 2026-07-27 13:16 Europe/Bratislava

### Fáza 12.C — jediné vytvorenie release

Používateľ oznámil jediné vykonanie `./release.sh patch`. Keďže vzdialený
`main` po vytvorení release ešte obsahoval `1.1.15`, bol vylúčený
`--auto-push`; používateľ následne samostatne commitol a pushol iba dve
vzniknuté položky.

```text
RELEASE_BASE_COMMIT=2eed8228392665e2b2e8d01bc2e94f2b1ed17e41
RELEASE_COMMIT=fb243698b3811ddf66ad772f89c2e171aa5bc3de
POČET_COMMITOV=1
ZMENENÉ_CESTY=RELEASE_VERSION+releases/metodika-codei-hostinger-1.1.16.zip
RELEASE_VERSION=1.1.16
ZIP_BLOB=24b4976831bc4f37eb80080c4b39e82d9513bf08
SHA256=04742c8b9075acc1fc280326e653ca6b7be57f514a0dc756c141560d5d062668
CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
AUTO_PUSH=false
DRUHÉ_SPUSTENIE=false
POČET_NOVÝCH_ZIPOV=1
JEDEN_RELEASE=true
```

Pôvodný konzolový výstup jediného spustenia nebol zachovaný. Skript sa preto
nespustil druhýkrát; chýbajúci výstup sa nenahradil vymysleným tvrdením:

```text
PÔVODNÝ_VÝSTUP_RELEASE_SH=NEZACHOVANÝ
DÔVOD=NEBOL_ZACHYTENÝ_PRI_JEDINOM_SPUSTENÍ
DODATOČNÉ_OPAKOVANIE_RELEASE_SH=ZAKÁZANÉ_A_NEVYKONANÉ
EVIDOVANÁ_ODCHÝLKA=true
FÁZA_12_C=SPLNENÁ
```

### Fáza 12.D — úplný audit ZIP-u

Rozhodujúci nezávislý GitHub Actions audit:

```text
WORKFLOW=.github/workflows/krok-12-release-audit.yml
WORKFLOW_RUN=30260322371
WORKFLOW_JOB=89958429604
AUDIT_HEAD=75f35c5b6521f30dcc84097d405fef6efd0df3fc
RUN_RESULT=success
ARTIFACT_ID=8650598126
ARTIFACT_NAME=metodika-1.1.16-audit
ARTIFACT_DIGEST_SHA256=147a79e1d7f75f7e145a3ec007e6041cf5f6eaff4f4ac5a6a164bdf68b9d3d37
ZIP_SHA256=04742c8b9075acc1fc280326e653ca6b7be57f514a0dc756c141560d5d062668
```

Prvý workflow run skončil až pri odovzdaní artefaktu, pretože skrytý adresár
`.audit-output` nebol zahrnutý. Samotný audit bol `PASS`; oprava zmenila iba
názov pomocného adresára na `audit-output`, nie release.

Výsledok úplného auditu a opätovného auditu po stiahnutí Actions artefaktu:

```text
OČAKÁVANÉ_SÚBORY=813
SKUTOČNÉ_SÚBORY=813
CHÝBAJÚCE=0
NEOČAKÁVANÉ=0
OBSAHOVÉ_ROZDIELY=0
MANIFEST_ZHODA=true
OBSAHOVÁ_ZHODA=true
MARKERY=1.1.16+1.1.16
ZAKÁZANÉ_ARTEFAKTY=0
TAJOMSTVÁ=0
NEPLATNÉ_CESTY=0
SYMLINKY=0
NEOČAKÁVANÉ_BINÁRNE_ARTEFAKTY=0
OPÄTOVNÝ_AUDIT_PO_PRENOSE=PASS
BALÍK_ZODPOVEDÁ_HEAD=true
FÁZA_12_D=SPLNENÁ
```

### Stav po checkpointe

```text
FÁZA_12_A=SPLNENÁ
FÁZA_12_B=SPLNENÁ
FÁZA_12_C=SPLNENÁ
FÁZA_12_D=SPLNENÁ
STOP_DÔVOD=ŽIADNY
AKTÍVNA_FÁZA=CHECKPOINT_PO_12_D
NEXT_PHASE=12.E_ROLLBACK_A_DÔKAZOVÁ_VÄZBA
NEXT_ALLOWED_ACTION=ČAKAŤ_NA_VÝSLOVNÝ_POKYN_PRED_FÁZOU_12_E
FÁZA_12_E=NEVYKONANÁ
PRODUKCIA_BEZ_ZÁSAHU=true
KROK_13=ZATVORENÝ
```
