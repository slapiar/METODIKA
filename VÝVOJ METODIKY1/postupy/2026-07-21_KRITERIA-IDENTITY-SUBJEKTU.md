# 7.9 Kritériá identity SUBJECT-u

## Otázka

Ako sa pre konkrétny druh `SUBJECT-u` určí a Validuje jeho jadro totožnosti bez svojvôle?

## Východisko

Z bodu 7.8 vyplýva:

```text
SUBJECT zostáva tým istým SUBJECT-om,
pokiaľ je zachovaná kontinuita jeho jadra totožnosti.
```

Samotné tvrdenie o jadre totožnosti však nestačí. Ak by sa jeho obsah určoval dodatočne podľa želaného výsledku, rozhodnutie o kontinuite by bolo svojvoľné.

## Pravidlo predbežného určenia

```text
Jadro identity
nesmie byť určované dodatočne
podľa želaného výsledku.
```

Kritériá kontinuity identity sa určia:

```text
pred začatím hodnotenia
alebo najneskôr
pri prvom prijatí SUBJECT-u.
```

Ak to pre povahu predmetu nie je možné, musí byť neskoršie určenie výslovne označené, časovo zachytené a odôvodnené. Nesmie sa spätne vydávať za kritérium, ktoré platilo od začiatku.

## Kritériá kontinuity identity

Pre každý `SUBJECT` musia byť určiteľné kritériá, podľa ktorých sa rozhoduje, či zmena:

- zachovala totožnosť,
- vytvorila nový samostatný `SUBJECT`,
- alebo vytvorila vzťah medzi pôvodným a novým predmetom.

Pracovné označenie:

```text
SUBJECT_IDENTITY_CRITERIA
```

Definícia:

```text
SUBJECT_IDENTITY_CRITERIA
=
explicitne určené podmienky,
podľa ktorých sa posudzuje
kontinuita totožnosti SUBJECT-u.
```

## Kritériá nie sú univerzálne vlastnosti

Jadro identity nie je pre všetky predmety rovnaké.

Príklady:

### Osoba

Meno, adresa ani povolanie nemusia tvoriť jadro identity, pretože sa môžu meniť bez zániku totožnosti osoby.

### Projekt

Verzia, repozitár, vlastník alebo technické umiestnenie nemusia samy osebe určovať totožnosť projektu. Rozhodujúce môže byť zachovanie jeho predmetu, účelu a kontinuity vývoja.

### Dokument

Názov súboru ani jeho umiestnenie nemusia určovať totožnosť dokumentu. Rozhodujúca môže byť kontinuita konkrétneho diela a jeho revízií.

### Udalosť

Pri udalosti môže byť rozhodujúci konkrétny historický výskyt. Jeho neskorší opis alebo hodnotenie nevytvára tú istú udalosť znovu.

Z toho vyplýva:

```text
Typológia SUBJECT-ov
môže pomáhať pri určovaní kritérií identity,
ale nesmie vytvoriť uzavretý zoznam
povolených druhov SUBJECT-u.
```

## Pravidlo explicitnosti

```text
Každý SUBJECT
musí mať určiteľné
kritériá kontinuity identity.
```

To neznamená, že pri každom jednoduchom predmete musí byť okamžite vytvorený rozsiahly samostatný dokument. Znamená to, že pri spore alebo zmene musí byť možné explicitne uviesť, podľa čoho sa jeho totožnosť posudzuje.

## Oddelenie identity od kritérií identity

```text
SUBJECT
≠
SUBJECT_IDENTITY_CRITERIA
```

`SUBJECT` je predmet skúmania.

`SUBJECT_IDENTITY_CRITERIA` sú pravidlá, podľa ktorých sa posudzuje kontinuita jeho totožnosti.

Kritériá nepredstavujú samotnú identitu a technický identifikátor nepredstavuje ani jedno z nich.

## Revízia kritérií

Kritériá identity sa môžu ukázať ako neúplné, chybné alebo nevhodné. Ich zmena nesmie prepísať historický stav.

Pracovné označenie:

```text
IDENTITY_CRITERIA_REVISION
```

Platí:

```text
zmena kritérií identity
=
nová historicky zachytiteľná revízia
```

nie:

```text
prepísanie pôvodných kritérií
bez zachovania ich histórie
```

Revízia musí umožniť zistiť najmenej:

- ktoré kritériá platili pred revíziou,
- aká zmena bola vykonaná,
- prečo bola vykonaná,
- kto ju navrhol,
- kto ju Validoval,
- odkedy sa používa,
- či a ako ovplyvňuje skoršie rozhodnutia o kontinuite.

## Spätná zdôvodniteľnosť rozhodnutia

```text
Rozhodnutie,
že ide alebo nejde
o ten istý SUBJECT,
musí byť spätne zdôvodniteľné.
```

Musí byť možné odpovedať:

1. Aké kritériá identity boli použité?
2. Kedy boli tieto kritériá platné?
3. Aké skutočnosti boli podľa nich posudzované?
4. Kto rozhodnutie vykonal?
5. Aká Autorita ho Validovala?
6. Boli kritériá alebo rozhodnutie neskôr revidované?

Samotné tvrdenie:

```text
tak sme sa rozhodli
```

nie je dostatočným metodickým zdôvodnením.

## Neutralita kritérií

Kritériá identity nesmú byť formulované tak, aby vopred vynútili želaný výsledok konkrétneho prípadu.

Neprípustný postup:

```text
najprv zvolený výsledok
→ potom prispôsobené kritériá
```

Prípustný postup:

```text
vopred určené kritériá
→ zistené skutočnosti
→ rozhodnutie o kontinuite
→ Validácia
```

## Pracovný model

```text
SUBJECT
      │
      ├── SUBJECT_DEFINITION
      ├── SUBJECT_SCOPE
      ├── SUBJECT_IDENTITY_CRITERIA
      ├── SUBJECT_STATE
      ├── SUBJECT_EVENT
      └── SUBJECT_EVALUATION
```

Tento zápis je významovým rozlíšením, nie návrhom SQL schémy.

## Pravidlo bodu 7.9

```text
Totožnosť SUBJECT-u
sa posudzuje podľa explicitne určených
kritérií kontinuity,
ktoré musia byť známe,
spätne citovateľné
a historicky sledovateľné.
```

Kritériá sa nesmú dodatočne meniť podľa želaného výsledku. Ich oprava alebo doplnenie vytvára novú revíziu a musí zachovať históriu pôvodného rozhodovania.

## Otvorená otázka pre bod 7.10

```text
Kto má oprávnenie
určovať, meniť a Validovať
kritériá identity SUBJECT-u?
```

Táto otázka prepája:

```text
SUBJECT
IDENTITY
AUTHORITY
VALIDATION
```
