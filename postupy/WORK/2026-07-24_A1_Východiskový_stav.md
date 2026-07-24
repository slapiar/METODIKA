# Fáza A1 — zmrazenie východiskového stavu

## Stav dokumentu

```text
PRACOVNÝ
```

## Väzba na záväzný plán

Tento záznam plní krok `A1. Zmraziť východiskový stav` dokumentu:

`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`

Záznam nemení aplikačný kód ani produkčné prostredie. Oddeľuje potvrdené údaje, interpretácie a chýbajúce dôkazy.

---

# A1.1 Auditný identifikátor

```text
METODIKA-A1-2026-07-24
TECHNICAL_RELEASE_BASE=1.1.9
TECHNICAL_RELEASE_COMMIT=3b91c4e7c4fcb95000595554e361ff417fc992e4
METHODICAL_HEAD_AT_A1_START=5fbe06afb0cc09fea86205972cab1bb132b58c05
BRANCH=main
```

`METHODICAL_HEAD_AT_A1_START` označuje HEAD vetvy `main` zistený pri začatí A1 ešte pred dokumentačnými zápismi záväznosti plánu a tohto záznamu. Technický produkčný základ zostáva release `1.1.9`.

---

# A1.2 Potvrdené skutočnosti

## Repozitár a vetva

- projekt: `METODIKA`,
- autoritatívny repozitár: `github.com/slapiar/metodika`,
- autoritatívna vetva: `main`,
- technický koreň: `/codei`.

## Posledný potvrdený produkčný release

```text
verzia: 1.1.9
commit: 3b91c4e7c4fcb95000595554e361ff417fc992e4
```

Commit `3b91c4e...` menil `RELEASE_VERSION` z `1.1.8` na `1.1.9` a pridal balík `releases/metodika-codei-hostinger-1.1.9.zip`.

## Commity po release `1.1.9` zistené pri začatí A1

```text
530a886b86f0d8b9233e984b6177a228a3ebfb39  Tvorba plánu práce na dnes
411c26f299def7a62767060d270f5ff8c4b03251  oprava prípony
5ed36dc463b67f7ccb9795a2b2e5359e048115ba  docs: vytvor uplny plan prace na 2026-07-24
78d1a4f0dc11e42c4c6dd3f3fe6dc6293288d794  docs: eviduj plan prace na 2026-07-24
f7a891ab07d21f196444baf9d8620f547dc4154e  docs: zapis vytvorenie uplneho planu prace
4caa88f219aac2800a310df3ef817e404ace063e  Doplnenie inicializácie práce
31393793f2e79fb04df7f07deac5c3294b9a84e4  merge do joyee-priority
5fbe06afb0cc09fea86205972cab1bb132b58c05  Štruktúra záznamov vykonanej práce podľa plánu
```

Podľa názvov a dostupných rozdielov ide o dokumentačné a metodické zmeny. Toto tvrdenie ešte nenahrádza úplný diff audit všetkých commitov, ktorý patrí do ďalších krokov Fázy A.

## Posledný potvrdený verejný výsledok produkčného runu

```text
state = COMPLETED_FAILED
A = FAILED_RUNTIME_ERROR
B = CREATED
barrierOpened = true
timeoutReached = false
dbUniquenessConfirmed = true
appReplayConfirmed = false
cleanupConfirmed = true
overallSuccess = false
```

Presný `runId`, uložený tombstone a presné timestampy nie sú v repozitári ani checkpointe zachované.

---

# A1.3 Produkčné feature flagy

## Potvrdené z repozitára

Diagnostická vetva používa najmenej:

```text
METODIKA_DIAGNOSTICS_ENABLED
METODIKA_CONCURRENCY_WEB_ENABLED
METODIKA_DIAGNOSTICS_TOKEN
```

## Aktuálny produkčný stav

```text
METODIKA_DIAGNOSTICS_ENABLED = NEOVERENÉ
METODIKA_CONCURRENCY_WEB_ENABLED = NEOVERENÉ
METODIKA_DIAGNOSTICS_TOKEN = existenciu ani hodnotu nezisťovať z verejného záznamu
```

GitHub repozitár nepotvrdzuje aktuálne hodnoty externého produkčného prostredia. Kým nebudú prakticky overené na serveri, nesmú sa označiť ako zapnuté ani vypnuté.

---

# A1.4 Posledný tombstone

## Skutočnosť

Checkpoint zachováva verejný výsledok posledného runu, ale nie celý tombstone ani `runId`.

## Chýbajúci dôkaz

Na úplné splnenie tejto časti A1 treba z produkčného run store alebo serverového záznamu získať bezpečne:

- `runId`,
- `completedAt`,
- `deleteAfter`,
- `readOnceConsumedAt`, ak vznikol,
- participant outcomes a bezpečné error kódy,
- finalization claimant,
- assertions,
- cleanup status.

Ak bol tombstone už sweepom odstránený, musí sa zaznamenať jeho neprítomnosť a použiť checkpoint spolu so serverovým logom ako náhradný historický dôkaz.

---

# A1.5 Časové okno serverového logu

## Potvrdené body

- checkpoint neúspešnej produkčnej Validácie bol commitnutý 2026-07-23 približne o `17:57 Europe/Bratislava`,
- posledný produkčný run prebehol pred vznikom checkpointu.

## Pracovné vyhľadávacie okno

```text
2026-07-23 17:30:00 až 18:05:00 Europe/Bratislava
2026-07-23 15:30:00 až 16:05:00 UTC
```

Toto okno je odvodené z času checkpointu, nie z presného timestampu runu. Pri nájdení logu sa musí zúžiť podľa `runId`, participantov alebo bezprostrednej následnosti správ.

Hľadaný vzor:

```text
Diagnostics acceptance failed [<code>]: <class>: <message>
```

---

# A1.6 Rozlíšenie výsledku

## Skutočnosť

- release `1.1.9` a jeho commit sú potvrdené,
- verejný výsledok posledného runu je potvrdený checkpointom,
- HEAD pri začatí A1 je potvrdený GitHub históriou.

## Interpretácia

- commity po `1.1.9` sa javia ako dokumentačné a metodické; úplný diff audit ešte nebol vykonaný,
- časové okno logu je odvodené, nie presne zmerané.

## Nepotvrdené

- aktuálne produkčné feature flagy,
- presný `runId`,
- celý tombstone,
- presný timestamp výnimky,
- trieda, správa a miesto výnimky.

---

# A1.7 Stav kroku

```text
A1 = ČIASTOČNE SPLNENÉ
```

Repozitárová a release časť je zmrazená. Produkčná časť A1 zostáva otvorená, kým sa prakticky neoveria feature flagy a nezíska posledný tombstone alebo dôkaz o jeho odstránení.

## Nasledujúci logický krok

Pokračovať v A1 získaním produkčných údajov, ktoré repozitár nemôže potvrdiť. Kód sa nemení. Po uzavretí A1 nasleduje A2 — úplný audit checklistu `1 – 14`.
