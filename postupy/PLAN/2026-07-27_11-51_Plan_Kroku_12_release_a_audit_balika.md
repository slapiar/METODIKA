# Plán Kroku 12 — jeden release a úplný audit balíka

Dátum vytvorenia: 2026-07-27 11:51 Europe/Bratislava

## Stav dokumentu

```text
PRACOVNÝ — ZÁVÄZNÝ PRE KROK 12
KROKY_1_AŽ_11=SPLNENÉ
AKTÍVNY_KROK=KROK_12
GATE_KROKU_12=OPEN
AKTÍVNA_FÁZA=12.A_ZMRAZENIE_AUDITNEJ_ZÁKLADNE
NOVÁ_VERZIA=1.1.16
NOVÝ_RELEASE_ZATIAĽ=false
NOVÝ_ZIP_ZATIAĽ=false
PRODUKCIA_BEZ_ZÁSAHU=true
KROK_13=ZATVORENÝ
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

Po publikovaní a vzdialenom read-backu tohto plánu:

```text
Vykonať iba Fázu 12.A.
Pred vytvorením ZIP-u znovu potvrdiť stabilitu main a stromu /codei.
Nenasadzovať.
Neotvárať Krok 13.
```
