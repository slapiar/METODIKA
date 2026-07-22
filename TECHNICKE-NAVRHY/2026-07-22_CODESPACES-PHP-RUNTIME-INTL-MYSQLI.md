# Codespaces PHP runtime: intl a mysqli

## Stav dokumentu

```text
ČIASTOČNE-IMPLEMENTOVANÝ
```

---

# 1. Predmet

Trvalá technická konfigurácia vývojového kontajnera pre repozitár METODIKA tak,
aby aktívny CLI runtime `php` po rebuilde načítal minimálne:

```text
intl
mysqli
```

bez prechodu na systémové `/usr/bin/php` a bez ručného jednorazového zásahu do
bežiaceho kontajnera.

---

# 2. Skutočný zistený stav pred zásahom

Prakticky overené:

```text
repozitár neobsahoval .devcontainer/
Codespaces používal default image mcr.microsoft.com/vscode/devcontainers/universal:latest
aktívny php bol /home/codespace/.php/current/bin/php -> /usr/local/php/current/bin/php -> /usr/local/php/8.4.15/bin/php
php -m obsahoval mbstring, ale neobsahoval intl ani mysqli
```

Dôsledok:

```text
CodeIgniter CLI príkazy závislé od DB capabilities zlyhávali na chýbajúcom intl
```

---

# 3. Príčina

Aktívny Codespaces PHP runtime bol vytvorený feature build procesom, ktorý pre
8.4.15 neobsahoval požadované build voľby pre:

```text
--enable-intl
--with-mysqli
```

Dodatočná kompilácia iba `intl.so` je možná, ale `mysqli` je v tomto prípade
naviazané na `mysqlnd` a bez konzistentného buildu celého runtime zostáva
nespoľahlivé.

---

# 4. Návrh najmenšieho trvalého zásahu

V rámci repozitára boli doplnené dva súbory:

```text
.devcontainer/devcontainer.json
.devcontainer/setup-php-extensions.sh
```

Princíp:

```text
1) po vytvorení kontajnera sa spustí setup skript,
2) skript overí, či aktívny php už má intl+mysqli,
3) ak nie, z rovnakého patch release (8.4.15) zostaví nový runtime do /usr/local/php/8.4.15-metodika,
4) aktivuje ho pre codespace cez /usr/local/php/current a /home/codespace/.php/current.
```

Tým sa nemení požadovaná verzia PHP a zachová sa aktívna cesta v `/usr/local/php`.

---

# 5. Implementácia

## 5.1 .devcontainer/devcontainer.json

- explicitne nastavuje image `mcr.microsoft.com/vscode/devcontainers/universal:latest`,
- nastavuje `postCreateCommand` na spustenie setup skriptu.

## 5.2 .devcontainer/setup-php-extensions.sh

- je idempotentný,
- pracuje s aktívnym runtime (realpath `php`),
- neprepína na `/usr/bin/php`,
- inštaluje build závislosti,
- buildne PHP v rovnakej verzii s `intl` a `mysqli`,
- aktivuje nový runtime cez symlink `current`,
- overuje výsledok cez `php -m`.

---

# 6. Obmedzenia

```text
- rebuild PHP runtime trvá dlhšie (kompilácia zo zdrojov),
- skript predpokladá dostupné apt repozitáre a passwordless sudo v Codespaces,
- DB diagnostický príkaz ostáva závislý od externého env a dostupného servera.
```

---

# 7. Dotknuté súbory

```text
.devcontainer/devcontainer.json
.devcontainer/setup-php-extensions.sh
TECHNICKE-NAVRHY/README.md
README.md
CHANGELOG.md
```
