# Technická politika opakovanej REQUEST_REFERENCE

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument odvodzuje technickú politiku opakovaného doručenia aplikačnej požiadavky z Validovaného kontraktu:

```text
postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
```

Nemení význam `REQUEST_REFERENCE`, `QUESTION_DERIVATION`, `DERIVATION_RUN_RESULT` ani režim `PARTIAL_RUN_WITH_ATOMIC_GATE`.

---

# 1. Východisko kontraktu

Validovaný kontrakt určuje:

```text
REQUEST_REFERENCE
=
jednoznačný odkaz na konkrétnu predloženú aplikačnú požiadavku
```

A korelačnú cestu:

```text
REQUEST_REFERENCE
→ QUESTION_DERIVATION
→ DERIVATION_RUN_RESULT
→ RESPONSE_TARGET_REFERENCE
```

Z toho vyplýva:

```text
rovnaký REQUEST_REFERENCE
=
rovnaká konkrétna aplikačná požiadavka
```

Nie:

```text
rovnaký REQUEST_REFERENCE
=
nový metodický pokus
```

---

# 2. Rozhodnutie politiky

Pre prvú aplikačnú službu sa prijíma:

```text
REQUEST_REPLAY_POLICY
=
IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE
```

Význam:

```text
prvé prijatie REQUEST_REFERENCE
→ môže založiť práve jeden QUESTION_DERIVATION

opakované prijatie tej istej REQUEST_REFERENCE
→ nesmie založiť ďalší QUESTION_DERIVATION
→ musí sa priradiť k už existujúcemu behu
```

Nový metodický pokus vyžaduje:

```text
nový REQUEST_REFERENCE
```

Technická vrstva nesmie sama vyhlásiť opakované doručenie za nový metodický úkon.

---

# 3. Identita obsahu požiadavky

Samotná zhoda referencie nestačí. Technická vrstva musí overiť, či opakované doručenie nesie rovnaký kontraktový obsah.

Na tento účel sa používa:

```text
REQUEST_PAYLOAD_FINGERPRINT
```

Je to deterministický technický odtlačok kanonicky usporiadaného obsahu `DERIVATION_APPLICATION_INPUT` okrem samotnej `request_reference` a čisto prenosových údajov, ktoré nie sú súčasťou kontraktového významu.

Odtlačok musí zachytiť najmenej:

```text
request_source
response_target_reference
source_question_reference
derivation_subject_reference
purpose
context
scope
domain_term_references
actor_reference
authority_context
run_mode
```

Kanonizácia musí byť stabilná. Poradie prvkov množiny `domain_term_references` nesmie meniť výsledný odtlačok.

```text
rovnaká REQUEST_REFERENCE
+
rovnaký REQUEST_PAYLOAD_FINGERPRINT
=
technický replay tej istej požiadavky
```

```text
rovnaká REQUEST_REFERENCE
+
odlišný REQUEST_PAYLOAD_FINGERPRINT
=
REQUEST_REFERENCE_CONFLICT
```

`REQUEST_REFERENCE_CONFLICT` je technické porušenie identity aplikačnej požiadavky. Nie je to:

```text
STOPPED_AT_GATE
BRANCH_STOPPED
odpoveď 0
nový QUESTION_DERIVATION
```

---

# 4. Správanie pri prvom prijatí

Prvé prijatie musí v jednej krátkej technickej transakcii:

```text
1. rezervovať REQUEST_REFERENCE,
2. uložiť REQUEST_PAYLOAD_FINGERPRINT,
3. vytvoriť derivation_reference,
4. založiť historický pokus QUESTION_DERIVATION.
```

Až po úspešnom založení tejto väzby môže začať atómová vstupná brána.

Platí:

```text
REQUEST_REFERENCE
→ práve jedna derivation_reference
```

v rozsahu tejto politiky.

---

# 5. Správanie pri opakovanom doručení

## 5.1 Rovnaký obsah a ukončený beh

```text
rovnaká referencia
+
rovnaký odtlačok
+
ukončený beh
→ vrátiť existujúci DERIVATION_RUN_RESULT
```

Nevytvára sa nový historický pokus, nová brána ani nové vetvy.

## 5.2 Rovnaký obsah a rozpracovaný beh

```text
rovnaká referencia
+
rovnaký odtlačok
+
rozpracovaný beh
→ priradiť požiadavku k existujúcej derivation_reference
→ nevytvoriť druhý beh
```

Konkrétny HTTP alebo CLI obraz rozpracovaného stavu sa určí neskôr. Táto politika určuje iba to, že opakované doručenie nesmie spustiť paralelný metodický pokus.

## 5.3 Odlišný obsah

```text
rovnaká referencia
+
odlišný odtlačok
→ odmietnuť ako REQUEST_REFERENCE_CONFLICT
```

Pôvodný beh ani jeho výsledky sa nemenia.

---

# 6. Súbežné doručenie

Pri súbežnom prvom prijatí rovnakej `REQUEST_REFERENCE` smie rezerváciu úspešne vytvoriť iba jeden technický tok.

Ostatné toky musia po zistení existujúcej rezervácie:

```text
overiť REQUEST_PAYLOAD_FINGERPRINT
→ pri zhode priradiť sa k existujúcemu behu
→ pri nezhode vrátiť REQUEST_REFERENCE_CONFLICT
```

Súbežnosť nesmie vytvoriť dve `derivation_reference` pre jednu `REQUEST_REFERENCE`.

Databázový unikátny mechanizmus môže túto politiku technicky vynútiť, ale nie ju definovať.

---

# 7. Hranica novej požiadavky

Ak ACTOR alebo nadradený proces zamýšľa vykonať nový metodický pokus, musí predložiť novú aplikačnú požiadavku s novou `REQUEST_REFERENCE`.

```text
nový zámer vykonať metodický pokus
→ nový REQUEST_REFERENCE
→ nový QUESTION_DERIVATION
```

Táto politika zatiaľ neurčuje vzťah medzi pôvodnou a novou požiadavkou, napríklad `retry_of`, `supersedes` alebo `follows`. Taký vzťah môže vzniknúť iba ako osobitne odvodený kontrakt, ak ho bude METODIKA potrebovať.

---

# 8. Technické zodpovednosti

Aplikačná vrstva potrebuje technickú zodpovednosť, pracovným názvom:

```text
RequestReplayGuard
```

Táto zodpovednosť smie:

```text
kanonizovať vstup
vypočítať REQUEST_PAYLOAD_FINGERPRINT
rezervovať REQUEST_REFERENCE
nájsť existujúcu derivation_reference
rozlíšiť FIRST_ACCEPTANCE, REPLAY a CONFLICT
```

Nesmie:

```text
vykonávať doménový algoritmus
určovať Autoritu
meniť metodický výsledok
zakladať nový pokus pri REPLAY
prekladať CONFLICT na odpoveď 0
```

---

# 9. Výsledok kontroly replay

Pracovný technický výsledok:

```text
REQUEST_REPLAY_DECISION
{
    request_reference,
    payload_fingerprint,
    decision,
    derivation_reference?,
    existing_run_state?,
    conflict_reason?
}
```

Pracovné hodnoty `decision`:

```text
FIRST_ACCEPTANCE
REPLAY_EXISTING_RUN
REQUEST_REFERENCE_CONFLICT
```

Tieto názvy nie sú metodickými stavmi ani globálnym číselníkom METODIKY.

---

# 10. Testovacie invarianty

```text
1. prvé prijatie založí práve jednu derivation_reference,
2. rovnaká referencia a rovnaký obsah nezaložia nový pokus,
3. replay ukončeného behu vráti existujúci výsledok,
4. replay rozpracovaného behu nevytvorí paralelný beh,
5. rovnaká referencia s odlišným obsahom skončí konfliktom,
6. konflikt nemení pôvodný beh ani jeho výsledky,
7. súbežné doručenie nevytvorí dve derivation_reference,
8. nový metodický pokus vyžaduje novú REQUEST_REFERENCE,
9. technický replay ani konflikt sa nezamenia s metodickým stavom alebo odpoveďou 0.
```

---

# 11. Čo politika neurčuje

```text
HTTP status kódy
JSON formát replay odpovede
časovú expiráciu uložených výsledkov
vzťah medzi dvoma rozdielnymi REQUEST_REFERENCE
automatické opakovanie po technickej chybe
obnovu prerušeného behu
```

Tieto otázky nesmú meniť základné pravidlo jednej konkrétnej požiadavky a jedného metodického behu.

---

# 12. Nasledujúci logický krok

```text
Validovať politiku proti aplikačnému kontraktu a technickému návrhu služby
→ doplniť politiku do technickej hranice aplikačnej služby
→ odvodiť repository kontrakt rezervácie REQUEST_REFERENCE
→ až potom navrhnúť databázové obmedzenie a API replay správanie
```
