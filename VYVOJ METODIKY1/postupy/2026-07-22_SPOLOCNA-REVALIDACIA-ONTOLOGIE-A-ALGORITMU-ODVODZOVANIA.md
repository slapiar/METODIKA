# Spoločná reValidácia ontológie a algoritmu odvodzovania špecifických otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument je novou historickou validačnou udalosťou nadväzujúcou na:

```text
postupy/2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md
```

Pôvodnú Validáciu neprepisuje. Preskúmava aktuálny spoločný významový celok po vykonaní troch povinných opráv v algoritme.

Validovaným predmetom je:

```text
postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md
+
postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md
```

---

# 1. VALIDATION_EVENT

```text
TARGET:
spoločný významový celok ontológie vstupov a opraveného algoritmu odvodzovania špecifických otázok

ACTOR:
ChatGPT vykonávajúca metodické preskúmanie na výslovný pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa opraviť tri zistené body a vykonať spoločnú reValidáciu
v repozitári slapiar/METODIKA, vetva main;
tento záznam nepredstiera konečnú Autoritu meniť autoritatívne definície METODIKY

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; preskúmanie pripravenosti významového modelu pred odvodením aplikačného kontraktu

SCOPE:
vzájomný súlad ontológie a opraveného algoritmu;
overenie troch pôvodne uložených opráv;
bez Validácie databázovej schémy, PHP tried, používateľského rozhrania a vykonania hodnotenia
```

---

# 2. Podklady reValidácie

```text
postupy/Inicializácia práce.md
POJMY-A-DEFINICIE.md
AUTORITA.md
OTAZKY/README.md
HODNOTENIA/README.md
postupy/2026-07-21_VALIDACIA.md
postupy/2026-07-21_METODICKE-UKONY.md
postupy/2026-07-21_MINIMALNY-LOGICKY-MODEL.md
postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md
postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md
postupy/2026-07-22_SPOLOCNA-VALIDACIA-ONTOLOGIE-A-ALGORITMU-ODVODZOVANIA.md
postupy/README.md
CHANGELOG.md
```

Podkladom je aktuálny obsah vetvy `main` po commite opravy algoritmu.

---

# 3. Kritériá reValidácie

Používajú sa rovnaké kritériá ako pri pôvodnej spoločnej Validácii, aby výsledok nebol získaný dodatočným prispôsobením kritérií:

```text
K1 — spoločný SUBJECT Validácie je jednoznačne vymedzený
K2 — ontológia a algoritmus používajú rovnaké identity, roly a vzťahy
K3 — vstupný kontrakt algoritmu zodpovedá ontológii
K4 — výstupný kontrakt algoritmu zodpovedá ontológii
K5 — poradie algoritmu rešpektuje význam metodického úkonu a jeho históriu
K6 — zastavovacie podmienky sú spätne preskúmateľné a nestrácajú históriu úkonu
K7 — ACTOR je oddelený od AUTHORITY a kontext Autority sa nedomýšľa
K8 — DERIVATION_CONTEXT je oddelený od DERIVATION_SCOPE
K9 — kandidát otázky je oddelený od prijatej otázky a budúceho hodnotenia
K10 — DERIVATION_TRACE je oddelený od EVIDENCE budúceho hodnotenia
K11 — význam odpovedí 1 a 0 zostáva viazaný na elementárnu podmienku
K12 — model nepredbieha technickú implementáciu
K13 — všetky povinné väzby možno spätne doložiť bez domýšľania
K14 — otvorené rozhodnutia sú priznané
K15 — dokumenty sú pripravené na odvodenie aplikačného kontraktu bez straty významu
```

---

# 4. Overenie troch povinných opráv

## Oprava 1 — INTENDED_APPLICABILITY_SCOPE

```text
1
```

Algoritmický výstupný kontrakt aj `QUESTION_CANDIDATE` už obsahujú:

```text
INTENDED_APPLICABILITY_SCOPE
```

Je výslovne odlíšený od `DERIVATION_SCOPE` a neurčuje automaticky konečný rozsah prijatej otázky.

## Oprava 2 — založenie úkonu pred kontrolami

```text
1
```

Pseudokód začína:

```text
record_question_derivation_attempt(raw_input, DERIVATION_ATTEMPT_RECORDED, time)
```

Až potom vykonáva vstupné kontroly. Neúspešný pokus preto nestráca historickú existenciu.

## Oprava 3 — jednotný výsledok každého zastavenia

```text
1
```

Algoritmus zaviedol spoločný mechanizmus:

```text
stop_derivation(...)
```

Každé explicitne uvedené zastavenie vracia:

```text
derivation
candidates
trace
state
stop_reason
failed_control
```

Žiadny uvedený kontrolný bod už nekončí iba tichým `REQUIRE` bez výsledku.

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
K15 = 1 S OBMEDZENÍM
```

## Odôvodnenie obmedzenia K15

Významový celok je pripravený na odvodenie aplikačného kontraktu. Pred samotným vykonaním algoritmu však musí aplikačný kontrakt výslovne určiť transakčnú hranicu jedného behu:

```text
ATOMIC RUN
=
pri zlyhaní ktoréhokoľvek kandidáta sa celý beh ukončí bez prijateľných kandidátov
```

alebo:

```text
PARTIAL RUN
=
už úplne vytvorené kandidáty sa zachovajú a neúspešný kandidát sa zaznamená osobitne
```

Aktuálny pracovný pseudokód funkcie `stop_derivation` vracia pri zastavení prázdny zoznam kandidátov. To zodpovedá atómovému správaniu, ale dokument ho ešte výslovne nevyhlasuje za metodické pravidlo.

Toto obmedzenie:

```text
- nemení identitu vstupov,
- nemení poradie odvodzovania,
- nemení význam kandidáta,
- nebráni odvodiť aplikačný kontrakt,
- musí byť však v aplikačnom kontrakte vyriešené skôr než vznikne implementácia služby.
```

---

# 6. Spoločný výsledok reValidácie

```text
VALIDATION_RESULT
=
VALID_WITH_LIMITATIONS
```

Význam výsledku:

```text
ontológia a algoritmus tvoria konzistentný spoločný významový celok
+
všetky tri pôvodné blokujúce nesúlady boli odstránené
+
možno odvodiť aplikačný kontrakt
+
aplikačný kontrakt musí určiť atómové alebo čiastočné spracovanie jedného behu
```

Výsledok `VALID_WITH_LIMITATIONS` neznamená všeobecnú pravdu ani konečné potvrdenie všetkých budúcich implementačných rozhodnutí. Platí iba pre uvedené kritériá, kontext, čas a rozsah.

---

# 7. Stav dokumentov

Táto reValidácia nemení ontológiu ani algoritmus na autoritatívne dokumenty. Oba zostávajú:

```text
PRACOVNÝ
```

Výsledok však odstraňuje predchádzajúcu blokáciu odvodenia aplikačného kontraktu.

Pôvodná Validácia s výsledkom `CONDITIONALLY_VALID` zostáva zachovaná ako historická udalosť. Nie je prepísaná ani zrušená.

---

# 8. Nasledujúci logický krok

```text
odvodiť aplikačný kontrakt prvého doménového algoritmu
→ v kontrakte výslovne určiť transakčnú hranicu behu
→ až potom navrhnúť službu, controller a technické úložisko v CodeIgniteri
```
