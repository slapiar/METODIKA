# INICIALIZÁCIA — Obnova metodického pravidla na aktuálnom HEAD

## Stav brány

```text
GATE=CLOSED
STAV_ZÁZNAMU=PREKONANÝ_ZMENOU_AUTORITATÍVNEHO_HEAD
BLOKUJÚCI_BOD=10. Implementácia až po analýze — zmena HEAD po otvorení brány
CHÝBAJÚCI_DÔKAZ=úplná nová inicializácia nad HEAD 1d7d7938754d1b167a50c9cbfc717862c35ab0b6
POVOLENÝ_ĎALŠÍ_ÚKON=iba nová úplná inicializácia metodického úkonu nad aktuálnym vzdialeným stavom
```

## Nadväznosť na predchádzajúci STOP

Predchádzajúci neúplný pokus zostáva zachovaný v:

`postupy/WORK/INI/2026-07-25_12-16_INI_Kontrola_predchadzajucich_ukonov_a_obnova_autority.md`

Tento druhý pokus bol korektne otvorený na commite `5929519d052d59a287f12ca541a44e26f259711e`, avšak bezprostredná kontrola pred prvým implementačným zápisom preukázala, že autoritatívny `main` je už o päť commitov vpredu.

## Predmet metodického úkonu

Doplniť do `postupy/Inicializácia práce.md` záväzné pravidlo:

1. pred vykonaním každého bodu nového projektového kroku najprv overiť, či už nebol vykonaný a doložený v niektorom predchádzajúcom kroku toho istého projektu,
2. platný existujúci výsledok neopakovať, ale znovu načítať, overiť jeho použiteľnosť a kontinuitu voči aktuálnemu stavu a použiť ho ako východisko,
3. ak výsledok alebo dôkaz nie je priamo dostupný, najprv sa ho pokúsiť bezpečne obnoviť alebo sprístupniť prostredníctvom vlastnej preukázanej administrátorskej autority a dostupných projektových nástrojov,
4. administrátorskú autoritu nepoužiť na domýšľanie výsledku, obchádzanie bezpečnostných hraníc ani neoprávnený zásah do produkcie.

## STOP

```text
STOP
PORUŠENÝ_BOD=10. Implementácia až po analýze — po otvorení brány sa zmenil autoritatívny HEAD
ČO_BOLO_VYKONANÉ_PREDČASNE=nevznikol implementačný zápis; vykonané bolo iba otvorenie tohto INI a povinné spätné načítanie
KTORÁ_BRÁNA_MALA_CHYBE_ZABRÁNIŤ=bezprostredná kontrola HEAD pred prvým zápisom
PREČO_NEZABRÁNILA=zabránila správne; porovnanie 5929519d... → main preukázalo päť nových commitov
STAV_VZNIKNUTÝCH_ARTEFAKTOV=tento INI zostáva historickým dôkazom korektného zastavenia; cieľová metodika týmto pokusom zmenená nebola
ROLLBACK_ALEBO_NÁPRAVA=zachovať záznam; načítať aktuálny HEAD 1d7d7938..., nový CHANGELOG a dotknutý header; vytvoriť novú samostatnú inicializáciu
```

## Zmena vzdialeného stavu

```text
HEAD_PRI_OTVORENÍ_BRÁNY=5929519d052d59a287f12ca541a44e26f259711e
NOVÝ_AUTORITATÍVNY_HEAD=1d7d7938754d1b167a50c9cbfc717862c35ab0b6
STATUS=ahead
AHEAD_BY=5
BEHIND_BY=0
DOTKNUTÉ_SÚBORY=CHANGELOG.md; codei/app/Views/partials/header.php
```

Zmena `header.php` je vykonateľná zmena mimo predmetu tohto metodického úkonu. Jej obsah sa týmto záznamom neposudzuje ani nemení, ale jej existencia ruší platnosť predchádzajúcej brány.

## Tabuľka stavu čítania a vykonávania metodických pokynov

| ID | Metodický pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Povinné nové načítanie vzdialeného autoritatívneho repozitára | 1 | 0 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 0 |
| 4 | Praktické overenie prístupov | 1 | 1 |
| 5 | Obnova projektového kontextu | 1 | 0 |
| 6 | Overenie skutočného stavu | 1 | 0 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 0 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 1 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 1 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |
