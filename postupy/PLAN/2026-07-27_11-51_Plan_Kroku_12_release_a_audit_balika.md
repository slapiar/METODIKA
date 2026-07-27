# Plán Kroku 12 — jeden release a úplný audit balíka

Dátum vytvorenia: 2026-07-27 11:51 Europe/Bratislava

## Stav dokumentu

```text
PRACOVNÝ — ZÁVÄZNÝ PRE KROK 12
KROKY_1_AŽ_11=SPLNENÉ
AKTÍVNY_KROK=KROK_12
GATE_KROKU_12=OPEN
FÁZA_12_A=SPLNENÁ
FÁZA_12_B=SPLNENÁ
FÁZA_12_C=SPLNENÁ
FÁZA_12_D=SPLNENÁ
FÁZA_12_E=SPLNENÁ
AKTÍVNA_FÁZA=12.F_UZAVRETIE_KROKU_12
NOVÁ_VERZIA=1.1.16
NOVÝ_RELEASE=true
NOVÝ_ZIP=true
PRODUKCIA_BEZ_ZÁSAHU=true
KROK_13=ZATVORENÝ
NEXT_ALLOWED_ACTION=FÁZA_12_F_PO_VZDIALENOM_READ_BACKU_CHECKPOINTU_12_E
```

## Inicializačná brána

- INI:
  `postupy/WORK/INI/2026-07-27_11-46_INI_Krok_12_Jeden_release_a_uplny_audit_balika.md`
- `GATE=OPEN`
- vzdialený `main` pri inicializácii:
  `11a62f688e6d3d641e8aad7e4f0b04bbb4c988e3`
- validovaný technický head:
  `bc85d18fd0edc1a52fad81f8fac54c1ae66a7014`
- validovaný a aktuálny strom `/codei`:
  `4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c`
- validačný run Kroku 11:
  `30252640028`

## Účel

Vytvoriť presne jeden nový release `1.1.16` z technického stromu úplne
validovaného v Kroku 11, dokázať jeho úplnú zhodu so zdrojom a pripraviť ho
pre samostatný Krok 13 bez akéhokoľvek nasadenia alebo zásahu do produkcie.

## Záväzné hranice

### V rozsahu

- zmrazenie presného zdrojového HEAD a stromu `/codei`,
- audit historickej základne a rollback balíka,
- jeden bump `1.1.15 → 1.1.16`,
- jedno spustenie autoritatívneho `release.sh`,
- jeden ZIP `releases/metodika-codei-hostinger-1.1.16.zip`,
- SHA-256, manifest, čisté rozbalenie a porovnanie každého súboru,
- audit tajomstiev, runtime zvyškov, vývojových artefaktov a názvov ciest,
- záznam výsledku, read-after-write, register, changelog a checkpoint.

### Mimo rozsahu

- nasadenie ZIP-u,
- zmena produkčného filesystemu alebo databázy,
- spustenie produkčných migrácií,
- zmena produkčných feature flagov,
- produkčný diagnostický run,
- cleanup produkčných dát,
- otvorenie alebo vykonanie Kroku 13.

---

# 1. Výsledok úvodnej analýzy

## 1.1 Zdroj

```text
AKTUÁLNY_MAIN=11a62f688e6d3d641e8aad7e4f0b04bbb4c988e3
AKTUÁLNY_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
VALIDOVANÝ_HEAD=bc85d18fd0edc1a52fad81f8fac54c1ae66a7014
VALIDOVANÝ_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
CODEI_ZHODA=true
```

Od validovaného technického headu po aktuálny `main` sa zmenilo iba deväť
metodických a evidenčných súborov. `/codei` sa nezmenil.

## 1.2 Autoritatívny release mechanizmus

```text
SCRIPT=release.sh
SCRIPT_BLOB=e365e7ec087ed81c350a7998ab5ac79450aed1c1
BASH_SYNTAX=PASS
ZDROJ_BALÍKA=iba_Gitom_sledovaný_obsah_codei/
AKTUÁLNA_VERZIA=1.1.15
PLÁNOVANÁ_VERZIA=1.1.16
```

Skript:

- vyžaduje čistý pracovný strom okrem `RELEASE_VERSION` a release ZIP-ov,
- kontroluje PHP syntax každého baleného PHP súboru,
- vylučuje testy, `phpunit.dist.xml`, `deploy/apache`, vendor a runtime payloady
  vo writable adresároch,
- zakazuje `.env` a lokálne konfiguračné súbory,
- vytvára koreň `codei/`,
- pridáva oba release markery,
- overuje základnú štruktúru a verziu balíka.

Aktuálny zdroj obsahuje 837 Gitom sledovaných súborov v `/codei`. Skript
vylúči 26 vývojových súborov a pridá dva release markery; očakávaný nový
archív preto pri nezmenenom zdrojovom strome obsahuje presne 813 súborov.

## 1.3 Oddelenie release základní

### Posledný zdrojovo preukázaný produkčný release

```text
VERZIA=1.1.9
PRODUKČNÝ_COMMIT=3b91c4e7c4fcb95000595554e361ff417fc992e4
ZIP=releases/metodika-codei-hostinger-1.1.9.zip
ZIP_BLOB=1e407b914e9be81b500612b69dd20492fbb63fa5
SHA256=5aa4d4bd458c9ae4a1a003594de101aba6a2d4e010d24c5bcc94e9b26a0b5e72
MANIFEST_ZODPOVEDÁ_COMMITU=true
OBSAH_ZODPOVEDÁ_COMMITU=true
POČET_SÚBOROV=790
```

Toto je posledná základňa, pri ktorej je doložená súčasne verzia, produkčný
commit aj presná zhoda uloženého ZIP-u s týmto commitom.

### Aktuálne pozorovaná produkčná verzia

```text
PRODUKČNE_POZOROVANÁ_VERZIA=1.1.15
ZDROJOVÝ_COMMIT_PRODUKČNÉHO_NASADENIA=NEZISTENÝ
REPOZITÁROVÝ_RELEASE_COMMIT=019a5c8fb741b640192dd89536be1091ef11e437
ZIP=releases/metodika-codei-hostinger-1.1.15.zip
SHA256=ea688fc678ec2c2fd80b2c624333fa3290b2894ab02363e0495b89cbc0851d5b
POČET_SÚBOROV=812
MANIFEST_ZODPOVEDÁ_RELEASE_COMMITU=true
OBSAH_ZODPOVEDÁ_RELEASE_COMMITU=false
ROZDIEL=codei/app/Views/diagnostics/database.php
```

ZIP `1.1.15` neobsahuje formulár tlačidla „Vytvoriť testovací dôkaz“, ktorý
už je v jeho repozitárovom release commite. Preto:

```text
ZIP_1.1.15=HISTORICKÝ_ARTEFAKT
OVERENÝ_ROLLBACK_BALÍK=false
ZHODA_S_PRODUKČNÝM_OBSAHOM=NEZISTENÁ
```

Tento rozdiel sa v Kroku 12 neopraví prepísaním historického ZIP-u.

---

# 2. Vykonávací plán

## Fáza 12.A — Zmrazenie auditnej základne

1. Znovu načítať vzdialený `main`.
2. Potvrdiť, že strom `/codei` je stále
   `4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c`.
3. Potvrdiť `RELEASE_VERSION=1.1.15`.
4. Potvrdiť blob `release.sh`.
5. Zaznamenať dva oddelené porovnávacie pohľady:
   - `1.1.9 / 3b91c4e... → validovaný strom Kroku 11`,
   - historický ZIP `1.1.15 → validovaný strom Kroku 11`.
6. Potvrdiť, že existujúci ZIP `1.1.9` zostáva byte-identický a dostupný.

### Kritérium

Všetky identifikátory sú stabilné a žiadny nezistený produkčný commit nebol
nahradený odhadom.

### STOP

- zmení sa `/codei`,
- zmení sa `release.sh`,
- zmení sa `RELEASE_VERSION`,
- zmizne alebo sa zmení rollback balík `1.1.9`.

### Výsledok vykonania — 2026-07-27 12:18 Europe/Bratislava

Vzdialený `main` bol načítaný na začiatku aj na konci kontroly a zostal
stabilný:

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

Prvý porovnávací pohľad:

```text
ZÁKLAD=1.1.9
PRODUKČNÝ_COMMIT=3b91c4e7c4fcb95000595554e361ff417fc992e4
POROVNANIE=3b91c4e..._AŽ_bc85d18...
ZMENENÉ_CESTY_V_CODEI=42
PRIDANÉ_CESTY=28
ZMENENÉ_CESTY=14
SÚHRN=3988_PRIDANÍ_A_42_ODSTRÁNENÍ
```

Rollbackový ZIP `1.1.9` bol znovu prakticky overený:

```text
ZIP_BLOB=1e407b914e9be81b500612b69dd20492fbb63fa5
SHA256=5aa4d4bd458c9ae4a1a003594de101aba6a2d4e010d24c5bcc94e9b26a0b5e72
OČAKÁVANÉ_SÚBORY=790
SKUTOČNÉ_SÚBORY=790
CHÝBAJÚCE_SÚBORY=0
NEOČAKÁVANÉ_SÚBORY=0
OBSAHOVÉ_ROZDIELY=0
ROLLBACK_1.1.9_DOSTUPNÝ_A_ZDROJOVO_ZHODNÝ=true
```

Druhý porovnávací pohľad bol jednorazovo uzavretý iba ako historická
klasifikácia ZIP-u `1.1.15` voči budúcemu manifestu validovaného stromu:

```text
OČAKÁVANÉ_SÚBORY=813
HISTORICKÝ_ZIP_SÚBORY=812
CHÝBAJÚCE_V_HISTORICKOM_ZIP=6
NEOČAKÁVANÉ_V_HISTORICKOM_ZIP=5
OBSAHOVÉ_ROZDIELY=17
ĎALŠIE_POUŽITIE_AKO_AKTÍVNY_ZÁKLAD=false
HISTORICKÝ_ZIP_ZMENENÝ=false
```

Nezistený zdrojový commit produkčnej verzie `1.1.15` nebol nahradený odhadom.
Historický ZIP `1.1.15` sa ďalej nepoužije na odvodenie nového release ani
ako rollback.

```text
FÁZA_12_A=SPLNENÁ
STOP_DÔVOD=ŽIADNY
NOVÝ_RELEASE=false
NOVÝ_ZIP=false
PRODUKCIA_BEZ_ZÁSAHU=true
NEXT_ALLOWED_ACTION=FÁZA_12_B_PO_VZDIALENOM_READ_BACKU_TOHTO_CHECKPOINTU
```

## Fáza 12.B — Príprava izolovaného release prostredia

1. Použiť projektový GitHub Actions runner `ubuntu-24.04`.
2. Pripraviť PHP 8.4 a Composer 2 rovnakým overeným spôsobom ako vo finálnom
   validačnom workflowe Kroku 11.
3. Overiť verzie `php`, `composer`, `git`, `zip`, `unzip` a `sha256sum`.
4. Načítať presný zdrojový commit pracovnej vetvy.
5. Pred balením potvrdiť zhodu stromu `/codei` s validovaným stromom.
6. Overiť čistý pracovný strom.

### Kritérium

Runtime je doložený priamo vo výstupe runu a zdrojový strom sa nezmenil.

### STOP

- PHP alebo povinné nástroje nie sú dostupné,
- checkout nie je presne identifikovaný,
- pracovný strom obsahuje neočakávanú zmenu,
- `/codei` sa nezhoduje s validovaným stromom.

### Výsledok vykonania — 2026-07-27 12:34 Europe/Bratislava

Fáza bola vykonaná jednoúčelovým workflowom
`.github/workflows/krok-12-release-environment.yml` na pracovnom commite
`97800c03180769fbb006856dc8ba91162d44d94e`.

GitHub Actions run `30258778406`, job `89953499069`, skončil `success`.
Výstup runu priamo doložil:

```text
RUNNER_IMAGE=ubuntu-24.04
OPERATING_SYSTEM=Ubuntu_24.04.4_LTS
CHECKED_OUT_HEAD=97800c03180769fbb006856dc8ba91162d44d94e
CHECKOUT_EXACT=true
CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
CODEI_TREE_MATCH=true
RELEASE_SCRIPT_BLOB=e365e7ec087ed81c350a7998ab5ac79450aed1c1
PHP=8.4.23
COMPOSER=2.10.2
GIT=2.54.0
ZIP=3.0
UNZIP=6.00
SHA256SUM=GNU_coreutils_9.4
PHP_EXTENSIONS=curl,intl,mbstring,mysqli,pcntl,xml
WORKTREE_CLEAN=true
WORKTREE_CLEAN_AFTER_SETUP=true
RELEASE_VERSION=1.1.15
NEW_RELEASE_EXISTS=false
PHASE_12_B_READY=true
```

Workflow použil rovnaký `shivammathur/setup-php@v2` kontrakt ako finálna
validácia Kroku 11: PHP 8.4, Composer 2, rovnaké povinné rozšírenia a vypnuté
coverage. `release.sh` nebol spustený.

```text
FÁZA_12_B=SPLNENÁ
STOP_DÔVOD=ŽIADNY
NOVÝ_RELEASE=false
NOVÝ_ZIP=false
RELEASE_VERSION=1.1.15_BEZ_ZMENY
CODEI_ZHODA=true
PRODUKCIA_BEZ_ZÁSAHU=true
NEXT_ALLOWED_ACTION=FÁZA_12_C_PO_VZDIALENOM_READ_BACKU_TOHTO_CHECKPOINTU
```

## Fáza 12.C — Jediné vytvorenie release

1. Spustiť autoritatívny príkaz:

   ```bash
   ./release.sh patch
   ```

2. Nepoužiť `--auto-push`.
3. Nevytvoriť druhý ZIP ani opravný ZIP pod rovnakou alebo ďalšou verziou.
4. Zachovať úplný výstup skriptu.
5. Vypočítať SHA-256 nového balíka.

### Očakávaný výsledok

```text
RELEASE_VERSION=1.1.16
ZIP=releases/metodika-codei-hostinger-1.1.16.zip
JEDEN_RELEASE=true
POČET_NOVÝCH_ZIPOV=1
```

### STOP

Ak samotný skript skončí chybou, release sa nepovažuje za vytvorený. Chyba sa
neobíde ručným ZIP-ovaním ani druhým bumpom verzie.

### Výsledok vykonania — 2026-07-27 12:51 Europe/Bratislava

Autoritatívny príkaz `./release.sh patch` bol podľa priameho hlásenia
používateľa spustený raz. Po jeho vykonaní zostal vzdialený `main` na
checkpointe Fázy 12.B a nový release existoval iba v miestnej pracovnej kópii.
Používateľ ho následne samostatným commitom a pushom zapísal na `main`:

```text
RELEASE_BASE_COMMIT=2eed8228392665e2b2e8d01bc2e94f2b1ed17e41
RELEASE_COMMIT=fb243698b3811ddf66ad772f89c2e171aa5bc3de
RELEASE_COMMIT_MESSAGE=1.1.16
POČET_COMMITOV_OD_ZÁKLADNE=1
ZMENENÉ_CESTY=2
RELEASE_VERSION=1.1.16
ZIP=releases/metodika-codei-hostinger-1.1.16.zip
ZIP_BLOB=24b4976831bc4f37eb80080c4b39e82d9513bf08
SHA256=04742c8b9075acc1fc280326e653ca6b7be57f514a0dc756c141560d5d062668
CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
CODEI_ZHODA=true
AUTO_PUSH=false
DRUHÉ_SPUSTENIE=false
POČET_NOVÝCH_ZIPOV=1
JEDEN_RELEASE=true
```

Diff release commitu obsahuje presne:

- `RELEASE_VERSION`,
- `releases/metodika-codei-hostinger-1.1.16.zip`.

Úplný konzolový výstup jediného spustenia `release.sh` nebol zachovaný.
Táto odchýlka je zapísaná pravdivo; skript sa kvôli jej dodatočnému
doplneniu druhýkrát nespustil:

```text
PÔVODNÝ_VÝSTUP_RELEASE_SH=NEZACHOVANÝ
DÔVOD=NEBOL_ZACHYTENÝ_PRI_JEDINOM_SPUSTENÍ
DODATOČNÉ_OPAKOVANIE_RELEASE_SH=ZAKÁZANÉ_A_NEVYKONANÉ
NÁHRADNÉ_DÔKAZY=PRIAME_HLÁSENIE_POUŽÍVATEĽA+JEDINÝ_RELEASE_COMMIT+PRESNÝ_DIFF+ZIP_BLOB+SHA256+ÚPLNÝ_AUDIT
```

```text
FÁZA_12_C=SPLNENÁ
EVIDOVANÁ_ODCHÝLKA=NEZACHOVANÝ_KONZOLOVÝ_VÝSTUP
STOP_DÔVOD=ŽIADNY
PRODUKCIA_BEZ_ZÁSAHU=true
```

## Fáza 12.D — Úplný audit ZIP-u

Audit sa vykoná nad čistým rozbalením a musí obsahovať všetky tieto kontroly:

1. SHA-256 ZIP-u.
2. Jediný koreň `codei/`; žiadny súbor mimo neho.
3. Presne 813 súborov, ak sa nezmenil vstupný strom.
4. Presná zhoda manifestu s:
   - Gitom sledovanými súbormi `/codei`,
   - pravidlami vylúčenia `release.sh`,
   - dvoma generovanými release markermi.
5. `codei/RELEASE_VERSION=1.1.16`.
6. `codei/deploy/RELEASE_VERSION.txt=1.1.16`.
7. Pre každý negenerovaný súbor zhodný Git blob a hash rozbaleného súboru.
8. Nulový počet:
   - `.env`, `.env.*`, `local-config.php`, `.local-config.php`,
   - private kľúčov, certifikátov, dumpov a záložných súborov,
   - logov, session, cache, uploadov, temp a diagnostických run-store dát,
   - `vendor/`, `tests/`, `phpunit.dist.xml`, `deploy/apache`,
   - súborov s koncovou medzerou alebo riadiacim znakom v ceste,
   - symlinkov a neočakávaných binárnych artefaktov.
9. Obsahový audit indikátorov tajomstiev:
   - hlavičky private kľúčov,
   - neanonymizované prihlasovacie údaje,
   - natvrdo uložené tokeny alebo heslá mimo výslovne posúdených neprodukčných
     príkladov.
10. Opätovný audit po prenose výsledného artefaktu z Actions.

### Kritérium

```text
HASH=ZAZNAMENANÝ
MANIFEST_ZHODA=true
OBSAHOVÁ_ZHODA=true
BALÍK_ZODPOVEDÁ_HEAD=true
ZAKÁZANÉ_ARTEFAKTY=0
TAJOMSTVÁ=0
NEPLATNÉ_CESTY=0
```

### STOP

Jediný rozdiel, neznámy súbor, zakázaný artefakt, nezhodný marker alebo
nepreskúmaný nález tajomstva zatvára Krok 12. Balík sa nezapíše ako platný
release a nesmie prejsť do Kroku 13.

### Výsledok vykonania — 2026-07-27 13:16 Europe/Bratislava

ZIP bol najprv úplne auditovaný proti Git blobom validovaného zdrojového
stromu. Nezávislý audit sa potom vykonal v GitHub Actions workflowe:

`.github/workflows/krok-12-release-audit.yml`

Prvý beh workflowu potvrdil výsledok auditu `PASS`, ale zlyhalo odovzdanie
dôkazov, pretože skrytý adresár `.audit-output` nebol zahrnutý do
`upload-artifact`. Oprava zmenila iba názov pomocného adresára na
`audit-output`; release ZIP, `/codei` ani verzia sa nemenili.

Úspešný rozhodujúci dôkaz:

```text
WORKFLOW_RUN=30260322371
WORKFLOW_JOB=89958429604
AUDIT_HEAD=75f35c5b6521f30dcc84097d405fef6efd0df3fc
RUN_RESULT=success
ARTIFACT_ID=8650598126
ARTIFACT_NAME=metodika-1.1.16-audit
ARTIFACT_SIZE_BYTES=1240982
ARTIFACT_DIGEST_SHA256=147a79e1d7f75f7e145a3ec007e6041cf5f6eaff4f4ac5a6a164bdf68b9d3d37
ZIP_BLOB=24b4976831bc4f37eb80080c4b39e82d9513bf08
ZIP_SHA256=04742c8b9075acc1fc280326e653ca6b7be57f514a0dc756c141560d5d062668
CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
```

Úplný audit aj opätovný audit ZIP-u stiahnutého z Actions artefaktu potvrdili:

```text
OČAKÁVANÉ_SÚBORY=813
SKUTOČNÉ_SÚBORY=813
CHÝBAJÚCE_SÚBORY=0
NEOČAKÁVANÉ_SÚBORY=0
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

Dva automatické textové indikátory boli jednotlivo posúdené ako neškodná
dynamická premenná databázového ovládača a jazyková hláška frameworku, nie
ako uložené prihlasovacie údaje alebo tajomstvá.

```text
FÁZA_12_D=SPLNENÁ
HASH=ZAZNAMENANÝ
STOP_DÔVOD=ŽIADNY
PRODUKCIA_BEZ_ZÁSAHU=true
KROK_13=ZATVORENÝ
NEXT_ALLOWED_ACTION=ČAKAŤ_NA_POKYN_PRED_FÁZOU_12_E
```

## Fáza 12.E — Rollback a dôkazová väzba

1. Potvrdiť dostupnosť a nezmenenosť presne auditovaného balíka `1.1.9`.
2. Historický ZIP `1.1.15` viesť výhradne ako nezhodný historický artefakt,
   nie ako overený rollback.
3. Zaznamenať väzbu:

   ```text
   ZDROJOVÝ_HEAD
   → CODEI_TREE
   → RELEASE_VERSION
   → ZIP_BLOB
   → SHA256
   → MANIFEST
   → AUDITNÝ_VÝSLEDOK
   ```

4. Pred nasadením zostáva rollbackom release dávky návrat
   `RELEASE_VERSION=1.1.15` a odstránenie iba nového ZIP-u `1.1.16`.
5. Produkčný rollback pre Krok 13 sa nesmie odvodiť iba z čísla verzie;
   Krok 13 musí znovu posúdiť nasadený stav, schému a bezpečnosť návratu.

### Kritérium

```text
ROLLBACK_BALÍK_1.1.9=DOSTUPNÝ_A_ZDROJOVO_ZHODNÝ
HISTORICKÝ_BALÍK_1.1.15=NEPOUŽITÝ_A_NEZMENENÝ
ROLLBACK_RELEASE_DÁVKY=DOLOŽENÝ
```

### Výsledok vykonania — 2026-07-27 13:34 Europe/Bratislava

Vzdialený `main` bol načítaný na commite
`13c66eae12011dc302b14ddb67f0bda9203545da`. Od release commitu
`fb243698b3811ddf66ad772f89c2e171aa5bc3de` sa v `/codei`,
`RELEASE_VERSION` ani v `releases/` nič nezmenilo.

Rollbackový balík `1.1.9` zostáva dostupný a byte-identický s balíkom úplne
auditovaným vo Fáze 12.A:

```text
ZIP=releases/metodika-codei-hostinger-1.1.9.zip
ZIP_BLOB=1e407b914e9be81b500612b69dd20492fbb63fa5
SHA256=5aa4d4bd458c9ae4a1a003594de101aba6a2d4e010d24c5bcc94e9b26a0b5e72
OČAKÁVANÉ_SÚBORY=790
SKUTOČNÉ_SÚBORY=790
CHÝBAJÚCE=0
NEOČAKÁVANÉ=0
OBSAHOVÉ_ROZDIELY=0
ROLLBACK_BALÍK_1.1.9=DOSTUPNÝ_A_ZDROJOVO_ZHODNÝ
```

Historický balík `1.1.15` zostal nezmenený a nebol použitý ako rollback:

```text
ZIP=releases/metodika-codei-hostinger-1.1.15.zip
ZIP_BLOB=aeaf80b31f299cbf824834590bd58435e216e99d
SHA256=ea688fc678ec2c2fd80b2c624333fa3290b2894ab02363e0495b89cbc0851d5b
KLASIFIKÁCIA=NEZHODNÝ_HISTORICKÝ_ARTEFAKT
OVERENÝ_ROLLBACK_BALÍK=false
HISTORICKÝ_BALÍK_1.1.15=NEPOUŽITÝ_A_NEZMENENÝ
```

Úplná dôkazová väzba release `1.1.16`:

```text
ZDROJOVÝ_HEAD=bc85d18fd0edc1a52fad81f8fac54c1ae66a7014
ZDROJOVÝ_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
RELEASE_COMMIT=fb243698b3811ddf66ad772f89c2e171aa5bc3de
RELEASE_COMMIT_CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
RELEASE_VERSION=1.1.16
ZIP_BLOB=24b4976831bc4f37eb80080c4b39e82d9513bf08
SHA256=04742c8b9075acc1fc280326e653ca6b7be57f514a0dc756c141560d5d062668
MANIFEST=813/813
CHÝBAJÚCE=0
NEOČAKÁVANÉ=0
OBSAHOVÉ_ROZDIELY=0
AUDITNÝ_VÝSLEDOK=PASS
AUDIT_RUN=30260322371
OPÄTOVNÝ_AUDIT_PO_PRENOSE=PASS
```

Pred nasadením je rollback release dávky doložený presne podľa plánu:

```text
ROLLBACK_RELEASE_VERSION=1.1.16_→_1.1.15
ODSTRÁNIŤ=iba_releases/metodika-codei-hostinger-1.1.16.zip
PONECHAŤ=/codei+ostatné_release_ZIPy
ROLLBACK_RELEASE_DÁVKY=DOLOŽENÝ
```

Tento rollback nebol vykonaný, pretože release dávka je platná a ešte nebola
nasadená. Produkčný rollback zostáva predmetom Kroku 13 a nesmie sa odvodiť
iba z čísla verzie; musí znovu posúdiť skutočne nasadený stav, schému a
bezpečnosť návratu.

```text
FÁZA_12_E=SPLNENÁ
STOP_DÔVOD=ŽIADNY
PRODUKCIA_BEZ_ZÁSAHU=true
KROK_13=ZATVORENÝ
NEXT_ALLOWED_ACTION=FÁZA_12_F_PO_VZDIALENOM_READ_BACKU_CHECKPOINTU_12_E
```

## Fáza 12.F — Uzavretie Kroku 12

1. Spätne načítať výsledný commit a všetky release artefakty.
2. Overiť SHA-256 po vzdialenom zápise.
3. Aktualizovať INI Kroku 12, WORK záznam, registre a changelog.
4. Vytvoriť checkpoint s presným commitom, verziou, ZIP blobom a SHA-256.
5. Potvrdiť, že produkcia zostala bez zásahu.

### Spoločné kritérium

```text
KROK_12=SPLNENÉ
JEDEN_RELEASE=true
HASH=ZAZNAMENANÝ
BALÍK_ZODPOVEDÁ_HEAD=true
ZAKÁZANÉ_ARTEFAKTY=0
ROLLBACK_BALÍK=DOSTUPNÝ
PRODUKCIA_BEZ_ZÁSAHU=true
NEXT_ALLOWED_STEP=KROK_13_S_VLASTNÝM_INI_A_GATEM
```

Ak niektorá hodnota nie je pravdivá, Krok 12 zostáva otvorený alebo sa uzavrie
riadeným STOP. Krok 13 sa neotvorí.

---

# 3. Povolený nasledujúci úkon

Po publikovaní a vzdialenom read-backu checkpointu Fázy 12.E:

```text
FÁZA_12_C=SPLNENÁ
FÁZA_12_D=SPLNENÁ
FÁZA_12_E=SPLNENÁ
NEXT_PHASE=12.F_UZAVRETIE_KROKU_12
NEXT_ALLOWED_ACTION=FÁZA_12_F_PO_VZDIALENOM_READ_BACKU_CHECKPOINTU_12_E
PRODUKČNÉ_NASADENIE=ZAKÁZANÉ
KROK_13=ZATVORENÝ
```
