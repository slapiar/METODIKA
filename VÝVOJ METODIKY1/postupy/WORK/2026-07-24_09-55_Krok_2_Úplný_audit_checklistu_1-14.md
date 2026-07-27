# Krok 2 — Úplný audit checklistu 1–14

## Stav kroku

```text
SPLNENÉ
```

## Väzba na záväzný plán

Tento dokument uzatvára výhradne `Krok 2 — Úplný audit checklistu 1–14` dokumentu:

`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`

Ďalší krok nebol počas vykonania tohto kroku otvorený.

---

# 1. Rozsah a spôsob auditu

Audit bol vykonaný oproti:

- aktívnemu checklistu `postupy/2026-07-23_12-27_Copilot-checklist a testovacia matica.md`,
- aktuálnemu kódu vetvy `main`,
- testovým súborom aktuálneho repozitára,
- poslednému potvrdenému produkčnému výsledku z checkpointu.

Pri každom bode sa osobitne rozlišuje:

```text
implementácia v kóde
× existencia testu
× historicky zaznamenané spustenie testu
× produkčné pozorovanie
× výsledný stav auditu
```

V tomto kroku sa testy znovu nespúšťali. Tvrdenie `test bol spustený` preto znamená iba historický záznam v repozitári alebo checkpointe, nie nové vykonanie počas Kroku 2.

Povolené hodnotenie:

```text
HOTOVÉ
ČIASTOČNE
NEOVERENÉ
CHYBNÉ
```

---

# 2. Súhrnná auditná tabuľka

| Krok | Oblasť | Stav |
|---:|---|---|
| 1 | Run store a lock protokol | ČIASTOČNE |
| 2 | Stavový model a validácia dokumentu | ČIASTOČNE |
| 3 | Feature flag a jeho testovanie | HOTOVÉ |
| 4 | START endpoint | ČIASTOČNE |
| 5 | HIT bez `accept()` | ČIASTOČNE |
| 6 | Bariéra a timeout | CHYBNÉ |
| 7 | `accept()` a zápis výsledkov | CHYBNÉ |
| 8 | Finalization claim | ČIASTOČNE |
| 9 | Invarianty a cleanup | ČIASTOČNE |
| 10 | Tombstone, result a sweep | ČIASTOČNE |
| 11 | UI | ČIASTOČNE |
| 12 | Unit testy | ČIASTOČNE |
| 13 | Integračný webový test | ČIASTOČNE |
| 14 | Produkčné diagnostické overenie | CHYBNÉ |

Súhrn:

```text
HOTOVÉ = 1
ČIASTOČNE = 9
NEOVERENÉ = 0
CHYBNÉ = 4
```

---

# 3. Audit jednotlivých krokov

## 1. Run store a lock protokol

### Implementované

`DiagnosticsConcurrencyRunStore` používa:

- adresár `writable/diagnostics/concurrency/`,
- stabilný `{runId}.lock`,
- samostatný `{runId}.json`,
- `flock()` pre zdieľaný a exkluzívny zámok,
- read-modify-write v `mutate()` pod jedným exkluzívnym zámkom,
- zápis cez dočasný súbor a atomický `rename()`.

### Pokrytie testom

`DiagnosticsConcurrencyRunStoreTest` overuje:

- save/load roundtrip,
- vznik `.lock` a `.json`,
- sekvenčný atomický read-modify-write,
- stabilitu inode lock súboru pri nahradení JSON,
- odstránenie temp súborov,
- idempotentný cleanup,
- odmietnutie neplatného runId, dokumentu a prechodu.

### Chýbajúci dôkaz

Test `testMutatePerformsAtomicReadModifyWrite()` používa iba jeden proces a sekvenčné volanie. Neexistuje dôkaz dvoch skutočne súbežných zápisov do toho istého runu.

### Vedľajší účinok

`cleanup()` odstraňuje lock súbor po uvoľnení zámku. Pri súbežnom procese, ktorý už má starý file descriptor alebo čaká na rovnaký inode, môže vzniknúť rozdiel medzi starým otvoreným lockom a novým lock súborom vytvoreným ďalším requestom. Tento scenár testovaný nie je.

### Stav

```text
ČIASTOČNE
```

---

## 2. Stavový model a validácia dokumentu

### Implementované

`DiagnosticsConcurrencyRunState` obsahuje všetkých jedenásť stavov deklarovaných checklistom a explicitnú mapu prechodov.

`DiagnosticsConcurrencyRunDocumentValidator` overuje:

- základné polia dokumentu,
- participant sloty,
- bariéru,
- finalization,
- cleanup,
- assertions,
- povinné tombstone polia v ukončených stavoch,
- dokončenie oboch participantov v `RESULTS_READY`,
- nemennosť `runId` pri prechode.

### Pokrytie testom

Existujú unit testy stavového modelu, validatora a základných prechodov. Run store test odmieta neplatný stav a neplatný prechod.

### Nedostatky

Validator neoveruje obsah `input`, hoci START a `accept()` na ňom závisia. Neoveruje ani významové väzby medzi:

- `barrier.openedAt` a stavom,
- `finalization.claimedAt` a `claimedBy`,
- ukončeným stavom a úplnosťou assertions,
- `COMPLETED_SUCCESS` a pravdivosťou všetkých troch osí.

Časť invariantov je teda ponechaná iba na kontroléri, nie na dokumentovom kontrakte.

### Stav

```text
ČIASTOČNE
```

---

## 3. Feature flag a jeho testovanie

### Implementované

Webová vetva kontroluje samostatný flag `METODIKA_CONCURRENCY_WEB_ENABLED`. START, HIT, RESULT aj zobrazenie UI sú flagom podmienené.

### Pokrytie testom

Session testy obsahujú scenáre vypnutého a zapnutého flagu pre START a zobrazenie UI.

### Produkčné pozorovanie

Diagnostická vetva bola v produkcii prakticky dostupná pri poslednom rune, čo potvrdzuje, že flag bol v tom čase zapnutý. Aktuálna hodnota externého prostredia nie je predmetom Kroku 2.

### Stav

```text
HOTOVÉ
```

---

## 4. START endpoint

### Implementované

Aktívna route smeruje na:

```text
DiagnosticsConcurrencyStartController::start
```

Táto vetva:

- vytvára runId a tokeny,
- ukladá iba hash tokenov,
- vracia plain tokeny iba v odpovedi,
- ukladá nemenný vstup,
- používa CSRF,
- vracia bezpečný START errorCode a zapisuje detail do logu.

### Pokrytie testom

Session test overuje vytvorenie runu, vrátenie tokenov a uloženie ich hashov.

### Nedostatok

V `DiagnosticsController` zostala druhá verejná metóda `startConcurrencyRun()` s vlastnou implementáciou dokumentu a odlišným chybovým správaním. Route ju nepoužíva. Ide o mŕtvu alebo historickú vetvu, ktorá môže pri budúcej zmene vytvoriť rozdiel medzi testovanou a zamýšľanou implementáciou.

### Stav

```text
ČIASTOČNE
```

---

## 5. HIT bez `accept()`

### Implementované

`handleHit()`:

- prijíma iba runId a participant token,
- kontroluje globálnu diagnostiku, feature flag a session autorizáciu,
- uvoľňuje PHP session pred čakaním,
- overuje token hash, consumed stav, TTL a participant slot,
- povoľuje HIT iba v `CREATED` alebo `WAITING_FOR_PARTNER`,
- zapisuje `consumedAt` a `readyAt`,
- pri chybe vracia fallback 404 bez úmyselnej mutácie.

### Pokrytie testom

Existujú session scenáre pre vytvorenie HIT toku, timeout a bezpečnostné hranice. Úplnosť všetkých deklarovaných negatívnych stavov bude predmetom Kroku 3 — auditu matice M01–M26.

### Nedostatok

Kontrola expirácie v prvej mutácii iba vyhodí výnimku a vráti 404. Nevedie expirovaný run cez finalization claim a cleanup tok, hoci to checklist požaduje v kroku 6.

### Stav

```text
ČIASTOČNE
```

---

## 6. Bariéra a timeout

### Implementované

- bariéra sa zapisuje pod lockom pomocou `openedAt ??=`,
- wait loop nedrží lock,
- timeout vetva používa finalization claim,
- `mutate()` má poslednú poistku, ktorá zabráni zápisu `PARTNER_TIMEOUT`, ak už aktuálny dokument obsahuje otvorenú bariéru.

### Potvrdené produkciou

Posledný produkčný run potvrdil:

```text
barrierOpened = true
timeoutReached = false
```

### Chyby a nedostatky

1. `DiagnosticsConcurrencyRunStore::load()` nevracia vždy presný uložený dokument. Pri uloženom `EXECUTING`, otvorenej bariére a iba jednom `startedAt` prepíše v návratovej kópii stav na `BARRIER_OPEN`.
2. Wait loop je na túto interpretáciu priamo odkázaný.
3. Test run store neoveruje túto interpretáciu.
4. Neexistuje samostatný regresný test pre pretekové okno „deadline dosiahnutý, bariéra otvorená tesne pred timeout mutáciou“.
5. Expirácia z HIT prechecku nejde cez finalization claim a cleanup.

Produkčný úspech bariéry nepotvrdzuje správnosť všetkých pretekových okien.

### Stav

```text
CHYBNÉ
```

---

## 7. `accept()` a zápis výsledkov

### Implementované

Po bariére sa:

- participant označí `startedAt`,
- stav sa posunie na `EXECUTING`,
- `accept()` sa vykoná mimo file locku,
- vstup sa načíta z run dokumentu,
- outcome a errorCode sa zapíšu pod lockom,
- po výsledku oboch participantov vznikne `RESULTS_READY`.

`DiagnosticsConcurrencyAcceptanceRunner` zachytáva výnimky aplikačnej služby, loguje triedu a správu a vracia bezpečný výsledok `FAILED_<CLASSIFIED_CODE>`.

### Potvrdená chyba

Vonkajší `try/catch` v `executeAcceptIfReady()` spája do jedného kódu `ACCEPT_RUNTIME_ERROR`:

- chybu zostavenia `InitialDerivationRun`,
- chybu načítania fingerprintu,
- chybu získania runnera,
- výnimku uniknutú z runnera.

Tým zahadzuje fázu vzniku chyby. Produkčný run zároveň skončil dvojicou:

```text
FAILED_RUNTIME_ERROR + CREATED
```

Aplikačný replay teda neprešiel.

### Vedľajší účinok

Outcome typu `FAILED_RUNTIME_ERROR` je ukladaný do poľa `outcome`, zatiaľ čo `errorCode` môže zostať null. Model výsledku a model chyby nie sú dôsledne oddelené.

### Stav

```text
CHYBNÉ
```

---

## 8. Finalization claim

### Implementované

Claim sa zapisuje v `mutate()` pod exkluzívnym lockom. Prvý participant nastaví `claimedAt` a `claimedBy`; druhý sa zapíše medzi waiters a nevykoná cleanup.

### Pokrytie testom

Existujú sekvenčné session a unit scenáre finalizácie a waitera.

### Chýbajúci dôkaz

Nie je potvrdený test dvoch procesov alebo requestov, ktoré skutočne súčasne súťažia o claim. Sekvenčné volania pod jedným PHP procesom nepreukazujú race správanie.

### Stav

```text
ČIASTOČNE
```

---

## 9. Invarianty a cleanup

### Implementované

Finalizer:

- počíta rezervácie pred cleanupom,
- overuje presnú dvojicu `CREATED + ALREADY_EXISTS`,
- vykoná cleanup iba ako claimant,
- vykoná post-check rezervácií, behov a doménových pojmov,
- nedovolí `COMPLETED_SUCCESS`, ak zlyhá replay alebo cleanup.

### Produkčné pozorovanie

Posledný run potvrdil:

```text
dbUniquenessConfirmed = true
cleanupConfirmed = true
appReplayConfirmed = false
overallSuccess = false
state = COMPLETED_FAILED
```

### Nedostatky

- DB unikátnosť sa redukuje na `reservations === 1`; počty behov a doménových väzieb pred cleanupom nie sú súčasťou DB invariantnej osi.
- Cleanup vetva zachytí výnimku iba všeobecným `CLEANUP_FAILED` bez fázy.
- Audit testov manuálneho cleanupu a cleanup fail scenárov patrí do Kroku 3.

### Stav

```text
ČIASTOČNE
```

---

## 10. Tombstone, result a sweep

### Implementované

- finalizácia redukuje dokument na tombstone,
- odstraňuje `input`, token hashy a pracovné timestampy,
- ponecháva outcomes, bezpečné errorCode, assertions, cleanup a finalization,
- vytvára `completedAt`, `deleteAfter`, `readOnceConsumedAt`,
- prvé čítanie RESULT nastaví `readOnceConsumedAt`,
- po `deleteAfter` RESULT volanie vykoná fyzický cleanup JSON a lock súboru.

### Nedostatky

- fyzické mazanie nie je samostatný periodický sweep; vykoná sa iba pri neskoršom RESULT requeste,
- produkčný tombstone ani odstránenie po TTL neboli pri poslednom rune zachované ako dôkaz,
- správanie pri súbehu RESULT čítania a cleanupu nie je preukázané.

### Stav

```text
ČIASTOČNE
```

---

## 11. UI

### Implementované

Diagnostics view obsahuje:

- START,
- paralelné fetch volania HIT A/B,
- polling RESULT,
- zobrazenie troch osí a overall výsledku.

UI sa zobrazuje iba pri zapnutom concurrency feature flagu.

### Produkčné pozorovanie

UI umožnilo vykonať produkčný run a zobraziť výsledky.

### Nedostatok

Checkpoint potvrdzuje riziko, že HTTP 200 a priebehové hlášky môžu používateľsky pôsobiť ako úspech, hoci aplikačný replay zlyhal. Samotná existencia troch osí nestačí; musí byť jednoznačne oddelený transportný priebeh od aplikačného výsledku.

### Stav

```text
ČIASTOČNE
```

---

## 12. Unit testy

### Implementované

Existujú unit testy pre:

- run store,
- stabilný lock súbor,
- stavový model,
- validator,
- základné invarianty a sanitizáciu.

Historický záznam uvádza úspešný beh unit suite pred produkčným runom.

### Chýbajúce pokrytie

Nie sú preukázané samostatné testy pre:

- dva skutočne súbežné procesy pri mutate,
- interpretovaný `load()` pri uloženom `EXECUTING`,
- poslednú timeout poistku v reálnom pretekovom okne,
- rozlíšenie fáz vonkajšieho `try/catch`,
- rollback prvého participantu a následné správanie druhého,
- skutočný race finalization claimu.

### Stav

```text
ČIASTOČNE
```

---

## 13. Integračný webový test

### Implementované

Session suite obsahuje tok `START → HIT → RESULT`, scenáre timeoutu, tombstone, cleanupu a bezpečnostných hraníc.

### Nedostatky

- ide o CodeIgniter feature/session testy v jednom testovom procese,
- acceptance runner a persistence service sú v rozhodujúcich scenároch mockované,
- „paralelnosť“ nie je dôkazom dvoch súčasných HTTP procesov,
- test neodhalil produkčný výsledok `FAILED_RUNTIME_ERROR + CREATED`,
- START testuje aktívny samostatný START controller, ale v hlavnom controlleri zostáva duplicitná implementácia.

### Stav

```text
ČIASTOČNE
```

---

## 14. Produkčné diagnostické overenie

### Vykonané pozorovanie

Produkčný run sa prakticky vykonal. Potvrdil:

- HTTP 200 pre START, HIT A, HIT B a RESULT,
- otvorenie bariéry,
- absenciu timeoutu,
- DB unikátnosť,
- cleanup.

### Výsledok

```text
state = COMPLETED_FAILED
A = FAILED_RUNTIME_ERROR
B = CREATED
appReplayConfirmed = false
overallSuccess = false
```

Sweep a fyzické odstránenie run súboru po TTL neboli potvrdené.

Produkčné overenie teda nebolo iba „otvorené“ alebo „neoverené“. Bolo vykonané a skončilo neúspechom.

### Stav

```text
CHYBNÉ
```

---

# 4. Hlavné závery auditu

## Potvrdené skutočnosti

1. Aktívny produkčný START endpoint používa `DiagnosticsConcurrencyStartController`, nie duplicitnú START metódu v `DiagnosticsController`.
2. Run store používa stabilný lock súbor a atomický replace JSON.
3. `load()` vracia pri konkrétnom stave interpretovanú kópiu namiesto presného uloženého stavu.
4. Posledná timeout poistka je implementovaná pod exkluzívnym lockom, ale nemá preukázaný regresný race test.
5. Vonkajší `try/catch` v `executeAcceptIfReady()` zahadzuje rozdiel medzi viacerými fázami chyby.
6. Existujúce webové testy nie sú skutočnými paralelnými dvojprocesovými HTTP testami.
7. Produkčný run potvrdil DB unikátnosť a cleanup, ale nepotvrdil aplikačný replay.

## Najzávažnejšie nedostatky

```text
Krok 6 — bariéra a timeout
Krok 7 — accept a diagnostické rozlíšenie
Krok 13 — integračný test nepreukazuje reálnu paralelnosť
Krok 14 — produkčný cieľ zlyhal
```

## Čo tento audit ešte nerieši

Audit Kroku 2 neurčuje podrobné mapovanie každého scenára `M01–M26`. To je výhradne nasledujúci Krok 3.

Audit nemení aplikačný kód, testy ani produkčné prostredie.

---

# 5. Validácia a uzavretie

Kritérium Kroku 2 bolo splnené:

- všetkých 14 bodov má samostatný záver,
- každý bod rozlišuje implementáciu, test, historické spustenie a produkčné pozorovanie,
- nič nebolo označené ako hotové iba podľa deklarácie checklistu,
- chýbajúce dôkazy a vedľajšie účinky sú pomenované.

```text
KROK_2=SPLNENÉ
NEXT_ALLOWED_STEP=Krok 3 — Audit testovacej matice M01–M26
```

Vykonateľný kód ani produkčné prostredie neboli týmto krokom zmenené.
