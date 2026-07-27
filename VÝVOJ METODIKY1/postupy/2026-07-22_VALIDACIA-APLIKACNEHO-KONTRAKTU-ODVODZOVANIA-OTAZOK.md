# Validácia aplikačného kontraktu odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument Validuje pracovný aplikačný kontrakt:

```text
postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
```

proti pracovnej ontológii, opravenému algoritmu a spoločnej reValidácii. Validácia neposudzuje PHP triedy, databázovú schému, HTTP rozhranie ani konkrétnu implementáciu.

---

# 1. VALIDATION_EVENT

```text
TARGET:
aplikačný kontrakt prvého doménového algoritmu odvodzovania otázok

ACTOR:
ChatGPT vykonávajúca metodické preskúmanie na výslovný pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa pokračovať podľa postupy/Inicializácia práce.md;
tento záznam nepredstiera konečnú Autoritu meniť autoritatívne definície METODIKY

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; preskúmanie topológie odozvy pred technickým návrhom aplikačnej služby

SCOPE:
vstupná a výstupná hranica operácie, atómová vstupná brána, čiastočné spracovanie kandidátskych vetiev, návrat výsledku k zdroju, zachovanie histórie a oddelenie metodickej od databázovej transakcie
```

---

# 2. Podklady Validácie

```text
postupy/Inicializácia práce.md
postupy/2026-07-21_VALIDACIA.md
postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md
postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md
postupy/2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md
postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
postupy/2026-07-22_09-38_CodeIgniter.md
postupy/README.md
CHANGELOG.md
```

---

# 3. Kritériá Validácie

Kritériá boli určené pred výsledkom:

```text
K1 — kontrakt zachováva vstupný význam ontológie a algoritmu
K2 — každý pokus vzniká pred vstupnými kontrolami
K3 — spoločná vstupná brána je skutočne atómová
K4 — kandidátske vetvy sú po bráne významovo oddelené
K5 — zlyhanie jednej vetvy nevymaže výsledky nezávislých vetiev
K6 — neúspešná vetva sa nesmie uložiť ako QUESTION_CANDIDATE
K7 — výsledok vetvy sa nezamieňa s odpoveďou 0
K8 — nadradený výsledok zachováva výsledky a stav celého behu
K9 — výsledok možno jednoznačne priradiť zdroju požiadavky a konkrétnemu pokusu
K10 — závislosti medzi vetvami sú určiteľné, spätne citovateľné a výsledkovo vyjadrené
K11 — súhrnný run_state je jednoznačne odvodený zo stavov brány a vetiev
K12 — ACTOR zostáva oddelený od REQUEST_SOURCE a AUTHORITY
K13 — metodická transakčná hranica je oddelená od databázovej transakcie
K14 — kontrakt nepredbieha technickú implementáciu
K15 — kontrakt je pripravený na technický návrh služby bez domýšľania významových väzieb
```

---

# 4. Výsledky kritérií

```text
K1  = 1
K2  = 1
K3  = 1
K4  = 1
K5  = 1
K6  = 1
K7  = 1
K8  = 1
K9  = 0
K10 = 0
K11 = 0
K12 = 1
K13 = 1
K14 = 1
K15 = 0
```

## K9 — jednoznačný návrat výsledku k zdroju

Vstupný kontrakt obsahuje:

```text
request_source
```

Výstupný `DERIVATION_RUN_RESULT` však neobsahuje jednoznačný odkaz na zdroj požiadavky ani koreláciu medzi požiadavkou a odpoveďou.

Samotný odkaz na `QUESTION_DERIVATION` nemusí bez ďalšieho pravidla určovať, komu a ku ktorej požiadavke sa má výsledok vrátiť.

Kontrakt musí doplniť najmenej:

```text
request_reference
response_target_reference
```

alebo významovo rovnocenné väzby, ktoré umožnia bez domýšľania spojiť:

```text
predložený aplikačný vstup
→ konkrétny beh
→ nadradený výsledok
→ zdroj, ktorému sa výsledok vracia
```

`REQUEST_SOURCE` zostáva odlišný od metodického `ACTOR-a`.

## K10 — závislosti kandidátskych vetiev

Kontrakt zavádza:

```text
branch_dependencies
BLOCKED_BY_DEPENDENCY
```

neurčuje však dostatočne:

```text
- z čoho závislosť vznikla,
- kto alebo ktorý úkon ju určil,
- na ktorú konkrétnu vetvu alebo výsledok odkazuje,
- či je závislosť významová, poradová alebo technická,
- podľa akej kontroly sa vetva označí ako BLOCKED_BY_DEPENDENCY.
```

Závislosť nesmie byť voľným technickým odhadom. Musí byť súčasťou auditnej stopy a odkazovať na konkrétny predchádzajúci výsledok alebo podmienku.

## K11 — odvodenie súhrnného run_state

Kontrakt uvádza pracovné stavy:

```text
STOPPED_AT_GATE
COMPLETED_ALL_BRANCHES
COMPLETED_WITH_BRANCH_FAILURES
RETURNED_FOR_DECOMPOSITION
```

Nie je však jednoznačne určené, aký nadradený stav vznikne pri kombináciách, napríklad:

```text
jeden CANDIDATE_CREATED
+
jeden RETURNED_FOR_DECOMPOSITION
```

alebo:

```text
jeden CANDIDATE_CREATED
+
jeden BRANCH_STOPPED
+
jeden BLOCKED_BY_DEPENDENCY
```

V režime `PARTIAL_RUN_WITH_ATOMIC_GATE` musí byť nadradený stav odvodený deterministicky z výsledku brány a zo všetkých vetvových stavov. Vetvové vrátenie na rozklad nesmie bez pravidla prepísať existenciu úspešne vytvorených kandidátov.

---

# 5. Obojstranné zabezpečenie

Kontrakt správne zabezpečuje smer dovnútra:

```text
neplatný spoločný vstup
→ nevznikne žiadna kandidátska vetva
```

Správne zabezpečuje aj izoláciu vetiev:

```text
zlyhanie jednej nezávislej vetvy
→ nevymaže úspešné výsledky ostatných vetiev
```

Smer navonok však zatiaľ nie je úplne uzavretý:

```text
výsledok
→ musí mať jednoznačný zdroj návratu
→ musí niesť úplnú topológiu závislostí
→ musí mať deterministický súhrnný stav
```

---

# 6. Výsledok Validácie

```text
VALIDATION_RESULT
=
CONDITIONALLY_VALID
```

Význam výsledku:

```text
jadro PARTIAL_RUN_WITH_ATOMIC_GATE je významovo správne
+
atómová brána a izolácia vetiev sú zachované
+
tri chýbajúce väzby zatiaľ bránia bezpečnému technickému návrhu služby
```

Kontrakt sa nemusí vytvoriť odznova. Pred reValidáciou treba vykonať tri presné opravy.

---

# 7. Povinné opravy

## Oprava 1 — korelácia zdroja a výsledku

Doplniť do vstupného a výstupného kontraktu jednoznačný odkaz, ktorý spojí požiadavku, beh, výsledok a cieľ návratu.

## Oprava 2 — ontológia vetvovej závislosti

Určiť minimálny významový tvar závislosti:

```text
BRANCH_DEPENDENCY
{
    dependency_id,
    dependent_branch_reference,
    prerequisite_reference,
    dependency_type,
    justification,
    trace
}
```

Zavedené názvy sú pracovné a nepredstavujú technickú schému.

## Oprava 3 — agregačné pravidlo run_state

Určiť deterministické pravidlo, ktoré z výsledku vstupnej brány a množiny `CANDIDATE_BRANCH_RESULT` odvodí práve jeden nadradený `run_state` bez straty informácie o jednotlivých vetvách.

---

# 8. Stav a nasledujúci krok

Aplikačný kontrakt aj táto Validácia zostávajú:

```text
PRACOVNÝ
```

Nasledujúci logický krok:

```text
opraviť tri kontraktové väzby
→ spätne načítať výsledok
→ vykonať reValidáciu aplikačného kontraktu
→ až pri VALID alebo VALID_WITH_LIMITATIONS odvodiť technický návrh aplikačnej služby
```
