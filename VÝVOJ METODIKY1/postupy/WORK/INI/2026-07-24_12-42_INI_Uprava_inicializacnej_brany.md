# INICIALIZÁCIA — Úprava povinnej pracovnej brány

## Identifikácia

```text
dátum a čas: 2026-07-24 12:42 Europe/Bratislava
projekt: METODIKA
činnosť: doplnenie a rozpracovanie záväznej inicializačnej brány
cieľový dokument: postupy/Inicializácia práce.md
autoritatívny repozitár: slapiar/METODIKA
autoritatívna vetva: main
HEAD pri overení: 4ebdb6e358c2963743d44d4c793d29b754c67148
```

## Povinná brána

1. Metodika načítaná: **ÁNO**  
   Dôkaz: aktuálny obsah `postupy/Inicializácia práce.md` bol načítaný z vetvy `main`, vrátane nového všeobecne záväzného stanoviska v bode 6.

2. Projekt a autoritatívny zdroj overený: **ÁNO**  
   Dôkaz: `PROJEKTY/ZoznamProjektov.md` určuje projekt METODIKA, repozitár `slapiar/METODIKA` a vetvu `main`.

3. Vetva a HEAD overené: **ÁNO**  
   Dôkaz: GitHub označuje `main` ako defaultnú a autoritatívnu vetvu; posledný commit pri začatí úkonu je `4ebdb6e358c2963743d44d4c793d29b754c67148`.

4. Potrebné prístupy prakticky overené: **ÁNO**  
   Dôkaz: úspešné čítanie repozitára, projektového registra a cieľového dokumentu; oprávnenie na zápis je potvrdené vytvorením tohto inicializačného záznamu.

5. Prostredie prakticky overené: **ÁNO**  
   Dôkaz: predmetom úkonu je výhradne dokumentačný zápis cez GitHub Contents API do vetvy `main`; čítanie aj zápis v tomto prostredí prešli.

6. Závislosti kroku dostupné: **ÁNO**  
   Dôkaz: dostupné sú cieľový dokument, `postupy/README.md`, `CHANGELOG.md`, projektový register a história posledného commitu.

7. Predmet a hranice zásahu určené: **ÁNO**  
   Predmetom je rozpracovanie záväznej brány tak, aby `prečítané ≠ splnené`, každý bod mal dôkaz a akýkoľvek stav `NIE` alebo `NEOVERENÉ` zabránil návrhu, implementácii a príkazom. Vykonateľný kód ani produkčné prostredie sa nemenia.

8. Kritérium úspechu určené: **ÁNO**  
   Úspech nastane, ak dokument explicitne určí povinný inicializačný záznam, dôkaz ku každému bodu, nepriechodnú bránu, zákaz implicitných predpokladov, pravidlá STOP a zosúladenie registra s `CHANGELOG.md`.

9. Rollback určený: **ÁNO**  
   Rollback predstavuje obnovenie predchádzajúceho blobu `postupy/Inicializácia práce.md` so SHA `413772e5ae459419058b1d6e480391df94ff1009` a zodpovedajúce spätné udalosti v registri a `CHANGELOG.md`; história sa nemaže.

```text
GATE=OPEN
```

## Povolený nasledujúci úkon

Doplniť `postupy/Inicializácia práce.md`, následne aktualizovať `postupy/README.md` a `CHANGELOG.md` a všetky zápisy spätne načítať.