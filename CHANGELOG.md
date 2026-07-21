# Changelog

Podstatné zmeny projektu METODIKA sa od 21. júla 2026 zapisujú priebežne do tohto súboru.

## 2026-07-21

### Pridané

- vytvorený adresár `app/` ako koreň obslužného softvéru, ktorý bude súčasťou release,
- pridaný `app/setup.php` na vytvorenie lokálneho súboru `local-config.php`,
- setup podporuje konfiguráciu databáz `u550121827_metodic` a `u550121827_mapmet`,
- setup umožňuje uložiť ďalšie lokálne API kľúče a hodnoty vo formáte `NAZOV=hodnota`,
- pridaný `.gitignore` pre `local-config`, `local-config.php`, lokálne `.env` súbory a prevádzkové logy,
- vytvorený súbor `POJMY-A-DEFINICIE.md` pre základné pojmy a pracovné definície METODIKY,
- zavedená vlastná merná jednotka elementárneho hodnotenia `[1/0]`,
- doplnený pojem hodnotiaceho záznamu vrátane odpovede, dôkazu, Validácie, Autority Validácie a časovej platnosti,
- vytvorená oddelená štruktúra `OTAZKY/UNIVERZALNE`, `OTAZKY/ATRIBUTOVE` a `OTAZKY/PROJEKTOVE`,
- vytvorený samostatný priestor `HODNOTENIA/` pre opis hodnotiacich záznamov,
- univerzálna objektívna matica presunutá do `OTAZKY/UNIVERZALNE/Objektivita-XY.md`,
- otázky Disciplíny presunuté pod `OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md`,
- vytvorený pracovný logický model databázy v `postupy/2026-07-21_13-52_LOG-MODEL-METODIC.md`,
- zavedená siedma plocha zmyslu života ako metaplocha, ktorá skúma logické vzťahy medzi samostatnými rozmermi `Z` a `T`,
- zavedený pracovný zápis `S = logický vzťah(Z, T)` a rozlíšenie operátorov AND, OR, IF a XOR,
- v `OTAZKY/README.md` definovaná elementárna otázka ako skúmanie jednej podmienky na jednom určenom predmete v jednom určenom význame,
- v `HODNOTENIA/README.md` definovaná elementárna odpoveď, význam hodnoty `0`, stav `nezistené` a minimálne významové zloženie hodnotiaceho záznamu,
- v `OTAZKY/README.md` doplnené rozlišovacie pravidlá a príklady pre otázky rozmerov X, Y, Z a T,
- vytvorený `OTAZKY/SIEDMA-PLOCHA-S.md` ako samostatný metodický koreň pre vznik, definíciu, vyhodnotenie a Validáciu vzťahu S medzi odpoveďami Z a T,
- vytvorený `postupy/2026-07-21_MINIMALNY-LOGICKY-MODEL.md`, ktorý oddeľuje opakovateľné identity, vzťahové udalosti a výsledkové vlastnosti bez predčasného návrhu SQL.

### Zmenené

- koreňové súbory `uQestions.md` a `DISCIPLINA.md` zostávajú ako rozcestníky pre staršie odkazy,
- definície otázok sú významovo oddelené od konkrétnych hodnotiacich záznamov,
- nepresný zápis `Subjektivita = Z × T` bol nahradený zápisom `Subjektivita = (Z, T)`,
- znak `×` v zápisoch rozmerov výslovne označuje spoločné skúmanie samostatných rozmerov, nie predvolený logický operátor AND,
- implikácia `T → Z` alebo `Z → T` sa nesmie zamieňať so súčasnou platnosťou `Z ∧ T`,
- dokument `postupy/2026-07-21_13-52_LOG-MODEL-METODIC.md` bol preradený z pracovného modelu pred SQL na neplatný pracovný návrh určený na revíziu,
- z logického modelu boli odstránené predčasné architektonické tvrdenia; zachované zostalo iba potvrdené jadro hodnotiaceho záznamu, rozmerov a siedmej plochy,
- `README.md` bolo zosúladené so skutočnou štruktúrou repozitára, platnými definíciami a povinným poradím ďalšej práce,
- staršie označenia `Subjektivita — Z/T` a odkazy na pôvodné umiestnenie otázok boli z autoritatívneho prehľadu odstránené.

### Určené

- `local-config.php` sa vytvára v koreni projektu a nie je súčasťou release,
- obslužné PHP skripty sa ukladajú do koreňa `/app`,
- podstatné zmeny sa od tohto bodu zapisujú priebežne do `CHANGELOG.md`,
- objektivita predstavuje prejav v existencii v rovine X/Y,
- subjektivita predstavuje zmysel prejavu skúmaný dvojicou samostatných rozmerov `(Z, T)`,
- `Z` určuje hodnotu a zmysel prejavu,
- `T` určuje čas, v ktorom zmysel platí a nadobúda prioritu,
- logický vzťah medzi `Z` a `T` musí určiť význam konkrétnej otázky; nesmie sa domyslieť automaticky,
- siedma plocha nie je ďalším základným rozmerom popri X, Y, Z a T, ale metaplochou ich logického vzťahu,
- zápis `X × Y = [1/0]^2` vyjadruje dve samostatne hodnotené binárne dimenzie,
- otázka a hodnotiaci záznam nie sú ten istý druh údajov a nesmú sa ukladať v jednom nerozlíšenom priestore,
- elementárna otázka skúma jednu podmienku; ak veta spája viac samostatne pravdivých podmienok, musí sa rozložiť,
- jedna elementárna otázka primárne skúma jeden rozmer X, Y, Z alebo T,
- rozmer otázky neurčuje predmet ani použité podstatné meno, ale druh skutočnosti, ktorú má odpoveď `[1/0]` potvrdiť,
- pri rozlišovaní rozmeru sa skúma, čo sa mení medzi odpoveďou `1` a odpoveďou `0`,
- predmet otázky a rozmer otázky sú odlišné významové údaje,
- gramatická forma otázky ani slová `čo`, `ako`, `koľko` a `kedy` samy osebe neurčujú rozmer,
- rozmer patrí významu elementárnej otázky; konkrétny predmet a čas použitia patria hodnotiacemu záznamu,
- ak kontext zmení druh skutočnosti overovanej odpoveďou, zmenil sa význam otázky alebo je potrebné otázku spresniť,
- samostatná otázka Z ani samostatná otázka T samy osebe nevytvárajú vzťah S; S vzniká až v zloženom hodnotení nad dvoma samostatnými odpoveďami,
- `S` je vopred určené pravidlo zloženia odpovedí `z` a `t`, kým `s` je výsledok `[1/0]` použitia tohto pravidla,
- operátor S aj poradie jeho argumentov sa musia určiť pred získaním výsledkov, nie dodatočne podľa želaného výsledku,
- `S` patrí zloženému hodnoteniu, nie jednej elementárnej otázke,
- pravdivostná tabuľka Validuje logický výpočet `s`, ale sama nevaliduje vstupné odpovede ani oprávnenosť výberu operátora S,
- výsledok `s` sa nesmie zamieňať s výsledkami otázok Z a T ani s konečným rozhodnutím o konaní,
- otázka, predmet, dôkaz a Autorita sú opakovateľné samostatné identity,
- hodnotenie, použitie dôkazu, Validácia a zložené hodnotenie S sú historicky zachytiteľné vzťahové udalosti,
- odpoveď `[1/0]`, stav `nezistené` a výsledok `s[1/0]` nie sú samostatné metodické identity, ale výsledky alebo stavy konkrétnych udalostí,
- hodnotenie je konkrétne použitie otázky na predmet a nie iba technická M:N spojka,
- dvojica `(QUESTION, SUBJECT)` sama osebe nemusí jednoznačne určovať hodnotenie, pretože rovnaká otázka môže byť na rovnaký predmet použitá opakovane v inom čase, rozsahu alebo stave,
- dôkaz a hodnotenie majú významový vzťah M:N; význam použitia dôkazu patrí ich väzbe,
- Validácia je udalosť s vlastnou históriou a nesmie byť nahradená jediným prepísateľným stavom hodnotenia,
- zložené hodnotenie S musí odkazovať na konkrétne vstupné hodnotenia Z a T, nie iba na všeobecné otázky alebo ich aktuálne odpovede,
- odpoveď `0` znamená nepotvrdenie skúmanej podmienky v určenom význame, nie automaticky neznalosť, chýbajúci dôkaz ani absolútnu neexistenciu,
- stav `nezistené` je stavom poznania alebo spracovania, nie treťou pravdivostnou hodnotou podmienky,
- `nezistené` sa nesmie do binárneho rozhodovania potichu previesť na `0` ani `1`,
- pôvodná verzia logického modelu zostáva dostupná v histórii Git ako záznam vývoja, ale nesmie byť použitá ako podklad SQL schémy,
- SQL schéma sa nesmie vytvoriť pred potvrdením elementárnej logiky a otvorených rozhodnutí uvedených v revidovanom logickom modeli.
