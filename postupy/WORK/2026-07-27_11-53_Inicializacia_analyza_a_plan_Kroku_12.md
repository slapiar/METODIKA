# Inicializácia, analýza a plán Kroku 12

Dátum: 2026-07-27 11:53 Europe/Bratislava

## Zadanie

Po potvrdenom uzavretí Kroku 11 začať Krok 12:

1. inicializáciou podľa `postupy/Inicializácia práce.md`,
2. analýzou aktuálneho vzdialeného stavu,
3. vytvorením záväzného vykonávacieho plánu.

## Inicializácia

Vznikol samostatný INI:

`postupy/WORK/INI/2026-07-27_11-46_INI_Krok_12_Jeden_release_a_uplny_audit_balika.md`

Všetkých deväť bodov je doložených:

```text
GATE=OPEN
BLOKUJÚCI_BOD=ŽIADNY
AKTÍVNY_KROK=KROK_12
```

## Načítaný autoritatívny stav

```text
REPOZITÁR=slapiar/METODIKA
VETVA=main
HEAD=11a62f688e6d3d641e8aad7e4f0b04bbb4c988e3
VALIDOVANÝ_TECHNICKÝ_HEAD=bc85d18fd0edc1a52fad81f8fac54c1ae66a7014
AKTUÁLNY_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
VALIDOVANÝ_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
CODEI_ZHODA=true
VALIDAČNÝ_RUN=30252640028
RELEASE_VERSION=1.1.15
PRODUKCIA_BEZ_ZÁSAHU=true
```

Načítané boli najmä:

- celý inicializačný protokol v2.0,
- celý záväzný plán Krokov 11 až 15,
- autoritatívny INI a konečný WORK záznam Kroku 11,
- opravný INI a WORK záznam evidenčného uzavretia Kroku 11,
- `release.sh`, `RELEASE_VERSION` a všetky evidované release ZIP-y,
- aktívny register `postupy/README.md`, checklist a changelog,
- existujúci validačný workflow a projektová definícia runtime.

## Výsledok analýzy

### 1. Kontinuita validovaného zdroja

Od finálne validovaného technického headu sa `/codei` nezmenil. Aktuálny
`main` obsahuje iba neskoršie dokumentačné uzavretie Kroku 11. Release
`1.1.16` preto možno vytvoriť z presne toho technického stromu, ktorý prešiel
finálnou Validáciou.

### 2. Vykonávacie prostredie

Pomocný Work runtime neobsahuje PHP ani Composer, preto sa v ňom release
nevytvorí. Je však doložené projektové GitHub Actions prostredie
`ubuntu-24.04` s PHP `8.4.23` a Composerom `2.10.2`, ktoré validovalo zhodný
technický strom. Plán ho určuje ako izolované vykonávacie prostredie Kroku 12.

### 3. Release mechanizmus

`release.sh`, blob `e365e7ec087ed81c350a7998ab5ac79450aed1c1`,
prešiel `bash -n`. Z 837 sledovaných súborov `/codei` vylúči 26 vývojových
súborov a pridá dva release markery. Pri nezmenenom zdroji má nový ZIP
obsahovať 813 súborov.

### 4. Posledný preukázateľný základ

Audit ZIP-u `1.1.9` potvrdil:

```text
PRODUKČNÝ_COMMIT=3b91c4e7c4fcb95000595554e361ff417fc992e4
MANIFEST_ZHODA=true
OBSAHOVÁ_ZHODA=true
POČET_SÚBOROV=790
SHA256=5aa4d4bd458c9ae4a1a003594de101aba6a2d4e010d24c5bcc94e9b26a0b5e72
```

Je to posledná základňa s doloženou väzbou produkčný commit, verzia a presný
obsah uloženého ZIP-u.

### 5. Rozpor historického ZIP-u 1.1.15

Produkčný footer preukázal verziu `1.1.15`, ale zdrojový commit nasadenia
zostáva nezistený. Audit repozitárového ZIP-u `1.1.15` navyše zistil:

```text
REPOZITÁROVÝ_RELEASE_COMMIT=019a5c8fb741b640192dd89536be1091ef11e437
MANIFEST_ZHODA=true
OBSAHOVÁ_ZHODA=false
ROZDIEL=codei/app/Views/diagnostics/database.php
SHA256=ea688fc678ec2c2fd80b2c624333fa3290b2894ab02363e0495b89cbc0851d5b
```

ZIP neobsahuje formulár tlačidla „Vytvoriť testovací dôkaz“, hoci release
commit ho už obsahuje. Balík `1.1.15` preto zostáva nedotknutým historickým
artefaktom a nepoužije sa ako overený rollback.

## Vytvorený plán

Záväzný plán:

`postupy/PLAN/2026-07-27_11-51_Plan_Kroku_12_release_a_audit_balika.md`

Rozdeľuje Krok 12 na:

1. 12.A — zmrazenie auditnej základne,
2. 12.B — prípravu izolovaného release prostredia,
3. 12.C — jediné vytvorenie release `1.1.16`,
4. 12.D — úplný audit ZIP-u,
5. 12.E — rollback a dôkazovú väzbu,
6. 12.F — uzavretie Kroku 12.

Aktuálny všeobecný plán bol zosúladený tak, aby jeho spodný historický blok už
neoznačoval Fázu 11.A za nasledujúci úkon.

## Vykonané zmeny

Iba metodické a evidenčné:

- nový INI Kroku 12,
- nový podrobný plán Kroku 12,
- zosúladenie aktuálneho všeobecného plánu,
- tento WORK záznam,
- register a changelog.

```text
/codei=BEZ_ZMENY
TESTY=BEZ_ZMENY
WORKFLOW=BEZ_ZMENY
RELEASE_VERSION=1.1.15_BEZ_ZMENY
NOVÝ_ZIP=false
PRODUKCIA=BEZ_ZÁSAHU
```

## Rollback plánovacieho úkonu

Vrátiť iba dokumentačnú dávku tohto plánovania. Technický merge Kroku 11,
existujúce release balíky a produkcia sa nemenia.

## Záver plánovacieho úkonu

```text
VÝSLEDOK=SPLNENÉ
KROK_12=OTVORENÝ
GATE_KROKU_12=OPEN
PLÁN_KROKU_12=VYTVORENÝ
AKTÍVNA_FÁZA=12.A
NEXT_ALLOWED_ACTION=PUBLIKÁCIA_A_VZDIALENÝ_READ_BACK_PLÁNU_POTOM_FÁZA_12_A
KROK_13=ZATVORENÝ
```

---

## Checkpoint Fázy 12.A — zmrazenie auditnej základne

Dátum vykonania: 2026-07-27 12:18 Europe/Bratislava

Fáza bola vykonaná presne podľa šiestich bodov záväzného plánu.

### Stabilita zdroja

```text
MAIN_PRED=771b0c2b69e3f1e1d7b74604b672275823bc9f95
MAIN_PO=771b0c2b69e3f1e1d7b74604b672275823bc9f95
MAIN_STABILNÝ=true
CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
VALIDOVANÝ_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
CODEI_ZHODA=true
RELEASE_VERSION=1.1.15
RELEASE_SH_BLOB=e365e7ec087ed81c350a7998ab5ac79450aed1c1
```

### Porovnávacie základne

Od produkčného commitu release `1.1.9`
`3b91c4e7c4fcb95000595554e361ff417fc992e4` po validovaný technický head
`bc85d18fd0edc1a52fad81f8fac54c1ae66a7014` sa v `/codei` zmenilo 42 ciest:
28 pribudlo a 14 sa zmenilo. Diff má 3988 pridaní a 42 odstránení.

Rollbackový ZIP `1.1.9` bol opätovne rozbalený a každý jeho súbor bol
porovnaný so zdrojovým commitom:

```text
ZIP_BLOB=1e407b914e9be81b500612b69dd20492fbb63fa5
SHA256=5aa4d4bd458c9ae4a1a003594de101aba6a2d4e010d24c5bcc94e9b26a0b5e72
OČAKÁVANÉ_SÚBORY=790
SKUTOČNÉ_SÚBORY=790
CHÝBAJÚCE=0
NEOČAKÁVANÉ=0
OBSAHOVÉ_ROZDIELY=0
```

Historický ZIP `1.1.15` bol jednorazovo uzavretý ako neaktívna základňa:
oproti očakávanému manifestu validovaného stromu má 6 chýbajúcich,
5 neočakávaných a 17 obsahovo rozdielnych súborov. Zostal bez zmeny a nebude
sa ďalej používať na odvodenie release ani rollbacku.

### Hranice

```text
/codei=BEZ_ZMENY
RELEASE_SH=BEZ_ZMENY
RELEASE_VERSION=1.1.15_BEZ_ZMENY
EXISTUJÚCE_ZIPY=BEZ_ZMENY
NOVÝ_RELEASE=false
NOVÝ_ZIP=false
PRODUKCIA=BEZ_ZÁSAHU
```

### Záver Fázy 12.A

```text
FÁZA_12_A=SPLNENÁ
STOP_DÔVOD=ŽIADNY
AKTÍVNA_FÁZA=12.B_PRÍPRAVA_IZOLOVANÉHO_RELEASE_PROSTREDIA
NEXT_ALLOWED_ACTION=FÁZA_12_B_PO_VZDIALENOM_READ_BACKU_CHECKPOINTU_12_A
KROK_13=ZATVORENÝ
```

---

## Checkpoint Fázy 12.B — príprava izolovaného release prostredia

Dátum vykonania: 2026-07-27 12:34 Europe/Bratislava

Fáza bola vykonaná novým jednoúčelovým workflowom:

`.github/workflows/krok-12-release-environment.yml`

Workflow používa iba čítacie oprávnenie `contents: read`, načíta presný head
pracovnej vetvy a pred aj po príprave runtime kontroluje zmrazený technický
strom, blob release skriptu, verziu, neprítomnosť nového ZIP-u a čistotu
pracovného stromu.

### Praktický dôkaz runtime

GitHub Actions run `30258778406`, job `89953499069`, nad commitom
`97800c03180769fbb006856dc8ba91162d44d94e` skončil `success`.

```text
RUNNER=ubuntu-24.04
OS=Ubuntu_24.04.4_LTS
PHP=8.4.23
COMPOSER=2.10.2
GIT=2.54.0
ZIP=3.0
UNZIP=6.00
SHA256SUM=GNU_coreutils_9.4
PHP_EXTENSIONS=curl,intl,mbstring,mysqli,pcntl,xml
```

Setup PHP a Composeru je rovnaký ako vo finálnom validačnom workflowe Kroku
11: `shivammathur/setup-php@v2`, PHP 8.4, Composer 2, rovnaké rozšírenia a
`coverage: none`.

### Kontinuita zdroja a hranice

```text
EXPECTED_HEAD=97800c03180769fbb006856dc8ba91162d44d94e
ACTUAL_HEAD=97800c03180769fbb006856dc8ba91162d44d94e
CHECKOUT_EXACT=true
CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
VALIDOVANÝ_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
CODEI_ZHODA=true
RELEASE_SH_BLOB=e365e7ec087ed81c350a7998ab5ac79450aed1c1
WORKTREE_CLEAN=true
WORKTREE_CLEAN_AFTER_SETUP=true
RELEASE_VERSION=1.1.15_BEZ_ZMENY
RELEASE_SCRIPT_SPUSTENÝ=false
NOVÝ_RELEASE=false
NOVÝ_ZIP=false
PRODUKCIA=BEZ_ZÁSAHU
```

### Rollback Fázy 12.B

Vrátiť jednoúčelový workflow a tento evidenčný checkpoint. Technický strom,
verzia, release ZIP-y a produkcia sa nemenili.

### Záver Fázy 12.B

```text
FÁZA_12_B=SPLNENÁ
STOP_DÔVOD=ŽIADNY
RUNTIME_DOLOŽENÝ_PRIAMO_V_RUN_LOGU=true
AKTÍVNA_FÁZA=12.C_JEDINÉ_VYTVORENIE_RELEASE
NEXT_ALLOWED_ACTION=FÁZA_12_C_PO_VZDIALENOM_READ_BACKU_CHECKPOINTU_12_B
KROK_13=ZATVORENÝ
```

---

## Checkpoint Fázy 12.C — jediné vytvorenie release

Dátum vykonania a publikovania: 2026-07-27 12:51 Europe/Bratislava

Používateľ spustil `./release.sh patch` raz bez `--auto-push`. Release vznikol
najprv iba miestne a na GitHub bol následne publikovaný samostatným commitom:

```text
RELEASE_BASE_COMMIT=2eed8228392665e2b2e8d01bc2e94f2b1ed17e41
RELEASE_COMMIT=fb243698b3811ddf66ad772f89c2e171aa5bc3de
RELEASE_VERSION=1.1.16
ZIP=releases/metodika-codei-hostinger-1.1.16.zip
ZIP_BLOB=24b4976831bc4f37eb80080c4b39e82d9513bf08
SHA256=04742c8b9075acc1fc280326e653ca6b7be57f514a0dc756c141560d5d062668
CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
POČET_COMMITOV_OD_CHECKPOINTU_12_B=1
POČET_ZMENENÝCH_CIEST=2
POČET_NOVÝCH_ZIPOV=1
JEDEN_RELEASE=true
```

Release commit mení výhradne `RELEASE_VERSION` a nový ZIP. `/codei` sa
nezmenil. Pôvodný konzolový výstup jediného spustenia nebol zachovaný; skript
sa kvôli jeho dodatočnému získaniu druhýkrát nespustil.

```text
PÔVODNÝ_VÝSTUP_RELEASE_SH=NEZACHOVANÝ
DÔVOD=NEBOL_ZACHYTENÝ_PRI_JEDINOM_SPUSTENÍ
DODATOČNÉ_OPAKOVANIE_RELEASE_SH=ZAKÁZANÉ_A_NEVYKONANÉ
EVIDOVANÁ_ODCHÝLKA=true
FÁZA_12_C=SPLNENÁ
PRODUKCIA=BEZ_ZÁSAHU
```

---

## Checkpoint Fázy 12.D — úplný audit ZIP-u

Dátum dokončenia auditu: 2026-07-27 13:16 Europe/Bratislava

Balík bol najprv lokálne úplne porovnaný so zdrojovým stromom a potom
nezávisle auditovaný workflowom:

`.github/workflows/krok-12-release-audit.yml`

Prvý beh potvrdil audit `PASS`, ale upload dôkazov nezahrnul skrytý adresár
`.audit-output`. Oprava na `audit-output` zasiahla iba workflow. Úspešný run:

```text
WORKFLOW_RUN=30260322371
WORKFLOW_JOB=89958429604
AUDIT_HEAD=75f35c5b6521f30dcc84097d405fef6efd0df3fc
RUN_RESULT=success
ARTIFACT_ID=8650598126
ARTIFACT_NAME=metodika-1.1.16-audit
ARTIFACT_SIZE_BYTES=1240982
ARTIFACT_DIGEST_SHA256=147a79e1d7f75f7e145a3ec007e6041cf5f6eaff4f4ac5a6a164bdf68b9d3d37
```

Výsledný auditný artefakt bol stiahnutý a vnútorný release ZIP bol opätovne
auditovaný po prenose:

```text
ZIP_BLOB=24b4976831bc4f37eb80080c4b39e82d9513bf08
ZIP_SHA256=04742c8b9075acc1fc280326e653ca6b7be57f514a0dc756c141560d5d062668
OČAKÁVANÉ_SÚBORY=813
SKUTOČNÉ_SÚBORY=813
CHÝBAJÚCE=0
NEOČAKÁVANÉ=0
OBSAHOVÉ_ROZDIELY=0
MANIFEST_ZHODA=true
OBSAHOVÁ_ZHODA=true
RELEASE_MARKERY=1.1.16+1.1.16
CRC_OK=true
JEDINÝ_KOREŇ_CODEI=true
DUPLICITY=0
SYMLINKY=0
ZAKÁZANÉ_ARTEFAKTY=0
TAJOMSTVÁ=0
NEPLATNÉ_CESTY=0
NEOČAKÁVANÉ_BINÁRNE_ARTEFAKTY=0
OPÄTOVNÝ_AUDIT_PO_PRENOSE=PASS
BALÍK_ZODPOVEDÁ_HEAD=true
```

Dva textové indikátory boli preskúmané ako neškodná dynamická premenná
databázového ovládača a jazyková hláška frameworku; neobsahujú tajomstvo ani
platné prihlasovacie údaje.

### Hranice a rollback evidenčného úkonu

```text
/codei=BEZ_ZMENY
RELEASE_ZIP=BEZ_ZMENY
RELEASE_VERSION=1.1.16_BEZ_ZMENY
PRODUKCIA=BEZ_ZÁSAHU
FÁZA_12_E=NEVYKONANÁ
KROK_13=ZATVORENÝ
```

Rollbackom tohto evidenčného úkonu je vrátenie auditného workflowu a
checkpointových zápisov. Release commit sa týmto rollbackom nemení.

### Záver Fáz 12.C–12.D

```text
FÁZA_12_C=SPLNENÁ
FÁZA_12_D=SPLNENÁ
HASH=ZAZNAMENANÝ
BALÍK_ZODPOVEDÁ_HEAD=true
ZAKÁZANÉ_ARTEFAKTY=0
STOP_DÔVOD=ŽIADNY
AKTÍVNA_FÁZA=CHECKPOINT_PO_12_D
NEXT_PHASE=12.E_ROLLBACK_A_DÔKAZOVÁ_VÄZBA
NEXT_ALLOWED_ACTION=ČAKAŤ_NA_VÝSLOVNÝ_POKYN_PRED_FÁZOU_12_E
PRODUKCIA_BEZ_ZÁSAHU=true
KROK_13=ZATVORENÝ
```
