# Validácia technického modelu uloženia odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Validovaný predmet:

```text
TECHNICKE-NAVRHY/2026-07-22_TECHNICKY-MODEL-ULOZENIA-ODVODZOVANIA-OTAZOK.md
```

Podklady:

```text
postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md
TECHNICKE-NAVRHY/2026-07-22_POLITIKA-OPAKOVANEJ-REQUEST-REFERENCE.md
TECHNICKE-NAVRHY/2026-07-22_REPOSITORY-KONTRAKT-REQUEST-REFERENCE.md
TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-REPOSITORY-KONTRAKTU-REQUEST-REFERENCE.md
```

Táto Validácia neposudzuje SQL schému, migrácie, CodeIgniter Models, repository adaptéry ani programový kód.

---

# 1. VALIDATION_EVENT

```text
TARGET:
technický model uloženia rezervácie, behov, vetiev, závislostí, kandidátov, výsledkov a auditnej stopy

ACTOR:
ChatGPT vykonávajúca technicko-metodické preskúmanie na výslovný pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa pokračovať podľa Inicializácie práce;
tento záznam nepredstiera konečnú Autoritu meniť autoritatívne definície METODIKY

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; posledný návrhový krok pred databázovým návrhom a migráciami

SCOPE:
záznamové skupiny, kardinality, invarianty, transakčné hranice, nemennosť, auditná stopa a hranice neodvodených implementačných rozhodnutí
```

---

# 2. Kritériá Validácie

```text
K1 — model zachováva jednu REQUEST_REFERENCE pre najviac jeden QUESTION_DERIVATION
K2 — rezervácia a historický beh vznikajú v jednej konzistentnej prvej hranici
K3 — model zachováva PARTIAL_RUN_WITH_ATOMIC_GATE
K4 — STOPPED_AT_GATE neumožňuje vetvy ani kandidátov
K5 — každá vetva patrí práve jednému behu a má vlastný výsledok
K6 — úspešné nezávislé vetvy sa ukladajú oddelene a nezaniknú pri zlyhaní inej vetvy
K7 — CANDIDATE_CREATED vyžaduje práve jedného úplného kandidáta
K8 — neúspešné vetvové stavy nesmú mať kandidáta
K9 — BLOCKED_BY_DEPENDENCY vyžaduje citovateľnú závislosť
K10 — finálny run_state nevytvára repository ani databázový mechanizmus
K11 — výsledok behu zachováva koreláciu požiadavky, behu a cieľa návratu
K12 — technické reservation_state zostáva oddelené od metodických stavov
K13 — auditná stopa sa nezamieňa s dôkazom alebo Validáciou
K14 — model zachováva nemennosť ukončených historických výsledkov
K15 — model nepredbieha SQL, indexy, migrácie, adaptéry ani retenčnú politiku
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

# 4. Overenie korelácie požiadavky a behu

Model zachováva:

```text
REQUEST_REFERENCE_RESERVATION_RECORD
1 ↔ 1
DERIVATION_RUN_RECORD
```

Rezervácia bez historického behu sa nesmie potvrdiť. Replay nevytvorí druhý beh a konflikt nemení existujúcu väzbu.

Tým zostáva zachované:

```text
jedna konkrétna aplikačná požiadavka
→ najviac jeden QUESTION_DERIVATION
```

---

# 5. Overenie atómovej brány

Pri zlyhaní brány model vyžaduje:

```text
DERIVATION_RUN_RECORD
+
DERIVATION_RUN_RESULT_RECORD
+
DERIVATION_TRACE_RECORD
```

A zakazuje:

```text
DERIVATION_BRANCH_RECORD
QUESTION_CANDIDATE_RECORD
```

Model teda nemení `STOPPED_AT_GATE` na prázdnu vetvu ani na kandidáta s nulovým výsledkom.

---

# 6. Overenie PARTIAL RUN

Každá vetva má samostatnú krátku transakčnú hranicu:

```text
DERIVATION_BRANCH_RECORD
+
0..N BRANCH_DEPENDENCY_RECORD
+
0..1 QUESTION_CANDIDATE_RECORD
+
DERIVATION_TRACE_RECORD
```

Preto zlyhanie neskoršej vetvy nevyžaduje rollback už potvrdenej nezávislej vetvy.

Model nepredpisuje jednu transakciu celého behu.

---

# 7. Overenie kandidáta

Platí obojsmerný invariant:

```text
branch_state = CANDIDATE_CREATED
↔
práve jeden úplný QUESTION_CANDIDATE_RECORD
```

A:

```text
BRANCH_STOPPED
RETURNED_FOR_DECOMPOSITION
BLOCKED_BY_DEPENDENCY
→ žiadny QUESTION_CANDIDATE_RECORD
```

Technický model teda nezamieňa vetvový výsledok s kandidátom a neukladá neúplný kandidát ako úspešný výsledok.

---

# 8. Overenie závislosti

`BRANCH_DEPENDENCY_RECORD` zachováva celý citovateľný obsah závislosti:

```text
dependency_reference
dependent_branch_reference
prerequisite_reference
dependency_type
justification
determined_by_reference
validation_control_reference
```

Platí:

```text
BLOCKED_BY_DEPENDENCY
→ najmenej jedna citovateľná závislosť
```

Technický model závislosť nevyrába iba zo skutočnosti, že iná vetva zlyhala.

---

# 9. Overenie finálneho výsledku

`DERIVATION_RUN_RESULT_RECORD` vzniká až po jedinej doménovej agregácii.

Uložené počty vetvových výsledkov sú kontrolovateľné voči vetvám, ale databáza ani repository nesmú z týchto počtov samostatne určovať význam `run_state`.

Ukončený výsledok je nemenný historický záznam a patrí tej istej:

```text
request_reference
derivation_reference
response_target_reference
```

---

# 10. Overenie auditnej stopy

Model výslovne zachováva:

```text
DERIVATION_TRACE_RECORD
≠ dôkaz
≠ Validácia
≠ automaticky metodická skutočnosť
```

Auditná stopa dokumentuje poradie a pôvod udalostí. Neudeľuje výsledku pravdivosť ani Autoritu.

---

# 11. Zistené otvorené rozhodnutia

Model vedome neurčuje:

```text
fyzické SQL tabuľky a dátové typy
indexy a cudzie kľúče
formát event_payload
šifrovanie citlivých vstupov
retenciu, archiváciu a anonymizáciu
obnovu prerušeného behu
vzťahy retry_of, supersedes a follows
```

Tieto otvorené body nemenia vnútornú konzistenciu modelu. Niektoré však musia byť rozhodnuté pred produkčnou implementáciou.

Najmä politika retencie a ochrany citlivých vstupov nemôže byť odvodená iba z databázovej techniky.

---

# 12. Výsledok Validácie

```text
VALIDATION_RESULT
=
VALID
```

Význam:

```text
technický model spĺňa všetkých pätnásť kritérií
+
zachováva Validovaný aplikačný kontrakt, replay politiku a repository invarianty
+
jednoznačne oddeľuje rezerváciu, beh, vetvy, závislosti, kandidátov, výsledok a auditnú stopu
+
možno odvodiť databázový návrh a migračné obmedzenia
```

`VALID` neValiduje budúcu SQL schému, migrácie, adaptéry ani implementovaný kód.

---

# 13. Nasledujúci logický krok

```text
odvodiť databázový návrh a migračné obmedzenia z Validovaného modelu uloženia
→ Validovať databázový návrh
→ až potom vytvoriť migrácie a repository adaptéry
```
