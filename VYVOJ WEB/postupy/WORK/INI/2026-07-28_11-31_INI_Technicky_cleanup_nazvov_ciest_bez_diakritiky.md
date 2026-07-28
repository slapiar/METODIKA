# INICIALIZÁCIA KROKU: Technický cleanup názvov ciest bez diakritiky

Dátum: 2026-07-28 11:31 Europe/Bratislava

## Účel

Tento INI záznam otvára samostatný technický cleanup názvov ciest v repozitári `slapiar/METODIKA`.

Cieľom nie je meniť význam dokumentov, obsah metodiky, stav brán ani výsledky predchádzajúcich krokov.

Cieľom je odstrániť diakritiku z názvov adresárov a súborov, aby boli cesty stabilne čitateľné pre GitHub UI, `grep`, shell, CI nástroje a ďalšie automatizované spracovanie.

## Kontrolná matica

1. Metodika načítaná: ÁNO | Dôkaz: `VÝVOJ WEB/METODIKA_2-1.md`, blob `6b440d0ef5b084a3d4eaa2d35121ab6105825025`
2. Projekt a autoritatívny zdroj: ÁNO | Dôkaz: `slapiar/METODIKA`, vetva `main`
3. Vetva a HEAD: ÁNO | Dôkaz: `4641b24730bfdb43c05e8fbe115f41c874cf4dc6`
4. Prístupy read/write: ÁNO | Dôkaz: GitHub konektor, oprávnenia `admin`, `maintain`, `push`, `pull`, `triage`
5. Prostredie a runtime: ÁNO | Dôkaz: zásah je repozitárový rename ciest; produkčný runtime a databáza sa nemenia
6. Závislosti kroku: ÁNO | Dôkaz: požiadavka Autority: odstrániť diakritiku z názvov a následne aplikovať nové názvy vo všetkých dotknutých záznamoch
7. Predmet a hranice zásahu: ÁNO | Dôkaz: výhradne názvy ciest a textové odkazy na tieto cesty
8. Kritérium úspechu: ÁNO | Dôkaz: v názvoch adresárov a súborov nezostane diakritika; odkazy v dotknutých markdown záznamoch budú ukazovať na nové ASCII cesty
9. Rollback plán: ÁNO | Dôkaz: návrat na HEAD pred zásahom `4641b24730bfdb43c05e8fbe115f41c874cf4dc6`

## Stav Brány

```text
GATE=OPEN
BLOKUJÚCI_BOD=Žiadny
POVOLENÝ_ĎALŠÍ_ÚKON=Pripraviť mapu premenovania a vykonať technický cleanup názvov ciest bez zmeny významu
```

## Povolená mapa zásahu

Pravidlo:

```text
odstrániť diakritiku
nemeniť význam
nemeniť obsah dokumentov okrem odkazov na premenované cesty
nemeniť produkciu
nemeniť databázu
```

Typické príklady:

```text
VÝVOJ METODIKY1 -> VYVOJ METODIKY1
VÝVOJ WEB -> VYVOJ WEB
poznámky -> poznamky
Inicializácia práce.md -> Inicializacia prace.md
Produkčný -> Produkcny
súbežné -> subezne
záverečné -> zaverecne
```

## Matica plnenia pokynov

| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---:|---:|
| ID-01 až ID-14 | 1 | 1 |

## Poznámka

Tento INI záznam je posledný povolený zápis v starej ceste s diakritikou pred samotným cleanupom názvov ciest.