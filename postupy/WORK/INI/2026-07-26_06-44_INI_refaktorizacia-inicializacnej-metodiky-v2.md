# INICIALIZÁCIA KROKU: Refaktorizácia inicializačnej metodiky na v2.0

Dátum a čas začatia: 2026-07-26 06:44 Europe/Bratislava
Projekt: METODIKA
Autoritatívny repozitár: `slapiar/METODIKA`
Autoritatívna vetva: `main`
Východiskový HEAD: `3c9f79c3dc75765cde58cb5a3216d41a2f2d0524`
Predmet: nahradiť celý obsah `postupy/Inicializácia práce.md` presne dodanou štandardizovanou verziou v2.0 a zosúladiť evidenciu.

## 1. Metodika načítaná: ÁNO

- Overené: celý aktuálny dokument `postupy/Inicializácia práce.md`, 432 riadkov.
- Úkon: nové vzdialené načítanie z vetvy `main` po nadväzujúcich rozsahoch až po posledný riadok.
- Výsledok: aktuálne platný predpis bol prečítaný celý pred jeho náhradou.
- Dôkaz: blob `1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19`.
- Zostáva neoverené: výsledný obsah v2.0 až po zápise a read-after-write.

## 2. Projekt a autoritatívny zdroj overený: ÁNO

- Overené: projekt METODIKA, repozitár `slapiar/METODIKA`, vetva `main`.
- Úkon: načítanie metadát repozitára.
- Výsledok: predvolená autoritatívna vetva je `main`; administrátorské oprávnenia sú dostupné.
- Dôkaz: aktuálne metadáta GitHub repozitára.
- Zostáva neoverené: nič pre tento dokumentačný krok.

## 3. Vetva a HEAD overené: ÁNO

- Overené: `main` je identický s commitom `3c9f79c3dc75765cde58cb5a3216d41a2f2d0524`.
- Úkon: porovnanie `3c9f79c...` proti `main`.
- Výsledok: `status=identical`, bez nových commitov alebo súborových rozdielov.
- Dôkaz: GitHub compare výsledok pred začatím zásahu.
- Zostáva neoverené: nový HEAD po tomto INI zápise.

## 4. Potrebné prístupy prakticky overené: NEOVERENÉ

- Overené: čítanie repozitára a deklarované oprávnenia `admin`, `maintain`, `pull`, `push`, `triage`.
- Úkon: vzdialené čítanie a kontrola metadát.
- Výsledok: čítací prístup funguje.
- Dôkaz: úspešné načítanie metodiky a metadát.
- Zostáva neoverené: praktický zápis a read-back; overí sa vytvorením a spätným načítaním tohto jediného povoleného INI artefaktu.

## 5. Prostredie prakticky overené: ÁNO PRE DOKUMENTAČNÝ KROK

- Overené: vzdialené GitHub prostredie umožňuje čítanie dokumentov; lokálny terminál nemá DNS prístup a nebude použitý ako autoritatívne prostredie.
- Úkon: bezpečné čítanie cez GitHub konektor a pomocný test terminálu bez zásahu do projektu.
- Výsledok: na požadovanú dokumentačnú zmenu postačuje GitHub Contents API.
- Dôkaz: úspešné vzdialené načítania; terminálový test skončil `Could not resolve host: github.com`.
- Zostáva neoverené: praktický zápis cez GitHub; bod 4.

## 6. Závislosti kroku dostupné: ÁNO

- Overené: aktuálny metodický súbor, `postupy/README.md`, `CHANGELOG.md`, používateľom dodaný úplný obsah v2.0 a prístup na vetvu `main`.
- Úkon: načítanie aktuálneho stavu a prevzatie explicitného zadania.
- Výsledok: všetky obsahové závislosti refaktorizácie sú dostupné.
- Dôkaz: aktuálne vzdialené súbory a zadanie používateľa.
- Zostáva neoverené: výsledný read-after-write.

## 7. Predmet a hranice zásahu určené: ÁNO

- Predmet: úplná náhrada obsahu `postupy/Inicializácia práce.md` verziou v2.0 presne podľa zadania.
- Povinné evidenčné zápisy: tento INI, WORK záznam, `postupy/README.md`, `CHANGELOG.md`.
- Hranice: bez zmeny `/codei`, testov, workflowov, migrácií, databázy, release verzie, balíkov a produkcie.
- Dôkaz: explicitné zadanie používateľa.
- Zostáva neoverené: nič pre určenie rozsahu.

## 8. Kritérium úspechu určené: ÁNO

Úspech nastane iba ak:

1. pôvodný 432-riadkový obsah bude celý nahradený dodaným textom v2.0,
2. vzdialený read-after-write potvrdí správny názov, úplnosť, nadpis, sekcie, code bloky a tabuľku,
3. `postupy/README.md` a `CHANGELOG.md` budú zosúladené,
4. vznikne WORK záznam vykonania,
5. výsledný diff sa obmedzí iba na metodické a evidenčné dokumenty,
6. bude uvedený výsledný commit a odkaz na nový súbor.

## 9. Rollback určený: ÁNO

- Kódový rollback: vrátiť commity tohto úkonu v opačnom poradí alebo obnoviť pôvodný blob `1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19`.
- Evidenčný rollback: novou historickou udalosťou zaznamenať návrat; neprepisovať históriu potichu.
- Produkčný a dátový rollback: nie je potrebný, pretože produkcia ani databáza nie sú predmetom zásahu.

## Stav brány pri prvom zápise

```text
GATE=CLOSED
BLOKUJÚCI_BOD=4 — praktický zápis a read-back ešte nebol potvrdený
CHÝBAJÚCI_DÔKAZ=commit a vzdialené spätné načítanie tohto INI záznamu
POVOLENÝ_ĎALŠÍ_ÚKON=iba spätné načítanie tohto INI a jeho aktualizácia po potvrdení zápisu
```

## Priebežná matica metodických pokynov

| #ID | Pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Nové načítanie vzdialeného autoritatívneho repozitára | 1 | 1 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 1 |
| 4 | Praktické overenie prístupov | 1 | 0 |
| 5 | Obnova projektového kontextu a kontrola predchádzajúcich úkonov | 1 | 1 |
| 6 | Overenie skutočného stavu | 1 | 1 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 0 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 0 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 0 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |
| STOP | Povinný postup pri STOP | 1 | 0 |
