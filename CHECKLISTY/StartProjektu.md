# Univerzálny checklist začatia projektu

## Účel

Tento checklist sa používa pred začatím práce na novom projekte aj pri obnovení práce na existujúcom projekte.

Jeho cieľom je zabrániť strate času, nesprávnym zásahom, práci v nesprávnom prostredí a rozhodovaniu bez overeného kontextu.

---

# 1. Identifikácia projektu

- [ ] Poznám presný názov projektu.
- [ ] Poznám vlastníka alebo zodpovednú osobu.
- [ ] Projekt je zapísaný v `PROJEKTY/ZoznamProjektov.md`.
- [ ] Poznám jeho účel, cieľ a aktuálnu prioritu.
- [ ] Viem, či ide o nový projekt, pokračovanie, obnovu, migráciu alebo opravu.

---

# 2. Overenie prístupov

- [ ] Mám prístup k repozitáru.
- [ ] Mám právo čítať obsah repozitára.
- [ ] Mám právo zapisovať do repozitára.
- [ ] Mám prístup k produkčnému prostrediu, ak je potrebný.
- [ ] Mám prístup ku stage prostrediu, ak existuje.
- [ ] Mám prístup k databáze, ak je potrebný.
- [ ] Mám prístup k súborovému systému alebo serveru, ak je potrebný.
- [ ] Mám prístup ku všetkým službám, API, kľúčom a integráciám potrebným pre úlohu.
- [ ] Funkčnosť prístupov bola prakticky overená.

> Prístup sa nepovažuje za funkčný iba preto, že bol pridelený. Musí byť overený reálnym čítaním alebo zápisom.

---

# 3. Určenie autoritatívneho zdroja

- [ ] Poznám autoritatívny repozitár projektu.
- [ ] Poznám autoritatívnu vetvu.
- [ ] Poznám hlavný pracovný koreň projektu.
- [ ] Viem, ktoré prostredie je aktuálne a ktoré je už len historické.
- [ ] Viem, či produkcia, stage, lokálna kópia a repozitár obsahujú rovnakú verziu.
- [ ] Viem, ktorý zdroj má pri rozdieloch najvyššiu prioritu.

---

# 4. Obnova kontextu

- [ ] Prečítala sa univerzálna metodika.
- [ ] Prečítal sa záznam projektu v `PROJEKTY/ZoznamProjektov.md`.
- [ ] Prečítali sa projektové metodické pokyny.
- [ ] Prečítal sa posledný checkpoint alebo sumarizácia.
- [ ] Skontrolovala sa história posledných zmien.
- [ ] Skontrolovali sa otvorené úlohy, chyby a nedokončené zásahy.
- [ ] Overil sa aktuálny stav projektu priamo v zdrojoch.

---

# 5. Jazyk a forma komunikácie

- [ ] Je určený pracovný jazyk projektu.
- [ ] Dokumentácia sa píše v spisovnej slovenčine, ak nie je určené inak.
- [ ] Dbá sa na syntax, interpunkciu a jednoznačnosť formulácií.
- [ ] Názvy súborov, priečinkov a technických objektov používajú dohodnutú konvenciu.
- [ ] Nepoužívajú sa nejednoznačné, domyslené alebo neoverené tvrdenia.

---

# 6. Vymedzenie úlohy

- [ ] Je jasne určené, čo sa má vykonať.
- [ ] Je jasne určené, čo sa vykonať nemá.
- [ ] Sú známe hranice zásahu.
- [ ] Sú známe dotknuté moduly, súbory, služby a prostredia.
- [ ] Sú známe závislosti a možné vedľajšie účinky.
- [ ] Je určené, podľa čoho sa vyhodnotí úspech.

---

# 7. Analýza pred implementáciou

- [ ] Aktuálny stav bol overený v reálnych súboroch alebo dátach.
- [ ] Neboli použité placeholdery, vymyslené hodnoty ani neoverené predpoklady.
- [ ] Boli identifikované príčiny problému, nie iba jeho prejavy.
- [ ] Boli posúdené riziká zásahu.
- [ ] Bolo zvolené najmenšie bezpečné riešenie.
- [ ] Existuje možnosť návratu do pôvodného stavu.

---

# 8. Príprava zmeny

- [ ] Je určená pracovná vetva alebo spôsob priameho zápisu.
- [ ] Je vytvorená záloha, ak je potrebná.
- [ ] Je určený rozsah commitu.
- [ ] Commit bude obsahovať iba súvisiace zmeny.
- [ ] Je pripravený stručný a výstižný názov commitu.

---

# 9. Overenie po zápise

- [ ] Zmena bola skutočne zapísaná.
- [ ] Obsah súboru bol po zápise znovu načítaný.
- [ ] Bolo overené, že sa zmenil správny súbor a správna vetva.
- [ ] Bolo overené, že nevznikol duplicitný alebo poškodený obsah.
- [ ] Boli overené názvy, odkazy, syntax a interpunkcia.
- [ ] Bol zaznamenaný identifikátor commitu.

> Zápis sa nepovažuje za dokončený, kým sa neoverí výsledný stav.

---

# 10. Ukončenie pracovného kroku

- [ ] Výsledok bol stručne zhrnutý.
- [ ] Boli uvedené vykonané zmeny.
- [ ] Boli uvedené nevyriešené body a riziká.
- [ ] Bol vytvorený checkpoint, ak išlo o významnú zmenu.
- [ ] Zoznam projektov bol aktualizovaný, ak sa zmenil stav projektu.
- [ ] Je jasne určený nasledujúci krok.

---

# Základné pravidlo

> Najprv overiť prístup, potom obnoviť kontext, následne analyzovať a až potom vykonať zmenu.
