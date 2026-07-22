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
| `2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md` | PRACOVNÝ | Odvodený technický návrh služby z Validovaného kontraktu `postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md`. Neobsahuje implementáciu ani databázovú schému. |

---

## Pravidlo aktualizácie

Pri vytvorení, významovej zmene, presunutí alebo implementovaní technického návrhu sa v tom istom pracovnom kroku aktualizuje:

```text
TECHNICKE-NAVRHY/README.md
CHANGELOG.md
príslušný metodický alebo technický odkaz, ak sa zmenil zdroj či náhrada
```

Technický stav dokumentu nie je Validáciou ontologického alebo metodického významu.