# Validácia bezpečnej databázovej konfigurácie

## Stav dokumentu

```text
PRACOVNÝ
```

Validovaný predmet:

```text
codei/app/Config/Database.php
+
codei/.env.example
+
codei/.gitignore
+
TECHNICKE-NAVRHY/2026-07-22_BEZPECNA-DATABAZOVA-KONFIGURACIA.md
```

Táto Validácia neposudzuje vykonanie rotácie hesla na hostingu ani úplné odstránenie tajomstva z histórie Git.

---

# 1. VALIDATION_EVENT

```text
TARGET:
aktuálna vetva a konfiguračný kontrakt databázových tajomstiev

ACTOR:
ChatGPT vykonávajúca technicko-bezpečnostné preskúmanie na výslovný pokyn používateľa

AUTHORITY_CONTEXT:
výslovný pokyn používateľa pokračovať podľa Inicializácie práce;
rotáciu externého databázového účtu môže vykonať iba oprávnený správca hostingu alebo databázy

TIME:
2026-07-22

CONTEXT:
projekt METODIKA; odstránenie commitnutých databázových prihlasovacích údajov pred praktickým overovaním servera

SCOPE:
aktuálny obsah vetvy main, konfiguračný tvar CodeIgnitera, lokálny `.env`, verejná šablóna a testovacia databázová skupina
```

---

# 2. Kritériá

```text
K1 — sledovaný Database.php neobsahuje produkčné používateľské meno
K2 — sledovaný Database.php neobsahuje produkčné heslo
K3 — sledovaný Database.php neobsahuje produkčný názov databázy
K4 — skutočné hodnoty možno dodať cez CodeIgniter premenné prostredia
K5 — codei/.env je ignorovaný Gitom
K6 — codei/.env.example je sledovateľná šablóna bez tajomstva
K7 — testovacia skupina nepoužíva produkčné prihlasovacie údaje
K8 — testovacia skupina používa oddelenú SQLite :memory: databázu
K9 — DBDebug má v sledovanom produkčnom tvare bezpečný default false
K10 — kolácia a charset zostávajú zlučiteľné s Validovaným databázovým návrhom
K11 — dokumentácia neopakuje kompromitovanú hodnotu
K12 — odstránenie z aktuálnej vetvy sa nezamieňa s rotáciou hesla
K13 — odstránenie z aktuálnej vetvy sa nezamieňa s očistením histórie Git
K14 — pred serverovým overením je rotácia označená ako povinná
K15 — nevznikol neoprávnený zásah do externého hostingu alebo databázy
```

---

# 3. Výsledky

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

---

# 4. Zistené obmedzenia

Aktuálna vetva je sanitizovaná, ale bezpečnostný incident ešte nie je úplne uzavretý:

```text
L1 — kompromitovaná hodnota nebola zrotovaná v externom hostingu,
L2 — staršie commity môžu naďalej obsahovať tajomstvo,
L3 — nebolo overené, či existujú klony, cache alebo forky s pôvodnou hodnotou,
L4 — nebolo vykonané praktické databázové pripojenie s novým tajomstvom.
```

Tieto obmedzenia nemožno odstrániť iba úpravou aktuálneho súboru.

---

# 5. Výsledok Validácie

```text
VALIDATION_RESULT
=
VALID_WITH_LIMITATIONS
```

Význam:

```text
aktuálna vetva už neobsahuje databázové tajomstvo v CodeIgniter konfigurácii
+
bezpečný spôsob lokálnej konfigurácie je jednoznačný
+
testovacia skupina je oddelená od produkčných údajov
+
externá rotácia a prípadné očistenie histórie zostávajú otvorené
```

`VALID_WITH_LIMITATIONS` neoprávňuje používať pôvodné heslo ani pokračovať v serverovom overovaní pred jeho rotáciou.

---

# 6. Nasledujúci logický krok

```text
oprávnený správca zrotuje heslo
→ overí zneplatnenie starej hodnoty
→ nové tajomstvo uloží iba do lokálneho alebo hostingového prostredia
→ prakticky sa overí databázový server
→ rozhodne sa, či a ako sa očistí Git história
```
