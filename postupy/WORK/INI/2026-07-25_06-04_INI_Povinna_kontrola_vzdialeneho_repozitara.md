# INICIALIZÁCIA — Povinná kontrola vzdialeného repozitára pred každým krokom

## Stav

```text
GATE=OPEN
```

## 1. Metodika načítaná: ÁNO

- Aktuálny obsah `postupy/Inicializácia práce.md` bol načítaný priamo z autoritatívnej vetvy `main`.
- Blob pred úpravou: `201bcaef882e21e99c484b2c9acd61489e1e9914`.
- Výsledok: metodika už zakazuje spoliehanie sa na pamäť, ale neobsahuje dostatočne explicitnú nepriechodnú kontrolu úplného vzdialeného stavu pred každým novým krokom.
- Neoverené: nič blokujúce tento metodický úkon.

## 2. Projekt a autoritatívny zdroj overený: ÁNO

- Projekt: `METODIKA`.
- Autoritatívny repozitár: `slapiar/METODIKA`.
- Autoritatívna vetva: `main`.
- Predmetom je iba spresnenie univerzálneho inicializačného postupu.

## 3. Vetva a HEAD overené: ÁNO

- Aktuálny vzdialený HEAD pred týmto INI záznamom: `5692fe981f816235b8a1bfcfd01811b1660a7317`.
- Overené novým načítaním histórie vzdialeného repozitára.
- Neoverené: nič potrebné pre tento repozitárový metodický úkon.

## 4. Potrebné prístupy prakticky overené: ÁNO

- Čítanie bolo overené načítaním metodiky z `main`.
- Zápis bol overený vytvorením tohto INI záznamu.
- Iné prístupy nie sú pre tento úkon potrebné.

## 5. Prostredie prakticky overené: ÁNO

- Potrebným prostredím je iba autoritatívny vzdialený GitHub repozitár.
- PHP, Composer, databáza, release ani produkcia sa nemenia a nie sú predmetom tohto úkonu.

## 6. Závislosti kroku dostupné: ÁNO

- Dostupný je aktuálny obsah `postupy/Inicializácia práce.md`.
- Dostupný je aktuálny HEAD a história `main`.
- Dostupný je konkrétny používateľov pokyn doplniť striktnú kontrolu vzdialeného repozitára pri každom novom kroku.

## 7. Predmet a hranice zásahu určené: ÁNO

- Predmet: doplniť nepriechodné pravidlo, že pred otvorením každého nového kroku sa musí znovu načítať a skontrolovať vzdialený autoritatívny repozitár a kompletné údaje potrebné pre krok.
- Hranica: iba `postupy/Inicializácia práce.md`.
- Bez zmeny plánu, vykonateľného kódu, testov, databázy, release alebo produkcie.

## 8. Kritérium úspechu určené: ÁNO

- Metodika výslovne zakáže otvorenie ďalšieho kroku, pokiaľ nebol načítaný aktuálny vzdialený HEAD, relevantná história, plán, INI/WORK záznamy a úplné dotknuté zdroje.
- Pamäť, konverzácia, register alebo starší lokálny stav nebudú prípustnou náhradou.
- Pri rozpore bude mať aktuálny vzdialený autoritatívny zdroj prednosť a brána zostane zatvorená do odstránenia rozporu.

## 9. Rollback určený: ÁNO

- Úpravu metodiky možno vrátiť revertným commitom.
- Iné súbory ani prostredia sa nemenia.

## Brána

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

## Jediný povolený nasledujúci úkon

Doplniť do `postupy/Inicializácia práce.md` striktnú nepriechodnú kontrolu aktuálneho vzdialeného autoritatívneho repozitára pred otvorením každého nového kroku a výsledok spätne načítať.