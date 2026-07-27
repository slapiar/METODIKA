# Krok 3 — Audit testovacej matice M01–M26

## Stav kroku

```text
SPLNENÉ
```

## Väzba na záväzný plán

Tento dokument uzatvára výhradne `Krok 3 — Audit testovacej matice M01–M26` dokumentu:

`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`

Ďalší krok nebol počas vykonania tohto kroku otvorený.

---

# 1. Rozsah a pravidlá auditu

Každý scenár bol posúdený podľa:

```text
existencia testu
× presná testovacia metóda
× verejný kontrakt alebo interná implementácia
× mock / jeden proces / dva procesy / dve reálne DB spojenia
× schopnosť odhaliť produkčný problém
× historický dôkaz spustenia
× chýbajúce pokrytie
```

V Kroku 3 sa testy znovu nespúšťali. Historický záznam uvádza, že suite `DiagnosticsControllerTest` a unit suite prešli pred posledným produkčným runom. Tento údaj nepotvrdzuje, že každý scenár matice má samostatný účinný test ani že test zodpovedá aktuálnemu produkčnému problému.

Použité hodnotenie:

```text
HOTOVÉ — test priamo overuje deklarovaný kontrakt v primeranom modeli
ČIASTOČNE — test existuje, ale model, rozsah alebo dôkaz je slabší než scenár
CHYBNÉ — test chýba alebo deklarovaný test nemôže overiť podstatnú časť scenára
```

---

# 2. Súhrn

```text
HOTOVÉ = 5
ČIASTOČNE = 15
CHYBNÉ = 6
SPOLU = 26
```

Scenáre `M02`, `M05`, `M06`, `M07`, `M08`, `M12`, `M17`, `M21` a `M24`, ktoré plán prikazuje preveriť osobitne, nie sú v stave `HOTOVÉ`.

---

# 3. Úplná mapa M01–M26

| ID | Stav | Testovací súbor a metóda | Model | Čo test skutočne dokazuje | Diera v pokrytí |
|---|---|---|---|---|---|
| M01 | HOTOVÉ | `DiagnosticsControllerTest::testConcurrencyStartCreatesRunAndReturnsTokens()` | jeden HTTP request, reálny file store | START vytvorí `CREATED`, uloží hash tokenov a plain tokeny vráti v odpovedi | neoveruje jednorazovosť odpovede pri opakovanom rovnakom requeste, ale základný kontrakt M01 spĺňa |
| M02 | ČIASTOČNE | `DiagnosticsConcurrencyRunStoreTest::testMutatePerformsAtomicReadModifyWrite()` | jeden proces, sekvenčná mutácia | read-modify-write prebehne pod lockom a nezostane temp súbor | nejde o dva súbežné zápisy; race condition ani strata aktualizácie sa nevytvoria |
| M03 | HOTOVÉ | `DiagnosticsControllerTest::testConcurrencyHitRejectsInvalidTokenWithoutRunChange()` | jeden HTTP request, reálny file store | neplatný token vráti 404 a porovnaný dokument sa nezmení | primerané kontraktu M03 |
| M04 | CHYBNÉ | samostatná testovacia metóda nenájdená | — | kód kontroluje `consumedAt` | chýba test opakovaného HIT s už spotrebovaným tokenom a dôkaz nulovej zmeny dokumentu |
| M05 | ČIASTOČNE | `testConcurrencyHitFlowOpensBarrierWhenPartnerIsAlreadyReady()` a `testConcurrencyHitSetsResultsReadyWhenBothParticipantsFinished()` | jeden proces, partner ručne predpripravený | `openedAt` vznikne pri už pripravenom partnerovi | neprebiehajú dva súbežné HIT requesty; neoveruje sa atómové „presne raz“ pri race |
| M06 | ČIASTOČNE | `testConcurrencyHitTimesOutAndClaimsFinalization()` | jeden request, krátky timeout, mock persistence | samotný participant dostane `PARTNER_TIMEOUT` a claim | chýba pretek „deadline dosiahnutý, partner otvorí bariéru tesne pred timeout mutáciou“ |
| M07 | ČIASTOČNE | `testConcurrencyHitTimeoutUsesWaiterModeWhenFinalizationAlreadyClaimed()` | claim je predpripravený, jeden proces | druhý participant vstúpi do waiter režimu pri existujúcom claime | nevznikajú dva súbežné pokusy o claim; atómové víťazstvo jedného procesu sa netestuje |
| M08 | ČIASTOČNE | `testConcurrencyHitAcceptFailureStoresSafeErrorCode()` | jeden proces, mock runner vyhodí výnimku | raw text sa neuloží do run dokumentu; uloží sa bezpečný kód | vonkajší catch zlieva build input, fingerprint, service lookup a runner chyby do `ACCEPT_RUNTIME_ERROR`; klasifikácia produkčnej fázy sa netestuje |
| M09 | ČIASTOČNE | nepriamy dôkaz v timeout/success testoch cez mock persistence | jeden proces, mockované počty | pri mock count `reservations=1` vznikne true | chýba samostatný test count=0, count>1 a reálnej DB unikátnosti v tomto webovom toku |
| M10 | ČIASTOČNE | `testConcurrencyHitSetsResultsReadyWhenBothParticipantsFinished()` | jeden participant ručne dokončený, druhý mock outcome | dvojica `CREATED + ALREADY_EXISTS` vedie k true | chýba tabuľkový test všetkých neprípustných dvojíc a reálne volanie aplikačnej služby |
| M11 | ČIASTOČNE | success, timeout a `testConcurrencyFinalizationMarksFailedCleanupWhenCleanupThrows()` | mock persistence | success/failure mocku sa premietne do cleanup osi | nejde o reálny cleanup DB; postcheck mimo mockovaných počtov sa neoveruje |
| M12 | ČIASTOČNE | `testConcurrencyWebIntegrationStartHitResultEndToEnd()` | jeden proces; participant A ručne predpripravený; runner aj persistence mock | route START→HIT B→RESULT a tombstone kontrakt fungujú sekvenčne | nie sú paralelné HIT A/B, nie je reálna DB ani reálny `FirstAcceptanceService`; test nemôže reprodukovať produkčný problém |
| M13 | HOTOVÉ | `testConcurrencyFinalizationMarksFailedCleanupWhenCleanupThrows()` | jeden proces, cielený mock failure | cleanup failure vedie k `COMPLETED_FAILED_CLEANUP` a overall false | primerané kontraktu riadenej failure vetvy, nie produkčnej DB |
| M14 | HOTOVÉ | `testConcurrencyResultMarksReadOnceAndReturnsRedactedTombstone()` | jeden proces, reálny file store | prvé RESULT nastaví `readOnceConsumedAt` a JSON zostane | primerané kontraktu M14 |
| M15 | ČIASTOČNE | `testConcurrencyResultSweepsExpiredTombstone()` | jeden proces; `deleteAfter` ručne posunutý do minulosti | result endpoint odstráni expirovaný JSON aj lock | nejde o nezávislý sweep; fyzické mazanie sa dokazuje iba pri ďalšom RESULT requeste |
| M16 | ČIASTOČNE | route konfigurácia + START testy a HIT testy | framework feature test | START route má CSRF filter a HIT route explicitný session-release filter | chýba explicitný test START bez CSRF a systematický dôkaz presnej CSRF výnimky pre oba HIT endpointy |
| M17 | CHYBNÉ | žiadny skutočný paralelný test | jeden proces | kód volá `session_write_close()` | test suite nevytvorí dva HTTP requesty, preto nemôže dokázať, že session lock ich neserializuje |
| M18 | HOTOVÉ | existujúce login/database/logout testy v `DiagnosticsControllerTest` | jeden proces, feature test | základná DB diagnostika a autorizácia majú regresné testy; historicky suite prešla | neoveruje produkciu, ale deklarovaný regresný kontrakt je pokrytý |
| M19 | ČIASTOČNE | `DiagnosticsConcurrencyRunStoreTest::testStableLockFilePersistsAcrossAtomicJsonReplace()` | jeden proces, sekvenčné save | inode `.lock` ostane a inode JSON sa zmení | neprebieha súbežný reader/writer počas rename; race sa nevytvára |
| M20 | ČIASTOČNE | `testConcurrencyHitRejectsDisallowedStateWithoutRunChange()` | jeden proces | odmietne stav `BARRIER_OPEN` bez zmeny dokumentu | testuje iba jeden z mnohých zakázaných stavov, nie celú deklarovanú množinu |
| M21 | CHYBNÉ | samostatná metóda nenájdená | — | HIT precheck pri expirácii iba vráti 404 | expirácia neprechádza finalization claimom a cleanup tokom; chýba implementačný aj testový dôkaz |
| M22 | CHYBNÉ | samostatná metóda ani verejný cleanup endpoint nenájdené | — | nič | deklarovaný manuálny cleanup po `COMPLETED_FAILED` sa nedá otestovať cez uvedený verejný kontrakt |
| M23 | ČIASTOČNE | `testConcurrencyResultMarksReadOnceAndReturnsRedactedTombstone()` a E2E test | jeden proces | input sa odstráni, tokenHash je v uloženom tombstone null a vo verejnom payload chýba | validator nevynucuje úplnú redakciu; test nekontroluje všetky nepotrebné pracovné údaje |
| M24 | ČIASTOČNE | `testConcurrencyHitAcceptFailureStoresSafeErrorCode()` | jeden proces, mock runner, druhý participant nedokončený | participant dostane bezpečný errorCode | test nekončí finalization claimom ani cleanupom; neoveruje úplný scenár deklarovaný M24 |
| M25 | ČIASTOČNE | všeobecné unauthorized testy a kontroly v kontroléroch | jeden proces | niektoré neautorizované diagnostics volania vracajú 404 | chýba tabuľkový test `start/hit A/hit B/result`; deklarovaný `cleanup` endpoint v routes neexistuje |
| M26 | ČIASTOČNE | `testConcurrencyStartReturns404WhenFeatureFlagIsDisabled()` a UI flag test | jeden proces | START a viditeľnosť UI reagujú na flag | chýba explicitný vypnutý/zapnutý test pre HIT A, HIT B a RESULT; produkčný aktuálny stav flagu sa tu neposudzuje |

---

# 4. Osobitné závery povinných scenárov

## M02 — lock exkluzivita

Existuje dôkaz správne použitého `flock()` v kóde, ale nie dôkaz dvoch súbežných mutácií. Sekvenčný unit test nemôže odhaliť stratenú aktualizáciu ani problém s inode locku počas cleanupu.

## M05 — otvorenie bariéry

Testy bariéru iba stimulujú ručne pripraveným partnerom. Neoverujú, že dva procesy zapíšu ready stav a `openedAt` presne raz pod reálnym pretekom.

## M06 — timeout

Testuje sa čistý timeout. Netestuje sa kritické okno tesne pred timeout mutáciou, ktoré bolo dôvodom neskoršej poistky v run store.

## M07 — finalization race

Waiter režim je overený iba po ručnom predpripravení claimu. Samotný race dvoch claimantov nevzniká.

## M08 — sanitácia chýb

Test dokazuje neprítomnosť raw správy v run dokumente, ale súčasne potvrdzuje príliš všeobecný `ACCEPT_RUNTIME_ERROR`. Nedokáže určiť fázu produkčného zlyhania.

## M12 — end-to-end úspech

Názov testu je širší než jeho model. Test je route integračný, nie súbežný ani databázovo integračný. Participant A sa zapisuje priamo do store a participant B používa mock runner aj mock persistence.

## M17 — session lock

Prítomnosť `session_write_close()` v kóde nie je test súbehu. Bez dvoch reálnych HTTP procesov sa nedá dokázať, že session requesty neboli serializované.

## M21 — expirácia

Scenár nie je iba netestovaný. Aktuálna HIT vetva pri expirácii vracia 404 bez finalization claimu a cleanup toku, takže deklarovaný kontrakt nie je implementovaný.

## M24 — pád participantu

Existujúci test končí v stave `EXECUTING`, pretože druhá strana nie je dokončená. Nepotvrdzuje finalization ani cleanup po zlyhaní participantu.

---

# 5. Historický dôkaz spustenia

V `CHANGELOG.md` je zaznamenané, že pred posledným produkčným runom prešli:

```text
vendor/bin/phpunit --filter DiagnosticsControllerTest
vendor/bin/phpunit tests/unit
```

Súčasný audit z toho vyvodzuje iba:

- uvedené suite vtedy skončili bez testovej chyby,
- existujúce testy boli syntakticky a runtime vykonateľné v danom prostredí.

Nevyvodzuje z toho:

- že matica M01–M26 bola úplná,
- že každý scenár mal samostatný test,
- že testy používali dva procesy alebo reálnu DB,
- že mohli odhaliť posledný produkčný `FAILED_RUNTIME_ERROR`.

---

# 6. Záväzný register dier pre neskoršie opravy

| Diera | Dotknuté scenáre | Povinný budúci dôkaz |
|---|---|---|
| skutočné dva procesy nad file store | M02, M05, M06, M07, M17, M19 | pomocný dvojprocesový test s deterministickou synchronizáciou |
| dve paralelné HTTP požiadavky | M05, M12, M17 | test, ktorý nespúšťa druhého participanta ručnou mutáciou |
| reálna aplikačná služba a dve DB spojenia | M09–M12, M24 | integračný test bez mock runnera a mock persistence |
| presná chybová fáza | M08, M24 | oddelené errorCode pre build input, fingerprint, runner invocation a runner výsledok |
| expirácia cez claim a cleanup | M21 | samostatný úspešný regresný test celej expiration vetvy |
| manuálny cleanup kontrakt | M22 | najprv určiť, či má existovať verejný endpoint alebo interný prevádzkový úkon; potom test |
| úplná autorizácia a feature flag | M25, M26 | tabuľkové testy všetkých aktívnych endpointov |
| úplný dokumentový invariant | M23 a súvisiace | validator testy pre redakciu, assertions a `COMPLETED_SUCCESS` |

Tento register je vstupom pre neskoršie opravné a validačné kroky záväzného plánu. V Kroku 3 sa kód ani testy nemenili.

---

# 7. Validácia a uzavretie

Kritérium Kroku 3 bolo splnené:

- všetkých 26 scenárov má samostatné hodnotenie,
- každý scenár je priradený ku konkrétnej metóde alebo označený ako chýbajúci,
- je rozlíšený verejný kontrakt a interná implementácia,
- je určený procesný a databázový model testu,
- je posúdená schopnosť odhaliť produkčný problém,
- historický dôkaz spustenia je oddelený od nového testovania,
- chýbajúce pokrytie je zapísané do záväzného registra dier.

```text
KROK_3=SPLNENÉ
NEXT_ALLOWED_STEP=Krok 4 — Audit routovania, START vetiev a UI
```

Vykonateľný kód, testy ani produkčné prostredie neboli týmto krokom zmenené.
