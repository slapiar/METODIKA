# Krok 7 — Reprodukcia koreňovej príčiny mimo produkcie

## Stav

```text
SPLNENÉ
```

## Východisko

Krok 7 záväzného plánu vyžadoval mimo produkcie prakticky potvrdiť rovnaký `InitialDerivationRun` a fingerprint, reálnu testovaciu databázu, rovnakú transakčnú cestu, kontrolované zlyhanie, dve nezávislé databázové spojenia a rollback.

## Inicializačné záznamy

- `postupy/WORK/INI/2026-07-24_13-02_INI_Krok_7_Reprodukcia_korenovej_priciny.md` — prvá brána `GATE=CLOSED`, pretože Codespaces nemal neprodukčnú MySQL/MariaDB databázu,
- `postupy/WORK/INI/2026-07-24_13-08_INI_Krok_7_GitHub_Actions_MariaDB.md` — otvorená brána pre izolovaný MariaDB service container v GitHub Actions,
- `postupy/WORK/INI/2026-07-24_13-20_INI_Krok_7_Oprava_PHP_84_workflowu.md` — nová brána po zistení nekompatibility PHP 8.3.6 s uzamknutými Symfony 8.1 balíkmi.

## Reprodukčné prostredie

GitHub Actions workflow:

```text
.github/workflows/krok-7-root-cause-reproduction.yml
```

Použité prostredie:

```text
Ubuntu 24.04
PHP 8.4
Composer 2
MariaDB 11.4 service container
izolovaná databáza metodika_krok7
DBDriver=MySQLi
DBDebug=false
```

Produkčná databáza ani produkčné prostredie neboli použité.

## Dôkazový beh

```text
workflow run: 30089261354
job: 89468590309
conclusion: success
```

Úspešne prešli všetky rozhodujúce kroky:

1. inicializácia MariaDB service containera,
2. nastavenie PHP 8.4, požadovaných rozšírení a Composeru,
3. inštalácia uzamknutých závislostí,
4. konfigurácia izolovanej databázy,
5. overenie cieľovej databázy a InnoDB,
6. migrácie M1–M8,
7. overenie schémy otázkového odvodzovania,
8. reprodukcia cez nezmenenú aplikačnú cestu.

## Potvrdená trieda koreňovej príčiny

Reprodukčný príkaz môže skončiť úspechom iba po potvrdení tejto príčinnej cesty:

```text
rovnaká REQUEST_REFERENCE
+ odlišná derivation_reference
+ DBDebug=false
+ reálna MySQLi/InnoDB cesta
→ neúspešný insert rezervácie bez kontroly návratovej hodnoty
→ postcheck iba podľa REQUEST_REFERENCE nájde rezerváciu prvého toku
→ druhý tok je nesprávne vyhodnotený ako CREATED
→ createInitialRun() nenájde presnú rezerváciu REQUEST_REFERENCE + derivation_reference
→ RuntimeException: Historický beh nemožno založiť bez presnej rezervácie REQUEST_REFERENCE.
→ rollback druhého toku
```

Tým bola statická hypotéza z Kroku 6 prakticky reprodukovaná.

## Rollback a cleanup

Reprodukčný príkaz overil, že druhý participant po zlyhaní nezanechal vlastné riadky. Následný cleanup potvrdil:

```text
0 rezervácií
+ 0 historických behov
+ 0 doménových väzieb
```

MariaDB service container a jeho sieť boli po skončení jobu odstránené GitHub Actions runnerom.

## Záver rozhodovacej brány 7

```text
REPRODUKOVATEĽNÝ_SCENÁR=ÁNO
TRIEDA_PRÍČINY_POTVRDENÁ=ÁNO
ROLLBACK_POTVRDENÝ=ÁNO
CLEANUP_POTVRDENÝ=ÁNO
ROZHODOVACIA_BRÁNA_7=SPLNENÁ
KROK_7=SPLNENÉ
```

Funkčnú opravu je už dovolené navrhovať až v kroku určenom záväzným plánom. Krok 7 sám funkčnú opravu nevykonal.

## Nasledujúci logický krok

```text
Krok 8 — Oprava diagnostického rozlíšenia
```
