# Predbežný plán práce — 2026-07-25

## Stav

```text
PREDBEŽNÝ
KROKY_1_AŽ_9=SPLNENÉ
JEDINÝ_NASLEDUJÚCI_KROK=KROK_10
```

## Účel

Bezpečne dokončiť záväzný lineárny plán z 2026-07-24 od Kroku 10 po Krok 15. Tento dokument nemení poradie ani obsah pôvodného záväzného plánu. Rozdeľuje zostávajúcu prácu na vykonateľné bloky a určuje podmienky STOP.

## Záväzné východisko

- autoritatívny repozitár: `slapiar/METODIKA`,
- autoritatívna vetva: `main`,
- posledný uzavretý krok: Krok 9,
- potvrdená koreňová príčina z Kroku 7:

```text
DBDebug=false
+ nekontrolovaný neúspešný insert rezervácie
+ postcheck iba podľa REQUEST_REFERENCE
→ druhý tok pokračuje ako CREATED
→ chýba presná rezervácia REQUEST_REFERENCE + derivation_reference
→ RuntimeException
→ rollback
```

- Krok 9 potvrdil, že `load()` ani timeoutová poistka nevyžadujú funkčnú zmenu.

---

# Ranná inicializácia

Pred akoukoľvek implementáciou:

1. znovu načítať `postupy/Inicializácia práce.md`,
2. overiť projekt, vetvu a aktuálny HEAD,
3. načítať záväzný plán, WORK záznamy Krokov 7–9 a aktuálne dotknuté repository služby,
4. prakticky overiť PHP, Composer, testovaciu MariaDB, migrácie, izoláciu a cleanup,
5. vytvoriť samostatný INI záznam Kroku 10,
6. otvoriť Krok 10 iba pri deviatich doložených hodnotách `ÁNO`.

```text
Bez GATE=OPEN sa Krok 10 nezačne.
```

---

# Krok 10 — Najmenšia funkčná oprava koreňovej príčiny

## Cieľ

Opraviť iba potvrdenú chybu rezervácie bez zásahu do diagnostiky, UI, run-store bariéry, timeoutu, databázovej schémy alebo release procesu.

## Povinná analýza pred návrhom

Určiť priamo v aktuálnych zdrojoch:

- presný repository komponent vykonávajúci rezerváciu,
- návratový kontrakt `insert()` pri `DBDebug=false`,
- aktuálny postcheck podľa `REQUEST_REFERENCE`,
- dostupný alebo chýbajúci presný lookup podľa `REQUEST_REFERENCE + derivation_reference`,
- invariant, podľa ktorého iba vlastník presnej rezervácie smie vytvoriť počiatočný history run,
- správanie prvého a druhého súbežného toku,
- rollback jediného funkčného commitu.

## Očakávaný minimálny výsledok

```text
prvý tok = CREATED
paralelný druhý tok = ALREADY_EXISTS
žiadny tok bez presnej rezervácie nepokračuje do CREATE_INITIAL_HISTORY_RUN
```

## STOP

Krok sa zastaví, ak:

- presnú príčinu nemožno prepojiť na jediný komponent,
- oprava by vyžadovala zmenu schémy,
- oprava by miešala diagnostiku, UI alebo testovacie pomôcky,
- izolovaná DB nie je pripravená.

## Rollback

Vrátenie jediného funkčného commitu Kroku 10.

---

# Krok 11 — Úplná lokálna a integračná Validácia

Otvorí sa iba po samostatnom uzavretí Kroku 10.

## Povinné testovacie bloky

1. run store a validator,
2. `FirstAcceptanceService`,
3. diagnostické chybové fázy,
4. session a security endpointy,
5. integračný DB rollback,
6. skutočný súbežný test s dvoma procesmi alebo spojeniami,
7. end-to-end `START → HIT A/B → RESULT`,
8. tombstone a sweep,
9. regresia login/database/logout diagnostiky.

## Povinné úspešné kritérium

```text
DB_UNIQUENESS=true
AND OUTCOMES=CREATED+ALREADY_EXISTS
AND CLEANUP=true
AND STATE=COMPLETED_SUCCESS
```

Pred aj po testoch zaznamenať:

- počty databázových riadkov,
- run-store JSON a lock súbory,
- temp súbory,
- cleanup výsledok.

## STOP

Pri jedinom neúspešnom povinnom kritériu:

```text
RELEASE=ZAKÁZANÝ
```

Vznikne pracovný záznam príčiny; ďalší krok sa neotvorí.

---

# Krok 12 — Release a kontrola balíka

Otvorí sa iba po úplnom úspechu Kroku 11.

## Povinné úkony

- určiť presný validovaný HEAD,
- skontrolovať diff od release `1.1.9`,
- zvýšiť verziu až po Validácii,
- vytvoriť ZIP autoritatívnym skriptom,
- vypočítať hash,
- rozbaliť balík do čistého adresára,
- porovnať auditované súbory so zdrojovým commitom,
- potvrdiť neprítomnosť `.env`, tokenov, logov, testovacích dát, private adresárov a vývojových artefaktov,
- zachovať predchádzajúci balík a presný rollback.

## STOP

Balík sa nenasadí pri rozdiele zdrojov, chýbajúcom hashi alebo prítomnosti zakázaného artefaktu.

---

# Krok 13 — Jeden čistý produkčný run

Otvorí sa iba po audite release balíka.

## Predpoklady

- nasadený presne overený release,
- známy commit, verzia a hash,
- otvorený serverový log,
- čisté testovacie dáta,
- dočasne zapnuté iba potrebné feature flagy,
- pripravený cleanup a rollback.

## Zaznamenať

- `runId`,
- časy START, HIT A a HIT B,
- participant outcomes,
- finalization claimant,
- DB uniqueness,
- application replay,
- cleanup,
- tombstone.

## Úspech

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

## STOP

Pri neúspechu:

- žiadny druhý pokus naslepo,
- vypnúť diagnostické flagy,
- vykonať cleanup,
- zachovať logy a dôkazy,
- vytvoriť checkpoint.

---

# Krok 14 — Tombstone, sweep a produkčný cleanup

Otvorí sa iba po ukončení jediného produkčného runu.

Overiť:

- prvé čítanie nastavilo `readOnceConsumedAt`,
- tombstone zostal do `deleteAfter`,
- sweep odstránil JSON aj lock podľa kontraktu,
- databáza neobsahuje testovacie riadky,
- feature flagy sú vypnuté,
- produkcia nezostala v diagnostickom režime.

---

# Krok 15 — Záznam, reValidácia a uzavretie

## Aktualizovať podľa skutočnosti

- checklist a maticu M01–M26,
- pracovné záznamy v `postupy/WORK/`,
- technický implementačný záznam,
- Validáciu alebo reValidáciu,
- registre,
- `CHANGELOG.md`,
- záverečný checkpoint.

## Povinné oddelenie v závere

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

---

# Pracovné pravidlo dňa

```text
Krok 10
→ až po uzavretí Krok 11
→ až po úplnom úspechu Krok 12
→ až po audite balíka Krok 13
→ Krok 14
→ Krok 15
```

Nie je dovolené:

- otvoriť dva kroky súčasne,
- pripraviť release pred ukončením testov,
- nasadiť balík bez čistého auditu,
- opakovať produkčný run naslepo,
- označiť plán za dokončený bez produkčného cleanupu a záverečnej reValidácie.

## Poznámka k rozsahu dňa

Kroky 10–15 sa nemusia dokončiť za každú cenu v jednom dni. Každá rozhodovacia brána má prednosť pred časovým plánom. Ak sa deň skončí po ktoromkoľvek korektne uzavretom kroku, vznikne checkpoint a ďalší krok sa otvorí až v novom pracovnom bloku.
