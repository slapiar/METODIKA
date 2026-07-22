# Bezpečná databázová konfigurácia CodeIgnitera

## Stav dokumentu

```text
ČIASTOČNE-IMPLEMENTOVANÝ
```

Tento dokument vedie aktuálny technický kontrakt databázovej konfigurácie po odstránení tajomstiev zo sledovaného kódu, rotácii pôvodného hesla a presunutí aktívnej konfigurácie mimo `/codei`.

Dokument neobsahuje a nesmie opakovať žiadnu minulú ani aktuálnu tajnú hodnotu.

---

# 1. Sledovaný kód

`codei/app/Config/Database.php` smie obsahovať iba technický tvar databázových skupín, neškodné defaulty, ovládač, charset, koláciu a oddelenú testovaciu SQLite konfiguráciu.

Nesmie obsahovať:

```text
produkčné používateľské meno,
produkčné heslo,
produkčný názov databázy,
API kľúč,
setup token,
iné tajomstvo.
```

---

# 2. Aktívne externé prostredie

Skutočné hodnoty sú uložené mimo webového a aplikačného koreňa v súrodeneckom súbore:

```text
../private/metodika.env
```

voči `/codei`.

Prednosť má serverová premenná:

```text
METODIKA_ENV_FILE
```

Ak nie je nastavená, `ExternalEnvironment` použije uvedenú súrodeneckú cestu. Loader sa vykoná pred webovým aj CLI bootstrapom CodeIgnitera.

Súbor s tajomstvom:

```text
nesmie byť súčasťou Git repozitára,
nesmie byť prístupný cez HTTP,
má byť čitateľný iba oprávneným serverovým používateľom,
nesmie sa kopírovať do codei/.env ani do release balíka.
```

`codei/.env.example` zostáva iba verejnou šablónou bez platných údajov.

---

# 3. Testovacia databáza

Testovacia skupina používa:

```text
SQLite3
:memory:
```

bez produkčných prihlasovacích údajov. Testovacie prostredie nesmie prepnúť na produkčnú databázu.

---

# 4. Stav pôvodného tajomstva

Používateľ potvrdil rotáciu pôvodného databázového hesla 22. júla 2026. Tým bola stará commitnutá hodnota zneplatnená pre ďalšie pripojenia.

Platí však:

```text
rotácia tajomstva
≠ odstránenie tajomstva z histórie Git
≠ dôkaz, že historická hodnota nebola prečítaná
```

Očistenie histórie zostáva samostatným deštruktívnym zásahom a nesmie sa vykonať bez samostatného oprávnenia a plánu návratu.

---

# 5. Povinné prevádzkové pravidlá

```text
1. žiadne tajomstvo do sledovaných súborov,
2. produkčné hodnoty iba v externom private env alebo serverovom secret store,
3. verejná šablóna iba so zástupnými hodnotami,
4. DBDebug v produkcii vypnutý,
5. testy používajú oddelenú databázovú skupinu,
6. logy a CLI výstupy nesmú vypisovať heslá ani celé DSN,
7. chyba pripojenia nesmie vracať detaily účtu klientovi,
8. nové tajomstvo sa nesmie zapísať do chatu ani repozitára.
```

---

# 6. Diagnostika servera

Pripravený CLI príkaz môže zaznamenať iba:

```text
server a verziu,
InnoDB,
utf8mb4_bin,
DATETIME(6),
úplný úspech alebo zlyhanie kontroly.
```

Nevypisuje používateľské meno, názov databázy, heslo ani DSN.

---

# 7. Aktuálny stav

```text
rotácia hesla = potvrdená používateľom,
externý private env = vytvorený používateľom,
loader = implementovaný,
diagnostický príkaz = implementovaný,
migrácie M1 až M8 = pripravené, ale nespustené,
praktický výsledok servera = zatiaľ nezistený.
```

---

# 8. Nasledujúci logický krok

```text
spustiť bezpečnú diagnostiku v hostiteľskom prostredí
→ pri úplnom úspechu spustiť migrácie M1 až M8
→ overiť fyzickú schému a cudzie kľúče
→ reValidovať implementovaný stav
```
