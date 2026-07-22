# Webová diagnostika produkčného databázového prostredia

## Stav dokumentu

```text
ČIASTOČNE-IMPLEMENTOVANÝ
```

---

# 1. Predmet

Bezpečne gated webová diagnostika pre CodeIgniter runtime METODIKA, určená iba na
čítacie overenie databázových schopností bez migrácií a bez zmeny schémy.

Overované podmienky:

```text
načítanie externého private/metodika.env
spojenie na databázu
verzia servera
InnoDB
utf8mb4_bin
DATETIME(6)
```

---

# 2. Bezpečnostný model

Prístup je podmienený dvoma serverovými premennými:

```text
METODIKA_DIAGNOSTICS_ENABLED=1
METODIKA_DIAGNOSTICS_TOKEN=<tajná hodnota>
```

Brána:

```text
ENABLED != 1 -> 404
chýbajúci token v konfigurácii -> 404
nesprávny token v požiadavke -> 404
správny token -> krátkodobé session oprávnenie
```

Token sa neposiela v URL. Používa sa iba POST formulár a porovnanie:

```text
hash_equals(expected, submitted)
```

Session pravidlá pre diagnostiku:

```text
session.use_strict_mode=1
session.cookie_httponly=1
session.cookie_samesite=Strict
session.cookie_secure=1 iba pri HTTPS
TTL oprávnenia = 900 sekúnd
```

---

# 3. Implementačná štruktúra

```text
codei/app/Controllers/DiagnosticsController.php
codei/app/Views/diagnostics/login.php
codei/app/Views/diagnostics/database.php
codei/app/Services/DatabaseCapabilityInspector.php
codei/app/Config/Routes.php
codei/app/Commands/VerifyDatabaseCapabilities.php
```

Routes:

```text
GET  /diagnostics/database
POST /diagnostics/database/login
POST /diagnostics/database/run
POST /diagnostics/database/logout
```

Auto-routing je explicitne vypnutý.

---

# 4. Zdieľaná diagnostická služba

`DatabaseCapabilityInspector` je spoločná implementácia pre:

```text
CLI: metodika:db-capabilities
web: DiagnosticsController
```

Služba vracia iba bezpečný dátový výsledok:

```text
connection
serverVersion
server
innodb
utf8mb4Bin
datetime6
errorCode (CONNECTION_FAILED | QUERY_FAILED | CAPABILITY_MISSING | null)
diagnosedAt
```

Služba nevracia text SQL výnimky ani tajomstvá.

---

# 5. Povolené SQL dotazy

Použité sú iba čítacie dotazy:

```text
SELECT VERSION() AS server_version;
SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB';
SHOW COLLATION LIKE 'utf8mb4_bin';
SELECT CAST('2026-01-01 00:00:00.123456' AS DATETIME(6)) AS datetime_6;
```

Nepoužívajú sa zápisové operácie ani migrácie.

---

# 6. Výstup stránky

Stránka zobrazuje iba:

```text
Externé prostredie načítané: ÁNO/NIE
Databázové spojenie: OK/NIE
Databázový server: verzia alebo NEZISTENÁ
InnoDB: OK/NIE
utf8mb4_bin: OK/NIE
DATETIME(6): OK/NIE
Celkový výsledok: PRIPRAVENÉ/NEPRIPRAVENÉ
Čas diagnostiky
```

A vždy obsahuje upozornenie:

```text
Diagnostika je iba čítacia. Migrácie neboli spustené.
```

---

# 7. HTTP hardening

Odpovede diagnostiky nastavujú minimálne:

```text
Cache-Control: no-store, no-cache, must-revalidate
Pragma: no-cache
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Referrer-Policy: no-referrer
Content-Security-Policy: default-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'; style-src 'self' 'unsafe-inline'
```

HTML obsahuje:

```text
<meta name="robots" content="noindex,nofollow,noarchive">
```

---

# 8. Dotknuté súbory

```text
codei/app/Config/ExternalEnvironment.php
codei/app/Services/DatabaseCapabilityInspector.php
codei/app/Config/Services.php
codei/app/Controllers/DiagnosticsController.php
codei/app/Views/diagnostics/login.php
codei/app/Views/diagnostics/database.php
codei/app/Config/Routes.php
codei/app/Commands/VerifyDatabaseCapabilities.php
codei/.env.example
codei/tests/session/DiagnosticsControllerTest.php
codei/tests/unit/DatabaseCapabilityInspectorTest.php
```
