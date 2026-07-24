# Krok 6 — Statická lokalizácia chybových fáz

## Stav kroku

```text
SPLNENÉ
```

## Väzba na záväzný plán

Tento dokument uzatvára výhradne `Krok 6 — Statická lokalizácia chybových fáz` dokumentu:

`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`

Ďalší krok nebol počas vykonania tohto kroku otvorený. Vykonateľný kód ani produkčné prostredie sa nemenili.

---

# 1. Predmet statického auditu

Bez zmeny kódu bola preverená cesta:

```text
DiagnosticsController::executeAcceptIfReady()
→ DiagnosticsConcurrencyAcceptanceRunner::accept()
→ FirstAcceptanceServiceFactory::fromDefaultConnection()
→ FirstAcceptanceService::accept()
→ DatabaseTransactionBoundary::run()
→ RequestReferenceRepository::reserveFirstAcceptance()
→ DerivationHistoryRepository::createInitialRun()
→ databáza
→ spätný zápis participant outcome do run store
```

Východiskovým produkčným dôkazom z Kroku 5 je:

```text
RuntimeException:
Historický beh nemožno založiť bez presnej rezervácie REQUEST_REFERENCE.
```

Táto správa vzniká iba v:

```text
codei/app/Infrastructure/Persistence/QuestionDerivation/DerivationHistoryRepository.php
DerivationHistoryRepository::createInitialRun()
```

pri nenájdení presnej dvojice:

```text
request_reference + derivation_reference
```

---

# 2. Zostavenie a vstupné fázy v kontroléri

`DiagnosticsController::executeAcceptIfReady()` najprv označí participanta ako spusteného a stav runu nastaví na `EXECUTING`.

Potom v jednom spoločnom `try/catch` vykonáva:

```text
BUILD_INITIAL_RUN
LOAD_PAYLOAD_FINGERPRINT
CREATE_OR_LOAD_ACCEPTANCE_RUNNER
RUN_ACCEPTANCE
```

Spoločný catch nerozlišuje fázu:

```text
catch (Throwable) {
    errorCode = ACCEPT_RUNTIME_ERROR
}
```

Ak zlyhá zostavenie `InitialDerivationRun`, načítanie fingerprintu alebo získanie runnera, verejný run dokument dostane:

```text
outcome=FAILED
errorCode=ACCEPT_RUNTIME_ERROR
```

Takáto chyba nevstúpi do interného catch-u runnera a nevytvorí log `Diagnostics acceptance failed [...]`.

Produkčný log tento tvar nemal. Obsahoval interný log runnera a outcome `FAILED_RUNTIME_ERROR`. Preto produkčná chyba nevznikla vo fázach `BUILD_INITIAL_RUN`, `LOAD_PAYLOAD_FINGERPRINT` ani pri získaní služby z `Config\Services`.

---

# 3. Runner a vytvorenie aplikačnej služby

`DiagnosticsConcurrencyAcceptanceRunner` vo svojom acceptore vykonáva:

```text
FirstAcceptanceServiceFactory::fromDefaultConnection()
→ accept(payloadFingerprint, run)
```

Celá táto cesta je obalená interným `try/catch` runnera.

Runner:

1. klasifikuje výnimku,
2. zapisuje serverový log:

```text
Diagnostics acceptance failed [<code>]: <class>: <message>
```

3. vracia bezpečný outcome:

```text
FAILED_<code>
```

Produkčný výsledok:

```text
FAILED_RUNTIME_ERROR
```

preto dokazuje, že výnimku zachytil runner, nie vonkajší catch kontroléra.

Factory vytvára všetky tri databázové komponenty nad tým istým objektom `BaseConnection`:

```text
RequestReferenceRepository
DerivationHistoryRepository
DatabaseTransactionBoundary
```

Staticky teda nejde o úmyselne oddelené spojenia medzi rezerváciou a históriou v rámci jedného participanta.

---

# 4. Transakčná cesta aplikačnej služby

`FirstAcceptanceService::accept()` vykonáva v jednej transakcii:

```text
RESERVE_FIRST_ACCEPTANCE
→ ak ALREADY_EXISTS, okamžite vráti existujúcu rezerváciu
→ ak CREATED, CREATE_INITIAL_HISTORY_RUN
→ TRANSACTION_COMMIT
```

Pri akejkoľvek výnimke `DatabaseTransactionBoundary` vykoná:

```text
TRANSACTION_ROLLBACK
→ znovu vyhodí pôvodnú výnimku
```

Produkčná `RuntimeException` z histórie preto spôsobila rollback všetkých zápisov daného participanta. Výnimku následne zachytil runner a premenil ju na `FAILED_RUNTIME_ERROR`.

---

# 5. Presný rozpor medzi rezerváciou a históriou

## 5.1 Rezervácia

`RequestReferenceRepository::reserveFirstAcceptance()` sa pokúsi vložiť:

```text
request_reference
payload_fingerprint
derivation_reference
reservation_state=RESERVED
```

Po insert-e však spätné overenie vykonáva iba:

```text
findByRequestReference(request_reference)
```

Táto metóda nevyžaduje zhodu:

```text
derivation_reference
payload_fingerprint
```

Ak nájde ľubovoľnú rezerváciu s rovnakým `request_reference`, repository vytvorí výsledok:

```text
ReservationResult::CREATED
```

aj keď nájdený riadok môže patriť druhému participantovi s iným `derivation_reference`.

## 5.2 História

`DerivationHistoryRepository::createInitialRun()` naopak vyžaduje presnú dvojicu:

```sql
WHERE request_reference = ?
  AND derivation_reference = ?
```

Ak rezervácia existuje iba pre derivation reference partnera, vyhodí presne produkčne zaznamenanú výnimku:

```text
Historický beh nemožno založiť bez presnej rezervácie REQUEST_REFERENCE.
```

Záver:

```text
RESERVATION_POSTCHECK_CONTRACT ≠ HISTORY_PRECONDITION
```

Rezervácia potvrdzuje iba existenciu `REQUEST_REFERENCE`, história vyžaduje presnú koreláciu `REQUEST_REFERENCE + derivation_reference`.

---

# 6. Produkčné DBDebug a nekontrolovaný insert

Produkčná databázová konfigurácia má:

```text
DBDebug=false
```

`reserveFirstAcceptance()` volá:

```php
$this->db->table(...)->insert(...);
```

ale návratovú hodnotu `insert()` nekontroluje.

Repository očakáva, že duplicitný kľúč vždy vyvolá `DatabaseException` s kódom `1062`. Pri `DBDebug=false` však databázová chyba môže byť zaznamenaná frameworkom a `insert()` môže vrátiť `false` bez vstupu do catch vetvy repository.

Potom aktuálny kód pokračuje:

```text
insert() = false
→ findByRequestReference(request_reference)
→ nájde rezerváciu druhého participanta
→ vráti CREATED
→ createInitialRun() hľadá vlastné derivation_reference
→ presnú rezerváciu nenájde
→ RuntimeException
→ rollback
→ FAILED_RUNTIME_ERROR
```

Toto je staticky úplná cesta, ktorá presne vysvetľuje produkčnú dvojicu:

```text
jeden participant = CREATED
druhý participant = FAILED_RUNTIME_ERROR
```

aj presnú správu produkčnej výnimky.

Výsledok Kroku 6:

```text
STATIC_ROOT_CAUSE_CLASS=
UNCHECKED_FAILED_RESERVATION_INSERT_WITH_NON_EXACT_POSTCHECK
```

Krok 6 tým ešte netvrdí, že táto cesta bola runtime reprodukovaná mimo produkcie. Túto povinnosť má Krok 7.

---

# 7. Mapa fáz, zachytenia a verejných výsledkov

| Fáza | Miesto | Možná chyba | Kto ju zachytí | Verejný výsledok |
|---|---|---|---|---|
| `BUILD_INITIAL_RUN` | `DiagnosticsController::buildInitialRunFromDocument()` a `InitialDerivationRun::__construct()` | chýbajúce pole, neplatná referencia, duplicita termínov, nesprávny režim | vonkajší catch `executeAcceptIfReady()` | `outcome=FAILED`, `errorCode=ACCEPT_RUNTIME_ERROR` |
| `LOAD_PAYLOAD_FINGERPRINT` | `payloadFingerprintFromDocument()` | chýbajúci input alebo fingerprint | vonkajší catch kontroléra | `FAILED / ACCEPT_RUNTIME_ERROR` |
| `CREATE_CONNECTION_OR_FACTORY` | runner → `FirstAcceptanceServiceFactory::fromDefaultConnection()` | chyba pripojenia alebo factory | catch runnera | podľa klasifikácie spravidla `FAILED_DATABASE_ERROR` alebo `FAILED_RUNTIME_ERROR` |
| `RESERVE_FIRST_ACCEPTANCE` — validácia | `RequestReferenceRepository` | neplatné referencie alebo fingerprint | catch runnera | `FAILED_RUNTIME_ERROR` |
| `RESERVE_FIRST_ACCEPTANCE` — DB výnimka mimo 1062 | `RequestReferenceRepository` | databázová chyba | catch runnera | `FAILED_DATABASE_ERROR` pri rozpoznanej DB výnimke |
| `RESERVE_FIRST_ACCEPTANCE` — 1062 s vyhodenou výnimkou | repository catch | existujúca rezervácia | repository ju spracuje | `ALREADY_EXISTS` |
| `RESERVE_FIRST_ACCEPTANCE` — `insert() === false` bez výnimky | repository | návratová hodnota sa nekontroluje | nikto v tejto fáze | môže vzniknúť falošné `CREATED` |
| `LOAD_EXISTING_RESERVATION` | `findByRequestReference()` | riadok nenájdený po insert-e alebo kolízii | repository vyhodí RuntimeException, runner zachytí | `FAILED_RUNTIME_ERROR` |
| `CREATE_INITIAL_HISTORY_RUN` — presná rezervácia | `DerivationHistoryRepository::createInitialRun()` | nenájdená dvojica request + derivation | runner | `FAILED_RUNTIME_ERROR`; presná produkčná fáza |
| `CREATE_INITIAL_HISTORY_RUN` — insert runu | `DerivationHistoryRepository` | DB chyba alebo neplatné `insertID` | runner | `FAILED_DATABASE_ERROR` alebo `FAILED_RUNTIME_ERROR` |
| `INSERT_DOMAIN_TERMS` | `DerivationHistoryRepository` | DB chyba termínu/FK/unikátnosti | runner | spravidla `FAILED_DATABASE_ERROR` |
| `TRANSACTION_COMMIT` | `DatabaseTransactionBoundary` | commit vráti false | runner | `FAILED_RUNTIME_ERROR`, pretože správa a trieda nemusia byť klasifikované ako DB |
| `TRANSACTION_ROLLBACK` | `DatabaseTransactionBoundary` | pôvodná výnimka sa po rollbacku znovu vyhodí; výsledok rollbacku sa nekontroluje | runner | kód podľa pôvodnej výnimky |
| `WRITE_PARTICIPANT_RESULT` | záverečný `runStore()->mutate()` v kontroléri | run nenájdený, neplatný slot, chyba store/validatora | lokálne nikto | výnimka môže uniknúť do frameworku a spôsobiť HTTP 500 |

---

# 8. Nedostatky diagnostického rozlíšenia potvrdené statickým auditom

1. Vonkajší catch kontroléra zlieva zostavenie runu, fingerprint a získanie runnera do `ACCEPT_RUNTIME_ERROR`.
2. Runner klasifikuje všeobecnú `RuntimeException` transakčnej alebo repository vrstvy ako `RUNTIME_ERROR`, aj keď príčina môže byť databázová.
3. `WRITE_PARTICIPANT_RESULT` nie je kryté lokálnym catchom.
4. Verejný participant `errorCode` zostáva pri outcome `FAILED_RUNTIME_ERROR` hodnotou `null`, pretože bezpečný kód je vložený iba do textu outcome runnera.
5. Nekontrolovaný výsledok `insert()` umožňuje pokračovať po databázovej chybe ako po úspešnom inserte.
6. Spätný postcheck rezervácie nie je presný a nekontroluje fingerprint ani derivation reference.

Tieto zistenia určujú predmet neskoršieho Kroku 8, ale v Kroku 6 sa kód nemenil.

---

# 9. Vzťah k neskoršiemu logu Duplicate entry

Neskorší produkčný log:

```text
Duplicate entry 'diag-request-6022895cf78e5aac'
for key 'uq_qdrr_request_reference'
```

nepatrí časovo k runu z Kroku 5. Staticky však potvrdzuje, že produkčné spojenie reálne zažilo duplicitný insert na rovnakom unikátnom kľúči. Samotný riadok nedokazuje, či v konkrétnom requeste vznikla výnimka alebo iba `false` návratová hodnota.

Preto zostáva samostatným podporným pozorovaním, nie korelačným dôkazom posledného runu.

---

# 10. Kritérium uzavretia

Pre každú plánom požadovanú fázu je určené:

- miesto vykonania,
- trieda alebo druh chyby,
- hranica zachytenia,
- verejný outcome alebo errorCode,
- prípad, keď chyba nie je lokálne zachytená.

Presná produkčná výnimka je lokalizovaná do `CREATE_INITIAL_HISTORY_RUN` a statická koreňová cesta je identifikovaná ako nekontrolovaný neúspešný insert spojený s nepresným postcheckom rezervácie.

```text
KROK_6=SPLNENÉ
ROOT_FAILURE_PHASE=CREATE_INITIAL_HISTORY_RUN
STATIC_CAUSAL_PATH=UNCHECKED_INSERT_FALSE_TO_NON_EXACT_REQUEST_REFERENCE_POSTCHECK
NEXT_ALLOWED_STEP=Krok 7 — Reprodukcia koreňovej príčiny mimo produkcie
```

Vykonateľný kód, testy ani produkčné prostredie sa nemenili.
