# Implementačný checklist a testovacia matica webového súbežného overenia

## Stav dokumentu

```text
PRACOVNÝ
```

## Čas vytvorenia

```text
2026-07-23 14:21 Europe/Bratislava
```

## Účel

Tento dokument je implementačným podkladom pre krátko-žijúci, jednorazový webový diagnostický scenár, ktorý má overiť súbežné prvé prijatie rovnakej `REQUEST_REFERENCE` cez dva samostatné HTTP requesty.

Dokument neimplementuje funkciu, nemení doménovú logiku a nezavádza nový trvalý databázový objekt. Určuje poradie najmenších bezpečných zásahov, výsledkové invarianty a testovaciu maticu.

## Záväzné hranice

1. Koordinácia sa ukladá iba do `writable/diagnostics/concurrency/`.
2. Každý run používa stabilný `{runId}.lock` pre `flock` a samostatný `{runId}.json` pre dáta.
3. JSON sa zapisuje cez dočasný súbor a atomický `rename`, vždy pod zámkom na `.lock` súbore.
4. Zámok sa nikdy nedrží počas čakania, volania `accept()` ani databázového cleanupu.
5. Participant endpointy prijímajú iba `runId` a jednorazový `participantToken`.
6. Celý nemenný aplikačný vstup vytvára server pri `START`.
7. Po autorizačnej kontrole sa pred bariérou, čakaním alebo databázovou operáciou vykoná `session_write_close()`.
8. CSRF výnimka sa môže týkať iba presných endpointov `hit/a` a `hit/b`.
9. Neplatný alebo už spotrebovaný participant token vracia diagnostický fallback `404` a nemení run dokument.
10. Do run dokumentu sa zapisujú iba bezpečné chybové kódy; surové výnimky patria iba do sanitizovaného interného logu.
11. `COMPLETED_SUCCESS` je prípustný iba pri súčasnom potvrdení databázovej unikátnosti, aplikačného replay výsledku a cleanupu.
12. Tombstone sa po prvom čítaní nemaže; fyzicky ho odstráni až sweep po `deleteAfter`.

---

# A. Implementačný checklist

## 0. Inicializácia pracovného kroku

- [ ] Znovu prečítať `postupy/Inicializácia práce.md`.
- [ ] Overiť autoritatívny repozitár `slapiar/METODIKA` a vetvu `main`.
- [ ] Načítať aktuálne verzie `DiagnosticsController.php`, `Routes.php`, `Security.php`, `FirstAcceptanceService.php`, `RequestReferenceRepository.php`, diagnostických views a testov.
- [ ] Overiť aktuálnu ochranu diagnostiky, fallback `404`, session TTL a feature flagy.
- [ ] Potvrdiť, že `RequestReferenceRepository` stále mapuje databázovú kolíziu `1062` na `ALREADY_EXISTS` opätovným načítaním rezervácie.
- [ ] Určiť presný rozsah jedného implementačného kroku a rollback postup.

## 1. Run store a lock protokol

- [ ] Vytvoriť službu pre adresár `writable/diagnostics/concurrency/`.
- [ ] Zaviesť dvojicu `{runId}.lock` a `{runId}.json`.
- [ ] Implementovať bezpečné vytvorenie adresára bez verejnej dostupnosti.
- [ ] Implementovať čítanie dokumentu pod `LOCK_SH` alebo primeraným výhradným lockom podľa operácie.
- [ ] Implementovať modifikáciu pod `LOCK_EX`.
- [ ] Implementovať zápis `temp + fflush + rename` pri stále držanom zámku na `.lock` súbore.
- [ ] Overiť, že žiadna vetva nedrží lock počas `sleep`, `accept()` alebo cleanupu.
- [ ] Implementovať idempotentné odstránenie `.json`, `.lock` a dočasných súborov.
- [ ] Zakázať názvy súborov odvodené priamo z nevalidovaného klientského vstupu.

## 2. Stavový model a validácia run dokumentu

- [ ] Zaviesť stavy `CREATED`, `WAITING_FOR_PARTNER`, `BARRIER_OPEN`, `EXECUTING`, `RESULTS_READY`, `FINALIZATION_CLAIMED`, `CLEANUP_PENDING`, `COMPLETED_SUCCESS`, `COMPLETED_FAILED`, `COMPLETED_FAILED_CLEANUP`, `EXPIRED`.
- [ ] Zaviesť samostatné participant polia pre A a B: `tokenHash`, `consumedAt`, `readyAt`, `startedAt`, `finishedAt`, `outcome`, `errorCode`.
- [ ] Zaviesť `barrier.openedAt` a `barrier.waitTimeoutMs`.
- [ ] Zaviesť `finalization.claimedAt`, `finalization.claimedBy`, `finalization.finishedAt`.
- [ ] Zaviesť cleanup a assertion polia.
- [ ] Validovať povinné polia a typy po každom načítaní.
- [ ] Odmietnuť neznámy alebo poškodený stav bezpečným interným zlyhaním bez úniku obsahu dokumentu.
- [ ] Udržať `RESULTS_READY` výhradne pre oba hotové participant výsledky.

## 3. Endpoint `POST /diagnostics/concurrency/start`

- [ ] Pridať samostatný feature flag `METODIKA_CONCURRENCY_WEB_ENABLED=1`.
- [ ] Zachovať existujúcu diagnostickú autorizáciu a fallback `404`.
- [ ] Zachovať CSRF ochranu.
- [ ] Po auth vykonať `session_write_close()`.
- [ ] Vygenerovať kryptograficky náhodný `runId`, token A a token B.
- [ ] Uložiť iba hash participant tokenov.
- [ ] Serverovo vytvoriť nemenný vstup: `requestReference`, `payloadFingerprint`, `derivationReferenceA`, `derivationReferenceB`, `derivationApplicationInput`.
- [ ] Nastaviť bezpečne obmedzené timeouty a TTL; klient nesmie presadiť neobmedzené hodnoty.
- [ ] Plain tokeny vrátiť iba v tejto jednej odpovedi.
- [ ] Nevypísať DB konfiguráciu, prihlasovacie údaje ani interné cesty.

## 4. Endpointy `hit/a` a `hit/b` bez aplikačného volania

- [ ] Pridať presné POST routes pre A a B.
- [ ] CSRF výnimku obmedziť iba na tieto dve route.
- [ ] Overiť diagnostickú auth a následne uzavrieť session lock.
- [ ] Akceptovať iba `runId` a `participantToken`.
- [ ] Povoliť prvý platný vstup iba v stavoch `CREATED` alebo `WAITING_FOR_PARTNER`.
- [ ] Porovnať token cez bezpečný hash mechanizmus.
- [ ] Pri neplatnom alebo spotrebovanom tokene vrátiť fallback `404` bez modifikácie runu.
- [ ] Atomicky nastaviť `consumedAt` a `readyAt` vlastného slotu.
- [ ] Atomicky otvoriť bariéru iba raz, keď sú pripravení obaja participanti.

## 5. Bariéra a timeout

- [ ] Implementovať polling iba ako krátke lock-read cykly.
- [ ] Sleep vykonávať vždy mimo locku.
- [ ] Zaviesť pevný horný limit `barrierWaitTimeoutMs`.
- [ ] Pri timeout vetve zapísať bezpečný kód `PARTNER_TIMEOUT`.
- [ ] Aj timeout vetvu viesť cez rovnaký atomický finalization claim.
- [ ] Zabezpečiť, že hraničný príchod druhého participantu nemôže spustiť dve finalizácie.

## 6. Dve nezávislé volania `accept()`

- [ ] Po `barrier.openedAt` uvoľniť všetky súborové zámky.
- [ ] Pre A použiť `derivationReferenceA`, pre B `derivationReferenceB`.
- [ ] Pre oba použiť rovnaké `requestReference` a `payloadFingerprint`.
- [ ] Atomicky zapísať vlastný `startedAt` bez prepisovania druhého slotu.
- [ ] Vykonať `FirstAcceptanceService::accept()` cez štandardné webové default DB spojenie daného HTTP requestu.
- [ ] Nezavádzať ručné klonovanie ani rekonštrukciu DB konfigurácie.
- [ ] Výnimku previesť na bezpečný kód `ACCEPT_RUNTIME_ERROR`; plný detail iba do sanitizovaného logu.
- [ ] Pod lockom zapísať `finishedAt`, `outcome` a prípadný `errorCode`.
- [ ] Pri jednom hotovom výsledku ponechať globálny stav `EXECUTING`.
- [ ] `RESULTS_READY` nastaviť až po zapísaní oboch výsledkov.

## 7. Atomický finalization claim

- [ ] Pod `LOCK_EX` skontrolovať `finalization.claimedAt`.
- [ ] Prvý proces nastaví `claimedAt`, `claimedBy` a stav `FINALIZATION_CLAIMED`.
- [ ] Druhý proces nehlási verejnú chybu; prejde do waiter režimu.
- [ ] Claim mechanizmus použiť aj pri timeoute a expirácii.
- [ ] Iba claimant smie vykonať kontrolu invariantov a cleanup.

## 8. Kontrola invariantov a DB cleanup

- [ ] Pred cleanupom spočítať rezervácie pre `requestReference`.
- [ ] Nastaviť `dbUniquenessConfirmed=true` iba pri presne jednej rezervácii.
- [ ] Potvrdiť, že množina outcome je presne `CREATED` a `ALREADY_EXISTS`.
- [ ] Potvrdiť zhodu rezervovanej `derivationReference` s víťazným tokom.
- [ ] Pri nesúlade použiť `RESERVATION_COUNT_MISMATCH` alebo `REPLAY_OUTCOME_MISMATCH`.
- [ ] Nastaviť stav `CLEANUP_PENDING` pred DB cleanupom.
- [ ] Odstrániť testovacie doménové väzby, historické behy a rezerváciu pre danú `requestReference`.
- [ ] Overiť výsledné počty `0 + 0 + 0`.
- [ ] Pri chybe nastaviť `CLEANUP_FAILED` a `COMPLETED_FAILED_CLEANUP`.
- [ ] Nikdy neoznačiť úspech pri nepotvrdenom cleanupe.

## 9. Tombstone, result a sweep

- [ ] Po finalizácii zredukovať dokument na bezpečný tombstone.
- [ ] Odstrániť token hashy a nepotrebný aplikačný vstup.
- [ ] Zachovať stavy, assertion výsledky, bezpečné chyby, `completedAt`, `deleteAfter` a minimálne participant výsledky.
- [ ] Implementovať GET result pod diagnostickou auth.
- [ ] Pod lockom nastaviť `readOnceConsumedAt`, ak ešte nie je vyplnené.
- [ ] Tombstone po prvom čítaní nemažť.
- [ ] Implementovať sweep po `deleteAfter`.
- [ ] Sweep spúšťať bezpečne pri vybraných diagnostických vstupoch bez dlhého blokovania requestu.
- [ ] Zmazanie urobiť idempotentné.

## 10. Manuálny cleanup

- [ ] Zachovať diagnostickú auth a CSRF.
- [ ] Manuálny endpoint musí používať rovnaký finalization claim.
- [ ] Manuálny cleanup potvrdzuje iba `cleanupConfirmed`.
- [ ] `COMPLETED_SUCCESS` smie vzniknúť iba vtedy, ak už sú `dbUniquenessConfirmed` aj `appReplayConfirmed` pravdivé.
- [ ] Po úspešnom uprataní zlyhaného testu zachovať `COMPLETED_FAILED` s `cleanupConfirmed=true`.

## 11. Webové UI

- [ ] Pridať diagnostickú stránku s jedným tlačidlom Start.
- [ ] Po štarte odoslať A a B cez dve paralelné `fetch` POST požiadavky.
- [ ] Neuchovávať participant tokeny v URL, logoch ani local storage.
- [ ] Výsledok načítať cez polling result endpointu.
- [ ] Zobraziť oddelene tri osi: DB unikátnosť, aplikačný replay a cleanup.
- [ ] Zobraziť `overallSuccess` iba pri potvrdení všetkých troch osí.
- [ ] Nezobraziť surové výnimky, cesty ani DB identifikátory.
- [ ] Zachovať `no-store`, `no-cache`, CSP, frame deny a nosniff hlavičky.

## 12. Dokumentácia a evidencia

- [ ] Po každom implementačnom kroku znovu načítať dotknuté súbory.
- [ ] Aktualizovať príslušný technický návrh, register a `CHANGELOG.md` v tom istom pracovnom kroku.
- [ ] Zaznamenať presné runtime výsledky bez zamieňania tvrdenia za dôkaz.
- [ ] Po úspešnom produkčnom overení vytvoriť osobitný validačný dokument; tento checklist neprepisovať na historický výsledok.

---

# B. Testovacia matica

| ID | Vrstva | Scenár | Očakávaný výsledok |
|---|---|---|---|
| STORE-01 | Unit | Vytvorenie nového runu | Vznikne `.lock` a validný `.json`; tokeny sú uložené iba ako hash. |
| STORE-02 | Unit | Súbežné zápisy dvoch procesov | JSON zostane validný, zmeny participantov sa nestratia. |
| STORE-03 | Unit | Zápis cez temp + rename | Lock ostáva na stabilnom `.lock`; čitateľ nikdy nevidí čiastočný JSON. |
| STORE-04 | Unit | Poškodený JSON | Bezpečné zlyhanie, sanitizovaný log, žiadny únik obsahu. |
| STORE-05 | Unit | Idempotentné delete | Opakované zmazanie nespôsobí runtime chybu. |
| AUTH-01 | HTTP | Diagnostika vypnutá | Fallback `404`. |
| AUTH-02 | HTTP | Chýbajúca alebo expirovaná auth session | Fallback `404`. |
| AUTH-03 | HTTP | Neplatný participant token | Fallback `404`; run dokument bezo zmeny. |
| AUTH-04 | HTTP | Opätovne použitý participant token | Fallback `404`; druhé vykonanie sa nespustí. |
| CSRF-01 | HTTP | Start bez CSRF | Request odmietnutý. |
| CSRF-02 | HTTP | Hit A/B bez CSRF | Request prejde iba pod diagnostickou auth a s platným participant tokenom. |
| CSRF-03 | HTTP | Iný diagnostický POST bez CSRF | Request odmietnutý. |
| SESSION-01 | Integration | Paralelné hit requesty s rovnakou auth session | Session lock ich neserializuje; oba dosiahnu bariéru. |
| BARRIER-01 | Integration | A príde prvý, B v limite | Stav `WAITING_FOR_PARTNER`, potom jednorazovo `BARRIER_OPEN`. |
| BARRIER-02 | Integration | B príde prvý, A v limite | Rovnaký výsledok bez závislosti od poradia. |
| BARRIER-03 | Integration | Partner nepríde | `PARTNER_TIMEOUT`, atomický finalization claim a cleanup. |
| BARRIER-04 | Integration | Obaja otvárajú bariéru na hranici | `barrier.openedAt` vznikne raz; bez poškodenia dokumentu. |
| EXEC-01 | Integration | Obaja začnú po bariére | Oba `startedAt` sú zapísané a volania pokračujú mimo locku. |
| EXEC-02 | Integration | Jeden skončí skôr | Stav zostáva `EXECUTING`, kým druhý výsledok nie je hotový. |
| EXEC-03 | Integration | Oba výsledky hotové | Prechod na `RESULTS_READY`. |
| FINAL-01 | Integration | Oba procesy sa pokúsia claimnuť finalizáciu | Presne jeden claimant; druhý waiter bez verejnej chyby. |
| FINAL-02 | Integration | Timeout a dokončenie sa stretnú | Presne jedna finalizácia a jeden cleanup. |
| DB-01 | Production diagnostic | Rovnaká `REQUEST_REFERENCE` cez A a B | Presne jedna rezervácia pred cleanupom. |
| APP-01 | Production diagnostic | Repository zachytí `1062` | Výsledky sú presne `CREATED` a `ALREADY_EXISTS`. |
| APP-02 | Production diagnostic | Replay výsledok | `ALREADY_EXISTS` odkazuje na rezerváciu víťazného toku. |
| CLEAN-01 | Integration | Úspešný cleanup | Počty rezervácií, behov a doménových väzieb sú `0 + 0 + 0`. |
| CLEAN-02 | Integration | Cleanup zlyhá | `COMPLETED_FAILED_CLEANUP`, `overallSuccess=false`. |
| CLEAN-03 | Integration | Manuálny cleanup po neúspešnom teste | `cleanupConfirmed=true`, ale test zostáva `COMPLETED_FAILED`. |
| RESULT-01 | HTTP | Prvé načítanie tombstone | Nastaví sa `readOnceConsumedAt`, súbor zostane dostupný. |
| RESULT-02 | HTTP | Opakované načítanie pred `deleteAfter` | Rovnaký bezpečný výsledok je dostupný. |
| SWEEP-01 | Integration | Sweep pred `deleteAfter` | Tombstone sa nezmaže. |
| SWEEP-02 | Integration | Sweep po `deleteAfter` | `.json`, `.lock` a temp zvyšky sa idempotentne odstránia. |
| UI-01 | Browser | Kliknutie Start | Browser vyšle dva paralelné hit requesty bez tokenov v URL. |
| UI-02 | Browser | Výsledok úspechu | Tri osi sú samostatne zelené a `overallSuccess=true`. |
| UI-03 | Browser | Čiastkové zlyhanie | Príslušná os je neúspešná; celkový úspech sa nezobrazí. |

---

# C. Produkčné akceptačné kritérium

Webové súbežné overenie možno označiť za úspešné iba vtedy, keď jediný diagnostický run preukáže:

```text
reservations before cleanup = 1
outcomes = {CREATED, ALREADY_EXISTS}
replay derivation reference = víťazná rezervácia
reservations after cleanup = 0
runs after cleanup = 0
domain links after cleanup = 0
overallSuccess = true
```

Súčasne nesmie zostať:

- testovací databázový riadok,
- pracovný run dokument po uplynutí `deleteAfter`,
- participant token v logu, URL alebo výsledkovom tombstone,
- otvorený súborový alebo session lock,
- zobrazený interný exception detail.

## Otvorené riziká pred implementáciou

1. Reálne paralelné spracovanie dvoch HTTP requestov závisí od produkčného PHP/FPM alebo hostingového worker modelu.
2. Filesystem musí podporovať spoľahlivý `flock` a atomický `rename` v rámci rovnakého adresára.
3. Časové limity musia byť kratšie než serverové request timeouty, ale dostatočné pre databázový lock wait.
4. CSRF výnimka musí byť overená na presných routes, nie na celej diagnostickej skupine.
5. Cleanup musí zostať bezpečný aj pri páde procesu medzi finalization claimom a zápisom tombstone.

## Nasledujúci logický krok

Implementovať iba prvú vrstvu: run store, stabilný lock protokol a jeho unit testy. Až po jej samostatnom overení pokračovať endpointom `START`.