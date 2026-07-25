# INICIALIZÁCIA — Dokončenie Kroku 10 po STOP

## Stav brány

```text
GATE=OPEN
```

## Účel kroku

Obnoviť úplný aktuálny vzdialený stav po STOP udalostiach, rozhodnúť o stave predčasných artefaktov, prakticky overiť všetkých deväť bodov inicializačnej brány a následne vykonať iba najmenšiu funkčnú opravu koreňovej príčiny Kroku 10.

## Overenie brány predchádzajúceho kroku

- Predchádzajúci krok: Krok 9 — Audit a test bariéry, `load()` a timeoutu.
- Stav: `SPLNENÉ`.
- Dôkaz: `postupy/WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md`.

## Náprava chybného tvrdenia o prostredí

Predchádzajúce tvrdenie o chýbajúcom Composer-i a DNS sa vzťahovalo iba na interný pomocný sandbox asistenta, nie na projektový Codespace ani na autoritatívne izolované testovacie prostredie projektu. Pre rozhodovanie o Kroku 10 bolo preto neplatné a týmto sa výslovne ruší.

Projektové prostredie bolo následne overené rovnakou praktickou cestou ako pri Kroku 7: opätovným spustením existujúceho izolovaného GitHub Actions jobu bez zmeny workflow, konfigurácie alebo vykonateľného kódu.

## Aktuálne praktické overenie prostredia

```text
pôvodný workflow run = 30089261354
aktuálne opakovaný job = 89652985988
čas opakovaného behu = 2026-07-25
status = completed
conclusion = success
```

Úspešne prešli:

1. inicializácia izolovaného MariaDB service containera,
2. checkout repozitára,
3. PHP 8.4, rozšírenia a Composer 2,
4. MariaDB klient,
5. inštalácia uzamknutých závislostí,
6. konfigurácia izolovanej databázy `metodika_krok7`,
7. overenie MariaDB a InnoDB,
8. migrácie M1–M8,
9. overenie otázkovej schémy,
10. reprodukcia potvrdenej koreňovej príčiny nezmenenou aplikačnou cestou,
11. zastavenie a odstránenie service containera.

Dôkaz kontinuity od pôvodného úspešného commitu `a918af1295ca1fa2dc4bb5fe231002c4cbb77624` po HEAD `2af3b925d06202701a8f8fab6020e75f849b426b` potvrdil, že sa nezmenili `composer.lock`, migrácie M1–M8, `FirstAcceptanceService`, repository adapter ani reprodukčný príkaz. Zmeny medzi nimi sa týkali diagnostiky, metodických záznamov, plánov a nového overovacieho workflow Kroku 10.

## Inicializačná tabuľka

| Bod | Stav | Dôkaz / výsledok |
|---|---|---|
| 1. Metodika načítaná | ÁNO | celý aktuálny obsah `postupy/Inicializácia práce.md`, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79`; načítané body 0–14, STOP aj základné pravidlo |
| 2. Projekt a autoritatívny zdroj | ÁNO | projekt METODIKA; autoritatívny repozitár `slapiar/METODIKA`; vetva `main`; technický koreň `/codei`; záväzný plán určuje ako jediný nasledujúci krok Krok 10 |
| 3. Vetva a HEAD | ÁNO | autoritatívna vetva `main`; HEAD pred týmto zápisom `2af3b925d06202701a8f8fab6020e75f849b426b`; načítaná relevantná história od Kroku 9 a porovnanie s úspešným reprodukčným commitom `a918af1295ca1fa2dc4bb5fe231002c4cbb77624` |
| 4. Potrebné prístupy | ÁNO | GitHub čítanie a administrátorský zápis prakticky potvrdené; oprávnenie na opätovné spustenie Actions jobu prakticky potvrdené prijatím a vykonaním jobu `89652985988` |
| 5. Prostredie | ÁNO | aktuálny opakovaný job `89652985988` skončil `success`; potvrdil Ubuntu 24.04, PHP 8.4, Composer 2, MySQLi a izolovanú MariaDB 11.4 |
| 6. Závislosti | ÁNO | job úspešne nainštaloval uzamknuté závislosti, vykonal migrácie M1–M8, overil schému, izolovaný databázový cieľ, reprodukčnú transakčnú cestu, rollback/cleanup a odstránenie service containera; kontinuita dotknutých zdrojov potvrdená porovnaním commitov |
| 7. Predmet a hranice | ÁNO | predmetom je iba najmenšia funkčná oprava rezervácie v Kroku 10; bez zmeny databázovej schémy, diagnostiky, UI, `DiagnosticsConcurrencyRunStore::load()`, timeoutu, release procesu alebo produkcie |
| 8. Kritérium úspechu | ÁNO | prvý tok=`CREATED`; paralelný druhý tok=`ALREADY_EXISTS`; tok bez presnej rezervácie `REQUEST_REFERENCE + derivation_reference` nesmie pokračovať do `CREATE_INITIAL_HISTORY_RUN`; nevznikne duplicitný počiatočný history run; existujúce aj reprodukčné testy prejdú |
| 9. Rollback | ÁNO | vrátenie jediného funkčného commitu Kroku 10; testovacie dáta a dočasné artefakty sa odstránia iba v izolovanom prostredí |

## Rozhodnutie o predčasných artefaktoch

### Workflow komentár v commite `41b612af...`

- Stav: `predčasný`, ale funkčne inertný.
- Rozhodnutie: nemení sa ani nevracia v Kroku 10.
- Vplyv na Krok 10: nemení vykonávaciu logiku workflow; zostáva historickým dôkazom STOP udalosti.

### Vetva `tmp-do-not-use`

- Stav: `predčasný`, neautoritatívny artefakt.
- Dôkaz: ukazuje na `44401bc7b41ab371b9220162be300874c1286789`, je bez vlastných commitov a bez rozdielov v súboroch voči merge-base.
- Rozhodnutie: neblokuje autoritatívnu vetvu `main`, nevstupuje do Kroku 10 a zostáva označená na neskorší administratívny cleanup nástrojom s operáciou delete ref.
- Vplyv na Krok 10: žiadny funkčný vplyv; nie je autoritatívnym zdrojom ani pracovnou vetvou.

## Vyhodnotenie projektovej brány

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

```text
POVOLENÝ_ĎALŠÍ_ÚKON=analýza aktuálnych dotknutých zdrojov a návrh najmenšej funkčnej opravy v presných hraniciach Kroku 10
```

## Výsledok vykonania Kroku 10

```text
KROK_10=SPLNENÉ
FUNKČNÝ_COMMIT=c90ae562a859de9fe0b2174f39e924c7f7bc6a4e
VALIDOVANÝ_PR_SHA=ca765c737bc5dffb90361fb89b22f5b00e6b97f8
WORKFLOW_RUN=30148480939
WORKFLOW_JOB=89654680309
VALIDÁCIA=SUCCESS
PRVÝ_TOK=RESERVATION_CREATED
DRUHÝ_TOK=ALREADY_EXISTS
DUPLICITNÝ_HISTORY_RUN=false
CLEANUP=true
```

Vykonané a overené:

- analýza potvrdila nekontrolovaný výsledok `insert()` pri `DBDebug=false` a nepresný postcheck iba podľa `REQUEST_REFERENCE`,
- návrh obmedzil zásah na repository adapter, regresný príkaz a jeho validačný workflow,
- implementácia kontroluje `insert()`, rozlišuje kód `1062` a povoľuje `CREATED` iba po presnej zhode `REQUEST_REFERENCE + payload_fingerprint + derivation_reference`,
- výsledné súbory boli spätne načítané priamo z `main`,
- regresná Validácia prešla nad PHP 8.4, Composerom 2, MariaDB 11.4, migráciami M1–M8 a dvoma nezávislými MySQLi spojeniami,
- pracovný výsledok je zaznamenaný v `postupy/WORK/2026-07-25_09-01_Krok_10_Najmensia_funkcna_oprava_rezervacie.md`,
- checkpoint je v `postupy/2026-07-25_09-05_CHECKPOINT-KROK-10-PRESNA-REZERVACIA.md`,
- záväzný plán, register `postupy/README.md` a `CHANGELOG.md` boli zosúladené,
- Krok 11 nebol otvorený.

## Ukončenie pracovného kroku

```text
čo sa vykonalo = najmenšia funkčná oprava presnej rezervácie prvého prijatia
čo sa zmenilo = repository adapter + regresný príkaz + trvalý validačný workflow
čo zostáva otvorené = úplná lokálna a integračná Validácia Kroku 11
pretrvávajúce riziko = úplný end-to-end diagnostický tok ešte nebol validovaný v rozsahu Kroku 11
rollback = revert commitu c90ae562a859de9fe0b2174f39e924c7f7bc6a4e
inicializačný záznam = tento súbor
nasledujúci logický krok = Krok 11, iba po vlastnej novej inicializačnej bráne
```

## Dôkaz prečítania a vykonania metodických pokynov

```text
#ID=0  R=1 W=1
#ID=1  R=1 W=1
#ID=2  R=1 W=1
#ID=3  R=1 W=1
#ID=4  R=1 W=1
#ID=5  R=1 W=1
#ID=6  R=1 W=1
#ID=7  R=1 W=1
#ID=8  R=1 W=1
#ID=9  R=1 W=1
#ID=10 R=1 W=1
#ID=11 R=1 W=1
#ID=12 R=1 W=1
#ID=13 R=1 W=1
#ID=14 R=1 W=1
```

```text
JEDINÝ_NASLEDUJÚCI_POVOLENÝ_KROK=KROK_11
KROK_11_GATE=CLOSED
```
