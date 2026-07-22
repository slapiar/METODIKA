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

- strom v [`README.md`](README.md) bol zosúladený s aktuálne evidovaným obsahom vetvy `main`,
- zavedený záväzný register stavov pracovných postupov v [`postupy/README.md`](postupy/README.md),
- zavedený záväzný register stavov pracovných poznámok v [`poznámky/README.md`](poznámky/README.md),
- každý existujúci dokument v `postupy/` a `poznámky/` dostal explicitný stav,
- dokument `postupy/2026-07-21_13-52_LOG-MODEL-METODIC.md` zostáva označený ako `NEPLATNÝ`; ostatné existujúce metodické postupy zostávajú `PRACOVNÉ`, kým nie sú potvrdené a prenesené do autoritatívnych dokumentov.

### Riadenie zmien

- CHANGELOG bol skrátený na záznam zmien s odkazmi na platné definície a registre stavov,
- do [`README.md`](README.md) bolo doplnené pravidlo, že zmena dokumentu, jeho stavu a záznamu v CHANGELOG tvoria jeden pracovný úkon.

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
