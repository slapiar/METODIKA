# Spoločná Validácia ontológie a algoritmu odvodzovania špecifických otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument zachytáva spoločnú Validáciu dvoch pracovných dokumentov ako jedného významového celku:

```text
postupy/2026-07-22_ONTOLOGIA-VSTUPOV-ODVODZOVANIA-OTAZOK.md
+
postupy/2026-07-22_ODVODZOVANIE-SPECIFICKYCH-OTAZOK.md
```

Validácia nie je potvrdením pravdy ani konečným prijatím dokumentov. Posudzuje ich podľa vopred určených kritérií, v aktuálnom stave vetvy `main`, v rozsahu prípravy prvého doménového algoritmu odvodzovania otázok.

---

# 1. VALIDATION_EVENT

```text
TARGET:
spoločný významový celok ontológie vstupov a algoritmu odvodzovania špecifických otázok

ACTOR:
ChatGPT vykonávajúca metodické preskúmanie na pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa vykonať spoločnú Validáciu v repozitári slapiar/METODIKA, vetva main;
tento záznam nepredstiera všeobecnú ani konečnú Autoritu meniť autoritatívne definície METODIKY

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; príprava prvého významového algoritmu pred návrhom aplikačného kontraktu v CodeIgniter 4.7.4

SCOPE:
iba vzájomný súlad ontológie a algoritmu, ich súlad s aktuálnymi autoritatívnymi definíciami a pripravenosť na ďalší metodický krok;
bez Validácie databázovej schémy, PHP tried, používateľského rozhrania, úplnej Autority prijatia kandidáta a vykonania hodnotenia
```

---

# 2. Podklady Validácie

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
postupy/README.md
CHANGELOG.md
```

Podkladom je aktuálny obsah uvedených dokumentov na vetve `main`. Táto Validácia nepoužíva predpokladanú budúcu implementáciu ako dôkaz správnosti významového modelu.

---

# 3. Kritériá Validácie

Kritériá boli určené pred výsledkom:

```text
K1 — spoločný SUBJECT Validácie je jednoznačne vymedzený
K2 — ontológia a algoritmus používajú rovnaké základné identity, roly a vzťahy
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
K14 — otvorené rozhodnutia sú priznané a neprezentujú sa ako potvrdené definície
K15 — dokumenty sú pripravené na odvodenie aplikačného kontraktu bez straty významu
```

---

# 4. Výsledky jednotlivých kritérií

## K1 — spoločný SUBJECT Validácie

```text
1
```

Validovaným predmetom nie sú dva izolované texty, ale jeden významový celok:

```text
ontológia určuje, čo vstupuje do odvodzovania a aké vzťahy musia trvať
+
algoritmus určuje, v akom poradí a za akých kontrol sa tieto vzťahy použijú
```

## K2 — spoločné identity, roly a vzťahy

```text
1
```

Oba dokumenty zhodne rozlišujú najmenej:

```text
QUESTION
SOURCE_QUESTION_REFERENCE
SUBJECT
DERIVATION_SUBJECT_REFERENCE
ACTOR
AUTHORITY_CONTEXT
QUESTION_DERIVATION
QUESTION_CANDIDATE
DERIVATION_TRACE
EVALUATION_SUBJECT
QUESTION_DERIVATION_RECORD
```

## K3 — vstupný kontrakt

```text
1
```

Vstupný kontrakt algoritmu zodpovedá ontológii:

```text
source_question
derivation_subject
purpose
context
scope
domain_terms
actor
authority_context
```

## K4 — výstupný kontrakt

```text
0
```

Ontológia vyžaduje, aby kandidát obsahoval alebo jednoznačne odkazoval aj na:

```text
intended applicability scope
```

Algoritmický výstupný kontrakt túto položku neobsahuje. Uvádza `DERIVATION_SUBJECT_REFERENCE`, `REQUIRED_QUESTION_CONTEXT` a ostatné povinné časti, ale neurčuje zamýšľaný rozsah opakovateľnej použiteľnosti kandidáta.

Tento nedostatok nemožno nahradiť samotným `DERIVATION_SCOPE`, pretože:

```text
DERIVATION_SCOPE
=
hranice konkrétneho odvodzovacieho úkonu
```

zatiaľ čo:

```text
INTENDED_APPLICABILITY_SCOPE
=
predbežný rozsah predmetov a situácií, pre ktoré môže byť kandidát navrhnutý na prijatie
```

Tieto rozsahy môžu byť totožné, ale nie je to automatické.

## K5 — poradie algoritmu a historický úkon

```text
0
```

Text algoritmu správne uvádza ako prvý krok založenie `QUESTION_DERIVATION`. Pracovný pseudokód však najprv vykoná sériu `REQUIRE` kontrol a až potom volá:

```text
begin_question_derivation(...)
```

Pri zlyhaní vstupnej kontroly by preto nevznikol historicky zachytiteľný záznam zastaveného pokusu. To je v napätí s pravidlom:

```text
QUESTION_DERIVATION
=
historicky zachytiteľný metodický úkon
```

Bezpečné poradie musí byť:

```text
prijať surový vstup
→ založiť záznam pokusu o QUESTION_DERIVATION
→ vykonať vstupné kontroly
→ pokračovať alebo ukončiť úkon so stop_reason
```

Založenie záznamu pokusu nesmie znamenať, že vstupy boli prijaté ako platné.

## K6 — zastavenie a história

```text
0
```

Zastavovacie dôvody sú pomenované a významovo užitočné. Pseudokód však explicitne vytvára `DERIVATION_RESULT` iba pre `NO_RELEVANT_MANIFESTATION`. Ostatné zlyhania sú vyjadrené iba príkazom `REQUIRE` bez jednotného výsledku, auditnej stopy a stavu ukončeného úkonu.

Každé zastavenie musí vytvoriť preskúmateľný výsledok najmenej s:

```text
derivation
state
stop_reason
trace
failed_control
```

## K7 — ACTOR a AUTHORITY

```text
1
```

Oba dokumenty zachovávajú:

```text
ACTOR ≠ AUTHORITY
vykonanie úkonu ≠ oprávnenosť úkonu
```

Autorita sa nemusí domyslieť ako potvrdená; musí sa však zachytiť jej kontext a stav Validácie.

## K8 — CONTEXT a SCOPE

```text
1
```

Dokumenty zhodne rozlišujú:

```text
DERIVATION_CONTEXT
=
okolnosti a významové vzťahy
```

```text
DERIVATION_SCOPE
=
hranice zahrnutého a vylúčeného predmetu odvodzovania
```

## K9 — kandidát, prijatá otázka a hodnotenie

```text
1
```

Zachované je:

```text
QUESTION_CANDIDATE
≠ ACCEPTED QUESTION
≠ EVALUATION
```

Rovnako je odlíšený `DERIVATION_SUBJECT` od budúceho `EVALUATION_SUBJECT-u`.

## K10 — DERIVATION_TRACE a EVIDENCE

```text
1
```

Oba dokumenty správne určujú:

```text
DERIVATION_TRACE
=
provenienčný a auditný záznam pôvodu kandidáta
```

```text
DERIVATION_TRACE
≠ EVIDENCE budúcej odpovede
```

## K11 — význam odpovedí 1 a 0

```text
1
```

Význam odpovedí zostáva viazaný na jednu špecifickú podmienku. Hodnota `0` nie je automaticky tvrdením absolútnej neexistencie SUBJECT-u a vyžaduje významové rozlíšenie.

## K12 — nepredbiehanie implementácie

```text
1
```

Dokumenty neurčujú SQL schému, databázové tabuľky, PHP triedy ani používateľské rozhranie. Technická reprezentácia zostáva dôsledkom významového modelu.

## K13 — spätná doložiteľnosť

```text
1 S OBMEDZENÍM
```

Povinné väzby sú pomenované. Úplná doložiteľnosť však závisí od opravy K5 a K6, aby aj neúspešné pokusy vytvárali historický záznam a auditnú stopu.

## K14 — otvorené rozhodnutia

```text
1
```

Dokumenty výslovne priznávajú otvorené otázky, najmä:

```text
definitívna identita DOMAIN_TERM
Autorita prijatia kandidáta
úplná Validácia kandidáta
rozsah opakovateľnej použiteľnosti prijatej otázky
technická implementácia
```

## K15 — pripravenosť na aplikačný kontrakt

```text
0
```

Významové jadro je dostatočne rozvinuté, ale aplikačný kontrakt ešte nesmie byť odvodený, kým sa neopravia K4, K5 a K6.

---

# 5. Spoločný výsledok Validácie

```text
VALIDATION_RESULT
=
CONDITIONALLY_VALID
```

Význam výsledku:

```text
ontológia a algoritmus tvoria spoločný, prevažne konzistentný významový celok
+
základné identity, roly, vstupy a hranice sú použiteľné
+
tri zistené nesúlady bránia odvodiť bezpečný aplikačný kontrakt
```

Výsledok neznamená, že dokumenty sú neplatné alebo že sa musia vytvoriť odznova. Znamená:

```text
jadro sa zachová
→ vykonajú sa tri presné opravy
→ vznikne nová spoločná reValidácia
```

---

# 6. Povinné opravy pred reValidáciou

## Oprava 1 — rozsah použiteľnosti kandidáta

Doplniť do algoritmického výstupného kontraktu a tvorby kandidáta:

```text
INTENDED_APPLICABILITY_SCOPE
```

Musí byť odlíšený od `DERIVATION_SCOPE` a nesmie automaticky určovať konečný rozsah prijatej otázky.

## Oprava 2 — založenie úkonu pred kontrolami

Upraviť pseudokód tak, aby najprv vznikol historický záznam pokusu o odvodzovací úkon a až potom sa Validovali vstupy.

```text
record derivation attempt
→ validate input
→ continue alebo stop
```

## Oprava 3 — jednotný výsledok každého zastavenia

Každá zastavovacia podmienka musí viesť k jednotnému `DERIVATION_RESULT`, ktorý zachová:

```text
derivation
state
stop_reason
trace
failed_control
```

`REQUIRE` nesmie ukončiť proces bez auditnej stopy.

---

# 7. Čo sa touto Validáciou nemení

Táto Validácia nemení stav ontológie ani algoritmu na autoritatívny alebo potvrdený dokument. Oba zostávajú:

```text
PRACOVNÝ
```

Nemení autoritatívne definície v `POJMY-A-DEFINICIE.md`, `AUTORITA.md`, `OTAZKY/README.md` ani `HODNOTENIA/README.md`.

Nevytvára databázovú ani softvérovú architektúru.

---

# 8. Nasledujúci logický krok

```text
opraviť algoritmus podľa troch povinných opráv
→ spätne načítať výsledok
→ vykonať spoločnú reValidáciu ontológie a algoritmu
→ až pri výsledku VALID alebo VALID_WITH_LIMITATIONS odvodiť aplikačný kontrakt
```
