# CodeIgniter 4.7.4 ako technické prostredie METODIKY

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument je aktívnym technickým základom pre návrhy uložené v `TECHNICKE-NAVRHY/`.

Nahrádza aktívne používanie historického pracovného dokumentu:

```text
postupy/2026-07-22_09-38_CodeIgniter.md
```

Pôvodný dokument zostáva zachovaný ako historický záznam. Tento dokument nepreberá ani nemení ontologické definície METODIKY.

---

# 1. Základné rozlíšenie

```text
METODIKA
= významová a rozhodovacia doména

CodeIgniter 4.7.4
= technické prostredie vykonania
```

Framework nesmie určovať význam SUBJECT-ov, otázok, hodnotení, dôkazov, Validácie, Autority ani metodických úkonov.

---

# 2. Architektonický tvar

Projekt sa vedie ako modulárny monolit:

```text
HTTP alebo CLI vstup
→ technický adaptér
→ aplikačná služba
→ doménový algoritmus
→ technické porty
→ infraštruktúrne adaptéry
→ výsledok aplikačnej operácie
```

Vrstvy:

```text
Controllers / Commands
= prijatie technickej požiadavky a odovzdanie službe

Application
= koordinácia jedného aplikačného prípadu použitia

Domain
= významové objekty a doménové algoritmy

Infrastructure
= databáza, logovanie, čas, generovanie referencií a externé rozhrania

Views / API Responses
= technická prezentácia výsledku
```

---

# 3. Hranice zodpovedností

Controller alebo CLI príkaz smie:

```text
načítať technický vstup
vykonať syntaktickú kontrolu
zostaviť aplikačný vstup
zavolať aplikačnú službu
preložiť výsledok do technickej odpovede
```

Nesmie:

```text
vykonávať doménový algoritmus
určovať Autoritu
vykonávať metodickú Validáciu
meniť význam odpovedí 1 a 0
skladať run_state mimo Validovaného kontraktu
```

Aplikačná služba koordinuje proces, ale nevytvára nové doménové pravidlá.

---

# 4. Umiestnenie vlastného kódu

```text
codei/app/
= vlastný kód METODIKY

codei/system/
= neupravovať

codei/vendor/
= neupravovať ručne
```

Odporúčané technické členenie:

```text
codei/app/
├── Application/
├── Domain/
├── Infrastructure/
├── Controllers/
├── Commands/
├── Config/
└── Views/
```

Samostatný modul môže vzniknúť až po potvrdení jeho významovej a zodpovednostnej hranice.

---

# 5. Technické transakcie

```text
metodická transakčná hranica
≠ databázová transakcia
```

Databázové transakcie majú byť krátke a viazané na konzistentný technický zápis. Nesmú rollbackom zrušiť historicky zachované výsledky nezávislých metodických vetiev, ak to Validovaný kontrakt nepovoľuje.

---

# 6. Chybové kanály

Treba zachovať:

```text
metodické zastavenie
≠ technická chyba infraštruktúry
≠ programátorská chyba invariantu
≠ odpoveď 0 na otázku
```

Metodické zastavenie sa vracia ako riadny aplikačný výsledok. Technická chyba sa nesmie prekladať na metodickú odpoveď ani na doménový stav bez pravidla v kontrakte.

---

# 7. Testovateľnosť

Aplikačné služby a doménové algoritmy musia byť testovateľné bez HTTP vrstvy a bez prehliadača.

```text
unit test
→ algoritmus, agregácia alebo dátový objekt

integration test
→ služba, porty a infraštruktúrne adaptéry

feature test
→ route alebo CLI, controller, služba a technická odpoveď
```

Rovnaký aplikačný prípad použitia musí byť dostupný z webu, API, CLI, testu alebo budúceho AI agenta bez paralelnej implementácie doménovej logiky.

---

# 8. Záverečné pravidlo

> CodeIgniter má technicky prenášať a vykonávať už odvodený a Validovaný význam. Nesmie ho potichu zjednodušiť, nahradiť ani spätne predefinovať.