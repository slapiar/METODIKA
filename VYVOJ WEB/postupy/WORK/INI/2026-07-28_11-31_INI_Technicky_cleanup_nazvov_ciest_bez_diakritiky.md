# INICIALIZÁCIA KROKU: Technický cleanup hlavných pracovných ciest bez diakritiky

Dátum: 2026-07-28 11:31 Europe/Bratislava

## Účel

Tento INI záznam otvára samostatný technický cleanup hlavných pracovných ciest v repozitári `slapiar/METODIKA`.

Cieľom nie je meniť význam dokumentov, obsah metodiky, stav brán ani výsledky predchádzajúcich krokov.

Cieľom je odstrániť diakritiku z názvov hlavných pracovných adresárov, aby boli cesty stabilne čitateľné pre GitHub UI, `grep`, shell, CI nástroje a ďalšie automatizované spracovanie.

## Kontrolná matica

1. Metodika načítaná: ÁNO | Dôkaz: `VYVOJ WEB/METODIKA_2-1.md`, blob `6b440d0ef5b084a3d4eaa2d35121ab6105825025`
2. Projekt a autoritatívny zdroj: ÁNO | Dôkaz: `slapiar/METODIKA`, vetva `main`
3. Vetva a HEAD: ÁNO | Dôkaz: `4641b24730bfdb43c05e8fbe115f41c874cf4dc6`
4. Prístupy read/write: ÁNO | Dôkaz: GitHub konektor, oprávnenia `admin`, `maintain`, `push`, `pull`, `triage`
5. Prostredie a runtime: ÁNO | Dôkaz: zásah je repozitárový rename ciest; produkčný runtime a databáza sa nemenia
6. Závislosti kroku: ÁNO | Dôkaz: požiadavka Autority: odstrániť diakritiku z hlavných pracovných názvov a následne aplikovať nové názvy vo všetkých dotknutých záznamoch
7. Predmet a hranice zásahu: ÁNO | Dôkaz: výhradne hlavné pracovné cesty a textové odkazy na tieto cesty
8. Kritérium úspechu: ÁNO | Dôkaz: hlavné pracovné adresáre budú bez diakritiky; odkazy v dotknutých markdown záznamoch budú ukazovať na nové ASCII cesty
9. Rollback plán: ÁNO | Dôkaz: návrat na HEAD pred zásahom `4641b24730bfdb43c05e8fbe115f41c874cf4dc6`

## Stav Brány

```text
GATE=OPEN
BLOKUJÚCI_BOD=Žiadny
POVOLENÝ_ĎALŠÍ_ÚKON=Pripraviť mapu premenovania a vykonať technický cleanup hlavných pracovných ciest bez zmeny významu
```

## Povolená mapa zásahu

Pravidlo:

```text
odstrániť diakritiku z hlavných pracovných koreňov
nemeniť význam
nemeniť obsah dokumentov okrem odkazov na premenované cesty
nemeniť produkciu
nemeniť databázu
```

Typické príklady:

```text
povodny koreň metodického vývoja -> VYVOJ METODIKY1
povodny koreň webového vývoja -> VYVOJ WEB
povodny koreň pracovných poznámok -> poznamky
povodny vnútorný adresár pracovných poznámok vo webovom vývoji -> VYVOJ WEB/poznamky
```

Názvy jednotlivých historických dokumentov sa týmto cleanupom nemenia.

## Matica plnenia pokynov

| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---:|---:|
| ID-01 až ID-14 | 1 | 1 |

## Poznámka

Tento INI záznam vznikol ešte pred samotným cleanupom názvov ciest. Po cleanup-e bol textovo zosúladený s výsledným rozsahom zásahu: hlavné pracovné korene bez diakritiky, nie hromadné premenovanie všetkých historických dokumentov.
