# Validácia technickej politiky opakovanej REQUEST_REFERENCE

## Stav dokumentu

```text
PRACOVNÝ
```

Validovaný predmet:

```text
TECHNICKE-NAVRHY/2026-07-22_POLITIKA-OPAKOVANEJ-REQUEST-REFERENCE.md
```

Podklady:

```text
postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
postupy/2026-07-22_REVALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md
TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md
TECHNICKE-NAVRHY/2026-07-22_VALIDACIA-APLIKACNEJ-SLUZBY-ODVODZOVANIA-OTAZOK.md
```

Táto Validácia neposudzuje databázovú schému, konkrétny unikátny index, HTTP status kódy ani implementovaný kód.

---

# 1. VALIDATION_EVENT

```text
TARGET:
technická politika IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE

ACTOR:
ChatGPT vykonávajúca technicko-metodické preskúmanie na výslovný pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa rozhodnúť technickú politiku opakovanej REQUEST_REFERENCE podľa Inicializácie práce;
tento záznam nepredstiera konečnú Autoritu meniť autoritatívne definície METODIKY

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; technická ochrana identity aplikačnej požiadavky pred návrhom repository a API

SCOPE:
vzťah REQUEST_REFERENCE k QUESTION_DERIVATION, replay rovnakého obsahu, konflikt odlišného obsahu, súbežné doručenie a hranica nového metodického pokusu
```

---

# 2. Kritériá Validácie

```text
K1 — politika vychádza z kontraktového významu REQUEST_REFERENCE
K2 — politika nemení Validovaný aplikačný kontrakt
K3 — jedna konkrétna požiadavka nevytvorí viac QUESTION_DERIVATION
K4 — nový metodický pokus vyžaduje novú REQUEST_REFERENCE
K5 — replay rovnakého obsahu sa priradí k existujúcemu behu
K6 — replay nevykoná znovu atómovú bránu ani kandidátske vetvy
K7 — rovnaká referencia s odlišným obsahom je rozpoznaná ako technický konflikt
K8 — konflikt sa nezamieňa s metodickým stavom ani odpoveďou 0
K9 — pôvodný beh a jeho výsledky zostávajú pri konflikte nezmenené
K10 — politika rieši súbežné prvé doručenie bez vzniku dvoch behov
K11 — odtlačok obsahu je deterministický a nezávislý od poradia množín
K12 — databázové obmedzenie politiku iba vynúti, ale nedefinuje
K13 — politika nepredbieha HTTP a JSON reprezentáciu
K14 — technická zodpovednosť replay nepreberá doménový algoritmus ani Autoritu
K15 — politiku možno odvodiť do repository kontraktu bez významového domýšľania
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

# 4. Overenie kontraktového odvodenia

Kontrakt definuje `REQUEST_REFERENCE` ako jednoznačný odkaz na konkrétnu predloženú aplikačnú požiadavku.

Preto platí:

```text
rovnaká konkrétna požiadavka
→ rovnaká REQUEST_REFERENCE
→ rovnaký QUESTION_DERIVATION
```

A naopak:

```text
nový QUESTION_DERIVATION
→ nový metodický pokus
→ nová konkrétna aplikačná požiadavka
→ nová REQUEST_REFERENCE
```

Politika teda nemení kontrakt. Technicky vynucuje jeho už Validovanú korelačnú cestu.

---

# 5. Overenie historického pokusu pred bránou

Pri prvom prijatí vznikne rezervácia referencie a historický pokus pred vstupnou bránou.

Pri replay nevzniká nový pokus, pretože nejde o novú aplikačnú požiadavku ani nový metodický úkon. Replay sa priradí k už existujúcemu historickému pokusu.

Tým sa neporušuje pravidlo:

```text
každý metodický pokus
→ musí byť založený pred svojimi vstupnými kontrolami
```

Replay nie je ďalším metodickým pokusom.

---

# 6. Overenie konfliktu identity

```text
rovnaká REQUEST_REFERENCE
+
odlišný REQUEST_PAYLOAD_FINGERPRINT
→ REQUEST_REFERENCE_CONFLICT
```

Konflikt znamená, že jedna referencia bola použitá pre dve rozdielne tvrdené identity požiadavky.

Je to technické porušenie invariantu korelácie, nie výsledok doménového algoritmu. Preto sa nesmie zapísať ako:

```text
STOPPED_AT_GATE
BRANCH_STOPPED
RETURNED_FOR_DECOMPOSITION
BLOCKED_BY_DEPENDENCY
odpoveď 0
```

---

# 7. Výsledok Validácie

```text
VALIDATION_RESULT
=
VALID
```

Význam:

```text
politika spĺňa všetkých pätnásť kritérií
+
jedna REQUEST_REFERENCE označuje práve jednu konkrétnu aplikačnú požiadavku a jeden QUESTION_DERIVATION
+
opakované doručenie rovnakého obsahu je idempotentný replay
+
odlišný obsah pod rovnakou referenciou je technický konflikt
+
možno odvodiť repository kontrakt rezervácie a vyhľadania REQUEST_REFERENCE
```

`VALID` neValiduje budúcu databázovú ani API implementáciu.

---

# 8. Vzťah k predchádzajúcej Validácii služby

Pôvodná technická Validácia služby zostáva historicky zachovaná s výsledkom:

```text
VALID_WITH_LIMITATIONS
```

Jej jediné obmedzenie — nerozhodnutá politika opakovanej `REQUEST_REFERENCE` — bolo týmto dokumentom odstránené.

Aktuálny spoločný technický základ tvorí:

```text
technický návrh aplikačnej služby
+
VALID politika IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE
```

Tento spoločný základ už neobsahuje uvedené obmedzenie.

---

# 9. Nasledujúci logický krok

```text
premietnuť Validovanú politiku do technického návrhu služby ako záväznú spolupracujúcu zodpovednosť
→ odvodiť repository kontrakt rezervácie, vyhľadania a konfliktu REQUEST_REFERENCE
→ Validovať repository kontrakt
→ až potom navrhnúť databázové obmedzenia a API replay správanie
```
