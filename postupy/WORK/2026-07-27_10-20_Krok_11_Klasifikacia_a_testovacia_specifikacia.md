# Krok 11 — klasifikácia zmien a úplná testovacia špecifikácia

Dátum: 2026-07-27
Projekt: METODIKA
Repozitár: slapiar/METODIKA
Vetva: agent/krok-11-validacia
Klasifikačný snapshot HEAD: cf7b2b097112d9da8c0341d5e2a31d0ffc6f1493
Pôvodný technický základ: d418e72c162bde324af7546c937af979bd75182e
Rozdiel: 117 commitov, 67 zmenených ciest
Aktívny krok: Krok 11
INI: postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md
GATE: OPEN

## Stav dokumentu

~~~text
FÁZA_11_B=SPLNENÁ
FÁZA_11_C=SPLNENÁ
FÁZA_11_D=SPLNENÁ
FÁZA_11_E=SPLNENÁ
FÁZA_11_F=SPLNENÁ
KROK_11=SPLNENÉ
GATE=OPEN_NAPLNENÁ_A_UZAVRETÁ_PO_VYKONANÍ
VALIDOVANÝ_TECHNICKÝ_HEAD=bc85d18fd0edc1a52fad81f8fac54c1ae66a7014
VALIDAČNÝ_RUN=30252640028
MERGE_COMMIT=4cb2fe0a9dc1ff3a94c450dbe1dc33c38b574b0c
TESTY=PASS
CLEANUP=true
RELEASE=BEZ_ZMENY
PRODUKCIA=BEZ_ZÁSAHU
NEXT_ALLOWED_ACTION=KROK_12_S_VLASTNÝM_INI_A_GATEM
~~~

## 1. Autoritatívny zámer

Doložené zadanie používateľa z 2026-07-25 určuje, že diagnostická stránka je určená na testovanie a má obsahovať tlačidlá pre vytvorenie testovacej session, kroku a Evidence. Neexistuje doložené rozhodnutie, že aktuálne verejné GATE API, placeholdery, pevné ID a surové chyby sú konečná podoba.

Rozhodnutie:

~~~text
DIAGNOSTICKÝ_ZÁMER=PONECHAŤ
AKTUÁLNA_BEZPEČNOSTNE_CHYBNÁ_PODOBA=OPRAVIŤ
NOVÁ_PRODUKTOVÁ_FUNKCIA=NEPRIDÁVAŤ
~~~

## 2. Fáza 11.B — klasifikácia skupín

### 2.1 Metodické a evidenčné zmeny

- PÔVOD: plánovanie a obnova metodiky po STOP.
- ZÁMER: zachovať dôkazovú kontinuitu.
- DÔKAZ PREDCHÁDZAJÚCEHO VYKONANIA: CHANGELOG, register postupy/README.md a pôvodný INI Kroku 11.
- AKTUÁLNA FUNKCIA: riadenie Krokov 11–15.
- VÄZBY: Inicializácia práce.md, záväzný plán 2026-07-26.
- TESTOVÉ POKRYTIE: read-after-write a porovnanie blobov.
- PRODUKČNÝ STAV: bez vplyvu na runtime.
- RIZIKO: nový INI 2026-07-27_08-47 odporuje pravidlu NOVÝ_INI_KROKU_11=false.
- ROZHODNUTIE: PONECHAŤ dôkazy; nový INI označiť pri uzavretí ako PREKONANÝ, nie ako pokračovaciu bránu.
- ROLLBACK: nevypúšťať historické dôkazy; vrátiť iba chybný registračný stav.

### 2.2 Presuny codei/app/app

- PÔVOD: oprava duplicitného koreňa.
- ZÁMER: jediný CodeIgniter app koreň.
- DÔKAZ: odstránené duplicitné controllery/views, gate_session.php presunutý do codei/app/Views.
- AKTUÁLNA FUNKCIA: autoload a renderovanie z korektnej cesty.
- VÄZBY: Routes.php, GateDashboard, GateSupervisor.
- TESTOVÉ POKRYTIE: chýba route/view regresia.
- PRODUKČNÝ STAV: dashboard sa načíta, detail route nie je doložená.
- RIZIKO: mŕtve app/public JS kópie.
- ROZHODNUTIE: PONECHAŤ_A_VALIDOVAŤ presun; odstrániť duplicitné neobsluhované kópie.
- ROLLBACK: obnoviť iba presunuté súbory, nie duplicitný runtime koreň.

### 2.3 Boot a externé prostredie

- PÔVOD: načítanie ExternalEnvironment vo všetkých runtime režimoch.
- ZÁMER: konzistentný bezpečný env bootstrap.
- DÔKAZ: dnešný produkčný read-back ExternalEnvironment=ÁNO a DB=OK.
- AKTUÁLNA FUNKCIA: produkcia CI_DEBUG=false, display_errors=0.
- VÄZBY: Database.php, DiagnosticsController, flags.
- TESTOVÉ POKRYTIE: existujúce environmentálne Actions dôkazy.
- PRODUKČNÝ STAV: funkčné na v1.1.15.
- RIZIKO: vývojové .env načítanie musí zostať bez prepisu serverových premenných.
- ROZHODNUTIE: PONECHAŤ_A_VALIDOVAŤ.
- ROLLBACK: vrátiť tri Boot súbory ako jednu skupinu.

### 2.4 Routes

- PÔVOD: GATE dashboard, diagnostické tlačidlá a API.
- ZÁMER: testovať session/krok/Evidence z autorizovanej diagnostiky.
- DÔKAZ: explicitné používateľské zadanie.
- AKTUÁLNA FUNKCIA: verejné GET aj POST API; GET state zapisuje do DB.
- VÄZBY: GateSupervisor, diagnostické controllery, JS.
- TESTOVÉ POKRYTIE: pre Gate routes chýba.
- PRODUKČNÝ STAV: /gate je verejne dostupné; API čítanie nebolo dôkazne získané.
- RIZIKO: chýba spoločná autorizácia, CSRF na zápisoch a čistota GET.
- ROZHODNUTIE: PONECHAŤ_PO_NAJMENŠEJ_OPRAVE.
- ROLLBACK: jedna skupina Routes + príslušné testy.

### 2.5 Pôvodná diagnostika súbežnosti

- PÔVOD: Krok 10 a pôvodná matica M01–M26.
- ZÁMER: START → paralelný HIT A/B → RESULT.
- DÔKAZ: Krok 10, run 30148480939 a pôvodné testy.
- AKTUÁLNA FUNKCIA: diagnostická vetva s tokenmi participantov, bariérou, finalizáciou a tombstone.
- VÄZBY: run store, FirstAcceptanceService, DB cleanup.
- TESTOVÉ POKRYTIE: existuje, ale nie je úplné ani úplne paralelné.
- PRODUKČNÝ STAV: UI je dostupné; nový run v Kroku 11 ešte nebol spustený.
- RIZIKO: M21/M22 a časť M02/M05/M07/M12/M17/M24.
- ROZHODNUTIE: PONECHAŤ_A_VALIDOVAŤ po doplnení potvrdených testových dier.
- ROLLBACK: vrátiť iba dávku Kroku 11, nie funkčný commit Kroku 10.

### 2.6 Gate Dashboard a Gate Supervisor

- PÔVOD: diagnostické rozhranie GATE.
- ZÁMER: vizualizovať session, kroky, Evidence a stav brány.
- DÔKAZ: diagnostický účel je potvrdený; verejná produktová funkcia potvrdená nie je.
- AKTUÁLNA FUNKCIA: CRUD-like API, výpočet stavu a dashboard.
- VÄZBY: IniSessionModel, IniStepModel, IniEvidenceModel, GateStateModel.
- TESTOVÉ POKRYTIE: samostatné testy sa nenašli.
- PRODUKČNÝ STAV: /gate sa načíta s prázdnou tabuľkou.
- RIZIKO: verejné zápisy, GET so zápisom, bez validácie, chýbajúca detail route, XSS cez innerHTML, placeholdery.
- ROZHODNUTIE: PONECHAŤ_PO_NAJMENŠEJ_OPRAVE ako autorizovanú diagnostickú funkciu.
- ROLLBACK: vrátiť celú Gate dávku; diagnostika DB a concurrency ostanú.

### 2.7 Diagnostické session/step/Evidence controllery

- PÔVOD: používateľom požadované tlačidlá na diagnostickej stránke.
- ZÁMER: kontrolovaný idempotentný test.
- DÔKAZ: explicitné zadanie 2026-07-25.
- AKTUÁLNA FUNKCIA: pevné session_id=1 a step_id=1, surové error payloady.
- VÄZBY: diagnostická session auth, DB modely.
- TESTOVÉ POKRYTIE: chýba.
- PRODUKČNÝ STAV: tlačidlá sú nasadené; nevykonali sa v dnešnom čítaní.
- RIZIKO: zásah do cudzieho ID, únik class/file/line/database_error, neúplná idempotencia.
- ROZHODNUTIE: PONECHAŤ_PO_NAJMENŠEJ_OPRAVE.
- ROLLBACK: odstrániť iba tieto tri diagnostické akcie a ich routes.

### 2.8 Modely a databázový kontrakt

- PÔVOD: GATE tabuľky.
- ZÁMER: session → steps → evidence → state.
- DÔKAZ: modely existujú; migračný kontrakt v repozitári nie je doložený.
- AKTUÁLNA FUNKCIA: allowedFields bez validačných pravidiel a bez doloženej unikátnosti.
- VÄZBY: všetky Gate controllery.
- TESTOVÉ POKRYTIE: chýba modelová a DB integračná sada.
- PRODUKČNÝ STAV: databáza je dostupná; konkrétne počty neboli získané.
- RIZIKO: duplicity krokov/Evidence, siroty, neznámy schema lifecycle.
- ROZHODNUTIE: PONECHAŤ_PO_NAJMENŠEJ_OPRAVE; pred implementáciou určiť existujúcu schému a doplniť chýbajúci migračný kontrakt iba ak ho potvrdí diff.
- ROLLBACK: migrácia musí mať down(); predchádzajúce produkčné dáta sa nemažú.

### 2.9 Views a layout

- PÔVOD: spoločný layout a diagnostické/GATE obrazovky.
- ZÁMER: ovládanie diagnostiky.
- DÔKAZ: produkčná diagnostika sa renderuje.
- AKTUÁLNA FUNKCIA: diagnostické tlačidlá, GATE tabuľka, produkčný inspector partial.
- VÄZBY: asset_url, routes, CSP nonce.
- TESTOVÉ POKRYTIE: pôvodné diagnostics view testy; nové partialy bez feature testu.
- PRODUKČNÝ STAV: produkčný inspector sa v dnešnom DOM nezobrazil napriek zdrojovému HEAD.
- RIZIKO: nesúlad release/source, zlé asset cesty /asset vs /assets vs /js.
- ROZHODNUTIE: PONECHAŤ_PO_NAJMENŠEJ_OPRAVE.
- ROLLBACK: vrátiť nové partialy a layout vloženia ako jeden celok.

### 2.10 JavaScript, CSS a zvuky

- PÔVOD: GATE dashboard/session UI.
- ZÁMER: dynamické načítanie a zmena stavov.
- DÔKAZ: zdrojové súbory existujú; WebSocket je zakomentovaný.
- AKTUÁLNA FUNKCIA: fetch na GATE API, innerHTML renderovanie, zvuky.
- VÄZBY: verejné API a statické assety.
- TESTOVÉ POKRYTIE: chýba.
- PRODUKČNÝ STAV: dashboard ostal prázdny.
- RIZIKO: DOM XSS, bez CSRF, zlé cesty, pevná doména, súbory so záverečnými medzerami.
- ROZHODNUTIE: PONECHAŤ_PO_NAJMENŠEJ_OPRAVE; renderovať textContent, dodať CSRF/auth, opraviť cesty a názvy.
- ROLLBACK: odstrániť iba UI asset dávku.

### 2.11 Release verzia a ZIP balíky 1.1.10–1.1.15

- PÔVOD: predčasné opakované releasey.
- ZÁMER: historické nasadenia.
- DÔKAZ: produkčný footer v1.1.15.
- AKTUÁLNA FUNKCIA: RELEASE_VERSION=1.1.15, šesť ZIP balíkov.
- VÄZBY: release.sh a Krok 12.
- TESTOVÉ POKRYTIE: chýba úplný audit balíkov.
- PRODUKČNÝ STAV: v1.1.15 nasadená; source commit nezistený.
- RIZIKO: ZIP nesmie byť vydávaný za validovaný rollback ani výsledok Kroku 11.
- ROZHODNUTIE: ODLOŽIŤ_MIMO_KROKU_11 ako historické; verziu nezvyšovať, nový ZIP nevytvárať.
- ROLLBACK: žiadny zásah do produkcie; Krok 12 vytvorí jediný nový auditovaný balík.

### 2.12 Produkčné testovacie dáta a cleanup

- PÔVOD: predčasné testy session/krok/Evidence a concurrency.
- ZÁMER: diagnostický dôkaz.
- DÔKAZ: dashboard bez viditeľného riadku; API a presné počty nezískané.
- AKTUÁLNA FUNKCIA: stav nezistený.
- VÄZBY: ini_* tabuľky a run store.
- TESTOVÉ POKRYTIE: chýba nulový postcheck.
- PRODUKČNÝ STAV: NEZISTENÉ.
- RIZIKO: zmazanie netestovacích dát alebo ponechanie testovacích zvyškov.
- ROZHODNUTIE: NEZISTENÉ — počas Kroku 11 nič nemaž; pripraviť presne označené testovacie dáta a cleanup pre Kroky 13–14.
- ROLLBACK: cleanup len podľa testovacieho identifikátora vytvoreného daným runom.

### 2.13 Nový produkčný read-only inspector

- PÔVOD: obnova dôkazov Fázy 11.A.
- ZÁMER: čítať release, runtime, flags, migrácie, počty tabuliek a run-store.
- DÔKAZ: service + unit test + view partial.
- AKTUÁLNA FUNKCIA: zdrojovo read-only; na produkčnom DOM v1.1.15 sa nezobrazil.
- VÄZBY: autorizovaná diagnostics session.
- TESTOVÉ POKRYTIE: unit test existuje; feature a bezpečnostný test chýba.
- PRODUKČNÝ STAV: zdroj/deployment nesúlad.
- RIZIKO: názvy run-store súborov môžu obsahovať identifikátory; počty nerozlišujú testovacie a netestovacie dáta.
- ROZHODNUTIE: PONECHAŤ_PO_NAJMENŠEJ_OPRAVE a validovať iba za diagnostickou autorizáciou.
- ROLLBACK: odstrániť service, test a partial ako jednu skupinu.

### 2.14 Pracovné a chybné artefakty

Predmet:

- METODIKA-main.bundle
- METODIKA-main-HEAD.txt
- METODIKA-working-tree.txt
- codei/writable/ metodika-collect-production-state.php
- codei/writable/metodika-production-state-error.json
- codei/writable/metodika-production-state.json
- duplicitné codei/app/public/js/*
- zvuky s koncovými medzerami v názvoch

Rozhodnutie:

~~~text
VRÁTIŤ_PRED_VALIDÁCIOU
~~~

Dôvod: pracovný bundle a snapshoty nie sú runtime zdroj; collector má chybnú cestu s úvodnou medzerou, error JSON dokazuje neúspech, výstupný JSON je prázdny, writable výstupy nemajú byť verzované, JS je duplicitný a názvy zvukov porušujú audit balíka.

## 3. Povinný implementačný rozsah Fázy 11.D

1. Zachovať diagnostické tlačidlá, ale previazať ich na novovytvorenú testovaciu session a krok, nie na pevné ID 1.
2. Zaviesť spoločnú diagnostics autorizáciu a feature flag na všetky Gate read/write routes.
3. CSRF na všetky browserové zápisy; GET musí byť bez vedľajšieho účinku.
4. Validovať JSON vstupy, ID, status a povinné reťazce; neznámy vstup odmietnuť bezpečným 404/422 podľa interného kontraktu.
5. Nevracať class, message, file, line ani database_error vo verejnom JSON.
6. Odstrániť alebo uzavrieť placeholder routes purge-cache a submit-step.
7. Opraviť detail route, asset cesty a DOM renderovanie bez innerHTML.
8. Opraviť/odstrániť pracovné artefakty uvedené v 2.14.
9. Doplniť testy GATE, inspectoru a všetkých dier M01–M26.
10. Nezvyšovať RELEASE_VERSION, nevytvárať ZIP, nenasadzovať.
11. Ak sa pre GATE potvrdí chýbajúca databázová migrácia, vytvoriť ju v tej istej dávke s down() bez mazania existujúcich dát.
12. Všetky zmeny udržať v jednom kontrolovanom diffe.

## 4. Fáza 11.C — spoločné testovacie prostredie

~~~text
PHP=8.4
COMPOSER=2
DB=izolovaná MariaDB 11.4/InnoDB
EXTENSIONS=mysqli,intl,mbstring,xml,curl,pcntl
PRODUCTION_UNTOUCHED=true
DB_PREFIX=jedinečný pre run
RUNSTORE=nový dočasný adresár pre run
HTTP_SERVER=PHP_CLI_SERVER_WORKERS=4
~~~

Základné príkazy:

~~~bash
composer install --no-interaction --prefer-dist --no-progress
php spark migrate --all
vendor/bin/phpunit --no-coverage tests/unit
vendor/bin/phpunit --no-coverage tests/session
vendor/bin/phpunit --no-coverage tests/integration
PHP_CLI_SERVER_WORKERS=4 php -S 127.0.0.1:8080 -t public public/index.php
~~~

Každý test zaznamená: presný HEAD, príkaz/request, výsledok, počty DB pred/po, run-store pred/po a cleanup. Pri prvom povinnom FAIL sa ďalšia fáza neotvorí.

## 5. M01–M26 — úplná testovacia špecifikácia

Spoločné predpoklady: izolovaná DB, nový run-store, diagnostics session autorizovaná iba tam, kde scenár vyžaduje autorizáciu. Spoločný rollback: zrušiť iba DB prefix a temp adresár vytvorený test runom.

| ID | Test a vrstva | Počiatočný stav a presný vstup | Očakávaný výsledok | Negatívny výsledok | Dôkaz a cleanup |
|---|---|---|---|---|---|
| M01 | Unit START | prázdny store; POST START s CSRF | CREATED, uložené iba hash tokenov, plain tokeny iba response | chýbajúci token/runId = FAIL | response + JSON diff; cleanup run |
| M02 | 2-process store | dva procesy mutujú rovnaký run po bariére IPC | oba zápisy zachované, JSON validný, bez temp | strata update/invalid JSON = FAIL | procesné logy + SHA JSON; cleanup |
| M03 | Security HIT | platný run, chybný token | 404, byte-identický JSON | akákoľvek mutácia = FAIL | hash pred/po |
| M04 | Security reuse | token už consumed; druhý rovnaký HIT | 404, bez zmeny | druhé prijatie = FAIL | hash pred/po |
| M05 | 2-process barrier | HIT A/B uvoľnené súčasne | openedAt presne raz, oba ready | dvojité otvorenie/timeout = FAIL | dve odpovede + JSON |
| M06 | timeout race | A čaká; B otvorí tesne pred timeout mutáciou | bez PARTNER_TIMEOUT po otvorení | neskorý timeout zápis = FAIL | deterministická synchronizácia |
| M07 | finalization race | dva claimant procesy súčasne | jeden claimant, druhý waiter | dvaja claimanti = FAIL | claimedBy + procesné logy |
| M08 | failure phases | tabuľka build/fingerprint/factory/accept/write failures | iba bezpečný phase×class code | raw detail alebo všeobecný nepresný kód = FAIL | JSON + log assertions |
| M09 | DB uniqueness | reálna DB count 0/1/2 | true iba pri 1 | iný výsledok = FAIL | SQL counts pred cleanupom |
| M10 | replay table | všetky dvojice outcomes | true iba CREATED+ALREADY_EXISTS | iná dvojica true = FAIL | data provider |
| M11 | cleanup invariant | úspech a vynútené DB cleanup zlyhanie | presná true/false os | success pri cleanup fail = FAIL | counts pred/po |
| M12 | parallel HTTP E2E | START, súčasný curl HIT A/B, RESULT poll | COMPLETED_SUCCESS, tri osi true | mock/ručný partner alebo failed state = FAIL | HTTP transcript + DB/store postcheck |
| M13 | HTTP cleanup fail | vynútená izolovaná cleanup chyba | COMPLETED_FAILED_CLEANUP | overall true = FAIL | RESULT + counts |
| M14 | first RESULT | dokončený tombstone, GET raz | readOnceConsumedAt vznikne, súbor ostane | okamžité zmazanie = FAIL | JSON pred/po |
| M15 | sweep | tombstone po deleteAfter, vyvolaný sweep | JSON/lock odstránené | zvyšok súboru = FAIL | directory listing |
| M16 | CSRF | START/Gate write bez a s CSRF; HIT podľa explicitného token kontraktu | zápisy bez CSRF odmietnuté, povolená výnimka presná | nekontrolovaný zápis = FAIL | tabuľka endpointov |
| M17 | session lock | dve HTTP HIT s rovnakou auth session | prebehnú paralelne po session_write_close | serializácia/timeout = FAIL | časová os requestov |
| M18 | diagnostics regression | login → database → logout | auth TTL, 200/redirect/404 kontrakt | session ostane autorizovaná po logout = FAIL | feature test |
| M19 | rename race | reader/writer proces počas temp+rename | stabilný .lock, konzistentné čítanie | inode race = FAIL | inode a procesné logy |
| M20 | disallowed states | tabuľka všetkých stavov mimo CREATED/WAITING | 404 a bez zmeny | prijatie v jedinom stave = FAIL | data provider + hash |
| M21 | expiration | expirovaný run pred HIT a počas wait | finalization claim, cleanup, bezpečný EXPIRED výsledok | iba 404 bez cleanupu = FAIL | DB/store postcheck |
| M22 | internal cleanup | interný CLI/service cleanup COMPLETED_FAILED | cleanupConfirmed true, overall ostáva false | verejný neautorizovaný endpoint alebo success state = FAIL | command output + counts |
| M23 | tombstone invariant | finalizácia úspech/neúspech | bez input/token hash/working timestamps, assertions úplné | citlivý alebo nepotrebný údaj = FAIL | recursive key audit |
| M24 | participant crash | A vyhodí v accept, B dokončí | safe code, claim, cleanup, overall false | visí EXECUTING alebo bez cleanupu = FAIL | RESULT + counts |
| M25 | auth matrix | každý START/HIT/RESULT/Gate endpoint bez auth | fallback 404; žiadny DB/store diff | verejný read/write = FAIL | endpoint data provider |
| M26 | feature flag matrix | diagnostics/concurrency/Gate flag OFF a ON | OFF 404 bez mutácie; ON definovaný kontrakt | endpoint ignoruje flag = FAIL | endpoint data provider |

## 6. GATE testovacia špecifikácia

| ID | Predmet | Vstup | Očakávanie | Cleanup |
|---|---|---|---|---|
| G01 | Dashboard auth | GET /gate bez/s auth | bez auth 404, s auth 200 | žiadny |
| G02 | API read auth | všetky Gate GET bez/s auth | bez auth 404, s auth bezpečný JSON | žiadny |
| G03 | API write auth+CSRF | všetky Gate POST bez/s auth a CSRF | zápis iba pri oboch podmienkach | zmazať run-owned dáta |
| G04 | Session create | jedinečný testRunId, projekt, agent | validovaná session, locked | run-owned cleanup |
| G05 | Step uniqueness | rovnaký session+step dvakrát | jediný krok/idempotentný výsledok | run-owned cleanup |
| G06 | Evidence uniqueness | rovnaký step+type+content dvakrát | jediný Evidence/idempotentný výsledok | run-owned cleanup |
| G07 | Gate state pure GET | opakovaný GET state | žiadny nový DB riadok | žiadny |
| G08 | Validation | chýbajúce/premrštené/neplatné polia | bezpečné odmietnutie bez zápisu | žiadny |
| G09 | Error sanitization | vynútená DB výnimka | bezpečný kód; bez class/file/line/message | žiadny |
| G10 | DOM XSS | názvy s HTML/JS payloadom | renderované ako text, nič sa nespustí | run-owned cleanup |
| G11 | Asset/detail routes | dashboard a detail session | JS/CSS 200, detail route existuje | žiadny |
| G12 | Placeholder closure | purge-cache/submit-step | 404 alebo plne definovaný interný kontrakt; žiadny falošný success | žiadny |

## 7. Produkčný read-only inspector — testovacia špecifikácia

| ID | Predmet | Očakávanie |
|---|---|---|
| P01 | Unit úplný stav | release/runtime/flags/migrations/counts/run-store bez obsahu záznamov |
| P02 | Chybové vetvy | iba stabilné bezpečné errorCode, žiadne tajomstvo ani raw exception |
| P03 | Feature auth | partial sa zobrazí iba autorizovanej diagnostickej session |
| P04 | Read-only invariant | nulový INSERT/UPDATE/DELETE/migrate/deploy a byte-identické sledované dáta pred/po |
| P05 | Deployment mismatch | chýbajúci source marker je NEZISTENÉ, nie odvodený commit |
| P06 | Run-store privacy | zobrazia sa počty; názvy sa buď redigujú, alebo majú dôkaz bezpečnosti |

## 8. Syntax, statická kontrola a úplný cleanup

Povinné kontroly:

~~~bash
php -l <každý zmenený PHP súbor>
node --check <každý zmenený JS súbor>
vendor/bin/phpunit --no-coverage tests/unit
vendor/bin/phpunit --no-coverage tests/session
vendor/bin/phpunit --no-coverage tests/integration
~~~

Povinný záver:

~~~text
M01_AŽ_M26=PASS_ALEBO_DÔKAZNE_VYRIEŠENÉ
G01_AŽ_G12=PASS
P01_AŽ_P06=PASS
DB_UNIQUENESS=true
OUTCOMES=CREATED+ALREADY_EXISTS
TEMP_FILES=0
TEST_DB_ROWS=0
PRODUCTION_UNTOUCHED=true
RELEASE_VERSION=1.1.15
NOVÝ_ZIP=false
~~~

## 9. Rollback Fázy 11.D

- Vrátiť celý implementačný diff Kroku 11 ako jednu dávku.
- Nevracať automaticky funkčný commit Kroku 10.
- Nemazať existujúce produkčné dáta.
- Ak vznikne migrácia, použiť jej down() iba v izolovanom validačnom prostredí; produkčný rollback patrí až do samostatného nasadzovacieho plánu.
- Obnoviť iba súbory dotknuté dávkou; release 1.1.15 a historické ZIP-y v Kroku 11 nemeníť.

## 10. Fáza 11.D — vykonaná implementačná dávka

Jedna súvisiaca dávka bola pripravená na vetve `agent/krok-11-validacia` voči základu
`dcc817c6d6627ec2c76629a4ca59c2a4f8903c02`. Technický snapshot potvrdený validačným
behom je `d55dcc2d7ff0d9eedb5327b94e757f42cce66bca`.

Vykonané zmeny:

- všetky GATE routes používajú spoločnú diagnostickú autorizáciu; zápisové routes navyše CSRF,
- čítanie stavu GATE je čisté GET bez zápisu a verejné chyby obsahujú iba stabilné bezpečné kódy,
- session, krok a Evidence majú validované vstupy, idempotentný výsledok a databázovú jedinečnosť,
- dashboard a detail session používajú existujúce session ID, bezpečné DOM operácie a platné asset routes,
- placeholder routes, neobsluhované kópie JavaScriptu, nulové zvuky s koncovou medzerou a pracovné snapshoty boli odstránené,
- migrácia M9 vytvára GATE tabuľky, cudzie kľúče a unikátne indexy v izolovanom validačnom prostredí; `down()` zámerne nemaže potenciálne dáta,
- expirovaný concurrency run a manuálny cleanup používajú spoločnú bezpečnú cleanup službu a interný CLI príkaz,
- produkčný read-only inspector nezverejňuje obsah run-store záznamov,
- doplnené boli GATE integračné testy, cleanup unit testy, session regresie a úplný Actions validačný workflow.

Nevznikla nová produktová funkcia, release, ZIP ani produkčný zásah.

### Predčasné artefakty

| Skupina | Rozhodnutie |
|---|---|
| duplicitný `codei/app/app` a `codei/app/public/js` runtime | odstránené neobsluhované kópie |
| pracovné bundle/snapshot/writable výstupy | odstránené z repozitára |
| nulové zvukové súbory s koncovou medzerou | odstránené |
| GATE diagnostika | ponechaná po autorizácii, validácii, CSRF a doplnení testov |
| produkčné session/step/Evidence dáta | bez zásahu; Krok 11 produkciu nemenil |
| `WORK/INI/2026-07-27_08-47_INI_nacitanie-aktualneho-stavu-a-pokracovanie-overenia.md` | `PREKONANÝ`; nenahrádza pôvodný INI Kroku 11 |

### Rollback

Rollbackom je vrátenie celej dávky Kroku 11 voči základu
`dcc817c6d6627ec2c76629a4ca59c2a4f8903c02`. Migráciu M9 možno vrátiť iba
v izolovanom prázdnom validačnom prostredí; na produkciu sa v Kroku 11 neaplikovala.

## 11. Fáza 11.E — úplná validačná evidencia

Autoritatívny dôkaz:

- GitHub Actions run `30252080061`,
- job `89932151438`,
- záver `success`,
- technický HEAD `d55dcc2d7ff0d9eedb5327b94e757f42cce66bca`.

### Prostredie

| Dôkaz | Výsledok |
|---|---|
| PHP CLI | `8.4.23` |
| Composer | `2.10.2` |
| MariaDB | `11.4.12-MariaDB-ubu2404` |
| Migrácie | M1–M9, `Migrations complete.` |
| unikátny index session/krok | `uq_ini_steps_session_number`, prítomný |
| unikátny index krok/Evidence | `uq_ini_evidence_step_hash`, prítomný |

### Testy

| Vrstva | Výsledok |
|---|---|
| syntax PHP a JavaScript | PASS |
| unit | 37 testov, 123 asercií, PASS; 1 neblokujúca PHPUnit deprecation |
| session | 26 testov, 190 asercií, PASS |
| integration | 6 testov, 67 asercií, PASS |
| spolu | 69 testov, 380 asercií |
| reálny HTTP tok | login `303`, START, paralelný HIT A/B, RESULT, PASS |
| výsledné invarianty | `COMPLETED_SUCCESS`, DB uniqueness, replay a cleanup `true` |

Workflow potvrdil:

```text
HTTP_SERVER_READY=true
LOGIN_CSRF_PRESENT=true
DIAGNOSTICS_LOGIN_STATUS=303
DIAGNOSTICS_LOGIN_OK=true
START_CSRF_PRESENT=true
CONCURRENCY_START_OK=true
RESULT_ASSERTIONS_OK=true
DATABASE_CLEANUP_CONFIRMED=true
RUN_STORE_CLEANUP_CONFIRMED=true
TOMBSTONE_CLEANUP_OK=true
```

Po poslednom cleanupe:

```text
question_derivation_request_reservations=0
question_derivation_runs=0
question_derivation_run_domain_terms=0
ini_sessions=0
ini_steps=0
ini_evidence=0
ini_gate_state=0
TEMP_FILES=0
```

### Matice a hranice

- `M01–M26`: PASS alebo dôkazne vyriešené v unit/session/integration a reálnom paralelnom HTTP toku.
- `G01–G12`: PASS v `GateDiagnosticsTest` a statickom audite routes, views a JavaScriptu.
- `P01–P06`: PASS v unit/session testoch produkčného read-only inspectora a v read-only invariantoch.
- Produkcia nebola použitá ako testovacie prostredie a nebola zmenená.
- Release zostáva `1.1.15`; nový ZIP nevznikol.

Neblokujúce technické upozornenia: unit suite hlási jednu PHPUnit deprecation a
GitHub runner upozorňuje na Node 20 deklarovaný v `actions/checkout@v4`.
Nemenia úspešný záver validačného jobu a nepredstavujú zásah do produkcie.

## 12. Fáza 11.F — záver

Read-after-write diffu, testových dôkazov, pôvodného INI, registra, záväzného plánu,
aktívneho checklistu a tejto matice bol vykonaný. Krok 11 je uzavretý s týmto výsledkom:

```text
M01_AŽ_M26=PASS_ALEBO_DÔKAZNE_VYRIEŠENÉ
G01_AŽ_G12=PASS
P01_AŽ_P06=PASS
DB_UNIQUENESS=true
OUTCOMES=CREATED+ALREADY_EXISTS
TEMP_FILES=0
TEST_DB_ROWS=0
CLEANUP=true
STATE=COMPLETED_SUCCESS
PRODUCTION_UNTOUCHED=true
RELEASE_VERSION=1.1.15
NOVÝ_ZIP=false
KROK_11=SPLNENÉ
GATE=OPEN_NAPLNENÁ_A_UZAVRETÁ_PO_VYKONANÍ
VALIDOVANÝ_TECHNICKÝ_HEAD=bc85d18fd0edc1a52fad81f8fac54c1ae66a7014
VALIDAČNÝ_RUN=30252640028
MERGE_COMMIT=4cb2fe0a9dc1ff3a94c450dbe1dc33c38b574b0c
NEXT_ALLOWED_STEP=KROK_12_S_VLASTNÝM_INI_A_GATEM
```

Krok 12 sa smie začať iba vlastným novým INI, novou deväťbodovou maticou a
`GATE=OPEN`. Tento záznam neoprávňuje vytvoriť release ani zasiahnuť produkciu.
