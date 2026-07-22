# Validácia Codespaces PHP runtime: intl a mysqli

## Stav dokumentu

```text
PRACOVNÝ
```

Validovaný predmet:

```text
.devcontainer/devcontainer.json
+
.devcontainer/setup-php-extensions.sh
+
TECHNICKE-NAVRHY/2026-07-22_CODESPACES-PHP-RUNTIME-INTL-MYSQLI.md
```

---

# 1. VALIDATION_EVENT

```text
TARGET:
trvalá konfigurácia vývojového kontajnera pre PHP 8.4.15

ACTOR:
GitHub Copilot

AUTHORITY_CONTEXT:
výslovný pokyn pokračovať podľa postupu Inicializácia práce

TIME:
2026-07-22

CONTEXT:
Codespaces default runtime bez intl a mysqli

SCOPE:
aktívny CLI PHP runtime, konfigurácia devcontainer, čítacia diagnostika DB
```

---

# 2. Kritériá

```text
K1  — repozitár obsahuje explicitný .devcontainer/devcontainer.json
K2  — postCreateCommand spúšťa idempotentný setup skript
K3  — setup skript cieli aktívny /usr/local/php runtime, nie /usr/bin/php
K4  — setup skript je syntakticky validný
K5  — po vykonaní setup skriptu je aktívne php stále 8.4.15
K6  — php --ini ukazuje aktívny runtime z /usr/local/php
K7  — php -m obsahuje intl
K8  — php -m obsahuje mysqli
K9  — spark command metodika:db-capabilities je dostupný
K10 — diagnostika DB nevyzrádza tajomstvá pri chybe pripojenia
K11 — neboli spustené migrácie
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
```

Merania:

```text
php -v
= PHP 8.4.15 (cli)

php --ini
= /usr/local/php/8.4.15-metodika/ini/php.ini

php -m | grep -E '^(intl|mysqli)$'
= intl
= mysqli
```

Čítacia diagnostika:

```text
cd /workspaces/METODIKA/codei
XDEBUG_MODE=off php spark metodika:db-capabilities

výstup: Databázové overenie zlyhalo. Skontrolujte externú konfiguráciu a dostupnosť servera.
```

---

# 4. Validácia výsledku

```text
VALIDATION_RESULT
=
VALID_WITH_LIMITATIONS
```

Význam:

```text
trvalá konfigurácia runtime pre intl a mysqli je funkčná
+
aktívny CLI runtime je po zásahu konzistentný
+
DB diagnostický príkaz je dostupný a bezpečne zlyháva bez výpisu tajomstiev
+
bez externého env súboru a dostupného DB servera nemožno v tomto kroku potvrdiť Server/InnoDB/utf8mb4_bin/DATETIME(6)
```

---

# 5. Otvorené obmedzenia

```text
L1 — pre plnú DB capability validáciu musí byť dostupný externý env a server,
L2 — rebuild kontajnera musí vykonať používateľ (Rebuild Container),
L3 — kompilácia PHP pri postCreate predlžuje inicializačný čas kontajnera.
```

---

# 6. Nasledujúci logický krok

```text
Rebuild Dev Container
→ overiť php -v, php --ini, php -m (intl,mysqli)
→ po sprístupnení externého env znovu spustiť metodika:db-capabilities
→ zaznamenať server capability výsledky
```
