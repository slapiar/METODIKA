# Validácia technického návrhu aplikačnej služby odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Validovaný predmet:

```text
TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md
```

Zdroj významu:

```text
postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
postupy/2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md
```

Táto Validácia neposudzuje implementovaný PHP kód, repository adaptéry, databázovú schému, controller ani API.

---

# 1. VALIDATION_EVENT

```text
TARGET:
technický návrh QuestionDerivationApplicationService

ACTOR:
ChatGPT vykonávajúca technicko-metodické preskúmanie na výslovný pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa vytvoriť odvodený technický návrh bez zmeny Validovaného kontraktu;
tento záznam nepredstiera konečnú Autoritu meniť autoritatívne definície METODIKY

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; oddelený technický koreň pred návrhom repository, migrácií a rozhraní

SCOPE:
zodpovednosť služby, technické dátové objekty, porty, poradie vykonania, transakčné hranice, chybové kanály a testovateľnosť
```

---

# 2. Kritériá Validácie

```text
K1 — návrh nemení PARTIAL_RUN_WITH_ATOMIC_GATE
K2 — historický pokus vzniká pred atómovou vstupnou bránou
K3 — REQUEST_REFERENCE a RESPONSE_TARGET_REFERENCE zostávajú zachované
K4 — ACTOR, REQUEST_SOURCE a AUTHORITY zostávajú oddelené
K5 — doménový algoritmus nie je duplikovaný v službe, controlleri ani repository
K6 — BRANCH_DEPENDENCY sa neurčuje voľným technickým odhadom
K7 — každá vetva má samostatný výsledok a krátku technickú transakciu
K8 — zlyhanie neskoršej vetvy nevymaže ukončené nezávislé vetvy
K9 — neúspešná vetva sa neukladá ako QUESTION_CANDIDATE
K10 — deterministická agregácia run_state má jedinú implementačnú zodpovednosť
K11 — metodické zastavenie je oddelené od technickej chyby a odpovede 0
K12 — služba je nezávislá od HTTP, CLI a používateľského rozhrania
K13 — návrh je testovateľný bez databázového a prezentačného adaptéra
K14 — návrh nepredbieha SQL, migrácie, controller ani API
K15 — otvorené technické rozhodnutia sú priznané a nepredstierané ako súčasť kontraktu
```

---

# 3. Výsledky kritérií

```text
K1  = 1
K2  = 1
K3  = 1
K4  = 1
K5  = 1
K6  = 1
K7  = 1
K8  = 1
K9  = 1
K10 = 1
K11 = 1
K12 = 1
K13 = 1
K14 = 1
K15 = 1
```

---

# 4. Zistené obmedzenie

Technický návrh správne rozlišuje:

```text
REQUEST_REFERENCE
= korelačný odkaz
≠ automaticky idempotency key
```

Z Validovaného aplikačného kontraktu nemožno bez ďalšieho technického rozhodnutia odvodiť, čo sa má stať pri opakovanom predložení rovnakej `REQUEST_REFERENCE`.

Možnosti zatiaľ nie sú rozhodnuté:

```text
vrátiť existujúci beh
založiť nový historický pokus s väzbou na pôvodnú požiadavku
odmietnuť duplicitné predloženie
```

Toto obmedzenie nemení zodpovednosť ani rozhranie aplikačnej služby. Blokuje však návrh unikátnych databázových obmedzení, retry správania a verejného API, kým nebude technická politika výslovne určená.

---

# 5. Výsledok Validácie

```text
VALIDATION_RESULT
=
VALID_WITH_LIMITATIONS
```

Význam:

```text
technický návrh verne reprezentuje Validovaný aplikačný kontrakt
+
všetkých pätnásť kritérií technického návrhu je splnených
+
možno navrhovať triedy služby, dátové objekty a porty
+
pred návrhom repository obmedzení a API retry správania treba rozhodnúť politiku opakovanej REQUEST_REFERENCE
```

Výsledok neValiduje budúcu implementáciu ani databázový model.

---

# 6. Stav a nasledujúci krok

Technický návrh aj táto Validácia zostávajú:

```text
PRACOVNÝ
```

Nasledujúci logický krok:

```text
rozhodnúť technickú politiku opakovanej REQUEST_REFERENCE
→ reValidovať dotknutú technickú hranicu
→ odvodiť repository kontrakty a technický model uloženia
→ až potom navrhnúť migrácie, adaptéry, controller a API
```