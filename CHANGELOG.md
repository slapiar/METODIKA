# Changelog

Tento súbor zaznamenáva **čo sa zmenilo, kedy sa to zmenilo a kde je uložená platná definícia alebo aktuálny pracovný stav**.

CHANGELOG nie je samostatným autoritatívnym zdrojom definícií. Pri rozpore rozhoduje dokument uvedený v odkaze a jeho stav podľa príslušného registra.

---

## 2026-07-22

### Bezpečnosť a prevádzka

- zabezpečený webový setup povinným serverovým tokenom `METODIKA_SETUP_TOKEN`, CSRF ochranou, bezpečnostnými HTTP hlavičkami, požiadavkou HTTPS a automatickým uzamknutím po vytvorení konfigurácie; pozri [`app/setup.php`](app/setup.php),
- vedomé prepísanie existujúcej konfigurácie je možné iba po serverovom nastavení `METODIKA_SETUP_ALLOW_OVERWRITE=1`,
- nezabezpečené HTTP možno použiť iba pri výslovne povolenom lokálnom vývoji cez `METODIKA_SETUP_ALLOW_HTTP=1`.

### Štruktúra a stav dokumentov

- projekt METODIKA bol doplnený do centrálneho registra [`PROJEKTY/ZoznamProjektov.md`](PROJEKTY/ZoznamProjektov.md) s autoritatívnym repozitárom, vetvou, aktuálnym stavom, významovými oblasťami a technickým prostredím,
- strom v [`README.md`](README.md) bol zosúladený s aktuálne evidovaným obsahom vetvy `main`,
- zavedený záväzný register stavov pracovných postupov v [`postupy/README.md`](postupy/README.md),
- zavedený záväzný register stavov pracovných poznámok v [`poznámky/README.md`](poznámky/README.md),
- každý existujúci dokument v `postupy/` a `poznámky/` dostal explicitný stav,
- dokument `postupy/2026-07-21_13-52_LOG-MODEL-METODIC.md` zostáva označený ako `NEPLATNÝ`; ostatné existujúce metodické postupy zostávajú `PRACOVNÉ`, kým nie sú potvrdené a prenesené do autoritatívnych dokumentov,
- vytvorený potvrdený pracovný postup [`postupy/Inicializácia práce.md`](postupy/Inicializácia%20práce.md), ktorý syntetizuje existujúce pravidlá povinnej prípravy pred každou prácou; jeho stav `POTVRDENÝ-NA-PRENESENIE` eviduje [`postupy/README.md`](postupy/README.md).

### Algoritmy otázok

- vytvorený prvý pracovný významový algoritmus [`postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md`](postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md), ktorý odvodzuje kandidáta špecifickej otázky z jednej univerzálnej otázky, logicky vymedzeného SUBJECT-u, účelu, kontextu a doménových pojmov,
- vytvorená pracovná ontológia vstupov [`postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md`](postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md), ktorá odlišuje zdrojovú otázku, SUBJECT a doménové pojmy od určení konkrétneho odvodzovacieho úkonu, jeho výstupov a neskoršieho hodnotenia; stav `PRACOVNÝ` eviduje [`postupy/README.md`](postupy/README.md),
- ontológia vstupov bola po preskúmaní revidovaná: doplnila `ACTOR` a `AUTHORITY_CONTEXT`, oddelila `CONTEXT` od `SCOPE`, zmenila zdrojovú otázku na rolu existujúcej `QUESTION`, odlíšila `DERIVATION_SUBJECT` od budúceho `EVALUATION_SUBJECT-u`, oslabila predčasný status `DOMAIN_TERM`, oddelila `DERIVATION_TRACE` od `DÔKAZU` a metodický úkon od jeho technického záznamu; stav zostáva `PRACOVNÝ`,
- odvodzovací algoritmus bol zosúladený s revidovanou ontológiou: prevzal úplný vstupný a výstupný kontrakt, `ACTOR_REFERENCE`, `AUTHORITY_CONTEXT`, oddelený kontext a rozsah, roly zdrojovej otázky a SUBJECT-u, nové zastavovacie podmienky, auditnú stopu a odlíšenie kandidáta od prijatej otázky a budúceho hodnotenia; stav zostáva `PRACOVNÝ`,
- vytvorená spoločná Validácia [`postupy/2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md`](postupy/2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md), ktorá posúdila ontológiu a algoritmus ako jeden významový celok; výsledok `CONDITIONALLY_VALID` zachováva jadro, ale pred reValidáciou vyžaduje doplniť `INTENDED_APPLICABILITY_SCOPE`, založiť historický záznam pokusu pred vstupnými kontrolami a vytvárať jednotný `DERIVATION_RESULT` pri každom zastavení,
- algoritmus zatiaľ nevykonáva hodnotenie, neodvodzuje odpoveď a neurčuje technickú implementáciu; jeho stav `PRACOVNÝ` eviduje [`postupy/README.md`](postupy/README.md),
- autoritatívnym východiskom zostávajú pravidlá otázok v [`OTAZKY/README.md`](OTAZKY/README.md), univerzálna matica v [`OTAZKY/UNIVERZALNE/Objektivita-XY.md`](OTAZKY/UNIVERZALNE/Objektivita-XY.md) a platné definície v [`POJMY-A-DEFINICIE.md`](POJMY-A-DEFINICIE.md).

### Riadenie zmien

- CHANGELOG bol skrátený na záznam zmien s odkazmi na platné definície a registre stavov,
- do [`README.md`](README.md) bolo doplnené pravidlo, že zmena dokumentu, jeho stavu a záznamu v CHANGELOG tvoria jeden pracovný úkon.

### Integrácia CodeIgniter a hosting

- overený SHA256 a rozbalený balík `codei-git-ready.zip`; do repozitára bol pridaný runtime strom [`codei/`](codei),
- pre CodeIgniter boli doplnené šablóny Apache + PHP-FPM v [`codei/deploy/apache/README.md`](codei/deploy/apache/README.md) a konfigoch v `codei/deploy/apache/`,
- pre Hostinger Business (shared hosting) boli doplnené fallback postupy a šablóny v [`codei/deploy/hostinger/README.md`](codei/deploy/hostinger/README.md),
- pre nasadenie bez zmeny website root boli pridané shim súbory [`codei/index.php`](codei/index.php) a [`codei/.htaccess`](codei/.htaccess), ktoré smerujú požiadavky do `public/`,
- base URL pre tento režim bola zosúladená na [`codei/app/Config/App.php`](codei/app/Config/App.php) s hodnotou `https://dremont.in/codei/public/`,
- vytvorený pracovný architektonický záznam [`postupy/2026-07-22_09-38_CodeIgniter.md`](postupy/2026-07-22_09-38_CodeIgniter.md), ktorý oddeľuje technickú logiku CodeIgniter 4.7.4 od významovej domény METODIKY; jeho stav `PRACOVNÝ` eviduje [`postupy/README.md`](postupy/README.md).

### Skripty spustenia a release

- pôvodná release logika bola presunutá do nového skriptu [`release.sh`](release.sh),
- skript [`startApp.sh`](startApp.sh) bol zmenený na lokálny launcher CodeIgniter servera (`php spark serve`) s otvorením URL v prehliadači,
- do hostinger dokumentácie boli doplnené aj voliteľné no-redirect šablóny pre variant bez HTTP presmerovania.

---

## 2026-07-21

### Základ metodiky

- vytvorený autoritatívny koreň pojmov a rozmerov v [`POJMY-A-DEFINICIE.md`](POJMY-A-DEFINICIE.md),
- potvrdené rozlíšenie `X × Y`, `Z`, `T`, subjektivity `(Z, T)` a vzťahu `S`; platné definície sú v [`POJMY-A-DEFINICIE.md`](POJMY-A-DEFINICIE.md),
- potvrdený pracovný vzorec Autority a zaradenie Disciplíny pod Zodpovednosť; pozri [`AUTORITA.md`](AUTORITA.md),
- oddelené otázky od hodnotiacich záznamov; pravidlá sú v [`OTAZKY/README.md`](OTAZKY/README.md) a [`HODNOTENIA/README.md`](HODNOTENIA/README.md),
- univerzálna objektívna matica presunutá do [`OTAZKY/UNIVERZALNE/Objektivita-XY.md`](OTAZKY/UNIVERZALNE/Objektivita-XY.md),
- otázky Disciplíny presunuté do [`OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md`](OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md),
- vytvorený metodický koreň siedmej plochy v [`OTAZKY/SIEDMA-PLOCHA-S.md`](OTAZKY/SIEDMA-PLOCHA-S.md),
- koreňové súbory [`uQestions.md`](uQestions.md) a [`DISCIPLINA.md`](DISCIPLINA.md) zostali ako rozcestníky pre staršie odkazy.

### Pracovné modely a poznámky

- vznikli pracovné dokumenty v `postupy/` a pracovná poznámka v `poznámky/`; ich aktuálny stav určuje [`postupy/README.md`](postupy/README.md) a [`poznámky/README.md`](poznámky/README.md),
- pôvodný dokument `postupy/2026-07-21_13-52_LOG-MODEL-METODIC.md` bol vyradený ako neplatný pracovný návrh určený na revíziu,
- potvrdené zostáva pravidlo, že databázová schéma ani implementácia nesmú predbehnúť elementárnu logiku a autoritatívne definície; pozri [`README.md`](README.md).

### Obslužný softvér

- vytvorený adresár `app/` a prvá verzia `app/setup.php` na tvorbu lokálneho `local-config.php`,
- zavedené lokálne konfigurácie databáz METODIKY a MAPMET,
- pridaný [`.gitignore`](.gitignore) pre lokálne konfigurácie a prevádzkové súbory.

---

## Pravidlo ďalších zápisov

Každý nový záznam má obsahovať iba:

```text
dátum
× stručnú zmenu
× dotknutý súbor alebo register
× odkaz na miesto platnej definície
× prípadnú zmenu stavu dokumentu
```

Definícia sa v CHANGELOG neopakuje. Zapisuje sa iba odkaz na dokument, ktorý ju nesie.
