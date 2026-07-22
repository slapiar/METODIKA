# Bezpečná databázová konfigurácia CodeIgnitera

## Stav dokumentu

```text
PRACOVNÝ
```

Tento dokument zaznamenáva bezpečnostný technický zásah po zistení, že sledovaný súbor `codei/app/Config/Database.php` obsahoval databázové prihlasovacie údaje.

Dokument neobsahuje a nesmie opakovať žiadnu kompromitovanú hodnotu.

---

# 1. Predmet zásahu

Dotknuté súbory:

```text
codei/app/Config/Database.php
codei/.env.example
codei/.gitignore
```

Cieľ:

```text
sledovaný kód bez tajomstiev
+
lokálne alebo hostingové premenné prostredia
+
oddelená testovacia konfigurácia bez produkčných údajov
```

---

# 2. Prijatý konfiguračný kontrakt

## 2.1 Sledovaný PHP súbor

`codei/app/Config/Database.php` smie obsahovať iba:

```text
technický tvar databázovej skupiny,
neškodné defaulty,
DBDriver,
charset a koláciu,
port,
technické prepínače,
testovaciu SQLite konfiguráciu.
```

Nesmie obsahovať:

```text
produkčné používateľské meno,
produkčné heslo,
produkčný názov databázy,
API kľúč,
setup token,
iné tajomstvo alebo súkromný prístupový údaj.
```

## 2.2 Lokálne prostredie

Skutočné hodnoty sa dodávajú prostredníctvom:

```text
codei/.env
```

alebo premenných prostredia hostingu.

`codei/.env` je ignorovaný pravidlami v `codei/.gitignore`.

## 2.3 Verejná šablóna

Sledovaný súbor:

```text
codei/.env.example
```

obsahuje iba názvy konfiguračných premenných a zástupné hodnoty. Nesmie obsahovať platný účet ani heslo.

---

# 3. Testovacia databáza

Testovacia skupina používa:

```text
SQLite3
:memory:
```

Bez databázového používateľského mena a hesla.

Testovacie prostredie nesmie pri automatickom teste prepnúť na produkčnú databázu.

---

# 4. Stav kompromitovaného tajomstva

Odstránenie tajomstva z aktuálnej vetvy:

```text
≠ rotácia tajomstva
≠ odstránenie tajomstva z histórie Git
≠ dôkaz, že tajomstvo nebolo prečítané
```

Preto platí:

```text
historicky commitnuté databázové heslo
→ považovať za kompromitované
→ zmeniť v hostiteľskom prostredí
→ starú hodnotu zneplatniť
```

Rotáciu musí vykonať oprávnený správca databázy alebo hostingu. Tento repozitárový zásah ju nevykonal.

---

# 5. História Git

Tajomstvo môže zostať dostupné v starších commitoch, klonoch, cache alebo forkoch.

Očistenie histórie je samostatný deštruktívny zásah, ktorý môže vyžadovať:

```text
zálohu repozitára,
identifikáciu všetkých výskytov,
prepísanie histórie,
force push,
obnovu alebo reclone pracovných kópií,
koordináciu s ostatnými používateľmi repozitára.
```

História sa nesmie prepísať automaticky bez samostatného výslovného oprávnenia a plánu návratu.

Rotácia tajomstva má prednosť pred očistením histórie, pretože zneplatňuje uniknutú hodnotu aj v existujúcich kópiách.

---

# 6. Povinné prevádzkové pravidlá

```text
1. žiadne tajomstvo do sledovaných PHP, Markdown, YAML ani JSON súborov,
2. produkčné hodnoty iba v lokálnom `.env` alebo serverovom secret store,
3. `.env.example` iba so zástupnými hodnotami,
4. DBDebug v produkcii vypnutý,
5. testy používajú oddelenú databázovú skupinu,
6. logy nesmú vypisovať heslá ani celé DSN,
7. chyba pripojenia nesmie vracať tajomstvo klientovi,
8. nové tajomstvo sa po rotácii nesmie zapísať do chatu ani repozitára.
```

---

# 7. Kritériá ďalšieho overenia servera

Praktické overenie databázového servera možno vykonať až po:

```text
rotácii kompromitovaného hesla,
bezpečnom vložení nového hesla do serverového prostredia,
overení, že aktuálna vetva neobsahuje staré ani nové tajomstvo.
```

Pri overení sa smú zaznamenať iba necitlivé technické výsledky:

```text
server a verzia,
InnoDB,
utf8mb4,
utf8mb4_bin,
DATETIME(6),
cudzie kľúče,
transakčné správanie.
```

---

# 8. Nasledujúci logický krok

```text
oprávnený správca zrotuje databázové heslo
→ nové tajomstvo uloží iba do hostiteľského prostredia
→ overí sa pripojenie bez zverejnenia tajomstva
→ overia sa vlastnosti databázového servera
→ až potom vzniknú migrácie M1 až M8
```
