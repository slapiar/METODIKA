# INICIALIZÁCIA — Zosúladenie Kroku 7 a Krok 8

## Identifikácia

```text
dátum a čas: 2026-07-24 13:34 Europe/Bratislava
projekt: METODIKA
činnosť: zosúladenie registra a CHANGELOG po Kroku 7; následná oprava diagnostického rozlíšenia v Kroku 8
autoritatívny repozitár: slapiar/METODIKA
autoritatívna vetva: main
HEAD pri overení: c24fc1b62118af4a11b5ed8afe7b581656b2535f
záväzný plán: postupy/PLAN/2026-07-24_08-04_Plán_práce.md
```

## Povinná brána

### 1. Metodika načítaná: ÁNO

Načítaný bol aktuálny dokument `postupy/Inicializácia práce.md`, blob `201bcaef882e21e99c484b2c9acd61489e1e9914`, vrátane dôkazovej brány, pravidiel spätného načítania, Validácie a STOP.

### 2. Projekt a autoritatívny zdroj overený: ÁNO

`PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`, určuje projekt METODIKA, repozitár `slapiar/METODIKA`, vetvu `main` a technický koreň `/codei`.

### 3. Vetva a HEAD overené: ÁNO

Aktuálny HEAD autoritatívnej vetvy `main` je `c24fc1b62118af4a11b5ed8afe7b581656b2535f`, commit `Uzavretie Kroku 7 reprodukciou koreňovej príčiny`.

### 4. Potrebné prístupy prakticky overené: ÁNO

Čítanie zdrojov, plánu, registra, CHANGELOG a GitHub Actions dôkazov prešlo. Zápis do `main` je prakticky potvrdený vytvorením tohto jediného povoleného INI záznamu.

### 5. Prostredie prakticky overené: ÁNO

Dokumentačné a zdrojové zmeny sa vykonajú cez GitHub Contents API. Testovacie prostredie Kroku 8 je GitHub Actions s PHP 8.4 a uzamknutými Composer závislosťami; rovnaká cesta úspešne vykonala run `30089261354`. Produkcia sa nepoužije.

### 6. Závislosti kroku dostupné: ÁNO

Dostupné sú:

- úspešný run `30089261354` a job `89468590309`,
- pracovný záznam Kroku 7,
- záväzný plán a presné kritériá Kroku 8,
- `DiagnosticsController::executeAcceptIfReady()`,
- `DiagnosticsConcurrencyAcceptanceRunner`,
- session testy diagnostického kontroléra,
- register `postupy/README.md` a `CHANGELOG.md`.

### 7. Predmet a hranice zásahu určené: ÁNO

Predmet:

1. zosúladiť `postupy/README.md` a `CHANGELOG.md` s výsledkom `KROK_7=SPLNENÉ`,
2. otvoriť a vykonať Krok 8,
3. rozlíšiť fázy `BUILD_INITIAL_RUN`, `LOAD_PAYLOAD_FINGERPRINT`, `CREATE_ACCEPTANCE_RUNNER`, `APPLICATION_ACCEPT` a `WRITE_PARTICIPANT_RESULT`,
4. vytvoriť bezpečný kód `fáza × trieda chyby`,
5. do serverového logu zapísať kód, fázu, triedu, správu, `runId` a participant,
6. do run dokumentu a UI nepustiť raw text výnimky,
7. doplniť testy každej diagnostickej fázy.

Mimo rozsahu zostáva funkčná oprava `RequestReferenceRepository`; patrí až do Kroku 10. Produkcia, databázová schéma a verejné endpointy sa nemenia.

### 8. Kritérium úspechu určené: ÁNO

Úspech nastane, ak:

- register a CHANGELOG evidujú Krok 7 ako `SPLNENÉ` a Krok 8 ako jediný otvorený krok,
- každá diagnostická fáza vytvorí odlišný bezpečný kód,
- log obsahuje úplný interný kontext,
- run dokument a UI obsahujú iba bezpečný kód,
- testy všetkých fáz a test neprítomnosti raw exception textu prejdú,
- funkčná koreňová chyba zostane nezmenená.

### 9. Rollback určený: ÁNO

Rollback je vrátenie samostatného diagnostického commitu Kroku 8 a samostatné spätné udalosti v `postupy/README.md` a `CHANGELOG.md`. História sa nemaže. Testovacie dáta nevznikajú v produkcii.

## Výsledok brány

```text
GATE=OPEN
POVOLENÝ_ÚKON=Zosúladiť Krok 7 a vykonať samostatnú diagnostickú opravu Kroku 8 s testami
```
