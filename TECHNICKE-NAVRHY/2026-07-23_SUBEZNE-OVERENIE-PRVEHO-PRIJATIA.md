# Súbežné overenie prvého prijatia

## Stav dokumentu

```text
ČIASTOČNE-IMPLEMENTOVANÝ
```

## Predmet

Praktické overenie súbežného prvého prijatia rovnakej `REQUEST_REFERENCE` cez dve fyzicky nezávislé MySQLi spojenia.

Overenie sa vykonáva príkazom:

```bash
php spark metodika:verify-concurrent-first-acceptance
```

## Implementovaný súbor

```text
codei/app/Commands/VerifyConcurrentFirstAcceptance.php
```

## Zdroj významu

```text
TECHNICKE-NAVRHY/2026-07-22_POLITIKA-OPAKOVANEJ-REQUEST-REFERENCE.md
+
TECHNICKE-NAVRHY/2026-07-22_REPOSITORY-KONTRAKT-REQUEST-REFERENCE.md
```

Test nemení replay politiku. Overuje iba jej súbežný invariant:

```text
rovnaká REQUEST_REFERENCE
→ najviac jedna víťazná rezervácia
→ najviac jedna derivation_reference
→ druhý tok sa priradí k existujúcemu behu
```

## Kontrolovaný priebeh

```text
spojenie A
→ otvorí vonkajšiu transakciu
→ FirstAcceptanceService vytvorí rezerváciu, beh a dve doménové väzby
→ vonkajšia transakcia zostane otvorená

spojenie B
→ asynchrónne odošle INSERT rovnakej REQUEST_REFERENCE
→ čaká na unikátny zámok InnoDB

spojenie A
→ commit

spojenie B
→ musí dostať databázovú kolíziu 1062
→ FirstAcceptanceService musí vrátiť ALREADY_EXISTS
→ reservation.derivation_reference musí patriť toku A
```

## Kritérium úspechu

```text
spojenia A a B majú rozdielny MySQL thread_id,
spojenie A vytvorí CREATED,
asynchrónny INSERT spojenia B skončí kódom 1062,
služba spojenia B vráti ALREADY_EXISTS,
spojenie B načíta derivation_reference toku A,
v databáze existuje presne 1 rezervácia + 1 beh + 2 doménové väzby,
po cielenom čistení zostane 0 + 0 + 0,
príkaz skončí EXIT_SUCCESS.
```

## Bezpečnostné vlastnosti

```text
každé spustenie používa náhodnú REQUEST_REFERENCE,
testovacie hodnoty sú technicky jednoznačné,
po commite víťazného toku sa vykoná cielené čistenie,
čistenie rešpektuje poradie cudzích kľúčov,
po čistení sa explicitne overí nulový zostatok,
test nemení pôvodné produkčné údaje.
```

## Aktuálny výsledok

```text
IMPLEMENTED_WITHOUT_RUNTIME_VALIDATION
```

Príkaz bol spätne načítaný a staticky porovnaný s aktuálnym MySQLi adaptérom, unikátnymi obmedzeniami rezervácie a CodeIgniter podporou nesdielaných spojení. Praktické spustenie v Codespaces ani nad Hostinger MySQLi ešte nebolo vykonané.

## Vedome neoverované

```text
rovnaká REQUEST_REFERENCE + odlišný payload_fingerprint,
REQUEST_REFERENCE_CONFLICT,
replay ukončeného výsledku,
HTTP alebo CLI obraz replay odpovede.
```

Tieto prípady patria do implementácie a reValidácie `RequestReplayGuard`.

## Nasledujúci krok

```text
php -l codei/app/Commands/VerifyConcurrentFirstAcceptance.php
→ overiť registráciu Spark príkazu
→ vytvoriť release
→ spustiť nad Hostinger MySQLi
→ zapísať výsledok
→ reValidovať súbežnú replay hranicu
→ implementovať RequestReplayGuard
```
