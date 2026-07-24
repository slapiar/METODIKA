# Fáza A1 — pokračovanie a produkčná blokácia

## Stav dokumentu

```text
PRACOVNÝ
```

## Väzba na záväzný plán

Tento záznam pokračuje v kroku `A1. Zmraziť východiskový stav` dokumentu:

`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`

Nadväzuje na:

`postupy/WORK/2026-07-24_A1_Východiskový_stav.md`

---

# A1.1 Obnovený aktuálny stav

- autoritatívny repozitár: `github.com/slapiar/metodika`,
- autoritatívna vetva: `main`,
- HEAD pri pokračovaní A1: `399564423b692877120a05929751e869486c821c`,
- produkčný technický základ: release `1.1.9`, commit `3b91c4e7c4fcb95000595554e361ff417fc992e4`,
- aktuálny pracovný krok: `A1`,
- vykonateľný kód sa v tomto kroku nemení.

Commity po predchádzajúcom zázname A1 zaviedli priečinok `postupy/WORK/`, presunuli doň záznam A1 a opravili pravidlo zapisovania pracovných krokov v `postupy/Inicializácia práce.md`.

---

# A1.2 Overenie novej štruktúry WORK

## Skutočnosť

`postupy/Inicializácia práce.md` prikazuje zapisovať stav vykonanej práce podľa štruktúry záväzného plánu do `postupy/WORK/`, do súboru označeného dátumom, časom a názvom kroku.

## Zistená nekonzistencia

Register `postupy/README.md` po presune stále odkazoval na neexistujúcu cestu:

```text
PLAN/2026-07-24_A1_Východiskový_stav.md
```

Skutočná cesta je:

```text
WORK/2026-07-24_A1_Východiskový_stav.md
```

Táto cesta sa v tom istom pracovnom kroku opravuje v registri a v `CHANGELOG.md`.

---

# A1.3 Produkčná časť A1

## Potvrdené

Z repozitára a checkpointu je potvrdené:

```text
release = 1.1.9
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

## Nepotvrdené

Bez praktického prístupu k produkčnému serveru nemožno potvrdiť:

- aktuálnu hodnotu `METODIKA_DIAGNOSTICS_ENABLED`,
- aktuálnu hodnotu `METODIKA_CONCURRENCY_WEB_ENABLED`,
- presný `runId`,
- celý tombstone alebo dôkaz jeho odstránenia sweepom,
- presný timestamp výnimky,
- triedu, správu a miesto výnimky.

## Rozhodovacia brána

A1 nemožno označiť za úplne splnené iba na základe repozitára. Záväzný plán zároveň určuje, že po uzavretí A1 nasleduje A2. Preto sa A2 zatiaľ nezačína.

---

# A1.4 Potrebný produkčný dôkaz

Na uzavretie A1 je potrebné bezpečne získať zo servera:

1. hodnoty diagnostických feature flagov bez zverejnenia tokenu,
2. zoznam alebo bezpečný výpis súborov v produkčnom run store,
3. posledný tombstone alebo potvrdenie, že už bol odstránený sweepom,
4. serverový log v pracovnom okne `2026-07-23 17:30 – 18:05 Europe/Bratislava`, najmä vzor:

```text
Diagnostics acceptance failed [<code>]: <class>: <message>
```

Citlivé údaje, tokeny, heslá, raw payload ani citlivé SQL sa nesmú zapisovať do repozitára.

---

# A1.5 Rozlíšenie výsledku

## Skutočnosť

- štruktúra `postupy/WORK/` je aktívna,
- záznam A1 bol do nej presunutý,
- register obsahoval starú cestu,
- produkčné údaje požadované plánom nie sú dostupné cez GitHub.

## Vykonaný úkon

- vytvorený tento časovaný záznam pokračovania A1,
- opravená cesta A1 v registri,
- zapísaná zmena do `CHANGELOG.md`.

## Výsledok

```text
A1 = ČIASTOČNE SPLNENÉ — BLOKOVANÉ PRODUKČNÝM DÔKAZOM
```

## Riziko

Preskočenie na A2 by porušilo poradie záväzného plánu a vytvorilo by audit bez úplne zmrazeného produkčného východiska.

## Nasledujúci logický krok

Získať uvedené produkčné údaje a doplniť ich do ďalšieho časovaného záznamu v `postupy/WORK/`. Až po uzavretí A1 možno začať A2.
