# INICIALIZÁCIA — Dokončenie Kroku 10 po STOP

## Stav brány

```text
GATE=CLOSED
```

## Účel kroku

Obnoviť úplný aktuálny vzdialený stav po STOP udalostiach, rozhodnúť o stave predčasných artefaktov a plynulo pokračovať iba v povinnom overení bodov 1 až 9 Kroku 10. Tento záznam je prvým a jediným pracovným artefaktom tohto pokusu pred otvorením brány.

## Overenie brány predchádzajúceho kroku

- Predchádzajúci krok: Krok 9 — Audit a test bariéry, `load()` a timeoutu.
- Stav: `SPLNENÉ`.
- Dôkaz: `postupy/WORK/2026-07-24_15-50_Krok_9_Audit_bariery_load_timeoutu.md`.

## Inicializačná tabuľka

| Bod | Stav | Dôkaz / výsledok |
|---|---|---|
| 1. Metodika načítaná | ÁNO | celý aktuálny obsah `postupy/Inicializácia práce.md`, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79`; načítané body 0–14, STOP aj základné pravidlo |
| 2. Projekt a autoritatívny zdroj | ÁNO | projekt METODIKA; autoritatívny repozitár `slapiar/METODIKA`; vetva `main`; technický koreň `/codei`; záväzný plán určuje ako jediný nasledujúci krok Krok 10 |
| 3. Vetva a HEAD | ÁNO | aktuálny vzdialený HEAD `main` po vytvorení tohto INI: `d4515ddd8033444a64d15ac1bf17f869703b0bab`; načítaná relevantná história od Kroku 9 vrátane commitov `227b5c62`, `41b612af`, `44401bc7`, `29985b8d`, `de8b0263`, `e696ac44` |
| 4. Potrebné prístupy | NEOVERENÉ | GitHub čítanie a zápis na `main` prakticky potvrdené; neoverený zostáva prístup k vykonateľnému runtime, Composeru, test runneru a izolovanej MariaDB |
| 5. Prostredie | NEOVERENÉ | workflow definuje PHP 8.4, Composer v2, MySQLi a MariaDB 11.4, ale pre commit `41b612afac7e8504e6393f42325cc5a1684a4f68` neexistuje dostupný workflow run ani status; lokálny izolovaný klon zlyhal na nedostupnom DNS pre `github.com` |
| 6. Závislosti | NEOVERENÉ | workflow obsahuje migrácie M1–M8, kontrolu prázdneho stavu, dve nezávislé DB spojenia, rollback a cleanup, ale nič z toho nebolo prakticky vykonané a doložené výstupom |
| 7. Predmet a hranice | ÁNO | aktuálne iba dokončiť praktické overenie prostredia Kroku 10; bez návrhu opravy, zmeny vykonateľného kódu, predmetového testu, databázovej schémy, UI, release alebo produkcie |
| 8. Kritérium úspechu | ÁNO | pre otvorenie brány: `PHP=true`, `COMPOSER=true`, `MYSQLI=true`, `MARIADB=true`, `MIGRATIONS_M1_M8=true`, `TWO_CONNECTIONS=true`, `ISOLATED_FROM_PRODUCTION=true`, `INITIAL_STATE_CONFIRMED=true`, `ROLLBACK=true`, `CLEANUP=true` |
| 9. Rollback | ÁNO | overovací rollback: odstrániť iba izolované testovacie dáta a dočasné artefakty; funkčný rollback po otvorení brány: vrátiť jediný budúci funkčný commit Kroku 10 |

## Rozhodnutie o predčasných artefaktoch

### Workflow komentár v commite `41b612af...`

- Stav: `predčasný`, ale funkčne inertný.
- Rozhodnutie: zatiaľ sa nemení ani nevracia, pretože jeho odstránenie by bolo ďalšou zmenou workflow pri `GATE=CLOSED`.
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
4=NEOVERENÉ
5=NEOVERENÉ
6=NEOVERENÉ
7=ÁNO
8=ÁNO
9=ÁNO
GATE=CLOSED
```

```text
BLOKUJÚCI_BOD=4, 5 a 6 — praktické prístupy, vykonávacie prostredie a vykonateľné závislosti
CHÝBAJÚCI_DÔKAZ=úspešný praktický výstup izolovaného prostredia potvrdzujúci PHP, Composer, MySQLi, MariaDB, migrácie M1–M8, dve spojenia, izoláciu od produkcie, počiatočný stav, rollback a cleanup
POVOLENÝ_ĎALŠÍ_ÚKON=iba vykonať alebo získať praktický dôkaz existujúceho workflow alebo ekvivalentného izolovaného prostredia bez zmeny workflow, konfigurácie, vykonateľného kódu alebo predmetového testu
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
#ID=8  R=1 W=0
#ID=9  R=1 W=0
#ID=10 R=1 W=0
#ID=11 R=1 W=1
#ID=12 R=1 W=0
#ID=13 R=1 W=1
#ID=14 R=1 W=0
```

Hodnoty `W=0` označujú úkony, ktoré sa nesmú vykonať pred otvorením projektovej brány alebo pred dokončením kroku.
