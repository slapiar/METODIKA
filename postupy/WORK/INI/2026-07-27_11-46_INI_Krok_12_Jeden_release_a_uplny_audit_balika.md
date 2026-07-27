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
POVOLENÝ_ĎALŠÍ_ÚKON=PUBLIKÁCIA_A_VZDIALENÝ_READ_BACK_PLÁNU_POTOM_FÁZA_12_A
```

## Povinné východiská analýzy

```text
KROK_11=SPLNENÉ
VALIDOVANÝ_TECHNICKÝ_HEAD=bc85d18fd0edc1a52fad81f8fac54c1ae66a7014
VALIDOVANÝ_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
AKTUÁLNY_MAIN=11a62f688e6d3d641e8aad7e4f0b04bbb4c988e3
AKTUÁLNY_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
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

## Checkpoint po analýze a vytvorení plánu

```text
INICIALIZÁCIA=SPLNENÁ
ANALÝZA=SPLNENÁ
PLÁN_KROKU_12=postupy/PLAN/2026-07-27_11-51_Plan_Kroku_12_release_a_audit_balika.md
WORK_ZÁZNAM=postupy/WORK/2026-07-27_11-53_Inicializacia_analyza_a_plan_Kroku_12.md
GATE=OPEN
AKTÍVNA_FÁZA=12.A_ZMRAZENIE_AUDITNEJ_ZÁKLADNE
RELEASE_VERSION=1.1.15_BEZ_ZMENY
NOVÝ_ZIP=false
PRODUKCIA_BEZ_ZÁSAHU=true
KROK_13=ZATVORENÝ
```

Zistený rozpor historického ZIP-u `1.1.15` nie je blokáciou vytvorenia nového
release z úplne validovaného stromu. Je však dôvodom, prečo sa tento historický
ZIP nesmie označiť za overený rollback balík a nesmie sa potichu prepísať.
