# Validácia databázového návrhu odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Validovaný predmet:

```text
TECHNICKE-NAVRHY/2026-07-22_DATABAZOVY-NAVRH-A-MIGRACNE-OBMEDZENIA-ODVODZOVANIA-OTAZOK.md
```

Podklady:

```text
TECHNICKE-NAVRHY/2026-07-22_TECHNICKY-MODEL-ULOZENIA-ODVODZOVANIA-OTAZOK.md
TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-TECHNICKEHO-MODELU-ULOZENIA-ODVODZOVANIA-OTAZOK.md
TECHNICKE-NAVRHY/2026-07-22_REPOSITORY-KONTRAKT-REQUEST-REFERENCE.md
```

Táto Validácia neposudzuje vytvorené migrácie, skutočný databázový server, repository adaptéry ani produkčné údaje.

---

# 1. VALIDATION_EVENT

```text
TARGET:
databázový návrh ôsmich fyzických tabuliek a migračných obmedzení

ACTOR:
ChatGPT vykonávajúca technicko-metodické preskúmanie na výslovný pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa pokračovať podľa Inicializácie práce;
tento záznam nemení autoritatívne definície METODIKY

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; posledný návrhový krok pred CodeIgniter migráciami

SCOPE:
tabuľky, stĺpce, identity, cudzie a unikátne kľúče, migračné poradie, transakčné hranice a rozdelenie databázových a aplikačných invariantov
```

---

# 2. Kritériá

```text
K1 — návrh reprezentuje všetky záznamové skupiny Validovaného modelu
K2 — interné identity nenahrádzajú metodické referencie
K3 — jedna REQUEST_REFERENCE má najviac jednu rezerváciu a jeden beh
K4 — rezervácia a beh majú jednoznačnú väzbu cez reservation_id
K5 — STOPPED_AT_GATE sa nezamieňa s vetvou ani kandidátom
K6 — každá vetva patrí existujúcemu behu a zostáva samostatne potvrdená
K7 — jedna vetva má najviac jedného kandidáta
K8 — závislosť zachováva celý citovateľný obsah
K9 — jeden beh má najviac jeden finálny výsledok
K10 — databáza ani trigger neodvodzujú run_state alebo branch_state
K11 — auditná stopa má stabilné poradie a nepredstiera dôkaz ani Validáciu
K12 — referenčné obmedzenia zakazujú kaskádové mazanie histórie
K13 — migračné poradie rešpektuje rodičovské a závislé tabuľky
K14 — významové invarianty zostávajú aplikačnej vrstve a testom
K15 — návrh priznáva neoverenú verziu servera, ochranu údajov a retenciu
```

---

# 3. Výsledky

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

# 4. Overenie fyzického rozdelenia

Návrh používa samostatné tabuľky pre:

```text
rezervácie,
behy,
doménové termíny,
vetvy,
závislosti,
kandidátov,
finálne výsledky,
auditnú stopu.
```

Technický model sa nezredukoval na jeden prepisovaný dokument ani na jednu nejednoznačnú tabuľku.

---

# 5. Overenie požiadavky a behu

Fyzická väzba:

```text
question_derivation_request_reservations.id
→ question_derivation_runs.reservation_id
```

používa:

```text
UNIQUE (reservation_id)
FOREIGN KEY ... ON DELETE RESTRICT ON UPDATE RESTRICT
```

Databáza vynúti najviac jeden beh na rezerváciu. Aplikačná transakcia overí zhodu `request_reference` a `derivation_reference` s rezerváciou. Nevzniká dvojznačná dvojitá cudzia väzba.

---

# 6. Overenie PARTIAL RUN

Každá vetva sa ukladá vo vlastnej krátkej transakcii spolu s prípadnými závislosťami, kandidátom a trace záznamom.

Návrh nevyžaduje jednu transakciu celého behu. Potvrdená nezávislá vetva preto nezanikne pri zlyhaní neskoršej vetvy.

---

# 7. Overenie kandidáta a závislosti

```text
UNIQUE (branch_id)
```

v tabuľke kandidátov zabezpečuje najviac jedného kandidáta na vetvu.

Významové invarianty:

```text
CANDIDATE_CREATED ↔ práve jeden úplný kandidát
BLOCKED_BY_DEPENDENCY → najmenej jedna citovateľná závislosť
```

zostávajú atómovým aplikačným pravidlom. Databázový návrh však uchováva všetky potrebné údaje na ich kontrolu.

---

# 8. Overenie finálneho výsledku

```text
UNIQUE (run_id)
```

zabezpečuje najviac jeden finálny výsledok na beh.

Databáza ani trigger nesmú skladať `run_state` z počtov vetiev. Počty sú kontrolným obrazom už vykonanej doménovej agregácie.

---

# 9. Overenie histórie a migrácií

Historické cudzie väzby používajú:

```text
ON DELETE RESTRICT alebo NO ACTION
ON UPDATE RESTRICT
```

Zakázané zostáva:

```text
ON DELETE CASCADE
REPLACE INTO
prepisovanie nemenných referencií
fyzické mazanie ako bežná oprava
```

Migračné poradie M1 až M8 vytvára rodičovské tabuľky pred závislými. Opačné rollback poradie je konzistentné iba pre prázdne alebo riadene spravované vývojové prostredie.

---

# 10. Otvorené predpoklady

Pred vytvorením a spustením migrácií treba overiť:

```text
konkrétny databázový server a jeho verziu,
podporu InnoDB,
podporu utf8mb4 a utf8mb4_bin,
podporu DATETIME(6),
správanie cudzích kľúčov v hostiteľskom prostredí.
```

Pred produkčným uložením citlivých snapshotov treba určiť ochranu údajov, retenciu, archiváciu, anonymizáciu a zálohovanie.

Tieto body nemenia vnútornú konzistenciu návrhu, ale obmedzujú oprávnenosť produkčného nasadenia.

---

# 11. Výsledok Validácie

```text
VALIDATION_RESULT
=
VALID
```

Význam:

```text
databázový návrh spĺňa všetkých pätnásť kritérií
+
verne reprezentuje Validovaný technický model uloženia
+
jednoznačne rozdeľuje databázové a aplikačné invarianty
+
po praktickom overení databázového servera možno vytvoriť migrácie M1 až M8
```

`VALID` neValiduje budúce migračné súbory, skutočný server, vykonanú schému, repository adaptéry ani produkčné nasadenie.

---

# 12. Nasledujúci logický krok

```text
prakticky overiť databázový server a jeho verziu
→ overiť InnoDB, utf8mb4, utf8mb4_bin a DATETIME(6)
→ vytvoriť CodeIgniter migrácie M1 až M8
→ preskúšať migrácie
→ vytvoriť repository adaptéry
→ vykonať integračné testy
→ Validovať implementovaný stav
```
