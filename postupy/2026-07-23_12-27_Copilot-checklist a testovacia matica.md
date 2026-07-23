# Copilot checklist a testovacia matica

## Kontekst

- Projekt: METODIKA
- Vetva: joyee-priority
- Autoritativny postup nacitany: `postupy/Inicializácia práce.md`
- Ucel: pripravit implementacny podklad pre bezpecnu webovu koordinaciu dvoch samostatnych HTTP poziadaviek pre diagnosticky scenar subeznosti

---

## Zavazne hranice specifikacie

1. `RESULTS_READY` je stav az po zapisani oboch participant vysledkov.
2. Jednorazovy `hit` je platny iba v aktivnych vstupnych stavoch `CREATED` a `WAITING_FOR_PARTNER`.
3. Timeout vetva pouziva rovnaky atomicky finalization claim ako hlavna vetva.
4. Manualny cleanup nepotvrdzuje uspesnost celeho testu, iba cleanup osu.
5. Neplatny participant token nemení run dokument a vracia fallback 404.
6. Lock a data su oddelene:
   - `runId.lock` pre stabilny `flock`
   - `runId.json` pre data
7. Neuspesny finalization claim je waiter rezim, nie verejna chyba.
8. `readOnceConsumedAt` sa zapisuje pod lock, ale tombstone sa maze az sweepom po `deleteAfter`.
9. Do run dokumentu sa ukladaju iba bezpecne technicke chybove kody, nie raw exception texty.

---

## Implementacny checklist (male overitelne kroky)

1. Run store a lock protokol
- Vytvorit suborovu sluzbu pre `writable/diagnostics/concurrency/`.
- Zaviesť dvojicu suborov `{runId}.lock` a `{runId}.json`.
- Zaviest atomicke read-modify-write pod lockom.

2. Stavovy model a validacia dokumentu
- Zaviesť enum stavov: `CREATED`, `WAITING_FOR_PARTNER`, `BARRIER_OPEN`, `EXECUTING`, `RESULTS_READY`, `FINALIZATION_CLAIMED`, `CLEANUP_PENDING`, `COMPLETED_SUCCESS`, `COMPLETED_FAILED`, `COMPLETED_FAILED_CLEANUP`, `EXPIRED`.
- Zaviesť validator pre povolene prechody a povinne polia dokumentu.

3. START endpoint
- Pridat endpoint na zalozenie runu.
- Server ulozi nemenny testovaci vstup:
  - `requestReference`
  - `payloadFingerprint`
  - `derivationReferenceA`
  - `derivationReferenceB`
  - `derivationApplicationInput`
- Vygenerovat participant tokeny A/B, ulozit len hash, plain token vratit iba raz.

4. HIT bez `accept()`
- Pridat `hit/a` a `hit/b` s inputom iba `runId` + `participantToken`.
- Po auth volat `session_write_close()` pred bariérou.
- Overit token hash, consumed flag, TTL, participant rolu.
- Zapisat `readyAt` pre participant slot.

5. Bariera a timeout
- Otvorit barieru nastavovanim `barrierOpenedAt` iba raz pod lockom, ked su ready obe strany.
- Wait loop bez drzania locku.
- Timeout vetvu napojit na atomicky finalization claim.

6. `accept()` a zapis vysledkov
- Po otvoreni bariery obidva procesy opustia lock a vykonaju `accept()` nezavisle.
- Vstupy pre `accept()` nacitat iba z run dokumentu.
- Zapisat participant outcome a bezpecny `errorCode`.

7. Finalization claim
- Zaviesť atomicke pole:
  - `finalization.claimedAt`
  - `finalization.claimedBy`
- Claim moze uspesne vykonat iba jeden participant.
- Druhy ide do waiter rezimu.

8. Invarianty a cleanup
- Finalizer overi tri osi:
  - DB unikátnosť
  - replay policy (`CREATED + ALREADY_EXISTS`)
  - cleanup potvrdeny
- Cleanup vykonava iba claimant.
- Pri zlyhani cleanupu nikdy neoznacit run ako uspesny.

9. Tombstone, result a sweep
- Po dokonceni odstranit token hashy a pracovné citlive medzistavy.
- Ponechat tombstone (`completedAt`, `deleteAfter`, assertions, cleanup status).
- Pri prvom citani nastavit `readOnceConsumedAt`, ale subor nemazat.
- Fyzicke mazanie len sweepom po `deleteAfter`.

10. UI
- Pridat diagnostics UI pre:
  - start runu
  - spustenie paralelnych hit A/B
  - polling resultu
  - zobrazenie 3 osí vysledku

11. Unit testy
- Run store, lock protokol, stavove prechody, finalization claim, timeout vetva, error sanitacia.

12. Integracny webovy test
- End-to-end scenar `START -> HIT A/B -> bariera -> accept -> finalization -> cleanup -> tombstone`.

13. Produkcne diagnosticke overenie
- Kratkodobe zapnutie diagnostickej vetvy.
- Overit tri osi vysledku.
- Overit sweep a odstranenie run suboru po TTL.

---

## Testovacia matica

| ID | Vrstva | Scenar | Vstup | Očakavanie |
|---|---|---|---|---|
| M01 | Unit | Vytvorenie runu | valid START | `CREATED`, hash tokenov ulozeny, plain tokeny iba v response |
| M02 | Unit | Lock exkluzivita | 2 subezne zapisy | konzistentny JSON bez race condition |
| M03 | Security | Neplatny token | HIT s nespravnym tokenom | fallback 404, bez zmeny run dokumentu |
| M04 | Security | Reuse tokenu | opakovany HIT s consumed tokenom | fallback 404, bez zmeny run dokumentu |
| M05 | Unit | Otvorenie bariery | valid HIT A + HIT B | `barrierOpenedAt` nastavene presne raz |
| M06 | Unit | Timeout | iba jeden participant | `PARTNER_TIMEOUT`, finalization claim iba raz |
| M07 | Unit | Finalization race | oba procesy claimnu naraz | claim ziska iba jeden, druhy waiter |
| M08 | Unit | Sanitacia chyb | runtime chyba v `accept()` | do run dokumentu iba bezpecny `errorCode` |
| M09 | Unit | Invariant DB unikátnosti | count=1 / count!=1 | true iba pre count=1 |
| M10 | Unit | Invariant replay | outcomes `CREATED+ALREADY_EXISTS` / ine | true iba pre presnu dvojicu |
| M11 | Unit | Invariant cleanup | cleanup success / fail | `cleanupConfirmed` true/false podla reality |
| M12 | Integration HTTP | End-to-end uspech | START + paralelny HIT A/B + RESULT | `COMPLETED_SUCCESS` a 3 osi true |
| M13 | Integration HTTP | Cleanup fail | vynutene zlyhanie cleanupu | `COMPLETED_FAILED_CLEANUP`, overall false |
| M14 | Integration HTTP | First result read | GET result po dokonceni | nastavi `readOnceConsumedAt`, subor zostava |
| M15 | Integration HTTP | Sweep delete | GET/sweep po `deleteAfter` | run subor fyzicky odstraneny |
| M16 | Security | CSRF hranice | START bez/so CSRF, HIT bez CSRF | START vyzaduje CSRF, HIT endpointy pod explicitnou vynimkou |
| M17 | Concurrency | Session lock hranica | paralelny HIT A/B | `session_write_close()` pred cakanim, requesty sa neserializuju session lockom |
| M18 | Regression | Diagnostika DB flow | login/database/logout | bez regresie existujucich diagnostics testov |

---

## Kritérium uspechu celej implementacie

`COMPLETED_SUCCESS` je pripustny iba vtedy, ked su sucasne potvrdene:

- databazova unikátnost,
- aplikacny vysledok `CREATED + ALREADY_EXISTS`,
- uspesny cleanup.

Ak chýba ktora kolvek z osí, run nie je uspesny.
