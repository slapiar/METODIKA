# INICIALIZÁCIA KROKU: Refaktorizácia inicializačnej metodiky na v2.0

Dátum a čas začatia: 2026-07-26 06:44 Europe/Bratislava
Čas otvorenia brány: 2026-07-26 06:47 Europe/Bratislava
Čas uzavretia: 2026-07-26 07:08 Europe/Bratislava
Projekt: METODIKA
Autoritatívny repozitár: `slapiar/METODIKA`
Autoritatívna vetva: `main`
Východiskový HEAD: `3c9f79c3dc75765cde58cb5a3216d41a2f2d0524`
INI commit: `907350a41435299d156a9e95bb3cd6deccfe87aa`
Predmet: nahradiť celý obsah `postupy/Inicializácia práce.md` presne dodanou štandardizovanou verziou v2.0 a zosúladiť evidenciu.

## 1. Metodika načítaná: ÁNO

- Overené: celý pôvodný dokument `postupy/Inicializácia práce.md`, 432 riadkov.
- Úkon: nové vzdialené načítanie z vetvy `main` po nadväzujúcich rozsahoch až po posledný riadok.
- Výsledok: dovtedy platný predpis bol prečítaný celý pred jeho náhradou.
- Dôkaz: pôvodný blob `1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19`.

## 2. Projekt a autoritatívny zdroj overený: ÁNO

- Overené: projekt METODIKA, repozitár `slapiar/METODIKA`, vetva `main`.
- Úkon: načítanie metadát repozitára.
- Výsledok: predvolená autoritatívna vetva je `main`; administrátorské oprávnenia sú dostupné.
- Dôkaz: aktuálne metadáta GitHub repozitára.

## 3. Vetva a HEAD overené: ÁNO

- Overené: `main` bol pred začatím identický s commitom `3c9f79c3dc75765cde58cb5a3216d41a2f2d0524`.
- Úkon: porovnanie `3c9f79c...` proti `main`.
- Výsledok: `status=identical`, bez nových commitov alebo súborových rozdielov.
- Dôkaz: GitHub compare výsledok pred začatím zásahu.

## 4. Potrebné prístupy prakticky overené: ÁNO

- Overené: čítanie, zápis a vzdialený read-back na vetve `main`.
- Úkon: vytvorenie tohto INI commitom `907350a41435299d156a9e95bb3cd6deccfe87aa` a jeho úplné spätné načítanie.
- Výsledok: správny súbor bol zapísaný na `main`; obsah je úplný a čitateľný.
- Dôkaz: prvý INI blob `0b415c932f7b1240128ef2c2aaa39ca79600012f` a read-after-write 113 riadkov.

## 5. Prostredie prakticky overené: ÁNO PRE DOKUMENTAČNÝ KROK

- Overené: vzdialené GitHub prostredie umožňuje čítanie a zápis dokumentov; lokálny terminál nebol použitý ako autoritatívne prostredie.
- Úkon: bezpečné čítanie a zápis cez GitHub konektor.
- Výsledok: na požadovanú dokumentačnú zmenu postačovalo vzdialené GitHub rozhranie.
- Dôkaz: úspešné commity a vzdialené read-after-write všetkých dotknutých dokumentov.

## 6. Závislosti kroku dostupné: ÁNO

- Overené: pôvodný metodický súbor, `postupy/README.md`, `CHANGELOG.md`, používateľom dodaný úplný obsah v2.0 a prístup na vetvu `main`.
- Úkon: načítanie aktuálneho stavu a prevzatie explicitného zadania.
- Výsledok: všetky obsahové závislosti refaktorizácie boli dostupné.
- Dôkaz: aktuálne vzdialené súbory a zadanie používateľa.

## 7. Predmet a hranice zásahu určené: ÁNO

- Predmet: úplná náhrada obsahu `postupy/Inicializácia práce.md` verziou v2.0 presne podľa zadania.
- Evidenčné zápisy: tento INI, WORK záznam, `postupy/README.md`, `CHANGELOG.md`.
- Hranice: bez zmeny `/codei`, testov, migrácií, databázy, release verzie, balíkov a produkcie.
- Dôkaz: explicitné zadanie používateľa a výsledný diff.

## 8. Kritérium úspechu určené: ÁNO

Splnené kritériá:

1. pôvodný 432-riadkový obsah bol celý nahradený dodaným textom v2.0,
2. vzdialený read-after-write potvrdil správny názov, úplnosť, nadpis, štyri sekcie, tri code bloky a tabuľku,
3. `postupy/README.md` a `CHANGELOG.md` boli zosúladené,
4. vznikol WORK záznam vykonania,
5. pomocný nepoužitý workflow bol odstránený a v aktuálnom strome nezostal,
6. výsledný diff sa obmedzil na metodické a evidenčné dokumenty.

## 9. Rollback určený: ÁNO

- Obsahový rollback: obnoviť pôvodný blob `1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19`.
- Evidenčný rollback: novou historickou udalosťou zosúladiť register a changelog.
- Produkčný a dátový rollback: nie je potrebný, pretože produkcia ani databáza neboli predmetom zásahu.

## Stav brány

```text
GATE=USED_AND_CLOSED
BLOKUJÚCI_BOD=Žiadny
VÝSLEDOK=SPLNENÉ
POVOLENÝ_ĎALŠÍ_ÚKON=návrat k aktuálnemu záväznému plánu
```

## Výsledné dôkazy

```text
METODIKA_COMMIT=6e566cc0e6df6a2b9c0cfdaaa4fb7827d8a6b4df
METODIKA_BLOB=44729126508a0c9151fb2358badcb1445a425bd6
WORK_COMMIT=f9ed1b24084be4c47cf9be7fb84fda5b6d215a38
WORK_BLOB=1b9fadfddf538a377b41ca76afd2f5cb186bb7bb
REGISTER_COMMIT=621e80558cc010654e4e0cbfc1495be2e3fa4456
REGISTER_BLOB=ebb620442596838493fb64c4ba38416d8067aa12
CHANGELOG_COMMIT=f14dcfd7a7eeaad1b993992f7392ae57005be2b0
CHANGELOG_BLOB=2b9879fd72c79bf75f54178eb3f91d606f80c7a7
READ_AFTER_WRITE=PASS
OTVORENÉ_RIZIKÁ=Žiadne v rozsahu dokumentačného úkonu
```

## Matica plnenia pokynov

| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---|---|
| ID-01 až ID-14 | 1 | 1 |
