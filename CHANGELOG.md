# Changelog

Tento súbor zaznamenáva **čo sa zmenilo, kedy sa to zmenilo a kde je uložená platná definícia alebo aktuálny pracovný stav**.

CHANGELOG nie je samostatným autoritatívnym zdrojom definícií. Pri rozpore rozhoduje dokument uvedený v odkaze a jeho stav podľa príslušného registra.

---

## 2026-07-22

### Bezpečnosť a prevádzka

- zabezpečený webový setup povinným serverovým tokenom `METODIKA_SETUP_TOKEN`, CSRF ochranou, bezpečnostnými HTTP hlavičkami, požiadavkou HTTPS a automatickým uzamknutím po vytvorení konfigurácie; pozri [`app/setup.php`](app/setup.php),
- vedomé prepísanie existujúcej konfigurácie je možné iba po serverovom nastavení `METODIKA_SETUP_ALLOW_OVERWRITE=1`,
- nezabezpečené HTTP možno použiť iba pri výslovne povolenom lokálnom vývoji cez `METODIKA_SETUP_ALLOW_HTTP=1`,
- z [`codei/app/Config/Database.php`](codei/app/Config/Database.php) boli odstránené commitnuté databázové prihlasovacie údaje; sledovaný súbor obsahuje iba neškodný konfiguračný tvar a testovaciu SQLite skupinu,
- vytvorená verejná šablóna [`codei/.env.example`](codei/.env.example) bez platných tajomstiev,
- používateľ potvrdil rotáciu pôvodného databázového hesla a vytvorenie externého súboru `private/metodika.env` mimo `/codei`,
- implementovaný [`codei/app/Config/ExternalEnvironment.php`](codei/app/Config/ExternalEnvironment.php), ktorý načítava externé prostredie pred webovým aj CLI bootstrapom; prednosť má `METODIKA_ENV_FILE`,
- bezpečnostný kontrakt bol aktualizovaný v [`TECHNICKE-NAVRHY/2026-07-22_BEZPECNA-DATABAZOVA-KONFIGURACIA.md`](TECHNICKE-NAVRHY/2026-07-22_BEZPECNA-DATABAZOVA-KONFIGURACIA.md),
- pôvodná Validácia sanitizácie zostáva historicky zachovaná v [`TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md`](TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md),
- nová reValidácia po rotácii a externom env je v [`TECHNICKE-NAVRHY/2026-07-22_REVALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md`](TECHNICKE-NAVRHY/2026-07-22_REVALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md) s výsledkom `VALID_WITH_LIMITATIONS`; praktické serverové pripojenie zostáva nepotvrdené.

### Štruktúra a stav dokumentov

- projekt METODIKA bol doplnený do centrálneho registra [`PROJEKTY/ZoznamProjektov.md`](PROJEKTY/ZoznamProjektov.md) s autoritatívnym repozitárom, vetvou, aktuálnym stavom, významovými oblasťami a technickým prostredím,
- strom v [`README.md`](README.md) bol zosúladený s aktuálne evidovaným obsahom vetvy `main`,
- zavedený záväzný register stavov pracovných postupov v [`postupy/README.md`](postupy/README.md),
- zavedený záväzný register stavov pracovných poznámok v [`poznámky/README.md`](poznámky/README.md),
- zavedený osobitný koreň technickej architektúry [`TECHNICKE-NAVRHY/`](TECHNICKE-NAVRHY) s vlastným registrom [`TECHNICKE-NAVRHY/README.md`](TECHNICKE-NAVRHY/README.md),
- každý existujúci dokument v `postupy/` a `poznámky/` dostal explicitný stav,
- dokument `postupy/2026-07-21_13-52_LOG-MODEL-METODIC.md` zostáva označený ako `NEPLATNÝ`; ostatné existujúce metodické postupy zostávajú `PRACOVNÉ`, kým nie sú potvrdené a prenesené do autoritatívnych dokumentov,
- historický technický dokument [`postupy/2026-07-22_09-38_CodeIgniter.md`](postupy/2026-07-22_09-38_CodeIgniter.md) bol v registri označený `PREKONANÝ`; aktívna technická náhrada je v [`TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md`](TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md),
- vytvorený potvrdený pracovný postup [`postupy/Inicializácia práce.md`](postupy/Inicializácia%20práce.md), ktorého stav `POTVRDENÝ-NA-PRENESENIE` eviduje [`postupy/README.md`](postupy/README.md).

### Algoritmy otázok

- vytvorený prvý pracovný významový algoritmus [`postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md`](postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md),
- vytvorená a revidovaná pracovná ontológia vstupov [`postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md`](postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md),
- vytvorená pôvodná spoločná Validácia [`postupy/2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md`](postupy/2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md) s historickým výsledkom `CONDITIONALLY_VALID`,
- v odvodzovacom algoritme boli opravené tri blokujúce body: doplnený `INTENDED_APPLICABILITY_SCOPE`, pokus o `QUESTION_DERIVATION` sa zaznamenáva pred kontrolami a všetky zastavenia používajú jednotný `DERIVATION_RESULT` s auditnou stopou,
- vytvorená spoločná reValidácia [`postupy/2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md`](postupy/2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md) s výsledkom `VALID_WITH_LIMITATIONS`,
- vytvorený prvý aplikačný kontrakt [`postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md`](postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md) s topológiou `PARTIAL_RUN_WITH_ATOMIC_GATE`,
- vytvorená pôvodná Validácia kontraktu [`postupy/2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md`](postupy/2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md) s historickým výsledkom `CONDITIONALLY_VALID`,
- aplikačný kontrakt bol opravený o koreláciu `REQUEST_REFERENCE → QUESTION_DERIVATION → DERIVATION_RUN_RESULT → RESPONSE_TARGET_REFERENCE`, významový model `BRANCH_DEPENDENCY` a úplné deterministické pravidlo agregácie nadradeného `run_state`,
- vytvorená reValidácia [`postupy/2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md`](postupy/2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md) s výsledkom `VALID`,
- aplikačný kontrakt, jeho pôvodná Validácia aj reValidácia zostávajú `PRACOVNÉ`; stav eviduje [`postupy/README.md`](postupy/README.md),
- autoritatívnym východiskom zostávajú pravidlá otázok v [`OTAZKY/README.md`](OTAZKY/README.md), univerzálna matica v [`OTAZKY/UNIVERZALNE/Objektivita-XY.md`](OTAZKY/UNIVERZALNE/Objektivita-XY.md) a platné definície v [`POJMY-A-DEFINICIE.md`](POJMY-A-DEFINICIE.md).

### Technické návrhy

- vytvorený aktívny technický základ [`TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md`](TECHNICKE-NAVRHY/2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md),
- vytvorený návrh [`TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md`](TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md) pre `QuestionDerivationApplicationService`,
- vytvorená replay politika a jej Validácia s rozhodnutím `IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE`,
- vytvorený repository kontrakt `REQUEST_REFERENCE` a spoločná Validácia s výsledkom `VALID`,
- vytvorený technický model uloženia a jeho Validácia s výsledkom `VALID`,
- vytvorený databázový návrh ôsmich tabuliek a jeho Validácia s výsledkom `VALID`,
- implementovaný bezpečný diagnostický príkaz [`codei/app/Commands/VerifyDatabaseCapabilities.php`](codei/app/Commands/VerifyDatabaseCapabilities.php), ktorý bez výpisu tajomstiev overuje verziu servera, InnoDB, `utf8mb4_bin` a `DATETIME(6)`,
- vytvorené CodeIgniter migrácie M1 až M8 v [`codei/app/Database/Migrations/`](codei/app/Database/Migrations), zatiaľ bez spustenia,
- implementačný stav je zaznamenaný v [`TECHNICKE-NAVRHY/2026-07-22_IMPLEMENTACIA-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md`](TECHNICKE-NAVRHY/2026-07-22_IMPLEMENTACIA-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md),
- statická Validácia implementácie je v [`TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-IMPLEMENTACIE-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md`](TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-IMPLEMENTACIE-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md) s výsledkom `VALID_WITH_LIMITATIONS`; server a vykonaná schéma zostávajú nepotvrdené,
- aktuálny stav technických dokumentov eviduje [`TECHNICKE-NAVRHY/README.md`](TECHNICKE-NAVRHY/README.md).

### Riadenie zmien

- CHANGELOG bol skrátený na záznam zmien s odkazmi na platné definície a registre stavov,
- do [`README.md`](README.md) bolo doplnené pravidlo, že zmena dokumentu, jeho stavu a záznamu v CHANGELOG tvoria jeden pracovný úkon.

### Integrácia CodeIgniter a hosting

- overený SHA256 a rozbalený balík `codei-git-ready.zip`; do repozitára bol pridaný runtime strom [`codei/`](codei),
- pre CodeIgniter boli doplnené šablóny Apache + PHP-FPM v [`codei/deploy/apache/README.md`](codei/deploy/apache/README.md) a konfigoch v `codei/deploy/apache/`,
- pre Hostinger Business boli doplnené fallback postupy a šablóny v [`codei/deploy/hostinger/README.md`](codei/deploy/hostinger/README.md),
- pre nasadenie bez zmeny website root boli pridané shim súbory [`codei/index.php`](codei/index.php) a [`codei/.htaccess`](codei/.htaccess), ktoré smerujú požiadavky do `public/`,
- base URL bola zosúladená v [`codei/app/Config/App.php`](codei/app/Config/App.php),
- pôvodný pracovný architektonický záznam v `postupy/` zostáva zachovaný historicky.

### Skripty spustenia a release

- pôvodná release logika bola presunutá do nového skriptu [`release.sh`](release.sh),
- skript [`startApp.sh`](startApp.sh) bol zmenený na lokálny launcher CodeIgniter servera,
- do hostinger dokumentácie boli doplnené voliteľné no-redirect šablóny,
- doplnená explicitná Codespaces konfigurácia [`.devcontainer/devcontainer.json`](.devcontainer/devcontainer.json) s `postCreateCommand` na technické zabezpečenie PHP runtime,
- vytvorený idempotentný skript [`.devcontainer/setup-php-extensions.sh`](.devcontainer/setup-php-extensions.sh), ktorý pre aktívny `/usr/local/php` rebuildne rovnakú verziu PHP s `intl` a `mysqli` a prepne symlink `current` bez prechodu na systémové `/usr/bin/php`,
- technický záznam je v [`TECHNICKE-NAVRHY/2026-07-22_CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md`](TECHNICKE-NAVRHY/2026-07-22_CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md),
- Validácia výsledku je v [`TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md`](TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md).
- doplnený bezpečný webový diagnostický endpoint databázy cez [`codei/app/Controllers/DiagnosticsController.php`](codei/app/Controllers/DiagnosticsController.php) a views v [`codei/app/Views/diagnostics/`](codei/app/Views/diagnostics),
- diagnostika je gated serverovými premennými `METODIKA_DIAGNOSTICS_ENABLED` a `METODIKA_DIAGNOSTICS_TOKEN`, používa POST token overenie s `hash_equals`, krátkodobú session autorizáciu, CSRF filter a no-cache/noindex hlavičky,
- SQL capability logika je zdieľaná v [`codei/app/Services/DatabaseCapabilityInspector.php`](codei/app/Services/DatabaseCapabilityInspector.php) a používaná súčasne CLI príkazom [`codei/app/Commands/VerifyDatabaseCapabilities.php`](codei/app/Commands/VerifyDatabaseCapabilities.php) aj webovým controllerom,
- explicitné routes sú v [`codei/app/Config/Routes.php`](codei/app/Config/Routes.php) s vypnutým auto-routingom,
- do [`codei/.env.example`](codei/.env.example) boli doplnené bezpečné placeholdery pre diagnostics gate bez commitu reálneho tokenu,
- minimálne testy boli doplnené v [`codei/tests/session/DiagnosticsControllerTest.php`](codei/tests/session/DiagnosticsControllerTest.php) a [`codei/tests/unit/DatabaseCapabilityInspectorTest.php`](codei/tests/unit/DatabaseCapabilityInspectorTest.php),
- technický záznam je v [`TECHNICKE-NAVRHY/2026-07-22_WEBOVA-DIAGNOSTIKA-PRODUKCNEJ-DATABAZY.md`](TECHNICKE-NAVRHY/2026-07-22_WEBOVA-DIAGNOSTIKA-PRODUKCNEJ-DATABAZY.md),
- Validácia je v [`TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-WEBOVEJ-DIAGNOSTIKY-PRODUKCNEJ-DATABAZY.md`](TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-WEBOVEJ-DIAGNOSTIKY-PRODUKCNEJ-DATABAZY.md) s výsledkom `VALID_WITH_LIMITATIONS`.

---

## 2026-07-21

### Základ metodiky

- vytvorený autoritatívny koreň pojmov a rozmerov v [`POJMY-A-DEFINICIE.md`](POJMY-A-DEFINICIE.md),
- potvrdené rozlíšenie `X × Y`, `Z`, `T`, subjektivity `(Z, T)` a vzťahu `S`,
- potvrdený pracovný vzorec Autority a zaradenie Disciplíny pod Zodpovednosť,
- oddelené otázky od hodnotiacich záznamov,
- univerzálna objektívna matica presunutá do [`OTAZKY/UNIVERZALNE/Objektivita-XY.md`](OTAZKY/UNIVERZALNE/Objektivita-XY.md),
- otázky Disciplíny presunuté do [`OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md`](OTAZKY/ATRIBUTOVE/ZODPOVEDNOST/Disciplina.md),
- vytvorený metodický koreň siedmej plochy v [`OTAZKY/SIEDMA-PLOCHA-S.md`](OTAZKY/SIEDMA-PLOCHA-S.md),
- koreňové súbory [`uQestions.md`](uQestions.md) a [`DISCIPLINA.md`](DISCIPLINA.md) zostali ako rozcestníky.

### Pracovné modely a poznámky

- vznikli pracovné dokumenty v `postupy/` a pracovná poznámka v `poznámky/`,
- pôvodný dokument `postupy/2026-07-21_13-52_LOG-MODEL-METODIC.md` bol vyradený ako neplatný,
- potvrdené zostáva pravidlo, že databázová schéma ani implementácia nesmú predbehnúť elementárnu logiku.

### Obslužný softvér

- vytvorený adresár `app/` a prvá verzia `app/setup.php`,
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
