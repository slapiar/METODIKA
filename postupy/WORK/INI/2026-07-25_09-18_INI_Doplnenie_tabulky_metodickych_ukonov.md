# INICIALIZÁCIA — Doplnenie tabuľky metodických úkonov

## Stav brány

```text
GATE=OPEN
```

## Dôvod úkonu

Pri uzavretí Kroku 10 bol na konci jeho INI záznamu uvedený iba textový blok `#ID / R / W`. Nebola vypísaná povinná tabuľka stavu čítania a vykonávania metodických pokynov na obrazovku ani zapísaná v tabuľkovom tvare na konci INI záznamu.

Tým nebolo splnené základné pravidlo dokumentu `postupy/Inicializácia práce.md`:

> Vypísať na obrazovku tabuľku stavu čítania a vykonávania pokynov, uvedenú na konci práve vytvoreného INI záznamu.

## Inicializačná tabuľka

| Bod | Stav | Overenie a dôkaz |
|---|---|---|
| 1. Metodika načítaná | ÁNO | Načítaný aktuálny úplný obsah `postupy/Inicializácia práce.md`, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79`; povinnosť tabuľky je v bode 13 a v základnom pravidle. |
| 2. Projekt a autoritatívny zdroj overený | ÁNO | Projekt METODIKA, repozitár `slapiar/METODIKA`, vetva `main`, technický koreň `/codei`. |
| 3. Vetva a HEAD overené | ÁNO | Aktuálny vzdialený HEAD pred týmto zápisom: `7deb45c394a16343d9a7781044ffdbe718d2c04d`. |
| 4. Potrebné prístupy prakticky overené | ÁNO | Čítanie aktuálnych súborov a vytvorenie tohto INI záznamu na `main` prakticky overujú čítanie aj zápis. |
| 5. Prostredie prakticky overené | ÁNO | Úkon je výlučne dokumentačný v autoritatívnom vzdialenom repozitári; GitHub zapisovacie prostredie je funkčné. |
| 6. Závislosti kroku dostupné | ÁNO | Načítané sú `Inicializácia práce.md`, pôvodný INI Kroku 10, aktuálny register, plán a `CHANGELOG.md`. |
| 7. Predmet a hranice zásahu určené | ÁNO | Doplniť skutočnú markdown tabuľku do INI Kroku 10, zosúladiť evidenciu a vypísať tabuľku používateľovi; bez zmeny funkčného kódu, testov, databázy, workflowu alebo produkcie. |
| 8. Kritérium úspechu určené | ÁNO | Na konci INI Kroku 10 bude tabuľka s ID 0–14, názvom pokynu, R a W; tabuľka bude zároveň zobrazená v odpovedi používateľovi; register a changelog zaznamenajú nápravu. |
| 9. Rollback určený | ÁNO | Vrátiť dokumentačné commity tejto nápravy; funkčný commit Kroku 10 sa nemení. |

## Vyhodnotenie brány

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

## Povolený úkon

```text
Nahradiť netabuľkový blok na konci INI Kroku 10 povinnou markdown tabuľkou metodických úkonov, aktualizovať register a CHANGELOG a výsledok spätne načítať.
```

## Tabuľka stavu čítania a vykonávania pokynov

| ID | Metodický pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Nové načítanie vzdialeného autoritatívneho repozitára | 1 | 1 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 1 |
| 4 | Praktické overenie prístupov | 1 | 1 |
| 5 | Obnova projektového kontextu | 1 | 1 |
| 6 | Overenie skutočného stavu | 1 | 1 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 0 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 0 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 0 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |
