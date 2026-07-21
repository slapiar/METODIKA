# 7.13 Následky metodických úkonov

## Východisko

Metodický úkon a jeho následok nie sú totožné.

```text
METHODICAL_ACTION
≠
METHODICAL_RESULT
```

Úkon je historicky zachytiteľná udalosť. Následok je významová zmena, ktorú tento úkon vyvolal alebo mal vyvolať.

Príklady:

```text
CREATE_SUBJECT
≠
SUBJECT
```

```text
VALIDATE
≠
VALID
```

```text
MERGE
≠
MERGED_SUBJECT
```

```text
DELETE
≠
NON_EXISTENCE
```

Vymazanie z evidencie samo osebe nepreukazuje zánik predmetu. Môže viesť k stavom ako `ARCHIVED`, `INVALID`, `SUPERSEDED` alebo `REMOVED_FROM_ACTIVE_SCOPE`.

---

## Pracovná definícia

```text
METHODICAL_RESULT
=
spätne citovateľný následok
konkrétneho metodického úkonu,
ktorý vytvoril, zmenil, potvrdil,
zneplatnil, nahradil, rozdelil,
zlúčil alebo interpretoval
metodickú skutočnosť.
```

---

## Základné pravidlá

### 1. Úkon nie je svojím následkom

Historická udalosť musí zostať rozlíšená od stavu alebo identity, ktorá po nej vznikla.

```text
ACTION
≠
RESULT
≠
STATE
```

### 2. Jeden úkon môže mať viac následkov

Napríklad úkon `MERGE_SUBJECTS` môže súčasne:

- ukončiť samostatnú kontinuitu viacerých SUBJECT-ov,
- vytvoriť nový SUBJECT,
- vytvoriť nové vzťahy pôvodu,
- zmeniť rozsah platných Autorít,
- vytvoriť nový stav evidencie.

Preto:

```text
METHODICAL_ACTION
1 : N
METHODICAL_RESULT
```

### 3. Jeden následok môže mať viac príčin

Následok nemusí byť dôsledkom jediného úkonu. Môže vzniknúť až súbehom viacerých udalostí, rozhodnutí alebo Validácií.

```text
METHODICAL_ACTION
N : M
METHODICAL_RESULT
```

Tento vzťah sa nesmie zjednodušiť na jediný automatický pôvod, ak ho realita nepotvrdzuje.

### 4. Následok môže meniť stav bez vzniku nového SUBJECT-u

Zmena názvu, vlastníka, umiestnenia, hodnotenia alebo prevádzkového stavu spravidla nemení identitu SUBJECT-u, pokiaľ zostáva zachované jeho jadro totožnosti.

```text
RESULT
→
NEW_STATE_OF_SAME_SUBJECT
```

### 5. Následok môže vytvoriť nový SUBJECT

Nový SUBJECT vzniká iba vtedy, ak výsledok úkonu vytvoril samostatne zdôvodniteľný predmet skúmania s vlastným významom, rozsahom a kritériami identity.

```text
RESULT
→
NEW_SUBJECT
```

neplatí automaticky.

Musí byť osobitne zdôvodnené.

### 6. Následok môže byť neskôr zrušený alebo prekonaný

Zrušenie následku nevymazáva pôvodný úkon z histórie.

```text
REVOKE_RESULT
≠
DELETE_ORIGINAL_ACTION
```

Pôvodný úkon zostáva historickou skutočnosťou. Novší úkon iba mení platnosť, účinnosť alebo interpretáciu jeho následku.

### 7. Následok musí byť spätne priraditeľný

Pri každom následku musí byť možné zistiť najmenej:

```text
ČO vzniklo alebo sa zmenilo
KTO vykonal príslušný úkon
KEDY úkon nastal
NA ZÁKLADE ČOHO bol vykonaný
AKÁ AUTORITA bola použitá
KTORÝ SUBJECT bol zasiahnutý
AKÝ STAV alebo IDENTITA po úkone vznikli
```

---

## Pracovné typy následkov

Tento zoznam nie je uzavretou SQL typológiou. Je iba významovým rozlíšením pre ďalšiu metodickú prácu.

```text
CREATED
MODIFIED
CONFIRMED
VALIDATED
INVALIDATED
REPLACED
SUPERSEDED
SPLIT
MERGED
ARCHIVED
RESTORED
INTERPRETED
DELEGATED
REVOKED
```

Typ následku nesmie byť odvodený iba z názvu úkonu. Musí zodpovedať skutočnému významu zmeny.

---

## Vzťah príčiny, úkonu, následku a stavu

Pracovný všeobecný model:

```text
CAUSE
  →
METHODICAL_ACTION
  →
METHODICAL_RESULT
  →
STATE
```

Treba zachovať rozlíšenie:

```text
PRÍČINA
≠
ÚKON
≠
NÁSLEDOK
≠
STAV
```

- príčina vysvetľuje, prečo úkon nastal,
- úkon zachytáva, čo sa historicky vykonalo,
- následok určuje, čo tým vzniklo alebo sa zmenilo,
- stav určuje, v akej platnej podobe sa SUBJECT nachádza po zohľadnení následku.

---

## Kontrolné otázky

Pri každom metodickom úkone treba overiť:

1. Aký konkrétny následok vznikol?
2. Vznikol jeden následok alebo viac následkov?
3. Je následok výsledkom jediného úkonu alebo viacerých príčin?
4. Mení následok stav existujúceho SUBJECT-u?
5. Vytvára následok nový samostatný SUBJECT?
6. Mení následok identitu, rozsah alebo iba aktuálny stav?
7. Je následok platný, účinný, zrušený alebo nahradený?
8. Je možné spätne citovať jeho pôvod, ACTOR-a, Autoritu a čas?

---

## Záver

```text
NÁSLEDOK
je prepojovacím článkom
medzi historickým úkonom
a novým stavom metodickej reality.
```

Bez explicitného zachytenia následkov by METODIKA vedela, že sa niečo stalo, ale nevedela by spoľahlivo určiť, čo tým vzniklo, čo sa zmenilo a ktorý stav je z toho odvodený.

Prirodzeným pokračovaním je určiť pravidlá platnosti, účinnosti a časového trvania metodického následku.