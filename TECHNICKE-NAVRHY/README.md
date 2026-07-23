# Register technických návrhov

## Účel

Adresár `TECHNICKE-NAVRHY/` je samostatný pracovný koreň technickej architektúry projektu METODIKA.

Obsahuje:

```text
technické prostredia
aplikačné služby a use cases
technické dátové objekty
rozhrania a porty
transakčné hranice
repository kontrakty
návrhy migrácií, controllerov a API
technické testovacie stratégie
```

Neobsahuje a nesmie nahrádzať:

```text
ontologické definície
metodické významy
pravidlá otázok
význam odpovedí 1 a 0
Autoritu
Validáciu
pravidlá doménových algoritmov
```

Základné rozlíšenie:

```text
postupy/
= významové, metodické a validačné pracovné dokumenty

TECHNICKE-NAVRHY/
= technická reprezentácia už odvodených a Validovaných významových vzťahov

codei/
= vykonateľná implementácia
```

Technický návrh nesmie spätne meniť Validovaný aplikačný kontrakt. Ak technická realizácia odhalí významový nesúlad, práce sa musia vrátiť ku kontraktu a vytvoriť novú metodickú udalosť; význam sa nesmie opraviť potichu v kóde.

---

## Povolené stavy

```text
PRACOVNÝ
VALIDOVANÝ-NA-IMPLEMENTÁCIU
ČIASTOČNE-IMPLEMENTOVANÝ
IMPLEMENTOVANÝ
PREKONANÝ
NEPLATNÝ
ARCHIVOVANÝ
```

Význam:

- `PRACOVNÝ` — otvorený technický návrh bez oprávnenia na automatickú implementáciu,
- `VALIDOVANÝ-NA-IMPLEMENTÁCIU` — návrh spĺňa určené technické kritériá a môže byť podkladom implementácie v určenom rozsahu,
- `ČIASTOČNE-IMPLEMENTOVANÝ` — iba časť návrhu bola prenesená do kódu,
- `IMPLEMENTOVANÝ` — návrh bol prenesený do kódu a výsledný stav bol overený,
- `PREKONANÝ` — nahradený novším technickým návrhom,
- `NEPLATNÝ` — nesmie byť použitý ako podklad implementácie,
- `ARCHIVOVANÝ` — zachovaný iba ako historický záznam.

---

## Aktuálny register

| Dokument | Stav | Zdroj významu alebo poznámka |
|---|---|---|
| `2026-07-22_CODEIGNITER-AKO-TECHNICKE-PROSTREDIE.md` | PRACOVNÝ | Aktívny technický základ CodeIgnitera 4.7.4. Význam domény preberá z autoritatívnych a Validovaných metodických dokumentov. Nahrádza aktívne použitie historického dokumentu `postupy/2026-07-22_09-38_CodeIgniter.md`. |
| `2026-07-22_WEBOVA-DIAGNOSTIKA-PRODUKCNEJ-DATABAZY.md` | ČIASTOČNE-IMPLEMENTOVANÝ | Zabezpečená webová DB diagnostika s POST token gate, session autorizáciou, CSRF ochranou, no-cache hlavičkami a zdieľanou službou pre CLI aj web bez migrácií. |
| `2026-07-22_VALIDACIA-WEBOVEJ-DIAGNOSTIKY-PRODUKCNEJ-DATABAZY.md` | PRACOVNÝ | Validácia webovej diagnostiky: disabled/wrong/correct token flow, čítací režim, neindexovateľnosť a spoločná služba s CLI. |
| `2026-07-22_CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md` | ČIASTOČNE-IMPLEMENTOVANÝ | Trvalá konfigurácia vývojového kontajnera cez `.devcontainer/` s idempotentným rebuildom aktívneho PHP 8.4.15 na variant s `intl` a `mysqli` bez prechodu na systémové `/usr/bin/php`. |
| `2026-07-22_VALIDACIA-CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md` | PRACOVNÝ | Validácia výsledku konfigurácie Codespaces runtime; aktívny CLI PHP po zásahu načíta `intl` a `mysqli`, diagnostický príkaz DB je dostupný, no v tomto kroku bez externého env zlyhal na bezpečnej chybe pripojenia. |
| `2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Aktualizovaný návrh služby. Validovaná replay politika je premietnutá cez `RequestReplayGuard`; spoločná Validácia s repository kontraktom skončila `VALID`. |
| `2026-07-22_VALIDACIA-APLIKACNEJ-SLUZBY-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Historická Validácia pôvodného technického návrhu služby s výsledkom `VALID_WITH_LIMITATIONS`; zachováva vtedy otvorenú replay politiku. |
| `2026-07-22_POLITIKA-OPAKOVANEJ-REQUEST-REFERENCE.md` | PRACOVNÝ | Technická politika `IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE`: jedna konkrétna požiadavka vytvára najviac jeden `QUESTION_DERIVATION`; replay používa existujúci beh a odlišný obsah vytvára technický konflikt. |
| `2026-07-22_VALIDACIA-POLITIKY-OPAKOVANEJ-REQUEST-REFERENCE.md` | PRACOVNÝ | Validácia replay politiky s výsledkom `VALID`. |
| `2026-07-22_REPOSITORY-KONTRAKT-REQUEST-REFERENCE.md` | PRACOVNÝ | Repository kontrakt atómovej rezervácie, vyhľadania a korelácie `REQUEST_REFERENCE`; neurčuje SQL schému ani adaptér. Spoločná Validácia skončila `VALID`. |
| `2026-07-22_VALIDACIA-REPOSITORY-KONTRAKTU-REQUEST-REFERENCE.md` | PRACOVNÝ | Spoločná Validácia aktualizovanej služby a repository kontraktu. Výsledok `VALID`; umožnila odvodiť technický model uloženia. |
| `2026-07-22_TECHNICKY-MODEL-ULOZENIA-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Technický model oddeleného uloženia rezervácie, behu, vetiev, závislostí, kandidátov, finálneho výsledku a auditnej stopy. Validácia skončila `VALID`; neurčuje SQL schému ani migrácie. |
| `2026-07-22_VALIDACIA-TECHNICKEHO-MODELU-ULOZENIA-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Validácia technického modelu uloženia. Všetkých pätnásť kritérií je splnených; umožnila odvodiť databázový návrh a migračné obmedzenia. |
| `2026-07-22_DATABAZOVY-NAVRH-A-MIGRACNE-OBMEDZENIA-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Fyzický návrh ôsmich tabuliek pre MySQLi/InnoDB, rozdelenie databázových a aplikačných invariantov a migračné poradie M1 až M8. Validácia skončila `VALID`; konkrétny server treba pred migráciami prakticky overiť. |
| `2026-07-22_VALIDACIA-DATABAZOVEHO-NAVRHU-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Validácia databázového návrhu a migračných obmedzení. Všetkých pätnásť kritérií je splnených; po overení servera možno vytvoriť CodeIgniter migrácie. |
| `2026-07-22_BEZPECNA-DATABAZOVA-KONFIGURACIA.md` | ČIASTOČNE-IMPLEMENTOVANÝ | Aktuálny kontrakt používa externý súrodenecký `private/metodika.env`, loader pred webovým aj CLI bootstrapom a sledovaný kód bez tajomstiev. Heslo bolo podľa potvrdenia používateľa zrotované. |
| `2026-07-22_VALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md` | PRACOVNÝ | Historická Validácia sanitizácie aktuálnej vetvy s výsledkom `VALID_WITH_LIMITATIONS`; zachováva stav pred potvrdenou rotáciou a externým private env. |
| `2026-07-22_REVALIDACIA-BEZPECNEJ-DATABAZOVEJ-KONFIGURACIE.md` | PRACOVNÝ | ReValidácia po rotácii a presunutí konfigurácie mimo `/codei`; výsledok `VALID_WITH_LIMITATIONS`, pretože serverové pripojenie a práva externého súboru ešte neboli prakticky overené. |
| `2026-07-22_IMPLEMENTACIA-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md` | ČIASTOČNE-IMPLEMENTOVANÝ | Externý env loader, diagnostika a migrácie M1 až M8 sú implementované; migrácie aj fyzická schéma boli 2026-07-23 prakticky potvrdené na Hostingeri. Otvorené zostávajú repository adaptéry a integračné testy. |
| `2026-07-22_VALIDACIA-IMPLEMENTACIE-EXTERNEHO-ENV-DIAGNOSTIKY-A-MIGRACII.md` | PRACOVNÝ | Historická statická Validácia implementácie s výsledkom `VALID_WITH_LIMITATIONS`; zachováva stav pred praktickou diagnostikou a vykonaním schémy. |
| `2026-07-23_REVALIDACIA-VYKONANIA-MIGRACII-M1-M8.md` | PRACOVNÝ | Praktická reValidácia vykonania migrácií a fyzickej schémy na Hostingeri. Výsledok `VALID_WITH_LIMITATIONS`; 8 tabuliek a 10 reštriktívnych cudzích kľúčov je potvrdených, repository adaptéry a integračné testy ešte chýbajú. |

---

## Pravidlo aktualizácie

Pri vytvorení, významovej zmene, presunutí alebo implementovaní technického návrhu sa v tom istom pracovnom kroku aktualizuje:

```text
TECHNICKE-NAVRHY/README.md
CHANGELOG.md
príslušný metodický alebo technický odkaz, ak sa zmenil zdroj či náhrada
```

Technický stav dokumentu nie je Validáciou ontologického alebo metodického významu.
