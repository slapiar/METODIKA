# Krok 5 — Historický produkčný dôkaz

## Stav kroku

```text
SPLNENÉ — dôkaz získaný
```

## Väzba na záväzný plán

Tento dokument uzatvára výhradne `Krok 5 — Pokus o získanie historického produkčného dôkazu` dokumentu:

`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`

Ďalší krok nebol počas vykonania tohto kroku otvorený.

---

# 1. Produkčné feature flagy

Používateľ prakticky overil v produkčnom súbore `.private/metodika.env`:

```text
METODIKA_DIAGNOSTICS_ENABLED=1
METODIKA_CONCURRENCY_WEB_ENABLED=1
```

Záver:

```text
PRODUCTION_DIAGNOSTICS_FLAG=ENABLED
PRODUCTION_CONCURRENCY_WEB_FLAG=ENABLED
```

---

# 2. Posledný produkčný run a tombstone

Na produkčnom serveri bol nájdený súbor:

```text
run-5c73222700d7863a1b05e135.json
```

Obsah potvrdzuje:

```text
runId=run-5c73222700d7863a1b05e135
state=COMPLETED_FAILED
createdAt=2026-07-23T15:44:24+00:00
completedAt=2026-07-23T15:44:25+00:00
deleteAfter=2026-07-23T15:54:25+00:00
readOnceConsumedAt=2026-07-23T15:44:25+00:00
```

Participant A:

```text
startedAt=2026-07-23T15:44:25+00:00
finishedAt=2026-07-23T15:44:25+00:00
outcome=FAILED_RUNTIME_ERROR
errorCode=null
```

Participant B:

```text
startedAt=2026-07-23T15:44:25+00:00
finishedAt=2026-07-23T15:44:25+00:00
outcome=CREATED
errorCode=null
```

Bariéra a finalizácia:

```text
barrier.openedAt=2026-07-23T15:44:25+00:00
barrier.waitTimeoutMs=2500
finalization.claimedAt=2026-07-23T15:44:25+00:00
finalization.claimedBy=a
finalization.finishedAt=2026-07-23T15:44:25+00:00
```

Výsledkové osi:

```text
dbUniquenessConfirmed=true
appReplayConfirmed=false
cleanupConfirmed=true
overallSuccess=false
```

Tombstone je redigovaný: `tokenHash` je pri oboch participantoch `null` a vstupné údaje už nie sú prítomné.

---

# 3. Stabilný lock súbor

Na serveri bol nájdený aj prázdny súbor:

```text
run-5c73222700d7863a1b05e135.lock
```

Prázdny obsah lock súboru je očakávaný; jeho významom je stabilná cesta pre file lock, nie uloženie dát.

Súčasná existencia `.json` aj `.lock` po čase `deleteAfter` je dôkazom, že tombstone nebol odstránený účinným následným sweepom. Toto podporuje otvorenú dieru M15 z Kroku 3.

---

# 4. Presná produkčná chyba

Serverový log v rovnakom čase ako posledný run obsahuje:

```text
ERROR - 2026-07-23 15:44:25 --> Diagnostics acceptance failed [RUNTIME_ERROR]: RuntimeException: Historický beh nemožno založiť bez presnej rezervácie REQUEST_REFERENCE.
```

Korelácia je priama:

```text
run.completedAt=2026-07-23T15:44:25+00:00
log.timestamp=2026-07-23 15:44:25
```

Presný dôkaz:

```text
SAFE_ERROR_CODE=RUNTIME_ERROR
EXCEPTION_CLASS=RuntimeException
EXCEPTION_MESSAGE=Historický beh nemožno založiť bez presnej rezervácie REQUEST_REFERENCE.
```

Záver:

```text
FAILED_RUNTIME_ERROR_CAUSE=DERIVATION_HISTORY_CREATED_WITHOUT_EXACT_REQUEST_REFERENCE_RESERVATION
```

---

# 5. Neskorší databázový log

Log obsahuje aj samostatný neskorší riadok:

```text
ERROR - 2026-07-23 16:01:40 --> Duplicate entry 'diag-request-6022895cf78e5aac' for key 'uq_qdrr_request_reference'
```

Tento záznam vznikol 17 minút a 15 sekúnd po run-e `run-5c73222700d7863a1b05e135`. Bez ďalšieho korelačného identifikátora sa nesmie považovať za príčinu tohto runu. Eviduje sa ako samostatný historický produkčný jav určený na posúdenie v Kroku 6.

---

# 6. Rozlíšenie skutočnosti, dôkazu a interpretácie

## Skutočnosť potvrdená artefaktmi

- oba feature flagy boli zapnuté,
- produkčný run existoval a skončil `COMPLETED_FAILED`,
- participant A skončil `FAILED_RUNTIME_ERROR`,
- participant B skončil `CREATED`,
- bariéra sa otvorila,
- DB unikátnosť a cleanup boli potvrdené,
- aplikačný replay potvrdený nebol,
- serverový log zachytil konkrétnu `RuntimeException`,
- tombstone zostal na serveri aj po `deleteAfter`.

## Interpretácia určená na ďalší krok

Chyba vznikla pri pokuse založiť historický beh bez presnej rezervácie `REQUEST_REFERENCE`. Presná cesta v kóde a poradie volaní ešte nie sú týmto krokom dokázané; tie určí Krok 6 statickou lokalizáciou chybových fáz.

---

# 7. Kritérium uzavretia

Záväzný plán požadoval jednorazovo preveriť dostupnosť flagov, posledného `runId`, tombstone alebo sweepu a serverového logu s presným acceptance záznamom.

Všetky požadované dôkazy boli získané.

```text
KROK_5=SPLNENÉ
RESULT=HISTORICAL_PRODUCTION_EVIDENCE_OBTAINED
NEXT_ALLOWED_STEP=Krok 6 — Statická lokalizácia chybových fáz
```

Vykonateľný kód, testy ani produkčné prostredie sa nemenili.