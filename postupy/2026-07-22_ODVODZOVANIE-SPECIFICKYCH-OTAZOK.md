# Odvodzovanie špecifických otázok z univerzálnych otázok

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument vytvára prvý minimálny významový algoritmus, ktorým možno z jednej univerzálnej otázky odvodiť kandidáta špecifickej otázky pre jeden logicky vymedzený `SUBJECT`.

Neurčuje databázovú schému, programovací jazyk, používateľské rozhranie ani autonómne rozhodovanie bez Validácie.

---

# 1. Účel algoritmu

Algoritmus má zabezpečiť, aby špecifická otázka nevznikla iba voľným preformulovaním textu, ale ako spätne preskúmateľné odvodenie:

```text
UNIVERZÁLNA OTÁZKA
+
LOGICKY VYMEDZENÝ SUBJECT
+
ÚČEL PRIESKUMU
+
KONTEXT A ROZSAH
+
DOMÉNOVÉ POJMY
→
KANDIDÁT ŠPECIFICKEJ OTÁZKY
```

Výstupom algoritmu ešte nie je odpoveď, hodnotenie ani pravda. Výstupom je kandidát otázky pripravený na metodickú kontrolu a Validáciu.

---

# 2. Základné rozlíšenia

```text
univerzálna otázka
≠ špecifická otázka
≠ hodnotiaci záznam
≠ odpoveď
```

```text
odvodenie otázky
≠ použitie otázky
```

```text
jazyková správnosť
≠ metodická správnosť
```

```text
kandidát otázky
≠ Validovaná otázka
```

Univerzálna otázka nesie všeobecnú skúmanú podmienku. Špecifická otázka túto podmienku zachováva, ale viaže ju na presne určený predmet, význam, kontext a rozsah.

---

# 3. Minimálne vstupy

Algoritmus sa nesmie spustiť bez týchto vstupov:

## 3.1 Univerzálna otázka

Musí byť určené najmenej:

```text
UNIVERSAL_QUESTION_ID
UNIVERSAL_QUESTION_TEXT
UNIVERSAL_CONDITION
PRIMARY_DIMENSION
PRINCIPLE_X
PRINCIPLE_Y
```

Pri otázkach objektívnej matice predstavujú `PRINCIPLE_X` a `PRINCIPLE_Y` polohu v matici 7 × 7.

## 3.2 SUBJECT

Musí byť logicky zdôvodnené:

```text
čo sa skúma
prečo je to samostatný predmet
kde sú jeho hranice
čím sa odlišuje od iných predmetov
podľa čoho sa určuje jeho totožnosť
```

Ak SUBJECT nie je dostatočne určený, algoritmus sa zastaví.

## 3.3 Účel prieskumu

Musí byť určené, prečo sa otázka odvodzuje, napríklad:

```text
poznanie aktuálneho stavu
odhalenie príčiny problému
porovnanie variantov
Validácia funkcie
posúdenie rizika
príprava rozhodnutia
```

Účel nesmie vopred určovať želanú odpoveď.

## 3.4 Kontext a rozsah

Musí byť možné určiť relevantný:

```text
vecný rozsah
časový rozsah
priestorový rozsah
procesný rozsah
verziu alebo stav
vzťah k iným SUBJECT-om
```

Do textu otázky sa vloží iba tá časť kontextu, bez ktorej by bola skúmaná podmienka nejednoznačná. Ostatný kontext patrí k definícii použitia alebo hodnotiacemu záznamu.

## 3.5 Doménové pojmy

Doménový pojem musí mať v danom projekte určený význam. Neznámy alebo viacznačný pojem sa nesmie automaticky doplniť odhadom.

---

# 4. Výstup algoritmu

Výstup musí obsahovať najmenej:

```text
SPECIFIC_QUESTION_CANDIDATE
SOURCE_UNIVERSAL_QUESTION
SUBJECT
PRIMARY_DIMENSION
SPECIFIC_CONDITION
MEANING_OF_1
MEANING_OF_0
REQUIRED_CONTEXT
DERIVATION_TRACE
VALIDATION_STATE
```

Po vytvorení má výstup stav:

```text
CANDIDATE
```

Až samostatná Validácia môže určiť, či je otázka prijateľná na použitie.

---

# 5. Minimálny odvodzovací algoritmus

## Krok 1 — Načítanie univerzálnej otázky

Načítať presné znenie, skúmanú univerzálnu podmienku, rozmer a polohu v matici.

Kontrola:

```text
Je zdrojová otázka jednoznačne identifikovaná?
```

Ak nie, algoritmus sa zastaví.

## Krok 2 — Prijatie SUBJECT-u

Overiť, že SUBJECT prešiel minimálnym testom určiteľnosti, rozlíšiteľnosti, rozsahu a neutrality výsledku.

Kontrola:

```text
Môže byť odpoveď na budúcu otázku omylom priradená inému predmetu?
```

Ak áno, SUBJECT treba spresniť alebo rozdeliť.

## Krok 3 — Extrakcia univerzálnej podmienky

Zo zdrojovej otázky oddeliť gramatickú formu od významu, ktorý má odpoveď `[1/0]` potvrdiť.

Pracovný zápis:

```text
UNIVERSAL_CONDITION
=
podmienka zachovaná vo všetkých prípustných špecifikáciách otázky
```

Algoritmus nesmie zachovať iba podobné slová a pritom zmeniť skúmanú podmienku.

## Krok 4 — Určenie relevantného prejavu SUBJECT-u

Vyhľadať, ktorá časť, vlastnosť, vzťah, stav, proces alebo udalosť SUBJECT-u zodpovedá univerzálnej podmienke.

Výsledok:

```text
SUBJECT_MANIFESTATION
```

Ak univerzálnej podmienke zodpovedá viac samostatných prejavov, nesmú sa zlúčiť do jednej otázky. Vznikne viac kandidátov.

## Krok 5 — Doménové dosadenie

Nahradiť všeobecné výrazy konkrétnymi doménovými pojmami bez zmeny logického významu.

```text
skúmaný jav
→ konkrétny SUBJECT alebo jeho presne určený prejav
```

```text
pôvod, zámer, zmena, hranica, cyklus, príčina alebo výsledok
→ doménovo určený prejav tej istej významovej triedy
```

Dosadenie nesmie vytvoriť nový predpoklad, ktorý univerzálna otázka ani definícia SUBJECT-u neobsahujú.

## Krok 6 — Určenie jednej špecifickej podmienky

Zostaviť jednu podmienku, ktorej potvrdenie alebo nepotvrdenie možno jednoznačne rozlíšiť.

```text
SPECIFIC_CONDITION
```

Ak veta obsahuje dve podmienky, ktoré môžu mať rozdielne odpovede, musí sa rozdeliť.

## Krok 7 — Kontrola rozmeru

Overiť, čo sa mení medzi odpoveďou `1` a `0`.

```text
X — predmet, existencia, identita alebo hranica
Y — spôsob, stav, mechanizmus alebo priebeh
Z — hodnota, miera, primeranosť alebo význam
T — čas, trvanie, poradie, platnosť alebo priorita
```

Špecifikácia nesmie zmeniť primárny rozmer bez toho, aby vznikla nová otázka s novým odôvodnením.

## Krok 8 — Zostavenie vety otázky

Vytvoriť jazykovo prirodzenú vetu, ktorá:

```text
skúma jednu podmienku
vzťahuje sa na určený SUBJECT
zachováva univerzálnu podmienku
nepredurčuje výsledok
umožňuje odpoveď [1/0]
```

Gramatický tvar nie je rozhodujúci. Rozhodujúci je význam odpovede.

## Krok 9 — Určenie významu odpovede 1

Význam `1` musí presne pomenovať, čo je potvrdené:

```text
MEANING_OF_1
=
špecifická podmienka je v určenom význame, rozsahu a kontexte potvrdená
```

## Krok 10 — Určenie významu odpovede 0

Význam `0` musí presne pomenovať, čo potvrdené nie je.

Nesmie automaticky znamenať absolútnu neexistenciu SUBJECT-u.

```text
MEANING_OF_0
=
špecifická podmienka v určenom význame nebola potvrdená
```

Podľa kontextu môže byť potrebné odlíšiť:

```text
podmienka neplatí
prejav neexistuje
chýba dôkaz
predmet je nepresne určený
otázka sa musí ďalej rozložiť
```

Tieto stavy sa nesmú bez ďalšej metodiky zlievať do jedného tvrdenia o realite.

## Krok 11 — Kontrola elementárnosti

Položiť kontrolnú otázku:

```text
Môže byť niektorá časť vety pravdivá a iná nepravdivá?
```

Ak áno, kandidát nie je elementárny a musí sa rozdeliť.

## Krok 12 — Kontrola spätnej odvoditeľnosti

Musí byť možné bez domýšľania odpovedať:

```text
Z ktorej univerzálnej otázky kandidát vznikol?
Ktorú univerzálnu podmienku zachoval?
Ktorý SUBJECT a prejav konkretizoval?
Ktoré doménové pojmy boli dosadené?
Prečo zostal v rovnakom rozmere?
```

Ak niektorý krok nemožno doložiť, kandidát sa odmietne alebo vráti na revíziu.

## Krok 13 — Kontrola neutrality

Otázka nesmie obsahovať hodnotiace alebo presviedčacie výrazy, ktoré vopred určujú odpoveď.

Neprípustné:

```text
Funguje už správne navrhnuté načítanie TEMP?
```

Prípustné:

```text
Spustí kliknutie na mapu požiadavku na načítanie TEMP pre zvolené súradnice?
```

## Krok 14 — Vytvorenie kandidáta a záznamu odvodenia

Ak všetky kontroly prešli, vytvoriť:

```text
QUESTION_CANDIDATE
DERIVATION_TRACE
```

Kandidát nesmie byť automaticky zaradený medzi platné projektové otázky bez samostatnej Validácie.

---

# 6. Zastavovacie podmienky

Algoritmus sa musí zastaviť, ak:

```text
nie je určená zdrojová univerzálna otázka
SUBJECT nie je logicky vymedzený
nie je známy účel prieskumu
nie je možné určiť jednu podmienku
špecifikácia mení rozmer bez odôvodnenia
použitý doménový pojem je nejednoznačný
význam 1 alebo 0 nemožno jednoznačne určiť
otázka predurčuje želaný výsledok
odvodenie nemožno spätne doložiť
```

Zastavenie nie je chybou systému. Je výsledkom, že vstupy zatiaľ nestačia na bezpečné odvodenie otázky.

---

# 7. Pracovný pseudokód

```text
FUNCTION derive_specific_question(
    universal_question,
    subject,
    survey_purpose,
    context,
    domain_terms
):

    REQUIRE identified(universal_question)
    REQUIRE justified(subject)
    REQUIRE neutral(survey_purpose)

    universal_condition = extract_condition(universal_question)
    manifestation_set = map_condition_to_subject(
        universal_condition,
        subject,
        context
    )

    IF manifestation_set is empty:
        STOP NO_RELEVANT_MANIFESTATION

    candidates = []

    FOR manifestation IN manifestation_set:
        specific_condition = specialize(
            universal_condition,
            manifestation,
            domain_terms,
            context
        )

        IF contains_multiple_conditions(specific_condition):
            specific_conditions = decompose(specific_condition)
        ELSE:
            specific_conditions = [specific_condition]

        FOR condition IN specific_conditions:
            candidate = formulate_question(condition, subject, context)

            REQUIRE same_primary_dimension(
                universal_question,
                candidate
            )
            REQUIRE elementary(candidate)
            REQUIRE result_neutral(candidate)
            REQUIRE meaning_of_1_defined(candidate)
            REQUIRE meaning_of_0_defined(candidate)
            REQUIRE derivation_traceable(candidate)

            candidates.append(
                QUESTION_CANDIDATE(
                    question = candidate,
                    source = universal_question,
                    subject = subject,
                    condition = condition,
                    meaning_1 = define_1(condition),
                    meaning_0 = define_0(condition),
                    trace = record_derivation(),
                    state = CANDIDATE
                )
            )

    RETURN candidates
```

---

# 8. Prvý skúšobný príklad

## Zdrojová univerzálna otázka

```text
MM:
Má skúmaný jav určiteľný pôvod, myšlienku, zámer alebo dôvod svojej existencie?
```

## SUBJECT

```text
proces automatického načítania TEMP
po kliknutí na mapu
v určenej verzii TermikaXC
```

## Účel prieskumu

```text
zistiť, či technický proces vzniká z určiteľného spúšťacieho podnetu
```

## Zachovaná univerzálna podmienka

```text
prejav má určiteľný pôvod alebo dôvod vzniku
```

## Relevantný prejav SUBJECT-u

```text
vznik požiadavky na načítanie TEMP
```

## Doménové dosadenie

```text
pôvod prejavu
→ kliknutie na mapu so zvolenými súradnicami

konkrétny prejav
→ požiadavka na načítanie TEMP
```

## Kandidát špecifickej otázky

```text
Spustí kliknutie na mapu požiadavku na načítanie TEMP pre zvolené súradnice?
```

## Význam odpovedí

```text
1 = po kliknutí na mapu vznikla pre zvolené súradnice preukázateľná požiadavka na načítanie TEMP

0 = vznik takejto požiadavky po kliknutí na mapu nebol v určenom kontexte potvrdený
```

## Poznámka k príkladu

Tento kandidát skúma bezprostredný spúšťací pôvod technického prejavu. Nehodnotí ešte:

```text
či server odpovedal
či boli údaje správne
či sa TEMP zobrazil
či bol výsledok použiteľný
```

Tieto podmienky musia byť odvodené ako samostatné otázky.

---

# 9. Minimálna Validácia kandidáta otázky

Pred prijatím špecifickej otázky sa musí overiť najmenej:

```text
1. Je zdrojová univerzálna otázka jednoznačne určená?
2. Je SUBJECT dostatočne vymedzený?
3. Zachovala sa univerzálna podmienka?
4. Skúma otázka iba jednu podmienku?
5. Zostal zachovaný správny rozmer?
6. Je význam odpovede 1 jednoznačný?
7. Je význam odpovede 0 jednoznačný?
8. Je otázka neutrálna voči výsledku?
9. Sú doménové pojmy určené bez domýšľania?
10. Je celý postup odvodenia spätne citovateľný?
```

Výsledok Validácie môže byť:

```text
ACCEPTED_FOR_USE
RETURNED_FOR_REVISION
REJECTED
NOT_VERIFIABLE
```

Tieto označenia sú pracovné a zatiaľ netvoria potvrdený číselník METODIKY.

---

# 10. Hranica prvého algoritmu

Tento algoritmus zatiaľ nerieši:

```text
automatický výber najrelevantnejších univerzálnych otázok
úplnosť celého prieskumu SUBJECT-u
poradie odvodených otázok
odvodzovanie otázok Z a T do zloženého vzťahu S
dedukciu odpovedí
výber dôkazov
vykonanie hodnotenia
Autoritu Validácie
technickú implementáciu
```

Najbližším logickým krokom je určiť algoritmus, ktorý pre konkrétny SUBJECT a účel prieskumu vyberie relevantné univerzálne otázky bez toho, aby ostatné svojvoľne vyradil.
