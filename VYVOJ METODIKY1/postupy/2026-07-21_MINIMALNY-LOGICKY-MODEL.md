# Minimálny logický model METODIKY

## Stav dokumentu

Tento dokument odvodzuje minimálne významové identity a vzťahové udalosti z už potvrdenej elementárnej logiky otázky, odpovede, hodnotenia, dôkazu, Validácie, Autority a siedmej plochy S.

Neurčuje ešte názvy SQL tabuliek, dátové typy, cudzie kľúče ani fyzickú databázovú schému.

---

# Odvodzovacie pravidlo

Samostatná identita je taký významový objekt, ktorý možno rozpoznať, odlíšiť a znovu použiť aj bez existencie jedného konkrétneho hodnotiaceho záznamu.

Vzťahová udalosť je taký významový objekt, ktorý vzniká iba konkrétnym spojením iných identít v určenom čase, význame alebo procese.

Vlastnosť alebo výsledok nemá samostatnú metodickú existenciu, ak jeho význam vzniká iba ako súčasť konkrétnej identity alebo udalosti.

Pracovný test:

```text
Môže byť tento objekt jednoznačne určený,
znovu použitý alebo citovaný,
aj keď konkrétne hodnotenie ešte nevzniklo?
```

Ak áno, je kandidátom samostatnej identity.

Ak nie a vzniká až použitím alebo spojením iných identít, ide o vzťahovú udalosť alebo jej vlastnosť.

---

# Samostatné identity

## 1. Otázka

Otázka je opakovateľný logický nástroj. Môže byť definovaná, kontrolovaná, opravovaná a používaná skôr, než sa položí na konkrétny predmet.

```text
QUESTION
```

Otázka preto potrebuje vlastnú významovú identitu.

Jej odpoveď nie je jej vlastnosťou. Tá vzniká až pri použití otázky.

## 2. Predmet

Predmet môže existovať alebo byť evidovaný nezávisle od konkrétnej otázky, ktorá sa naň neskôr použije.

```text
SUBJECT
```

Predmetom môže byť vec, jav, osoba, agent, dokument, proces, vzťah, rozhodnutie, úloha alebo iný jednoznačne určený prejav.

Predmet nie je iba text vložený do hodnotenia. Rovnaký predmet môže vstupovať do mnohých hodnotení a vzťahov.

## 3. Dôkaz

Dôkaz môže vzniknúť pred hodnotením a môže podporovať viac hodnotení.

```text
EVIDENCE
```

Dokument, meranie, pozorovanie, údaj, záznam systému alebo výpočtový výsledok sa nestáva novým dôkazom iba preto, že bol použitý pri ďalšej otázke.

Preto dôkaz potrebuje vlastnú identitu oddelenú od spôsobu jeho použitia.

Samotná existencia dôkazu však ešte neurčuje:

```text
- ktorú odpoveď podporuje,
- či ju podporuje alebo vyvracia,
- v akom rozsahu je relevantný,
- či je dostatočný,
- kto jeho použitie Validoval.
```

Tieto významy patria väzbe dôkazu na konkrétne hodnotenie.

## 4. Autorita

Autorita Validácie musí byť rozpoznateľná a opakovateľne použiteľná v mnohých Validáciách.

```text
AUTHORITY
```

Autoritou môže byť človek, rola, orgán, organizácia, algoritmus, systém alebo iný agent, ak je určený pôvod jeho Oprávnenia, Povinnosti, Zodpovednosť a rozsah Validácie.

Autorita nie je totožná s jednou Validáciou. Existuje ako oprávnený účastník, ktorý môže vykonať viac validačných udalostí.

---

# Vzťahové udalosti

## 5. Hodnotenie

Hodnotenie vzniká až použitím jednej otázky na jeden predmet v určenom význame a čase.

```text
EVALUATION = použitie(QUESTION, SUBJECT)
```

Preto hodnotenie nemôže existovať bez:

```text
QUESTION
SUBJECT
```

Nie je iba technickou spojovacou tabuľkou. Je samostatne identifikovateľnou vzťahovou udalosťou, pretože nesie vlastný výsledok, čas, stav poznania, dôkazy a následné Validácie.

Rovnaká otázka použitá na rovnaký predmet môže vytvoriť viac hodnotení, ak sa zmení napríklad:

```text
- čas, ku ktorému sa otázka vzťahuje,
- čas zistenia,
- relevantný rozsah,
- význam použitia,
- dostupný dôkaz,
- stav predmetu.
```

Preto dvojica `(QUESTION, SUBJECT)` sama osebe nemusí byť identitou hodnotenia.

## 6. Väzba dôkazu na hodnotenie

Dôkaz a hodnotenie majú vzťah M:N:

```text
EVALUATION ↔ EVIDENCE
```

Jeden dôkaz môže byť použitý vo viacerých hodnoteniach a jedno hodnotenie môže používať viac dôkazov.

Samotná väzba musí vedieť významovo rozlíšiť najmenej:

```text
- úlohu dôkazu v hodnotení,
- rozsah jeho použitia,
- či odpoveď podporuje, spochybňuje alebo vyvracia,
- prípadne mieru jeho dostatočnosti.
```

Presný číselník týchto významov zatiaľ nie je určený.

## 7. Validácia

Validácia je udalosť, nie trvalý atribút hodnotenia.

```text
VALIDATION = overenie(EVALUATION, AUTHORITY)
```

Nemôže existovať bez konkrétneho hodnotenia a bez určenej Autority.

Jedno hodnotenie môže mať viac Validácií v čase. Validácia môže byť potvrdená, odmietnutá, nahradená alebo zrušená ďalšou validačnou udalosťou bez prepísania histórie.

Preto aktuálny validačný stav nesmie nahradiť záznam validačných udalostí.

## 8. Zložené hodnotenie S

Zložené hodnotenie vzniká použitím vopred určeného logického pravidla S na dve samostatné hodnotenia rozmerov Z a T.

```text
COMPOSITE_EVALUATION_S = S(EVALUATION_Z, EVALUATION_T)
```

Musí odkazovať na konkrétne vstupné hodnotenia, nie iba na všeobecné otázky alebo ich aktuálne odpovede.

Dôvod:

```text
rovnaká otázka Z môže mať viac hodnotení,
rovnaká otázka T môže mať viac hodnotení,
a S musí byť spätne vypočítateľné z presne tých vstupov,
ktoré boli pri rozhodnutí použité.
```

Zložené hodnotenie S je vzťahová udalosť s vlastným výsledkom `s[1/0]`.

---

# Vlastnosti a výsledky, nie samostatné identity

## Odpoveď

Odpoveď `[1/0]` vzniká iba ako výsledok konkrétneho hodnotenia.

```text
answer ∈ EVALUATION
```

Samostatná hodnota `1` alebo `0` bez otázky, predmetu, významu a času nemá metodický význam.

Preto odpoveď sama osebe nie je samostatnou identitou.

## Stav „nezistené“

`Nezistené` je stav poznania alebo spracovania hodnotenia, nie odpoveď a nie samostatná pravdivostná entita.

```text
knowledge_state ∈ EVALUATION
```

## Význam odpovede

Význam hodnoty `0` alebo `1` musí byť určený v kontexte otázky a hodnotenia. Zatiaľ nie je potvrdené, či pôjde o vlastnosť hodnotenia, väzbu na číselník alebo odvodený význam.

Preto sa ešte nesmie predčasne modelovať ako samostatná tabuľka.

## Časová platnosť

Časové údaje patria ku konkrétnemu hodnoteniu, Validácii alebo zloženému hodnoteniu podľa toho, ktorú udalosť čas opisuje.

Samotný čas nie je v tomto jadre samostatnou identitou. To nevylučuje neskorší spoločný model časových intervalov, ak ho bude vyžadovať opakované použitie alebo výpočty.

## Operátor S

Operátor je súčasťou definície zloženého pravidla. Zatiaľ nie je rozhodnuté, či jednotlivé pravidlá S budú opakovateľnými samostatnými definíciami alebo budú uložené priamo v type zloženého hodnotenia.

Samotný symbol `∧`, `∨`, `→` alebo `⊕` ešte nie je dostatočnou identitou pravidla, pretože význam zahŕňa aj poradie argumentov a interpretáciu výsledku.

---

# Minimálne významové jadro

Z potvrdenej metodiky vyplýva toto jadro:

```text
SAMOSTATNÉ IDENTITY

QUESTION
SUBJECT
EVIDENCE
AUTHORITY

VZŤAHOVÉ UDALOSTI

EVALUATION
EVALUATION_EVIDENCE
VALIDATION
COMPOSITE_EVALUATION_S

VLASTNOSTI ALEBO VÝSLEDKY

answer [1/0]
knowledge_state
answer_meaning
time validity
validation result
s [1/0]
```

Vzťahový obraz:

```text
QUESTION ─────┐
              ├── EVALUATION ──↔── EVIDENCE
SUBJECT ──────┘        │
                       └── VALIDATION ─── AUTHORITY

EVALUATION_Z ──┐
               ├── COMPOSITE_EVALUATION_S
EVALUATION_T ──┘
```

---

# Kardinality potvrdené významom

```text
QUESTION 1 : N EVALUATION
SUBJECT  1 : N EVALUATION

EVALUATION M : N EVIDENCE

EVALUATION 1 : N VALIDATION
AUTHORITY  1 : N VALIDATION

EVALUATION_Z 1 : N COMPOSITE_EVALUATION_S
EVALUATION_T 1 : N COMPOSITE_EVALUATION_S
```

Posledné dve kardinality vyjadrujú, že jedno vstupné hodnotenie môže byť použité vo viacerých zložených rozhodnutiach. Každé jedno zložené hodnotenie S však musí mať presne určený vstup Z a presne určený vstup T.

---

# Čo model zatiaľ zámerne neurčuje

Tento model ešte neurčuje:

```text
- fyzické názvy tabuliek,
- primárne a cudzie kľúče,
- typ identifikátorov,
- verziovanie otázok,
- typológiu predmetov,
- formát dôkazov,
- číselník výsledkov Validácie,
- číselník významov odpovede 0,
- reprezentáciu stavu „nezistené“,
- presnú reprezentáciu časov a intervalov,
- technické uloženie pravidiel S,
- dedenie alebo skladanie predmetov,
- oprávnenia používateľov aplikácie.
```

Tieto rozhodnutia sa musia odvodiť v ďalších krokoch. Nesmú sa doplniť iba preto, že ich bežne obsahujú databázové schémy.

---

# Pracovný záver

```text
Otázka, predmet, dôkaz a Autorita
sú opakovateľné samostatné identity.

Hodnotenie, použitie dôkazu, Validácia
a zložené hodnotenie S
sú historicky zachytiteľné vzťahové udalosti.

Odpoveď [1/0] a výsledok s[1/0]
sú výsledky konkrétnych udalostí,
nie samostatné metodické identity.
```

Toto je prvý minimálny logický model, ktorý možno použiť ako podklad ďalšieho skúmania. Ešte nie je SQL schémou.
