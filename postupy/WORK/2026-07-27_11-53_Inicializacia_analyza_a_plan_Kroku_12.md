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
