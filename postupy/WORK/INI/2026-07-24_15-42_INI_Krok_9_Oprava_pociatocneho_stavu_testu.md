# INICIALIZÁCIA OPRAVY POČIATOČNÉHO STAVU TESTU KROKU 9

## Kontext

GitHub Actions run `30097645452` prešiel kontrolou prostredia, syntaxou aj existujúcimi testami `DiagnosticsConcurrencyRunStoreTest`. Nový dvojprocesový test zlyhal ešte pred vytvorením druhého procesu pri počiatočnom `save()` s výnimkou `Run dokument nie je validny.`

## Povinná inicializačná brána

1. **Metodika načítaná: ÁNO**
   - Overené opätovným čítaním `postupy/Inicializácia práce.md`, blob `201bcaef882e21e99c484b2c9acd61489e1e9914`.
   - Výsledok: samostatná oprava testu vyžaduje vlastný INI záznam.
   - Neoverené: nič.

2. **Projekt a autoritatívny zdroj overený: ÁNO**
   - Projekt: METODIKA; repozitár `slapiar/METODIKA`; autoritatívna vetva `main`.
   - Overené čítaním `PROJEKTY/ZoznamProjektov.md` a aktuálnych zdrojov.
   - Neoverené: nič.

3. **Vetva a HEAD overené: ÁNO**
   - Autoritatívny HEAD pred opravou: `28b6d09e9b847195d0e89defb105f3510b612f06`.
   - Dôkaz: checkout workflowu runu `30097645452` a používateľom potvrdený push.
   - Neoverené: nič.

4. **Potrebné prístupy prakticky overené: ÁNO**
   - Čítanie GitHubu potvrdené načítaním testu a validátora.
   - Zápis potvrdený predchádzajúcim vytvorením INI, testu a workflowu Kroku 9.
   - Databázový ani produkčný prístup nie je potrebný.
   - Neoverené: nič.

5. **Prostredie prakticky overené: ÁNO**
   - Run `30097645452` potvrdil PHP 8.4.23, `pcntl`, Composer, syntax a úspešný existujúci run-store test suite.
   - Nový test sa spustil a zlyhal deterministicky pri počiatočnom `save()`.
   - Neoverené: samotná dvojprocesová časť, pretože ju neplatný fixture ešte nedosiahol.

6. **Závislosti kroku dostupné: ÁNO**
   - `DiagnosticsConcurrencyRunStore`, `DiagnosticsConcurrencyRunDocumentValidator`, `DiagnosticsConcurrencyRunState`, PHPUnit a `pcntl` sú dostupné.
   - Stavový kontrakt bol overený v `DiagnosticsConcurrencyRunState::assertTransitionAllowed()`.
   - Neoverené: nič.

7. **Predmet a hranice zásahu určené: ÁNO**
   - Predmet: iba `codei/tests/integration/DiagnosticsConcurrencyBarrierProcessTest.php`.
   - Zmena: najprv uložiť `CREATED`, potom platnou `mutate()` zmenou prejsť do `WAITING_FOR_PARTNER` a nastaviť `participant a.readyAt`.
   - Mimo rozsahu: produkčný run store, validátor, kontrolér, stavový automat, timeoutová logika, databáza a produkcia.
   - Neoverené: nič.

8. **Kritérium úspechu určené: ÁNO**
   - Test prejde počiatočným save/transition a dostane sa do dvojprocesovej časti.
   - Existujúce run-store testy zostanú úspešné.
   - Dvojprocesový výsledok sa vyhodnotí až podľa nového runu; táto oprava sama Krok 9 neuzatvára.
   - Neoverené: výsledok dvojprocesovej Validácie.

9. **Rollback určený: ÁNO**
   - Vrátiť samostatný commit opravy testovacieho fixture.
   - Produkčný kód ani prostredie sa nemenia.
   - Neoverené: nič.

## Výsledok brány

```text
GATE=OPEN
POVOLENÝ_ZÁSAH=OPRAVA_POČIATOČNÉHO_STAVU_DVOJPROCESOVÉHO_TESTU
KROK_9=OTVORENÝ
```
