# ReValidácia bezpečnej databázovej konfigurácie

## Stav dokumentu

```text
PRACOVNÝ
```

Predmet:

```text
aktualizovaný bezpečnostný kontrakt
+ externý private/metodika.env
+ ExternalEnvironment
+ webový a CLI bootstrap
+ diagnostický príkaz
```

## Kritériá

```text
K1 — pôvodné heslo bolo podľa potvrdenia používateľa zrotované,
K2 — aktívne tajomstvo je mimo /codei,
K3 — aktívne tajomstvo nie je v Git repozitári,
K4 — loader podporuje METODIKA_ENV_FILE,
K5 — predvolená cesta smeruje do súrodeneckého private adresára,
K6 — loader je použitý webom aj CLI,
K7 — sledovaný Database.php zostáva bez tajomstiev,
K8 — testovacia skupina zostáva oddelená,
K9 — diagnostika nevypisuje identitu účtu ani DSN,
K10 — chyba diagnostiky nevypisuje databázovú výnimku,
K11 — verejná env šablóna neobsahuje platné údaje,
K12 — rotácia sa nezamieňa s očistením Git histórie,
K13 — migrácie sú iba pripravené, nie vydávané za spustené,
K14 — praktický výsledok servera zostáva otvorený,
K15 — nevznikol neoprávnený zápis do hostingu alebo databázy.
```

## Výsledky

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

## Obmedzenia

```text
L1 — tajomstvo môže zostať v historických commitoch,
L2 — nebol prakticky overený vlastník a režim súboru private/metodika.env,
L3 — nebolo vykonané serverové pripojenie,
L4 — nebol potvrdený výsledok diagnostiky schopností.
```

## Výsledok

```text
VALIDATION_RESULT
=
VALID_WITH_LIMITATIONS
```

Aktuálna konfigurácia je pripravená na bezpečné serverové overenie. Výsledok neValiduje skutočné pripojenie ani databázový server.
