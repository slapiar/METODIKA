# Repository kontrakt rezervácie a vyhľadania REQUEST_REFERENCE

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument odvodzuje technický repository kontrakt z:

```text
TECHNICKE-NAVRHY/2026-07-22_POLITIKA-OPAKOVANEJ-REQUEST-REFERENCE.md
+
TECHNICKE-NAVRHY/2026-07-22_APLIKACNA-SLUZBA-ODVODZOVANIA-OTAZOK.md
```

Neurčuje databázové tabuľky, stĺpce, indexy, CodeIgniter Model ani konkrétny adaptér.

---

# 1. Predmet kontraktu

Pracovné rozhranie:

```text
RequestReferenceRepositoryPort
```

Jeho úlohou je technicky zachovať invariant:

```text
jedna REQUEST_REFERENCE
→ najviac jedna derivation_reference
```

Repository tento invariant nedefinuje. Iba vykonáva Validovanú politiku `IDEMPOTENT_REPLAY_BY_REQUEST_REFERENCE`.

---

# 2. Záznam rezervácie

Pracovný dátový tvar:

```text
REQUEST_REFERENCE_RESERVATION
{
    request_reference,
    payload_fingerprint,
    derivation_reference,
    reservation_state,
    run_state?,
    result_reference?,
    reserved_at,
    updated_at
}
```

Význam:

```text
request_reference
= identita konkrétnej aplikačnej požiadavky

payload_fingerprint
= odtlačok jej kontraktového obsahu

derivation_reference
= jediný metodický beh priradený k požiadavke

reservation_state
= technický stav korelácie, nie metodický run_state
```

Pracovné hodnoty `reservation_state`:

```text
RESERVED
RUNNING
COMPLETED
```

Tieto názvy nie sú globálnym číselníkom METODIKY.

---

# 3. Povinné operácie portu

## 3.1 reserveFirstAcceptance

Pracovný kontrakt:

```text
reserveFirstAcceptance(
    request_reference,
    payload_fingerprint,
    derivation_reference,
    reserved_at
)
→ RESERVATION_RESULT
```

Operácia musí byť atómová voči súbežnému prijatiu tej istej `REQUEST_REFERENCE`.

Výsledok:

```text
RESERVATION_CREATED
ALREADY_EXISTS
```

Pri `RESERVATION_CREATED` musí byť v tej istej technickej transakcii možné založiť historický `QUESTION_DERIVATION`, alebo sa nesmie rezervácia potvrdiť. Nesmie vzniknúť osirelá rezervácia bez metodického pokusu.

## 3.2 findByRequestReference

```text
findByRequestReference(request_reference)
→ REQUEST_REFERENCE_RESERVATION?
```

Vyhľadanie nesmie meniť stav behu ani vytvárať nový pokus.

## 3.3 markRunning

```text
markRunning(request_reference, derivation_reference, updated_at)
```

Môže potvrdiť technický stav rozpracovaného behu. Nesmie meniť metodický `run_state`.

## 3.4 attachCompletedResult

```text
attachCompletedResult(
    request_reference,
    derivation_reference,
    run_state,
    result_reference,
    updated_at
)
```

Pripojí odkaz na už vytvorený `DERIVATION_RUN_RESULT`. Repository nesmie výsledok zostavovať ani meniť.

## 3.5 loadCompletedResult

```text
loadCompletedResult(request_reference)
→ DERIVATION_RUN_RESULT?
```

Vrátený výsledok musí patriť rovnakej `request_reference` a `derivation_reference`.

---

# 4. Výsledok rezervácie

```text
RESERVATION_RESULT
{
    outcome,
    reservation
}
```

Pracovné hodnoty:

```text
RESERVATION_CREATED
ALREADY_EXISTS
```

`ALREADY_EXISTS` nie je chyba ani replay rozhodnutie. `RequestReplayGuard` po ňom musí porovnať uložený a vypočítaný fingerprint a odvodiť:

```text
REPLAY_EXISTING_RUN
alebo
REQUEST_REFERENCE_CONFLICT
```

Repository nesmie toto rozhodnutie domýšľať.

---

# 5. Invarianty repository

```text
R1 — REQUEST_REFERENCE je v úložisku jedinečná v rozsahu služby,
R2 — jedna REQUEST_REFERENCE odkazuje najviac na jednu derivation_reference,
R3 — derivation_reference rezervácie sa po potvrdení nemení,
R4 — payload_fingerprint sa po potvrdení nemení,
R5 — ALREADY_EXISTS nevytvorí nový historický pokus,
R6 — rozdielny fingerprint nemení pôvodnú rezerváciu,
R7 — ukončený výsledok patrí tej istej požiadavke a behu,
R8 — technický stav rezervácie sa nezamieňa s metodickým run_state,
R9 — súbežné prijatie nevytvorí dve potvrdené rezervácie,
R10 — databázový rollback nesmie vymazať už potvrdené výsledky nezávislých vetiev.
```

---

# 6. Hranica transakcie prvého prijatia

Prvý technický zápis musí tvoriť jednu konzistentnú hranicu:

```text
REQUEST_REFERENCE_RESERVATION
+
QUESTION_DERIVATION historický pokus
```

Buď vzniknú oba, alebo nevznikne ani jeden.

Táto hranica:

```text
≠ transakcia celého PARTIAL RUN
```

Po jej potvrdení pokračuje brána a každá vetva používa vlastné krátke transakcie podľa technického návrhu služby.

---

# 7. Súbežnosť

Pri dvoch súbežných pokusoch rezervovať rovnakú referenciu:

```text
jeden tok
→ RESERVATION_CREATED

ostatné toky
→ ALREADY_EXISTS
```

Následné porovnanie fingerprintu rozhodne replay alebo konflikt. Repository kontrakt nepredpisuje konkrétny zámok, databázový index ani úroveň izolácie.

---

# 8. Zakázané správanie

Repository nesmie:

```text
vytvárať nový QUESTION_DERIVATION pri ALREADY_EXISTS
meniť payload_fingerprint existujúcej rezervácie
prepisovať derivation_reference
prekladať konflikt na STOPPED_AT_GATE alebo odpoveď 0
určovať Autoritu
vykonávať doménový algoritmus
agregovať run_state
zostavovať QUESTION_CANDIDATE
```

---

# 9. Chybové kanály

```text
reservation storage unavailable
atomic reservation failed
reservation invariant violated
completed result unavailable
correlation mismatch
```

Sú to technické chyby. Nie sú metodickými výsledkami.

---

# 10. Testovací kontrakt

```text
1. prvá rezervácia vráti RESERVATION_CREATED,
2. druhá rovnaká rezervácia vráti ALREADY_EXISTS,
3. súbežné rezervácie vytvoria najviac jeden potvrdený záznam,
4. rezervácia bez historického pokusu sa nepotvrdí,
5. existujúci fingerprint ani derivation_reference nemožno prepísať,
6. vyhľadanie nemení stav,
7. ukončený výsledok možno načítať iba pre správnu koreláciu,
8. repository nerozhoduje replay alebo konflikt,
9. technická chyba sa nezmení na metodický stav,
10. kontrakt možno implementovať bez zmeny aplikačného kontraktu.
```

---

# 11. Čo kontrakt neurčuje

```text
názov databázovej tabuľky
stĺpce a dátové typy
primárny alebo unikátny index
cudzie kľúče
CodeIgniter Model
konkrétnu SQL izoláciu
časovú expiráciu
HTTP a JSON reprezentáciu
obnovu prerušeného behu
```

---

# 12. Nasledujúci logický krok

```text
Validovať repository kontrakt spolu s aktualizovanou službou
→ odvodiť technický model uloženia
→ až potom navrhnúť migráciu a repository adaptér
```
