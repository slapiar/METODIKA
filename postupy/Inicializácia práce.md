# Inicializácia práce

## Stav dokumentu

```text
POTVRDENÝ-NA-PRENESENIE
```

Tento postup vznikol syntézou existujúcich záväzných pravidiel v `README.md`, `CHECKLISTY/StartProjektu.md`, registroch stavov a pracovných metodických dokumentoch. Nezavádza nové definície. Určuje operačné poradie, ktoré sa má vykonať pred každou prácou na projekte.

---

# Povinný postup



## 1. Obnova univerzálnej metodiky

Znovu načítať aktuálny obsah univerzálnej metodiky a autoritatívnych dokumentov. Nespoliehať sa iba na pamäť predchádzajúcej práce.

## 2. Identifikácia projektu

Overiť projekt v `PROJEKTY/ZoznamProjektov.md` a určiť:

- presný názov projektu,
- vlastníka alebo zodpovednú osobu,
- účel a aktuálnu prioritu,
- či ide o nový projekt, pokračovanie, obnovu, migráciu alebo opravu.

## 3. Určenie autoritatívneho zdroja

Určiť:

- autoritatívny repozitár,
- autoritatívnu vetvu,
- hlavný pracovný koreň,
- aktuálne a historické prostredia,
- zdroj s najvyššou prioritou pri rozdieloch.

## 4. Praktické overenie prístupov

Overiť reálnym čítaním alebo zápisom iba tie prístupy, ktoré sú pre úlohu potrebné. Pridelený prístup sa nepovažuje za funkčný, kým nebol prakticky overený.

## 5. Obnova projektového kontextu

Prečítať:

- projektové metodické pokyny,
- posledný checkpoint alebo sumarizáciu,
- históriu posledných zmien,
- otvorené úlohy, chyby a nedokončené zásahy,
- aktuálny stav priamo v zdrojoch.

## 6. Overenie skutočného stavu

Nevychádzať z domnienok, placeholderov ani vymyslených údajov. Rozlíšiť najmenej:

```text
skutočnosť
≠ pozorovanie alebo meranie
≠ výsledok
≠ interpretácia
≠ tvrdenie
≠ dôkaz
≠ Validácia
```
Všeobecne Záväzné pravidlo:
Vytvára sa explicitný záznam vo /WORK/INI/ s označením dátumu a času a skratky kroku alebo popisu činnosti, s príponou.md
INICIALIZÁCIA KROKU X

1. Metodika načítaná: ÁNO / NIE
2. Projekt a autoritatívny zdroj overený: ÁNO / NIE
3. Vetva a HEAD overené: ÁNO / NIE
4. Potrebné prístupy prakticky overené: ÁNO / NIE
5. Prostredie prakticky overené: ÁNO / NIE
6. Závislosti kroku dostupné: ÁNO / NIE
7. Predmet a hranice zásahu určené: ÁNO / NIE
8. Kritérium úspechu určené: ÁNO / NIE
9. Rollback určený: ÁNO / NIE

GATE = OPEN iba vtedy, keď všetko = ÁNO

Ak je čo i len jeden bod NIE alebo NEOVERENÉ,
nesmie vzniknúť:
- návrh riešenia,
- nový súbor,
- commit,
- príkaz na spustenie,
- zmena prostredia,
- pokračovanie ďalším krokom.

## 7. Vymedzenie predmetu a rozsahu práce

Skontrolovať priečinok /postupy/PLAN/ a vyhľadať plán práce na dnes. Pokiaľ plán na dnes neexistuje, je potrebné ho vytvoriť a zapísať do súboru, podľa vzoru predošlých dní. Musí obsahovať ako dátumovú, tak aj časovú značku, v názve a .md. Každý plán sa musí skladať z jednoznačných krokov, navzájom nadväzujúcich a vykonateľných bez prerušenia práce očakávaním na výsledky iných krokov alebo meraní.
Z plánu a záverov prevedenej práce určiť ďalej:

- čo presne je predmetom práce,
- čo sa má vykonať,
- čo sa vykonať nemá,
- hranice zásahu,
- dotknuté súbory, moduly, služby a prostredia,
- závislosti a možné vedľajšie účinky,
- kritérium úspechu.

## 8. Analýza pred návrhom

Najprv určiť príčinu a až potom riešenie. Posúdiť riziká, kontinuitu dotknutých SUBJECT-ov a možné následky metodického úkonu.

Zachovať rozlíšenie:

```text
ACTOR ≠ AUTHORITY
úkon ≠ oprávnenosť úkonu
úkon ≠ jeho výsledok
príčina ≠ úkon ≠ následok ≠ stav
```

## 9. Návrh najmenšieho bezpečného riešenia

Zvoliť najmenší zásah, ktorý spĺňa zadanie a nemení nič mimo jeho rozsahu. Ak je potrebná možnosť návratu, určiť ju pred implementáciou.

## 10. Implementácia až po analýze

Implementácia nesmie predbehnúť metodiku ani potvrdené významové vzťahy. Databázová a softvérová štruktúra musí byť ich dôsledkom, nie náhradou.

## 11. Spätné načítanie po zápise

Po každom zápise znovu načítať výsledný obsah a overiť:

- správny súbor,
- správnu vetvu,
- úplnosť zápisu,
- absenciu duplicít alebo poškodenia,
- syntax, názvy, odkazy a interpunkciu.

Zápis sa nepovažuje za dokončený, kým nebol overený výsledný stav.

## 12. Validácia výsledku

Posúdiť výsledok podľa vopred určených kritérií, v konkrétnom kontexte, čase a rozsahu. Validácia nie je pravda ani vlastnosť reality.
Štruktúra záznamov vykonanej práce podľa plánu, musí rešpektovať štruktúru plánu a takto bude aj zapisovaná po jednotlivých jeho krokoch, do zložky /postupy/WORK, do súboru s označením dátumu a času a názvu kroku podľa plánu.md.

## 13. Záznam metodického úkonu

Ak sa mení dokument v `postupy/` alebo `poznámky/`, v tom istom pracovnom kroku aktualizovať:

- príslušný register stavov,
- `CHANGELOG.md`,
- autoritatívny cieľ alebo náhradu, ak sa zmenil stav dokumentu.

História sa neopravuje prepísaním významu. Dopĺňa sa novou udalosťou, ktorá pôvodný stav potvrdí, nahradí, zneplatní alebo opraví.

## 14. Ukončenie pracovného kroku

Na konci stručne uviesť:

- čo sa vykonalo,
- čo sa zmenilo,
- čo zostáva otvorené,
- aké riziká pretrvávajú,
- identifikátor commitu,
- zapísať,
- nasledujúci logický krok.

Ak išlo o významnú zmenu, vytvoriť checkpoint.

---

# Základné pravidlo

> Najprv overiť prístup, potom obnoviť kontext, následne analyzovať a až potom vykonať zmenu a nezabudnúť ju zapísať kde treba.

Skrátené poradie:

```text
metodika
→ projekt
→ autoritatívny zdroj
→ kontext
→ skutočný stav
→ rozsah
→ analýza
→ návrh
→ implementácia
→ overenie
→ Validácia
→ záznam
→ checkpoint
```
