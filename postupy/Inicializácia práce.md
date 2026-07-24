# Inicializácia práce

## Stav dokumentu

```text
POTVRDENÝ-NA-PRENESENIE
```

Tento postup vznikol syntézou existujúcich záväzných pravidiel v `README.md`, `CHECKLISTY/StartProjektu.md`, registroch stavov a pracovných metodických dokumentoch. Nezavádza nové definície. Určuje operačné poradie, ktoré sa musí vykonať pred každou prácou na projekte.

---

# Všeobecne záväzná inicializačná brána

Tento dokument nie je iba text určený na prečítanie. Je to nepriechodná vykonávacia brána.

```text
prečítané ≠ splnené
tvrdené ≠ overené
overované ≠ overené
overenie bez dôkazu = NEOVERENÉ
predpoklad ≠ skutočný stav
existencia nástroja ≠ pripravenosť prostredia
```

Pred každým samostatným pracovným krokom, metodickým úkonom, opravou, migráciou, testom, diagnostikou, nasadením alebo inou činnosťou sa vytvorí explicitný inicializačný záznam v:

```text
postupy/WORK/INI/
```

Názov súboru musí obsahovať:

```text
YYYY-MM-DD_HH-MM_INI_<skratka-kroku-alebo-popis-cinnosti>.md
```

Inicializačný záznam je prvým a jediným dovoleným pracovným artefaktom pred otvorením brány. Jeho vytvorenie nie je implementáciou ani návrhom riešenia; zaznamenáva iba výsledok povinného overovania.

Ak sa brána neotvorí, inicializačný záznam zostáva jediným novým súborom daného pokusu. Nesmie po ňom nasledovať návrh, implementácia, príkaz ani zmena prostredia.

## Povinný obsah inicializačného záznamu

```text
INICIALIZÁCIA KROKU X

1. Metodika načítaná: ÁNO / NIE / NEOVERENÉ
2. Projekt a autoritatívny zdroj overený: ÁNO / NIE / NEOVERENÉ
3. Vetva a HEAD overené: ÁNO / NIE / NEOVERENÉ
4. Potrebné prístupy prakticky overené: ÁNO / NIE / NEOVERENÉ
5. Prostredie prakticky overené: ÁNO / NIE / NEOVERENÉ
6. Závislosti kroku dostupné: ÁNO / NIE / NEOVERENÉ
7. Predmet a hranice zásahu určené: ÁNO / NIE / NEOVERENÉ
8. Kritérium úspechu určené: ÁNO / NIE / NEOVERENÉ
9. Rollback určený: ÁNO / NIE / NEOVERENÉ

GATE = OPEN iba vtedy, keď všetko = ÁNO
```

Pri každom bode musí byť uvedené:

- čo bolo overené,
- akým konkrétnym úkonom to bolo overené,
- aký výsledok overenie prinieslo,
- kde je dôkaz dostupný,
- čo zostáva neoverené.

Samotné slovo `ÁNO` bez uvedeného dôkazu sa posudzuje ako `NEOVERENÉ`.

## Nepriechodnosť brány

Ak je čo i len jeden bod `NIE` alebo `NEOVERENÉ`, nesmie vzniknúť:

- návrh riešenia,
- nový pracovný súbor okrem samotného INI záznamu,
- úprava existujúceho pracovného súboru,
- commit pracovnej zmeny,
- príkaz určený na vykonanie používateľom,
- spustenie testu, diagnostiky, migrácie alebo cleanupu,
- zmena konfigurácie alebo prostredia,
- zásah do databázy,
- nasadenie,
- pokračovanie ďalším krokom.

V takom prípade sa zaznamená:

```text
GATE=CLOSED
BLOKUJÚCI_BOD=<číslo a názov>
CHÝBAJÚCI_DÔKAZ=<presný dôkaz>
POVOLENÝ_ĎALŠÍ_ÚKON=<iba overenie chýbajúceho predpokladu>
```

Overovanie chýbajúceho predpokladu nesmie samo vykonať predmet práce, ktorý brána blokuje.

---

# Povinný postup

## 1. Obnova univerzálnej metodiky

Znovu načítať aktuálny obsah univerzálnej metodiky a autoritatívnych dokumentov. Nespoliehať sa iba na pamäť predchádzajúcej práce.

Za splnenie sa nepovažuje všeobecné tvrdenie, že metodika je známa. Inicializačný záznam musí uviesť konkrétne načítané dokumenty a ich aktuálnu verziu, stav alebo blob/commit identifikátor, ak je dostupný.

## 2. Identifikácia projektu

Overiť projekt v `PROJEKTY/ZoznamProjektov.md` a určiť:

- presný názov projektu,
- vlastníka alebo zodpovednú osobu,
- účel a aktuálnu prioritu,
- či ide o nový projekt, pokračovanie, obnovu, migráciu alebo opravu.

Názov repozitára, adresára alebo predchádzajúca konverzácia samy osebe nie sú dôkazom správnej identifikácie projektu.

## 3. Určenie autoritatívneho zdroja

Určiť:

- autoritatívny repozitár,
- autoritatívnu vetvu,
- hlavný pracovný koreň,
- aktuálne a historické prostredia,
- zdroj s najvyššou prioritou pri rozdieloch.

Vetva otvorená v lokálnom termináli nemusí byť autoritatívnou vetvou. Lokálny pracovný stav, GitHub, produkcia a historický release sa musia rozlíšiť.

## 4. Praktické overenie prístupov

Overiť reálnym čítaním alebo bezpečným zápisom iba tie prístupy, ktoré sú pre úlohu potrebné. Pridelený prístup sa nepovažuje za funkčný, kým nebol prakticky overený.

Platí:

```text
oprávnenie deklarované ≠ prístup funkčný
úspešné čítanie ≠ právo zápisu
právo zápisu ≠ právo nasadenia
prístup k repozitáru ≠ prístup k databáze alebo hostingu
```

Overenie musí zodpovedať druhu plánovaného úkonu. Ak sa má zapisovať, samotné čítanie nestačí; ak sa má pracovať s databázou, prístup ku GitHubu nestačí.

## 5. Obnova projektového kontextu

Prečítať:

- projektové metodické pokyny,
- posledný checkpoint alebo sumarizáciu,
- históriu posledných zmien,
- otvorené úlohy, chyby a nedokončené zásahy,
- aktuálny stav priamo v zdrojoch.

Predchádzajúca konverzácia alebo interná pamäť slúži iba ako navigácia k zdrojom. Nenahrádza ich nové načítanie.

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

Povinne sa rozlišuje aj:

```text
súbor existuje ≠ súbor je aktívny
príkaz existuje ≠ príkaz je vykonateľný v danom prostredí
knižnica alebo rozšírenie existuje ≠ služba alebo server je dostupný
konfigurácia existuje ≠ konfigurácia je načítaná
pripojenie je nakonfigurované ≠ pripojenie bolo úspešne overené
test prešiel inde ≠ test prejde v aktuálnom prostredí
produkčný dôkaz ≠ bezpečné testovacie prostredie
```

Pred návrhom testu alebo diagnostiky sa musí prakticky overiť celé prostredie, ktoré test potrebuje, najmä:

- runtime a jeho skutočná verzia,
- aktívne prostredie aplikácie,
- dostupné služby a procesy,
- databázový server a konkrétne neprodukčné spojenie,
- schéma a migrácie,
- potrebné premenné prostredia,
- oprávnenia,
- izolácia od produkcie,
- cleanup a rollback.

Existencia klienta, ovládača, PHP rozšírenia, triedy, integračného príkazu alebo konfiguračného súboru nedokazuje pripravenosť prostredia.

## 7. Vymedzenie predmetu a rozsahu práce

Skontrolovať priečinok `postupy/PLAN/` a vyhľadať plán práce na dnes. Pokiaľ plán na dnes neexistuje, je potrebné ho vytvoriť a zapísať do súboru podľa vzoru predošlých dní. Musí obsahovať dátumovú aj časovú značku v názve a príponu `.md`.

Každý plán sa musí skladať z jednoznačných krokov, navzájom nadväzujúcich a vykonateľných bez prerušenia práce očakávaním na výsledky iných krokov alebo meraní.

Z plánu a záverov prevedenej práce určiť:

- čo presne je predmetom práce,
- čo sa má vykonať,
- čo sa vykonať nemá,
- hranice zásahu,
- dotknuté súbory, moduly, služby a prostredia,
- závislosti a možné vedľajšie účinky,
- kritérium úspechu,
- podmienky zastavenia,
- rollback.

Ak predmet práce závisí od prostredia, služby, databázy, externého dôkazu alebo prístupu, ich dostupnosť musí byť potvrdená pred otvorením brány. Nemožno ich dopĺňať až po vytvorení riešenia.

## 8. Analýza pred návrhom

Najprv určiť príčinu a až potom riešenie. Posúdiť riziká, kontinuitu dotknutých SUBJECT-ov a možné následky metodického úkonu.

Zachovať rozlíšenie:

```text
ACTOR ≠ AUTHORITY
úkon ≠ oprávnenosť úkonu
úkon ≠ jeho výsledok
príčina ≠ úkon ≠ následok ≠ stav
```

Analýza nesmie nahradiť chýbajúce overenie prostredia domnienkou. Ak príčinu nemožno skúmať bez chýbajúceho predpokladu, brána zostáva zatvorená.

## 9. Návrh najmenšieho bezpečného riešenia

Zvoliť najmenší zásah, ktorý spĺňa zadanie a nemení nič mimo jeho rozsahu. Ak je potrebná možnosť návratu, určiť ju pred implementáciou.

Návrh môže vzniknúť až po otvorení inicializačnej brány. Nemožno najprv vytvoriť riešenie a až potom overovať, či je prostredie schopné ho vykonať.

## 10. Implementácia až po analýze

Implementácia nesmie predbehnúť metodiku, inicializačnú bránu, analýzu ani potvrdené významové vzťahy. Databázová a softvérová štruktúra musí byť ich dôsledkom, nie náhradou.

Pred prvým zápisom sa ešte raz musí overiť, že:

```text
GATE=OPEN
```

Ak sa od otvorenia brány zmenil HEAD, vetva, prostredie, prístup, plán alebo iný podstatný predpoklad, pôvodná brána stráca platnosť a musí vzniknúť nová inicializácia.

## 11. Spätné načítanie po zápise

Po každom zápise znovu načítať výsledný obsah a overiť:

- správny súbor,
- správnu vetvu,
- úplnosť zápisu,
- absenciu duplicít alebo poškodenia,
- syntax, názvy, odkazy a interpunkciu.

Zápis sa nepovažuje za dokončený, kým nebol overený výsledný stav.

Spätné načítanie musí overovať skutočný vzdialený výsledok, nie iba lokálny obsah, ktorý mal byť zapísaný.

## 12. Validácia výsledku

Posúdiť výsledok podľa vopred určených kritérií, v konkrétnom kontexte, čase a rozsahu. Validácia nie je pravda ani vlastnosť reality.

Štruktúra záznamov vykonanej práce podľa plánu musí rešpektovať štruktúru plánu a zapisuje sa po jednotlivých krokoch do `postupy/WORK/`, do súboru s dátumom, časom a názvom kroku podľa plánu.

Validácia nesmie spätne vyhlásiť za splnený predpoklad, ktorý nebol overený pred návrhom alebo implementáciou.

## 13. Záznam metodického úkonu

Ak sa mení dokument v `postupy/` alebo `poznámky/`, v tom istom pracovnom kroku aktualizovať:

- príslušný register stavov,
- `CHANGELOG.md`,
- autoritatívny cieľ alebo náhradu, ak sa zmenil stav dokumentu.

História sa neopravuje prepísaním významu. Dopĺňa sa novou udalosťou, ktorá pôvodný stav potvrdí, nahradí, zneplatní alebo opraví.

Inicializačný záznam musí byť uvedený v pracovnom zázname alebo v `CHANGELOG.md` ako dôkaz otvorenia brány.

## 14. Ukončenie pracovného kroku

Na konci stručne uviesť:

- čo sa vykonalo,
- čo sa zmenilo,
- čo zostáva otvorené,
- aké riziká pretrvávajú,
- identifikátor commitu,
- kde je inicializačný záznam,
- nasledujúci logický krok.

Ak išlo o významnú zmenu, vytvoriť checkpoint.

---

# Povinný postup pri STOP

Ak sa zistí, že práca začala bez otvorenej inicializačnej brány alebo že niektorý bod bol označený `ÁNO` bez dôkazu, musí sa okamžite zastaviť.

Nesmie sa pokračovať ospravedlnením, improvizovanou opravou ani ďalším príkazom. Najprv sa zaznamená:

```text
STOP
PORUŠENÝ_BOD=<číslo a názov bodu>
ČO_BOLO_VYKONANÉ_PREDČASNE=<presný zásah>
KTORÁ_BRÁNA_MALA_CHYBE_ZABRÁNIŤ=<bod>
PREČO_NEZABRÁNILA=<presná príčina>
STAV_VZNIKNUTÝCH_ARTEFAKTOV=<platný / predčasný / neplatný / na rozhodnutie>
ROLLBACK_ALEBO_NÁPRAVA=<presný postup>
```

Predčasne vytvorený artefakt sa nesmie automaticky považovať za platný výsledok kroku. O jeho ponechaní, oprave alebo odstránení sa rozhodne až po novej, úplnej inicializácii.

---

# Základné pravidlo

> Najprv dôkazom otvoriť inicializačnú bránu, potom obnoviť kontext, analyzovať a až následne navrhnúť alebo vykonať zmenu.

Skrátené poradie:

```text
INI záznam
→ metodika
→ projekt
→ autoritatívny zdroj
→ vetva a HEAD
→ prístupy
→ prostredie
→ závislosti
→ skutočný stav
→ rozsah
→ kritérium úspechu
→ rollback
→ GATE=OPEN
→ analýza
→ návrh
→ implementácia
→ spätné načítanie
→ Validácia
→ záznam
→ checkpoint
```
