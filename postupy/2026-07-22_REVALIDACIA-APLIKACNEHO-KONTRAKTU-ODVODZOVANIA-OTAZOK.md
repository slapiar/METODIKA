# ReValidácia aplikačného kontraktu odvodzovania otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument je novou historickou validačnou udalosťou. Nadväzuje na:

```text
postupy/2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md
```

Pôvodnú Validáciu s výsledkom `CONDITIONALLY_VALID` neprepisuje. Posudzuje aktuálny aplikačný kontrakt po vykonaní troch povinných opráv.

---

# 1. VALIDATION_EVENT

```text
TARGET:
opravený aplikačný kontrakt prvého doménového algoritmu odvodzovania otázok

ACTOR:
ChatGPT vykonávajúca metodické preskúmanie na výslovný pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa vykonať opravy a ďalšiu reValidáciu
v repozitári slapiar/METODIKA, vetva main;
tento záznam nepredstiera konečnú Autoritu meniť autoritatívne definície METODIKY

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; uzavretie topológie odozvy pred technickým návrhom aplikačnej služby

SCOPE:
korelácia požiadavky a výsledku, model vetvovej závislosti,
deterministická agregácia run_state a zachovanie predchádzajúcich kontraktových hraníc;
bez Validácie PHP tried, databázovej schémy, HTTP rozhrania alebo konkrétnej implementácie
```

---

# 2. Podklady reValidácie

```text
postupy/Inicializácia práce.md
postupy/2026-07-21_VALIDACIA.md
postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md
postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md
postupy/2026-07-22_SPOLOCNA-REVALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md
postupy/2026-07-22_APLIKACNY-KONTRAKT-ODVODZOVANIA-OTAZOK.md
postupy/2026-07-22_VALIDACIA-APLIKACNEHO-KONTRAKTU-ODVODZOVANIA-OTAZOK.md
postupy/2026-07-22_09-38_CodeIgniter.md
postupy/README.md
CHANGELOG.md
```

Podkladom je aktuálny obsah vetvy `main` po oprave kontraktu.

---

# 3. Kritériá reValidácie

Používajú sa rovnaké kritériá ako pri pôvodnej Validácii kontraktu:

```text
K1 — kontrakt zachováva vstupný význam ontológie a algoritmu
K2 — každý pokus vzniká pred vstupnými kontrolami
K3 — spoločná vstupná brána je skutočne atómová
K4 — kandidátske vetvy sú po bráne významovo oddelené
K5 — zlyhanie jednej vetvy nevymaže výsledky nezávislých vetiev
K6 — neúspešná vetva sa nesmie uložiť ako QUESTION_CANDIDATE
K7 — výsledok vetvy sa nezamieňa s odpoveďou 0
K8 — nadradený výsledok zachováva výsledky a stav celého behu
K9 — výsledok možno jednoznačne priradiť zdroju požiadavky a konkrétnemu pokusu
K10 — závislosti medzi vetvami sú určiteľné, spätne citovateľné a výsledkovo vyjadrené
K11 — súhrnný run_state je jednoznačne odvodený zo stavov brány a vetiev
K12 — ACTOR zostáva oddelený od REQUEST_SOURCE a AUTHORITY
K13 — metodická transakčná hranica je oddelená od databázovej transakcie
K14 — kontrakt nepredbieha technickú implementáciu
K15 — kontrakt je pripravený na technický návrh služby bez domýšľania významových väzieb
```

---

# 4. Overenie troch povinných opráv

## Oprava 1 — korelácia zdroja a výsledku

```text
1
```

Vstup aj výstup obsahujú:

```text
REQUEST_REFERENCE
RESPONSE_TARGET_REFERENCE
```

Kontrakt určuje úplnú korelačnú cestu:

```text
REQUEST_REFERENCE
→ QUESTION_DERIVATION
→ DERIVATION_RUN_RESULT
→ RESPONSE_TARGET_REFERENCE
```

Zároveň výslovne zachováva:

```text
REQUEST_SOURCE
≠ ACTOR
≠ AUTHORITY
```

## Oprava 2 — významový model vetvovej závislosti

```text
1
```

Kontrakt určuje `BRANCH_DEPENDENCY` s odkazom na závislú vetvu, konkrétny predpoklad, typ závislosti, odôvodnenie, úkon alebo ACTOR-a, ktorý ju určil, validačnú kontrolu a auditnú stopu.

Vetva môže dostať stav `BLOCKED_BY_DEPENDENCY` iba pri existencii citovateľnej závislosti a nesplneného predpokladu. Zlyhanie jednej vetvy nevytvára automaticky závislosť ostatných vetiev.

## Oprava 3 — deterministická agregácia run_state

```text
1
```

Kontrakt určuje prioritu vstupnej brány a následne úplné, vzájomne rozlíšené pravidlá pre všetky kombinácie počtov:

```text
CANDIDATE_CREATED
BRANCH_STOPPED
RETURNED_FOR_DECOMPOSITION
BLOCKED_BY_DEPENDENCY
```

Každý beh dostane práve jeden nadradený `run_state`, pričom jednotlivé vetvové výsledky zostávajú zachované.

---

# 5. Výsledky kritérií

```text
K1  = 1
K2  = 1
K3  = 1
K4  = 1
K5  = 1
K6  = 1
K7  = 1
K8  = 1
K9  = 1
K10 = 1
K11 = 1
K12 = 1
K13 = 1
K14 = 1
K15 = 1
```

## K9

Požiadavku, metodický beh, nadradený výsledok a cieľ návratu možno spojiť bez technického alebo metodického domýšľania.

## K10

Každá vetvová závislosť má určiteľný pôvod, predpoklad, typ, odôvodnenie, kontrolu a auditnú stopu. Technická infraštruktúrna závislosť sa nezamieňa s metodickou závislosťou vetiev.

## K11

Agregačné pravidlo je deterministické a nestráca informáciu o úspešných, zlyhaných, rozkladaných ani zablokovaných vetvách.

## K15

Technický návrh služby už nemusí domýšľať, komu sa vracia výsledok, ako sa určujú závislosti ani ako vzniká nadradený stav. Môže tieto významové pravidlá iba technicky reprezentovať.

---

# 6. Obojstranné zabezpečenie

Smer dovnútra:

```text
neplatný spoločný vstup
→ žiadna kandidátska vetva
```

Izolácia vo vnútri:

```text
zlyhanie nezávislej vetvy
→ nevymaže ostatné výsledky
```

Smer navonok:

```text
každý výsledok
→ patrí konkrétnej požiadavke
→ nesie cieľ návratu
→ zachováva vetvovú topológiu
→ má deterministický nadradený stav
```

Obojstranná hranica je významovo uzavretá.

---

# 7. Výsledok reValidácie

```text
VALIDATION_RESULT
=
VALID
```

Význam výsledku:

```text
aplikačný kontrakt spĺňa všetkých pätnásť určených kritérií
+
tri pôvodné blokujúce väzby boli odstránené
+
topológia odozvy je uzavretá smerom dovnútra, medzi vetvami aj smerom navonok
+
možno odvodiť technický návrh aplikačnej služby
```

`VALID` neznamená všeobecnú pravdu ani potvrdenie budúcej implementácie. Znamená, že aktuálny kontrakt spĺňa uvedené kritériá v určenom kontexte, čase a rozsahu.

---

# 8. Stav dokumentov

Aplikačný kontrakt, pôvodná Validácia aj táto reValidácia zostávajú:

```text
PRACOVNÝ
```

Pôvodná Validácia s výsledkom `CONDITIONALLY_VALID` zostáva zachovaná ako historická udalosť.

---

# 9. Nasledujúci logický krok

```text
odvodiť technický návrh aplikačnej služby
→ určiť technické vstupy, výstupy a zodpovednosti bez zmeny kontraktu
→ následne Validovať návrh služby
→ až potom odvodiť repository, migrácie, controller a API odpoveď
```
