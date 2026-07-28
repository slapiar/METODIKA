# Checkpoint webového súbežného overenia po neúspešnej produkčnej Validácii

## Stav dokumentu

```text
PRACOVNÝ
```

## Identifikácia

- Projekt: `METODIKA`
- Autoritatívny repozitár: `github.com/slapiar/metodika`
- Autoritatívna vetva: `main`
- Technický koreň: `/codei`
- Predmet práce: webové súbežné overenie atómového prvého prijatia
- Dátum checkpointu: `2026-07-23`

## Dôvod zastavenia

Práca sa zastavuje, pretože priebežné zásahy a interpretácie sa začali odchyľovať od záväzného poradia podľa `postupy/Inicializácia práce.md`. Produkčné výpisy potvrdili viacero čiastkových technických udalostí, ale celý diagnostický cieľ zostáva nesplnený. Nie je prípustné označiť čiastkový priebeh za úspech ani pokračovať ďalšími opravami bez úplnej obnovy kontextu a auditu aktuálneho zdrojového stavu.

## Posledný produkčný výsledok

```text
Stav runu: COMPLETED_FAILED
DB unikátnosť: POTVRDENÉ
Aplikačný replay: NEPOTVRDENÉ
Cleanup: POTVRDENÉ
Celkovo: NEPOTVRDENÉ
A: FAILED_RUNTIME_ERROR
B: CREATED
barrierOpened: áno
timeoutReached: nie
```

## Skutočne potvrdené pozorovania

1. `START`, `HIT A`, `HIT B` a `RESULT` odpovedali HTTP 200 s JSON obsahom.
2. Oba HIT requesty sa vykonali bez pozorovaného session timeoutu.
3. `barrier.openedAt` bolo v produkčnom behu potvrdené.
4. V poslednom behu sa nezapísal `PARTNER_TIMEOUT`.
5. Databázová unikátnosť bola výsledkom diagnostiky potvrdená.
6. Cleanup bol výsledkom diagnostiky potvrdený.
7. Očakávaná replay dvojica `CREATED + ALREADY_EXISTS` nevznikla.
8. Jeden participant skončil `CREATED`, druhý `FAILED_RUNTIME_ERROR`.
9. Celý run správne skončil `COMPLETED_FAILED`, nie `COMPLETED_SUCCESS`.

## Čo nie je potvrdené

1. Presná trieda, správa a miesto výnimky vedúcej k `FAILED_RUNTIME_ERROR`.
2. Či výnimka vzniká v aplikačnej službe, repository adaptéri, transakčnej hranici, histórii derivácie, databázovej vrstve alebo pri zostavení diagnostického vstupu.
3. Či aktuálna implementácia bariéry zodpovedá pôvodnému checklistu bez vedľajšej interpretácie uloženého stavu.
4. Či posledná poistka pred timeoutom má samostatný regresný test pre pretekové okno.
5. Či všetky testy deklarované ako hotové v aktívnom checkliste stále zodpovedajú aktuálnemu kódu po dnešných zásahoch.
6. Či pomenovanie a zobrazenie priebehu v UI dostatočne odlišuje úspešný transport od úspechu aplikačnej operácie.

## Dnešné zásahy, ktoré treba zajtra auditovať

- `604c4abab577cfcec1a97177579a19968e566eae` — rozšírené logovanie HIT odpovedí v UI.
- `c2f7a45f5b6f2c19d8d2bcac66928f8024681003` — prvá širšia úprava zachovania bariéry v run store.
- `4f6829a1e2cc07520f10bdda25903d57e145f277` — rozbalenie acceptance chyby na bezpečný výsledkový kód a zápis detailu do serverového logu.
- `9f8e8da50074ed19b657afaac7a1a2c29cb56024` — nahradenie širšieho bariérového invariantu kontrolou `barrier.openedAt` a poistkou pred timeoutom pod zámkom.

Tieto commity sú historické skutočnosti, nie dôkaz správnosti výslednej implementácie.

## Otvorené riziká

- `DiagnosticsConcurrencyRunStore::load()` môže vracať interpretovanú návratovú kópiu namiesto presného uloženého stavu.
- Všeobecný výsledok `FAILED_RUNTIME_ERROR` stále neodhaľuje príčinu bez serverového logu.
- Existujúce testy môžu overovať starší stav implementácie a nemusia pokrývať dnešné pretekové scenáre.
- Zelené HTTP a priebehové hlášky v UI môžu používateľsky pôsobiť ako úspech, hoci ide iba o úspešný transport a dokončenie requestu.
- Nie je zatiaľ preukázané, že je prítomná iba jedna zostávajúca chyba.

## Záväzný začiatok pokračovania

Pred akýmkoľvek ďalším návrhom alebo zápisom vykonať celý postup z `postupy/Inicializácia práce.md`, najmä:

1. znovu načítať autoritatívne metodické a projektové dokumenty,
2. načítať tento checkpoint a aktívny checklist,
3. načítať aktuálny kód všetkých dotknutých služieb, kontroléra, run store, repository adaptérov, transakčnej hranice, testov a UI,
4. porovnať aktuálny kód s checklistom bod po bode,
5. oddeliť skutočnosť, pozorovanie, výsledok, interpretáciu a dôkaz,
6. vytvoriť auditnú tabuľku `HOTOVÉ / ČIASTOČNE / NEOVERENÉ / CHYBNÉ`,
7. až potom určiť najmenší ďalší zásah.

## Prvý cieľ zajtrajšej práce

Nevykonávať ďalšiu opravu. Najprv vytvoriť úplný audit skutočného stavu webového súbežného overenia a presne určiť:

- ktoré kroky checklistu sú stále reálne splnené,
- ktoré boli iba deklarované alebo overené staršími testami,
- ktoré dnešné zásahy treba ponechať, upraviť alebo vrátiť,
- aký konkrétny dôkaz chýba na identifikáciu `FAILED_RUNTIME_ERROR`.

## Kritérium uzavretia budúceho pracovného kroku

Ďalší krok sa nesmie označiť za dokončený iba podľa HTTP 200, otvorenej bariéry alebo vykonaného cleanupu. Úspech celej diagnostiky zostáva:

```text
DB unikátnosť = true
AND aplikačný replay = CREATED + ALREADY_EXISTS
AND cleanup = true
AND state = COMPLETED_SUCCESS
```
