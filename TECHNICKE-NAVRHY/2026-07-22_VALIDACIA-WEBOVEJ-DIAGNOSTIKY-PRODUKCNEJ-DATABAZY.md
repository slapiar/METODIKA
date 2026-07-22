# Validácia webovej diagnostiky produkčného databázového prostredia

## Stav dokumentu

```text
PRACOVNÝ
```

Validovaný predmet:

```text
codei/app/Controllers/DiagnosticsController.php
+
codei/app/Services/DatabaseCapabilityInspector.php
+
codei/app/Commands/VerifyDatabaseCapabilities.php
+
codei/app/Config/Routes.php
+
codei/app/Views/diagnostics/login.php
+
codei/app/Views/diagnostics/database.php
```

---

# 1. VALIDATION_EVENT

```text
TARGET:
webová čítacia diagnostika databázy s bezpečnostnou bránou

ACTOR:
GitHub Copilot

AUTHORITY_CONTEXT:
výslovný pokyn pokračovať podľa postupu Inicializácia práce

TIME:
2026-07-22

CONTEXT:
CodeIgniter 4.7.4 runtime, externý env mimo /codei

SCOPE:
routing, autorizácia tokenom, session gate, zdieľaná SQL diagnostika CLI+web
```

---

# 2. Kritériá

```text
K1  — disabled gate vracia 404
K2  — missing token v login POST neodhalí výsledky
K3  — wrong token vracia 404
K4  — correct token sprístupní iba diagnostiku
K5  — výstup neobsahuje DSN ani názvy citlivých env premenných
K6  — diagnostika nevykonáva zápisové SQL operácie
K7  — CLI a web používajú tú istú diagnostickú službu
K8  — po vypnutí nie je stránka dostupná
K9  — migrácie zostali nespustené
K10 — odpoveď obsahuje no-store/no-cache bezpečnostné hlavičky
K11 — stránka obsahuje robots noindex,nofollow,noarchive
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

Praktické overenia vykonané bez migrácií:

```text
disabled GET /diagnostics/database -> 404
enabled GET /diagnostics/database -> 200 (login formulár)
wrong token POST /diagnostics/database/login -> 404
correct token POST /diagnostics/database/login -> 303 redirect
authorized GET /diagnostics/database -> 200
HTTP hlavičky no-cache a bezpečnostné hlavičky sú prítomné
```

CLI kontrola po refaktore:

```text
XDEBUG_MODE=off php spark metodika:db-capabilities
-> bezpečný výstup bez tajomstiev
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
bezpečnostná brána, čítacia diagnostika a zdieľaná služba sú implementované
+
prakticky potvrdené správanie disabled/wrong/correct token
+
v tomto kroku nebola potvrdená úspešná produkčná DB konektivita, iba bezpečné zlyhanie bez tajomstiev
```

---

# 5. Otvorené obmedzenia

```text
L1 — vendor/bin/phpunit nie je dostupný (vendor priečinok v prostredí chýba),
L2 — plný test beh vyžaduje doinštalované testovacie závislosti,
L3 — výsledky capability checks v produkcii závisia od dostupného externého env a DB servera.
```

---

# 6. Nasledujúci logický krok

```text
po doplnení testovacích závislostí spustiť phpunit testy pre diagnostics
→ v produkcii dočasne zapnúť diagnostics gate
→ vykonať capability overenie
→ diagnostics gate vypnúť
```
