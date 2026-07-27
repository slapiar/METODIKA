# Plán práce — dokončenie testovacej sústavy a Krokov 11 až 15

Dátum vytvorenia: 2026-07-26 06:07 Europe/Bratislava

## Stav dokumentu

```text
PRACOVNÝ — ZÁVÄZNÝ
KROKY_1_AŽ_11=SPLNENÉ
AKTÍVNY_KROK=KROK_12
KROK_11=UZAVRETÝ
GATE_KROKU_11=OPEN_NAPLNENÁ_A_UZAVRETÁ_PO_VYKONANÍ
VALIDOVANÝ_TECHNICKÝ_HEAD=bc85d18fd0edc1a52fad81f8fac54c1ae66a7014
VALIDAČNÝ_RUN=30252640028
MERGE_COMMIT=4cb2fe0a9dc1ff3a94c450dbe1dc33c38b574b0c
RELEASE=1.1.15_BEZ_ZMENY
PRODUKCIA=BEZ_ZÁSAHU
GATE_KROKU_12=OPEN
FÁZA_12_A=SPLNENÁ
FÁZA_12_B=SPLNENÁ
NEXT_ALLOWED_ACTION=FÁZA_12_C_PO_VZDIALENOM_READ_BACKU_CHECKPOINTU_12_B
```

## Inicializácia plánovacieho úkonu

- `postupy/WORK/INI/2026-07-26_05-51_INI_dokoncenie-denneho-planu.md`
- `GATE=OPEN`
- HEAD pred vytvorením plánu: `b422b082efcd71062218081c0a348ceb419028d1`

## Účel plánu

Dokončiť pôvodný lineárny plán Krokov 11 až 15 bez ďalšieho čiastkového vývoja a bez opakovaných releaseov.

Záväzné poradie je:

```text
najprv obnoviť a zmraziť skutočný stav
→ potom rozhodnúť o predčasných artefaktoch
→ potom navrhnúť celú testovaciu sústavu naraz
→ potom vykonať jednu súvisiacu implementáciu
→ potom vykonať jednu úplnú lokálnu a integračnú Validáciu
→ až potom vytvoriť jeden release
→ potom vykonať jedno nasadenie
→ potom jeden súvislý produkčný diagnostický priechod
→ potom úplný cleanup
→ potom reValidácia, registre a checkpoint
```

Tento plán nemení výsledky Krokov 1 až 10. Nahrádza iba spôsob pokračovania od otvoreného Kroku 11 po včerajšom STOP.

## Autoritatívne východisko pri vytvorení plánu — historický stav

- repozitár: `slapiar/METODIKA`,
- vetva: `main`,
- technický koreň: `/codei`,
- posledný uzavretý funkčný krok: Krok 10,
- funkčný commit Kroku 10: `c90ae562a859de9fe0b2174f39e924c7f7bc6a4e`,
- posledný technický základ pôvodne použitý pri Kroku 11: `d418e72c162bde324af7546c937af979bd75182e`,
- Krok 11 sa vykonal a uzavrel výhradne v:
  `postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md`,
- historický stav brány pri vytvorení plánu bol `GATE=CLOSED`; brána bola neskôr
  dôkazne otvorená, naplnená a Krok 11 bol uzavretý merge commitom `4cb2fe0a...`,
- platí jeho uložený pokyn:

```text
NOVÝ_INI_KROKU_11=false
NOVÝ_ENVIRONMENTÁLNY_TEST_PRI_OBNOVE_BRÁNY=false
```

Nový krokový INI sa vytvorí až pre Krok 12. Samostatný opravný INI z 2026-07-27
11:28 iba napravuje evidenčný rozpor po uzavretí a Krok 11 znovu neotvára.

## Skutočnosti zistené pred vytvorením plánu

1. Aktuálny `main` bol oproti technickému základu `d418e72...` o 71 commitov vpredu.
2. Zmenilo sa 45 súborov vrátane vykonateľného kódu, konfigurácie, routes, kontrolérov, modelu, views, JavaScriptu, release verzie a ZIP balíkov.
3. Testovacie súbory sa v tomto rozdiele nezmenili.
4. Aktuálna release verzia v repozitári je `1.1.15`; pribudli balíky `1.1.10` až `1.1.15`.
5. Pre aktuálny HEAD ani pre posledný technický commit `b8c2344...` nie je priradený workflow run.
6. Pribudla samostatná vetva Gate Supervisora a diagnostické zápisy session, kroku a Evidence.
7. Časť nových endpointov pracuje s pevnými testovacími ID, časť verejných API routes nemá spoločnú diagnostickú autorizáciu a v kóde ostali placeholder metódy.
8. Diagnostický Evidence endpoint môže vo svojej odpovedi vrátiť databázovú chybu.
9. Pre nové moduly neboli nájdené zodpovedajúce testy.
10. Aktuálny produkčný release, feature flagy, testovacie riadky a cleanup po včerajších pokusoch nie sú v repozitári spoľahlivo doložené.
11. Predčasné diagnostické artefakty zostávajú podľa STOP záznamu `na rozhodnutie`; nie sú automaticky platným výsledkom.

## Záväzné pravidlá vykonávania

1. Krok 11 používa jediný existujúci INI záznam a otvorí sa až po deviatich aktuálne doložených hodnotách `ÁNO`.
2. Vnútorné fázy 11.A až 11.F nie sú samostatné projektové kroky a nevytvárajú nové INI; vedú sa v jednom WORK zázname Kroku 11 s priebežnými checkpointmi dôkazov.
3. Pred každou fázou sa preverí, či jej úkon už nebol vykonaný a či dôkaz zostáva kontinuálny voči aktuálnemu HEAD a prostrediu.
4. Žiadny predčasný artefakt sa neprevezme iba preto, že existuje alebo bol nasadený.
5. Žiadny predčasný artefakt sa neodstráni bez určenia jeho pôvodu, väzieb, produkčného stavu, dôsledku a rollbacku.
6. Analýza celej testovacej sústavy sa musí dokončiť pred prvou zmenou testu alebo vykonateľného kódu.
7. Všetky potvrdené chýbajúce testy a najmenšie nevyhnutné opravy vzniknú v jednej implementačnej dávke a jednom kontrolovanom pracovnom prúde.
8. Počas Kroku 11 sa nezvyšuje release verzia a nevytvára produkčný balík.
9. Release vznikne iba po úplnom úspechu všetkých povinných blokov Kroku 11.
10. Nasadenie a produkčný run sa nevykonajú po častiach.
11. Pri prvom neúspešnom povinnom kritériu sa ďalšia fáza neotvorí.
12. Produkčný run sa pri chybe neopakuje naslepo.

---

# Krok 11 — Úplná lokálna a integračná Validácia

## Predpoklad pokračovania

Najprv sa v tom istom pôvodnom INI Kroku 11 vykoná:

```text
nové načítanie stabilného HEAD
→ porovnanie s c90ae562... a d418e72...
→ overenie aktuálnej produkcie, release a testovacích dát
→ overenie všetkých závislostí
→ aktualizácia deviatich bodov
→ GATE=OPEN
```

Bez otvorenia tejto brány je dovolené iba čítanie, obnova dôkazov a aktualizácia samotného INI.

## Fáza 11.A — Obnova a zmrazenie skutočného stavu

### Cieľ

Získať jeden spoločný, časovo označený východiskový stav repozitára, releaseov, produkcie, databázy a diagnostických artefaktov.

### Povinné úkony

1. Znovu načítať aktuálny vzdialený HEAD a potvrdiť stabilitu počas kontroly.
2. Zaznamenať úplný diff:
   - `c90ae562... → aktuálny HEAD`,
   - `d418e72... → aktuálny HEAD`,
   - release `1.1.9 → aktuálny repozitár`.
3. Zistiť bez zmeny produkcie:
   - presne nasadenú verziu,
   - zdrojový commit nasadenia, ak je dostupný,
   - stav flagov `METODIKA_DIAGNOSTICS_ENABLED` a `METODIKA_CONCURRENCY_WEB_ENABLED`,
   - existenciu a obsah testovacích session, krokov a Evidence iba v bezpečnom rozsahu,
   - zvyšné run-store JSON, lock a temp súbory,
   - aktuálny stav databázových testovacích riadkov.
4. Zaznamenať runtime verzie a migrácie iba z praktického dôkazu, nie z konfigurácie.
5. Oddeliť:

```text
stav repozitára
stav release balíkov
stav nasadenej produkcie
stav databázy
stav dočasných súborov
stav feature flagov
```

### Výstup

Jedna dôkazová mapa v WORK zázname Kroku 11 s presným časom a bez tvrdenia, že nezistené znamená nulu.

### Kritérium úspechu

Každý uvedený stav je buď doložený, alebo výslovne označený `NEZISTENÉ` s uvedeným chýbajúcim prístupom či dôkazom.

### STOP

- HEAD sa počas kontroly zmení,
- produkčný stav nemožno bezpečne čítať,
- neexistuje spôsob rozlíšiť testovacie a netestovacie dáta,
- akýkoľvek čítací úkon by vyžadoval neplánovanú zmenu produkcie.

### Rollback

Žiadny dátový rollback; fáza je iba čítacia. Pri vlastnom dočasnom lokálnom artefakte odstrániť iba tento artefakt po zaznamenaní výsledku.

## Fáza 11.B — Klasifikácia 71 commitov a predčasných artefaktov

### Cieľ

Rozhodnúť o každej skupine zmien podľa dôkazu, nie podľa dojmu.

### Povinné skupiny

1. metodické a evidenčné zmeny,
2. presuny duplicitných súborov `codei/app/app/`,
3. Boot a externé prostredie,
4. routes,
5. pôvodná diagnostika súbežnosti,
6. Gate Dashboard a Gate Supervisor,
7. diagnostické session/step/Evidence endpointy,
8. modely,
9. views a layout,
10. klientsky JavaScript a zvuky,
11. release verzia a balíky 1.1.10 až 1.1.15,
12. produkčné testovacie dáta a ich cleanup.

### Pre každú skupinu určiť

```text
PÔVOD
ZÁMER
DÔKAZ_PREDCHÁDZAJÚCEHO_VYKONANIA
AKTUÁLNA_FUNKCIA
VÄZBY
TESTOVÉ_POKRYTIE
PRODUKČNÝ_STAV
RIZIKO
ROZHODNUTIE
ROLLBACK
```

### Povolené rozhodnutia

```text
PONECHAŤ_A_VALIDOVAŤ
PONECHAŤ_PO_NAJMENŠEJ_OPRAVE
VRÁTIŤ_PRED_VALIDÁCIOU
ODLOŽIŤ_MIMO_KROKU_11
NEZISTENÉ — VYŽADUJE ROZHODNUTIE AUTORITY
```

Rozhodnutie o zámere nového Gate Supervisora, ak ho nemožno jednoznačne odvodiť z autoritatívneho projektu a doloženého zadania, sa pred implementáciou predloží používateľovi. Dovtedy sa nesmie domyslieť.

### Kritérium úspechu

Žiadny zmenený vykonateľný súbor ani release balík nezostane bez klasifikácie.

### STOP

- nie je možné určiť autoritatívny zámer,
- zmena mieša viac nezávislých funkcií bez rozlíšiteľného rollbacku,
- produkcia obsahuje odlišný kód než ktorýkoľvek identifikovaný release.

## Fáza 11.C — Návrh celej testovacej sústavy naraz

### Cieľ

Pred prvou implementáciou vytvoriť úplnú mapu testov, príkazov, dát, očakávaní, cleanupu a dôkazov.

### Povinné jadro deviatich blokov Kroku 11

1. run store a validator,
2. `FirstAcceptanceService`,
3. diagnostické chybové fázy,
4. session a security endpointy,
5. integračný DB rollback,
6. skutočný súbežný test s dvoma procesmi alebo spojeniami,
7. end-to-end `START → HIT A/B → RESULT`,
8. tombstone a sweep,
9. regresia login/database/logout diagnostiky.

### Povinné mapovanie

- všetkých 14 bodov aktívneho implementačného checklistu,
- všetkých 26 scenárov M01–M26,
- všetkých potvrdených zmien ponechaných z Fázy 11.B,
- všetkých nových routes a verejných endpointov,
- všetkých modelov a databázových zápisov ponechaných v rozsahu,
- release skriptu a balíka iba ako budúcej závislosti Kroku 12.

### Osobitné povinné rozhodnutia

1. Či expirácia M21 patrí do implementácie Kroku 11 alebo sa musí samostatne vrátiť k významovému kontraktu.
2. Či manuálny cleanup M22 bude interný prevádzkový úkon alebo autorizovaný endpoint.
3. Ako sa vykoná skutočne paralelný HTTP dôkaz M12/M17 bez ručného predpripravenia participanta.
4. Ako sa otestujú všetky bezpečnostné hranice M16, M25 a M26.
5. Ako sa Validuje úplný tombstone invariant M23.
6. Ako sa bezpečne klasifikujú všetky fázy zlyhania M08/M24 bez úniku interných údajov.
7. Ak sa ponechá Gate Supervisor:
   - jeho autentifikácia a autorizácia,
   - CSRF hranice,
   - validácia vstupov,
   - jedinečnosť krokov a Evidence,
   - význam `gate_state`,
   - bezpečné chybové odpovede,
   - cleanup testovacích záznamov,
   - UI a API regresia.

### Pre každý test určiť

```text
ID
PREDMET
VRSTVA
PREDPOKLADY
VSTUP
PROSTREDIE
POČIATOČNÝ_STAV
PRESNÝ_PRÍKAZ_ALEBO_REQUEST
OČAKÁVANÝ_VÝSLEDOK
NEGATÍVNY_VÝSLEDOK
DÔKAZ
CLEANUP
ROLLBACK
VÄZBA_NA_CHECKLIST_A_MATICU
```

### Výstup

Jedna úplná testovacia špecifikácia. Nesmie vzniknúť nový testovací súbor ani zmena kódu, kým táto špecifikácia neobsahuje všetky ponechané funkcie.

### Kritérium úspechu

Každý scenár je priradený ku konkrétnemu budúcemu testu alebo má doložené rozhodnutie, prečo sa vracia k významovému kontraktu či odkladá mimo rozsahu.

### STOP

- scenár nemá jednoznačný očakávaný výsledok,
- cleanup nie je možné dokázať,
- test by musel používať produkciu namiesto izolovaného prostredia,
- chýba rozhodnutie Autority o zámere ponechanej funkcie.

## Fáza 11.D — Jedna súvisiaca implementačná dávka

### Predpoklad

Úplne uzavretá a read-backom potvrdená Fáza 11.C.

### Cieľ

Implementovať naraz všetky potvrdené chýbajúce testy a iba tie najmenšie opravy vykonateľného kódu, ktoré sú nevyhnutné na splnenie potvrdeného kontraktu.

### Záväzný rozsah

- jedna pracovná vetva alebo jeden izolovaný implementačný prúd,
- jeden spoločný diff pripravený na úplnú Validáciu,
- žiadny release bump,
- žiadny produkčný zásah,
- žiadna nová funkcia nad rámec potvrdeného zámeru,
- žiadny placeholder,
- žiadny pevný produkčný identifikátor testovacích dát,
- žiadny raw exception alebo databázová správa vo verejnej odpovedi,
- žiadne obchádzanie spoločnej autorizácie.

### Povinné implementačné pravidlo

```text
najprv test reprodukujúci potvrdený nedostatok
→ potom najmenší funkčný zásah
→ potom lokálny read-back diffu
→ bez čiastkového merge, release alebo nasadenia
```

### Kritérium úspechu

Všetky zmeny zodpovedajú testovacej špecifikácii; nič navyše nie je v diffe.

### STOP

- oprava vyžaduje zmenu ontologického alebo aplikačného kontraktu,
- objaví sa potreba databázovej migrácie, ktorá nebola v špecifikácii,
- zmena by miešala nezávislú novú funkciu,
- HEAD alebo závislosti sa zmenia mimo pracovného prúdu.

### Rollback

Vrátiť celú implementačnú dávku; existujúci funkčný commit Kroku 10 sa automaticky nevracia.

## Fáza 11.E — Jedna úplná lokálna a integračná Validácia

### Povinné poradie

1. syntax a statická kontrola všetkých zmenených PHP a JS súborov,
2. unit testy run store, validatora, stavov, `FirstAcceptanceService` a nových modulov,
3. feature a security testy všetkých endpointov,
4. integračný DB rollback,
5. dvojprocesový file-store test,
6. dve reálne MySQLi spojenia,
7. skutočne paralelný HTTP tok,
8. úplný `START → HIT A/B → RESULT`,
9. tombstone, sweep a cleanup,
10. login/database/logout regresia,
11. úplná matica M01–M26,
12. prípadné testy Gate Supervisora, ak bol potvrdený na ponechanie,
13. finálny audit dočasných súborov a databázových riadkov.

### Povinné dôkazy

- presný testovaný HEAD,
- presný diff,
- verzie PHP, Composeru a MariaDB,
- rozšírenia a procesné schopnosti,
- stav migrácií,
- príkazy a celé výsledky,
- počty databázových riadkov pred a po,
- run-store JSON, lock a temp stav pred a po,
- potvrdenie izolácie od produkcie,
- potvrdenie cleanupu.

### Spoločné úspešné kritérium

```text
VŠETKY_POVINNÉ_TESTY=PASS
AND M01_AŽ_M26=VALIDOVANÉ_ALEBO_DÔKAZNE_VYRIEŠENÉ
AND DB_UNIQUENESS=true
AND OUTCOMES=CREATED+ALREADY_EXISTS
AND CLEANUP=true
AND TEMP_FILES=0
AND TEST_DB_ROWS=0
AND STATE=COMPLETED_SUCCESS
AND PRODUCTION_UNTOUCHED=true
```

### STOP

Pri jedinom neúspešnom povinnom kritériu:

```text
KROK_11=NEUZAVRETÝ
RELEASE=ZAKÁZANÝ
KROK_12=ZATVORENÝ
```

Vznikne presný záznam príčiny. Nevykoná sa čiastkový release ani produkčný pokus.

## Fáza 11.F — Uzavretie Kroku 11

### Povinné úkony

1. Spätne načítať výsledný diff a všetky testové dôkazy.
2. Aktualizovať pôvodný INI Kroku 11.
3. Vytvoriť alebo dokončiť jeden WORK záznam Kroku 11.
4. Aktualizovať aktívny checklist a maticu M01–M26 podľa skutočnosti.
5. Zaznamenať všetky ponechané, vrátené a odložené predčasné artefakty.
6. Potvrdiť nulový cleanup.
7. Určiť presný validovaný HEAD pre Krok 12.

### Kritérium úspechu

```text
KROK_11=SPLNENÉ
VALIDOVANÝ_HEAD=presne_určený
CLEANUP=true
NEXT_ALLOWED_STEP=KROK_12
```

### Skutočný výsledok — 2026-07-27

```text
KROK_11=SPLNENÉ
VALIDOVANÝ_TECHNICKÝ_HEAD=bc85d18fd0edc1a52fad81f8fac54c1ae66a7014
VALIDAČNÝ_RUN=30252640028
MERGE_COMMIT=4cb2fe0a9dc1ff3a94c450dbe1dc33c38b574b0c
VŠETKY_POVINNÉ_TESTY=PASS
M01_AŽ_M26=PASS_ALEBO_DÔKAZNE_VYRIEŠENÉ
G01_AŽ_G12=PASS
P01_AŽ_P06=PASS
CLEANUP=true
TEMP_FILES=0
TEST_DB_ROWS=0
PRODUCTION_UNTOUCHED=true
RELEASE_VERSION=1.1.15
NOVÝ_ZIP=false
NEXT_ALLOWED_STEP=KROK_12
```

Presné vykonanie, dôkazy, rollback a zostávajúce neblokujúce upozornenia sú v
`postupy/WORK/2026-07-27_10-20_Krok_11_Klasifikacia_a_testovacia_specifikacia.md`
a v pôvodnom INI Kroku 11.

---

# Krok 12 — Jeden release a úplný audit balíka

## Predpoklad otvorenia

- Krok 11 je úplne uzavretý,
- existuje nový samostatný INI Kroku 12,
- deväť bodov je `ÁNO`,
- `GATE=OPEN`.

## Povinné úkony

1. Znovu potvrdiť validovaný HEAD.
2. Určiť rozdiel od posledného preukázateľne nasadeného a overeného release, nie iba od čísla v `RELEASE_VERSION`.
3. Zvýšiť verziu iba raz.
4. Vytvoriť jeden ZIP autoritatívnym skriptom.
5. Vypočítať hash.
6. Rozbaliť ZIP do čistého adresára.
7. Porovnať každý auditovaný súbor so zdrojovým HEAD.
8. Potvrdiť neprítomnosť tajomstiev, `.env`, logov, testovacích dát, private adresárov, vývojových artefaktov a neplatných ciest.
9. Overiť názvy súborov vrátane nežiaducich koncových medzier.
10. Zachovať posledný overený release na rollback.

## Kritérium úspechu

```text
JEDEN_RELEASE=true
HASH=ZAZNAMENANÝ
BALÍK_ZODPOVEDÁ_HEAD=true
ZAKÁZANÉ_ARTEFAKTY=0
ROLLBACK_BALÍK=DOSTUPNÝ
```

## STOP

Ak sa zistí jediný rozdiel, neznámy pôvod súboru, zakázaný artefakt alebo neoverený rollback, balík sa nenasadí.

---

# Krok 13 — Jedno nasadenie a jeden súvislý produkčný diagnostický priechod

## Predpoklad otvorenia

- Krok 12 je uzavretý,
- existuje samostatný INI Kroku 13,
- nasadzovaný commit, verzia a hash sú presne známe,
- rollback je pripravený,
- produkčný východiskový stav z Fázy 11.A je znovu potvrdený.

## Povinné poradie

1. zaznamenať stav pred nasadením,
2. nasadiť jediný auditovaný balík,
3. overiť verziu a základnú dostupnosť,
4. zapnúť iba potrebné diagnostické flagy,
5. potvrdiť, že neprebieha iný test,
6. vykonať celý pripravený diagnostický scenár v určenom poradí,
7. zaznamenať všetky odpovede, runId, časy a logy,
8. nevkladať medzi jednotlivé časti nový release ani funkčnú opravu,
9. pri chybe prejsť priamo na bezpečný STOP a cleanup.

## Povinný produkčný výsledok

```text
barrierOpened=true
timeoutReached_A=false
timeoutReached_B=false
outcomes=CREATED+ALREADY_EXISTS
dbUniquenessConfirmed=true
appReplayConfirmed=true
cleanupConfirmed=true
overallSuccess=true
state=COMPLETED_SUCCESS
```

Ak bol Gate Supervisor potvrdený a zahrnutý do release, jeho produkčný smoke test sa vykoná v tom istom pripravenom priechode, s vlastnými vopred určenými testovacími dátami a cleanupom. Nesmie sa dopĺňať improvizovaným tlačidlom počas produkčného testu.

## STOP

- žiadny druhý produkčný pokus naslepo,
- vypnúť dočasné flagy,
- zachovať logy a dôkazy,
- vykonať pripravený cleanup,
- podľa kritéria vykonať rollback release,
- vytvoriť checkpoint príčiny.

---

# Krok 14 — Tombstone, sweep a úplný produkčný cleanup

## Predpoklad otvorenia

- produkčný run sa skončil úspechom alebo riadeným STOP,
- existuje samostatný INI Kroku 14.

## Povinné overenie

1. `readOnceConsumedAt`,
2. tombstone do `deleteAfter`,
3. sweep JSON a lock súboru,
4. nulové temp súbory,
5. nulové testovacie databázové riadky,
6. cleanup session, kroku a Evidence vytvorených testom,
7. vypnuté feature flagy,
8. diagnostický režim vypnutý,
9. zachované iba určené logové dôkazy,
10. produkcia bez zvyškov všetkých včerajších aj dnešných testovacích dát.

## Kritérium úspechu

```text
TOMBSTONE_CONTRACT=true
SWEEP=true
RUNSTORE_FILES=0
TEMP_FILES=0
DB_TEST_ROWS=0
GATE_TEST_ROWS=0
FEATURE_FLAGS=OFF
DIAGNOSTIC_MODE=OFF
PRODUCTION_CLEAN=true
```

Krok 15 sa neotvorí, kým nie je každý bod pravdivo potvrdený.

---

# Krok 15 — ReValidácia, registre a záverečné uzavretie

## Predpoklad otvorenia

- Krok 14 je úplne uzavretý,
- existuje samostatný INI Kroku 15.

## Povinné zápisy

1. aktualizovať checklist 1–14,
2. aktualizovať maticu M01–M26,
3. uzavrieť WORK záznamy Krokov 11 až 15,
4. aktualizovať technické implementačné a validačné dokumenty,
5. aktualizovať `TECHNICKE-NAVRHY/README.md`, ak sa zmenil technický stav,
6. aktualizovať `postupy/README.md`,
7. aktualizovať `CHANGELOG.md`,
8. uviesť presný produkčný commit, verziu a hash,
9. zaznamenať otvorené riziká,
10. vytvoriť záverečný checkpoint.

## Povinné oddelenie tvrdení

```text
skutočnosť
pozorovanie
výsledok testu
interpretácia
potvrdená príčina
vykonaný úkon
následok
Validácia
```

## Kritérium úspechu

```text
VŠETKY_KROKY_MAJÚ_INI_A_WORK=true
VŠETKY_TVRDENIA_MAJÚ_DÔKAZ=true
REGISTRE_ZODPOVEDAJÚ_SKUTOČNOSTI=true
CHANGELOG_ZODPOVEDÁ_SKUTOČNOSTI=true
PRODUKČNÝ_COMMIT_VERZIA_HASH=URČENÉ
OTVORENÉ_RIZIKÁ=ZAZNAMENANÉ
CHECKPOINT=VYTVORENÝ
PLÁN=SPLNENÝ
```

---

# Historický stav predčasných artefaktov pred ukončením Fázy 11.B

Nasledujúci blok platil iba do ukončenia Fázy 11.B. Konečná klasifikácia
a rozhodnutie sú zapísané v
`postupy/WORK/2026-07-27_10-20_Krok_11_Klasifikacia_a_testovacia_specifikacia.md`.

```text
postupy/WORK/INI/2026-07-25_17-33_INI_testovaci-krok-session-1.md
postupy/WORK/INI/2026-07-25_17-46_INI_diagnostika-evidence-kroku-1.md
súvisiace routes, controllery, views, JS a release balíky

STAV=NA_ROZHODNUTIE
AUTOMATICKY_PLATNÉ=false
AUTOMATICKY_NA_ODSTRÁNENIE=false
POVOLENÉ=iba čítanie, dôkazová klasifikácia a bezpečné zistenie produkčného stavu
```

# Aktuálny nasledujúci povolený pracovný úkon

Krok 11 je splnený. Krok 12 bol otvorený samostatným INI:

`postupy/WORK/INI/2026-07-27_11-46_INI_Krok_12_Jeden_release_a_uplny_audit_balika.md`

Jeho podrobný záväzný vykonávací plán je:

`postupy/PLAN/2026-07-27_11-51_Plan_Kroku_12_release_a_audit_balika.md`

```text
KROK_12=AKTÍVNY
GATE_KROKU_12=OPEN
FÁZA_12_A=SPLNENÁ
FÁZA_12_B=SPLNENÁ
MAIN_CHECKPOINTU_12_A=771b0c2b69e3f1e1d7b74604b672275823bc9f95
CODEI_TREE=4ec1d8408f84d9c21699aba9c2f2a70592f7ac6c
RELEASE_VERSION=1.1.15_BEZ_ZMENY
NOVÝ_ZIP=false
RELEASE_ENVIRONMENT_RUN=30258778406
NEXT_ALLOWED_ACTION=FÁZA_12_C_PO_VZDIALENOM_READ_BACKU_CHECKPOINTU_12_B
PRODUKČNÉ_NASADENIE=ZAKÁZANÉ
KROK_13=ZATVORENÝ
```

Fáza 12.A zmrazila auditnú základňu, potvrdila zhodu aktuálneho `/codei`
s validovaným stromom Kroku 11 a znovu overila rollbackový ZIP `1.1.9`
voči jeho zdrojovému commitu bez jedinej manifestovej alebo obsahovej
odchýlky. Historický ZIP `1.1.15` bol jednorazovo klasifikovaný, zostal
nedotknutý a ďalej sa nepoužíva ako aktívna release ani rollbacková základňa.

Fáza 12.B bola vykonaná runom `30258778406`. Ten doložil presný checkout,
Ubuntu 24.04.4, PHP 8.4.23, Composer 2.10.2, povinné nástroje, zhodný strom
`/codei` a čistý pracovný strom bez zmeny verzie alebo vytvorenia ZIP-u.

Fáza 12.C smie začať až po publikovaní a vzdialenom read-backu checkpointu
Fázy 12.B. Vykoná presne jedno spustenie `./release.sh patch`; produkčné
nasadenie a otvorenie Kroku 13 zostávajú zakázané.

# Historická podmienka ukončenia plánovacieho zadania z 2026-07-26

Pôvodné zadanie „dokončiť plán“ bolo splnené po:

1. vytvorení tohto plánu,
2. vzdialenom read-backu,
3. vytvorení WORK záznamu plánovacieho úkonu,
4. aktualizácii `postupy/README.md`,
5. aktualizácii `CHANGELOG.md`,
6. konečnej aktualizácii a uzavretí plánovacieho INI,
7. potvrdení, že `/codei`, testy, release a produkcia zostali bez zmeny.
