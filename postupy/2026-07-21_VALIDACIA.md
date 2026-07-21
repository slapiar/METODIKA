# 7.17 Validácia

## Východisko

Validácia nie je pravda, dôkaz ani samotný výsledok.

```text
VALIDÁCIA
≠
PRAVDA
≠
DÔKAZ
≠
VÝSLEDOK
```

Validácia je metodický úkon, ktorým sa posudzuje, či konkrétny cieľ spĺňa vopred určené kritériá v konkrétnom kontexte.

## Základné pravidlo

```text
VALIDÁCIA
=
POSÚDENIE
PODĽA KRITÉRIÍ
V KONTEXTE
V ČASE
```

Validácia teda nikdy nesmie byť neurčitým potvrdením typu:

```text
je to správne
```

Musí byť spätne zistiteľné:

```text
ČO bolo validované
PODĽA AKÝCH kritérií
KTO validáciu vykonal
NA ZÁKLADE AKEJ autority
KEDY validácia prebehla
V AKOM kontexte a rozsahu
S AKÝM výsledkom
NA ZÁKLADE AKÝCH podkladov
```

## Cieľ validácie

Cieľom validácie môže byť napríklad:

```text
SUBJECT
IDENTITY_CRITERIA
MEASUREMENT_RESULT
EVIDENCE
ASSERTION
METHODICAL_ACTION
METHODICAL_RESULT
MODEL
INTERPRETATION
```

Pre každý cieľ sa musia použiť kritériá zodpovedajúce jeho typu.

```text
VALIDÁCIA MERANIA
≠
VALIDÁCIA DÔKAZU
≠
VALIDÁCIA TVRDENIA
≠
VALIDÁCIA SUBJECT-u
```

## Kritériá validácie

Validácia bez explicitných kritérií nie je preskúmateľná.

```text
VALIDATION_CRITERIA
```

musia byť:

- známe pred rozhodnutím,
- primerané validovanému cieľu,
- spätne citovateľné,
- historicky sledovateľné,
- oddelené od výsledku validácie.

Základné pravidlo:

```text
KRITÉRIÁ
NESMÚ BYŤ
DODATOČNE PRISPÔSOBENÉ
ŽELANÉMU VÝSLEDKU
```

## Podklady validácie

Validácia môže používať:

```text
MERANIE
POZOROVANIE
DÔKAZ
ZÁZNAM
VÝPOČET
TEST
POROVNANIE
SVEDČENIE
EXTERNÝ ZDROJ
```

Podklad validácie však nie je totožný s výsledkom validácie.

```text
VALIDATION_INPUT
≠
VALIDATION_RESULT
```

Prítomnosť podkladu sama osebe neurčuje výsledok.

## Výsledok validácie

Validácia nemá byť obmedzená iba na dvojicu áno/nie.

Možné výsledky:

```text
VALID
INVALID
INCONCLUSIVE
CONDITIONALLY_VALID
VALID_WITH_LIMITATIONS
NOT_VERIFIABLE
```

Každý výsledok musí mať uvedený rozsah platnosti.

```text
VALID
```

neznamená:

```text
pravdivé vždy a všade
```

ale:

```text
spĺňa určené kritériá
v určenom kontexte
v určenom čase
v určenom rozsahu
```

## Platnosť validácie v čase

Validácia je historická udalosť.

```text
VALIDATION_EVENT
```

Jej výsledok môže neskôr prestať byť použiteľný, ak sa zmenia:

- kritériá,
- podklady,
- kontext,
- rozsah,
- poznanie,
- stav validovaného SUBJECT-u.

To však neruší skutočnosť, že pôvodná validácia prebehla.

```text
ZÁNIK ÚČINNOSTI VALIDÁCIE
≠
VYMAZANIE VALIDÁCIE
```

Namiesto prepisovania musí vzniknúť nový metodický úkon, napríklad:

```text
REVALIDATE
REVOKE_VALIDATION
SUPERSEDE_VALIDATION
LIMIT_VALIDATION_SCOPE
```

## Validácia a autorita

Validáciu vykonáva ACTOR.

Ak má mať autoritatívny účinok, musí existovať doložiteľný AUTHORITY_CONTEXT.

```text
ACTOR
≠
AUTHORITY
```

```text
VYKONANÁ VALIDÁCIA
≠
OPRÁVNENÁ VALIDÁCIA
```

Validácia vykonaná bez potrebnej autority môže historicky existovať, ale nemusí vyvolať zamýšľaný metodický následok.

## Validácia a pravda

Najdôležitejšie rozlíšenie:

```text
VALIDOVANÉ TVRDENIE
≠
PRAVDIVÉ TVRDENIE
```

Validácia určuje stav posúdenia podľa dostupných kritérií a podkladov. Neurčuje realitu samu.

Preto môže nastať:

```text
pravdivé, ale nevalidované
pravdivé, ale zatiaľ nepreukázané
validované, ale neskôr vyvrátené
validné iba v obmedzenom rozsahu
nevalidné pre nedostatok podkladov
```

## Validácia validácie

Aj samotná validácia môže byť predmetom ďalšieho preskúmania.

```text
VALIDATION_REVIEW
```

Preskúmanie môže posudzovať:

- správnosť použitých kritérií,
- úplnosť podkladov,
- oprávnenie ACTOR-a,
- konzistentnosť postupu,
- správnosť aplikácie kritérií,
- primeranosť rozsahu výsledku.

Platí:

```text
VALIDÁCIA
NIE JE NEODVOLATEĽNÝ KONIEC POZNANIA
```

## Minimálny model

```text
VALIDATION_EVENT
        │
        ├── ACTOR
        ├── AUTHORITY_CONTEXT
        ├── TARGET
        ├── VALIDATION_CRITERIA
        ├── VALIDATION_INPUT
        ├── CONTEXT
        ├── TIME
        ├── RESULT
        ├── SCOPE
        └── JUSTIFICATION
```

## Záverečné pravidlá

```text
Validácia
je metodický úkon,
nie vlastnosť reality.
```

```text
Výsledok validácie
je platný iba vzhľadom
na svoje kritériá,
kontext,
čas
a rozsah.
```

```text
Každá validácia
musí byť spätne preskúmateľná.
```

```text
Nové poznanie
nesmie prepisovať starú validáciu,
ale musí vytvoriť
novú historickú udalosť.
```
