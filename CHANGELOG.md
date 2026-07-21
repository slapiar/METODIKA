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
- otázky Disciplíny presunuté pod `OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md`.

### Zmenené

- koreňové súbory `uQestions.md` a `DISCIPLINA.md` zostávajú ako rozcestníky pre staršie odkazy,
- definície otázok sú významovo oddelené od konkrétnych hodnotiacich záznamov.

### Určené

- `local-config.php` sa vytvára v koreni projektu a nie je súčasťou release,
- obslužné PHP skripty sa ukladajú do koreňa `/app`,
- podstatné zmeny sa od tohto bodu zapisujú priebežne do `CHANGELOG.md`,
- objektivita predstavuje prejav v existencii v rovine X/Y,
- subjektivita predstavuje hodnotu a časový zmysel prejavu v rovine Z/T,
- zápis `X × Y = [1/0]^2` vyjadruje dve samostatne hodnotené binárne dimenzie,
- zápisy `[1²]`, `[1³]` a `[1⁴]` v `README.md` boli zjednotené na `[1/0]^2`, `[1/0]^3` a `[1/0]^4`,
- otázka a hodnotiaci záznam nie sú ten istý druh údajov a nesmú sa ukladať v jednom nerozlíšenom priestore.
