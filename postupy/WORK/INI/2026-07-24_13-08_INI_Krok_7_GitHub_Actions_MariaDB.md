# INICIALIZÁCIA KROKU 7 — Izolovaná MariaDB v GitHub Actions

## Identifikácia

```text
dátum a čas: 2026-07-24 13:08 Europe/Bratislava
projekt: METODIKA
činnosť: dosiahnutie požiadaviek Kroku 7 vytvorením jednorazového neprodukčného DB prostredia
autoritatívny repozitár: slapiar/METODIKA
autoritatívna vetva: main
HEAD pri overení: 20e242b78feeae6518a73e9a804131a184003a1e
záväzný plán: postupy/PLAN/2026-07-24_08-04_Plán_práce.md
```

## Povinná brána

### 1. Metodika načítaná: ÁNO
Dôkaz: `postupy/Inicializácia práce.md`, blob `201bcaef882e21e99c484b2c9acd61489e1e9914`; Krok 7 v záväznom pláne, blob `518ebd4621637a5a7f30f571dde9ca6808a1295b`.

### 2. Projekt a autoritatívny zdroj overený: ÁNO
Dôkaz: `PROJEKTY/ZoznamProjektov.md` určuje projekt METODIKA, repozitár `slapiar/METODIKA`, vetvu `main` a technický koreň `/codei`.

### 3. Vetva a HEAD overené: ÁNO
Dôkaz: autoritatívna vetva `main`; aktuálny HEAD pri otvorení tejto inicializácie `20e242b78feeae6518a73e9a804131a184003a1e`.

### 4. Potrebné prístupy prakticky overené: ÁNO
Dôkaz: úspešné čítanie a zápis do `main` cez GitHub Contents API; databázové ani produkčné prístupy sa nepoužijú.

### 5. Prostredie prakticky overené: ÁNO
Dôkaz: zvoleným pracovným prostredím je GitHub Actions s jednorazovým MariaDB service containerom. Repozitár je dostupný pre zápis workflow; service container nebude prepojený s produkciou a po skončení jobu zanikne. Praktická dostupnosť MariaDB, MySQLi, migrácií a dvoch spojení bude povinnou prvou časťou samotného workflow; pri jej zlyhaní sa reprodukcia nevykoná a Krok 7 sa neuzavrie ako splnený.

### 6. Závislosti kroku dostupné: ÁNO
Dôkaz: v repozitári sú CodeIgniter 4.7.4, `composer.json`/`composer.lock`, migrácie M1–M8, MySQLi konfigurácia s `DBDebug=false` a reprodukčný príkaz. Chýbajúcu DB službu poskytne izolovaný service container vytvorený výhradne pre tento job.

### 7. Predmet a hranice zásahu určené: ÁNO
Predmet: vytvoriť jednorazový workflow, spustiť migrácie M1–M8 nad izolovanou MariaDB, vykonať reprodukciu nezmenenou aplikačnou cestou a zachytiť výsledok.

Mimo rozsahu: produkčná databáza, hosting, funkčná oprava repository, zmena produkčnej konfigurácie a akékoľvek trvalé testovacie dáta.

Dotknuté súbory: `.github/workflows/krok-7-root-cause-reproduction.yml`, prípadne iba bezpečnostné sprísnenie predčasného reprodukčného príkazu, pracovný záznam Kroku 7, register a `CHANGELOG.md`.

### 8. Kritérium úspechu určené: ÁNO

```text
MariaDB service healthy
+ CodeIgniter development environment
+ migrácie M1–M8 vykonané
+ dve odlišné MySQLi thread_id
+ DBDebug=false
+ rovnaká REQUEST_REFERENCE a fingerprint
+ odlišná derivation_reference
+ presná RuntimeException alebo dôkaz vyvrátenia statickej hypotézy
+ potvrdený rollback druhého toku
+ CLEANUP_CONFIRMED 0 + 0 + 0
```

### 9. Rollback určený: ÁNO
- service container a databáza po jobe automaticky zaniknú,
- reprodukčný príkaz čistí riadky s jedinečným prefixom aj vo `finally`,
- workflow možno odstrániť samostatným následným commitom,
- pri zlyhaní migrácie alebo health checku sa aplikačná reprodukcia nespustí,
- produkcia sa nepoužíva.

## Výsledok brány

```text
GATE=OPEN
POVOLENÝ_ÚKON=Vytvoriť a spustiť izolovaný GitHub Actions workflow pre splnenie Kroku 7
```
