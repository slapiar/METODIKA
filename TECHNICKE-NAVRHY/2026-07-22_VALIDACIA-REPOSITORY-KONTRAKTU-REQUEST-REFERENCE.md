# Validácia repository kontraktu REQUEST_REFERENCE a aktualizovanej aplikačnej služby

## Stav dokumentu

```text
PRACOVNÝ
```

Validovaný predmet:

```text
TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md
+
TECHNICKE-NAVRHY/2026-07-22_REPOSITORY-KONTRAKT-REQUEST-REFERENCE.md
```

Zdroj:

```text
TECHNICKE-NAVRHY/2026-07-22_POLITIKA-OPAKOVANEJ-REQUEST-REFERENCE.md
VALIDATION_RESULT = VALID
```

Táto Validácia neposudzuje SQL schému, migráciu, CodeIgniter Model, repository adaptér ani API.

---

# 1. VALIDATION_EVENT

```text
TARGET:
aktualizovaná technická hranica QuestionDerivationApplicationService
spolu s RequestReferenceRepositoryPort

ACTOR:
ChatGPT vykonávajúca technicko-metodické preskúmanie na výslovný pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa pokračovať podľa Inicializácie práce;
tento záznam nepredstiera konečnú Autoritu meniť autoritatívne definície METODIKY

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; uzavretie repository hranice identity požiadavky pred technickým modelom uloženia

SCOPE:
replay guard, prvé prijatie, atómová rezervácia, historický pokus, vyhľadanie existujúceho behu, korelácia výsledku, súbežnosť a oddelenie technických a metodických stavov
```

---

# 2. Kritériá Validácie

```text
K1 — aktualizovaná služba zachováva Validovaný aplikačný kontrakt
K2 — replay politika je vykonaná pred založením nového behu
K3 — iba FIRST_ACCEPTANCE môže založiť QUESTION_DERIVATION
K4 — replay ukončeného behu vracia existujúci výsledok
K5 — replay rozpracovaného behu nevytvára paralelný beh
K6 — konflikt identity sa nezamieňa s metodickým stavom alebo odpoveďou 0
K7 — repository kontrakt invariant iba vykonáva a nedefinuje
K8 — rezervácia a historický pokus tvoria jednu konzistentnú prvú transakciu
K9 — osirelá rezervácia bez historického pokusu sa nesmie potvrdiť
K10 — súbežné prijatie vytvorí najviac jednu väzbu request_reference → derivation_reference
K11 — repository nemení fingerprint ani derivation_reference potvrdenej rezervácie
K12 — repository nerozhoduje REPLAY_EXISTING_RUN alebo REQUEST_REFERENCE_CONFLICT
K13 — technický reservation_state zostáva oddelený od metodického run_state
K14 — návrh nepredbieha databázové tabuľky, indexy, adaptéry ani API
K15 — možno odvodiť technický model uloženia bez významového domýšľania
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

# 4. Overenie prvého prijatia

Aktualizovaná služba vykonáva replay kontrolu pred vznikom nového `derivation_reference`.

Iba výsledok:

```text
FIRST_ACCEPTANCE
```

umožní vytvoriť v jednej krátkej technickej transakcii:

```text
REQUEST_REFERENCE_RESERVATION
+
QUESTION_DERIVATION historický pokus
```

Až potom môže začať `ATOMIC_INPUT_GATE`. Tým zostáva zachované pravidlo historického pokusu pred vstupnými kontrolami.

---

# 5. Overenie replay a konfliktu

```text
REPLAY_EXISTING_RUN
→ nevytvorí nový pokus
→ nevykoná znovu bránu ani vetvy
```

```text
REQUEST_REFERENCE_CONFLICT
→ technický konflikt identity
→ nemení pôvodný beh
→ nie je metodickým výsledkom
```

Repository vráti iba stav rezervácie a uloženú koreláciu. Porovnanie fingerprintu a konečné replay rozhodnutie zostáva zodpovednosťou `RequestReplayGuard`.

---

# 6. Overenie súbežnosti

Repository kontrakt vyžaduje atómovú rezerváciu bez predpísania konkrétneho databázového mechanizmu.

```text
dva súbežné toky
→ najviac jeden RESERVATION_CREATED
→ ostatné ALREADY_EXISTS
```

Tým je zachovaný invariant:

```text
jedna REQUEST_REFERENCE
→ najviac jedna derivation_reference
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
aktualizovaná služba verne vykonáva Validovanú replay politiku
+
repository kontrakt zachováva všetky korelačné invarianty
+
služba a repository majú oddelené zodpovednosti
+
prvé prijatie, replay, konflikt a súbežnosť sú jednoznačne rozlíšené
+
možno odvodiť technický model uloženia
```

`VALID` neValiduje budúcu databázovú ani programovú implementáciu.

---

# 8. Stav predchádzajúcich udalostí

Pôvodná Validácia služby s výsledkom `VALID_WITH_LIMITATIONS` zostáva historicky zachovaná. Jej obmedzenie už bolo odstránené Validovanou replay politikou a teraz aj premietnuté do služby a repository hranice.

Aktuálny spoločný technický základ je v tomto rozsahu:

```text
VALID
```

---

# 9. Nasledujúci logický krok

```text
odvodiť technický model uloženia rezervácie, behov, vetiev a výsledkov
→ Validovať model uloženia
→ až potom navrhnúť databázové migrácie a repository adaptéry
```
