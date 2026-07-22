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

---

## Pravidlo aktualizácie

Pri vytvorení, významovej zmene, presunutí alebo implementovaní technického návrhu sa v tom istom pracovnom kroku aktualizuje:

```text
TECHNICKE-NAVRHY/README.md
CHANGELOG.md
príslušný metodický alebo technický odkaz, ak sa zmenil zdroj či náhrada
```

Technický stav dokumentu nie je Validáciou ontologického alebo metodického významu.
