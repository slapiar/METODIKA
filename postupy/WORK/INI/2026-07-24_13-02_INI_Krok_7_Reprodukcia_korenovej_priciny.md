# INICIALIZÁCIA KROKU 7 — Reprodukcia koreňovej príčiny mimo produkcie

## Identifikácia

```text
dátum a čas: 2026-07-24 13:02 Europe/Bratislava
projekt: METODIKA
činnosť: opakované otvorenie Kroku 7 opraveným inicializačným postupom
autoritatívny repozitár: slapiar/METODIKA
autoritatívna vetva: main
HEAD pri overení: e566d837b42aa49fa49b6770c4296954778bbd01
záväzný plán: postupy/PLAN/2026-07-24_08-04_Plán_práce.md
```

## Povinná brána

### 1. Metodika načítaná: ÁNO

**Čo bolo overené:** aktuálna povinná inicializačná brána a presný Krok 7 záväzného plánu.

**Konkrétny úkon:** nové načítanie `postupy/Inicializácia práce.md` z vetvy `main` a načítanie Kroku 7 v `postupy/PLAN/2026-07-24_08-04_Plán_práce.md`.

**Výsledok:** metodika vyžaduje dôkaz ku každému bodu; Krok 7 vyžaduje reálnu testovaciu DB, rovnakú transakčnú cestu, kontrolované zlyhania, dve reálne spojenia alebo procesy a overenie rollbacku.

**Dôkaz:** `postupy/Inicializácia práce.md`, blob `201bcaef882e21e99c484b2c9acd61489e1e9914`; plán, blob `518ebd4621637a5a7f30f571dde9ca6808a1295b`.

**Neoverené:** nič v tomto bode.

### 2. Projekt a autoritatívny zdroj overený: ÁNO

**Čo bolo overené:** projekt METODIKA a jeho autoritatívny zdroj.

**Konkrétny úkon:** nové načítanie `PROJEKTY/ZoznamProjektov.md` a metadata repozitára.

**Výsledok:** projekt je `METODIKA`, autoritatívny repozitár `slapiar/METODIKA`, autoritatívna vetva `main`, technický koreň CodeIgniter je `/codei`.

**Dôkaz:** `PROJEKTY/ZoznamProjektov.md`; GitHub metadata repozitára.

**Neoverené:** nič v tomto bode.

### 3. Vetva a HEAD overené: ÁNO

**Čo bolo overené:** autoritatívna vetva a jej aktuálny HEAD.

**Konkrétny úkon:** načítanie posledných commitov vetvy `main`.

**Výsledok:** autoritatívna vetva je `main`; HEAD pri inicializácii je `e566d837b42aa49fa49b6770c4296954778bbd01`.

**Dôkaz:** GitHub commit `e566d837b42aa49fa49b6770c4296954778bbd01`.

**Neoverené:** lokálna používateľská vetva `joyee-priority` nie je autoritatívnym zdrojom tohto úkonu a nebola použitá ako dôkaz stavu `main`.

### 4. Potrebné prístupy prakticky overené: ÁNO

**Čo bolo overené:** čítanie a zápis do autoritatívneho repozitára.

**Konkrétny úkon:** úspešné čítanie metodiky, plánu a projektového registra; zápis tohto jediného povoleného INI záznamu.

**Výsledok:** prístup ku GitHub repozitáru je funkčný pre čítanie aj zápis do `main`.

**Dôkaz:** tento súbor a jeho commit.

**Neoverené:** neprodukčný databázový prístup; patrí do bodu 5.

### 5. Prostredie prakticky overené: NIE

**Čo bolo overené:** Codespaces, dostupné izolované pracovné prostredie a existujúce CI podklady repozitára.

**Konkrétne úkony a výsledky:**

1. V Codespaces bol spustený pôvodne vytvorený reprodukčný príkaz bez prepnutia prostredia.
   - výsledok: CodeIgniter hlásil `production` a bezpečnostná brzda príkaz zastavila.
2. V Codespaces bol príkaz spustený jednorazovo s `CI_ENVIRONMENT=development`.
   - výsledok: `Unable to connect to the database. Main connection [MySQLi]: No such file or directory`.
   - aplikačná transakčná cesta nebola vykonaná.
3. V dostupnom izolovanom pracovnom prostredí boli prakticky overené binárky a runtime.
   - PHP 8.4.16 je dostupné.
   - MySQL klient, MariaDB klient, MySQL/MariaDB server, Docker ani Podman nie sú dostupné.
4. V repozitári boli vyhľadané existujúce workflow alebo CI definície s `workflow_dispatch`, MySQL alebo MariaDB službou.
   - výsledok: žiadne pripravené neprodukčné CI databázové prostredie nebolo nájdené.

**Výsledok:** neexistuje prakticky potvrdené neprodukčné prostredie s reálnou MySQL/MariaDB databázou, dvoma nezávislými spojeniami a izoláciou od produkcie.

**Dôkaz:** výstupy Codespaces uvedené používateľom; praktický audit dostupného izolovaného prostredia; nulové výsledky vyhľadania existujúceho CI databázového workflow.

**Neoverené:** konkrétny neprodukčný DB server, hostname/port, databáza, používateľ, oprávnenia, dve nezávislé spojenia, transakčné správanie a izolácia od produkcie.

### 6. Závislosti kroku dostupné: NIE

**Čo bolo overené:** závislosti výslovne požadované Krokom 7.

**Konkrétny úkon:** porovnanie kritérií Kroku 7 so skutočne dostupným prostredím.

**Výsledok:** dostupný je aplikačný zdrojový kód, CodeIgniter a migrácie; nedostupná je reálna neprodukčná MySQL/MariaDB databáza so schémou M1–M8 a potvrdeným cleanupom.

**Dôkaz:** Krok 7 záväzného plánu; výsledky bodu 5.

**Neoverené:** vykonaná schéma M1–M8 mimo produkcie, InnoDB, `utf8mb4_bin`, cudzie kľúče, dve DB spojenia a úplný cleanup.

### 7. Predmet a hranice zásahu určené: ÁNO

**Predmet:** reprodukovať mimo produkcie triedu príčiny identifikovanú v Kroku 6.

**Má sa vykonať:** rovnaký `InitialDerivationRun` a fingerprint, reálna testovacia DB, nezmenená transakčná cesta, dve spojenia alebo procesy, kontrola presnej výnimky, rollbacku a výsledku druhého participanta.

**Nemá sa vykonať:** funkčná oprava, použitie produkčnej databázy, zmena produkcie, predpokladanie dostupnosti DB, použitie SQLite ako náhrady MySQLi správania.

**Dotknuté hranice:** výhradne izolované neprodukčné DB dáta s jednoznačným prefixom a povinným cleanupom.

**Predčasný artefakt:** `codei/app/Commands/ReproduceFirstAcceptanceRootCause.php` z commitu `e7c42f56b1db957d75fbc60c15c00b4957c9c471` vznikol pred overením prostredia. V tejto inicializácii sa nepovažuje za platný výsledok Kroku 7 a nesmie sa spustiť, upraviť ani ďalej rozvíjať bez otvorenej brány.

### 8. Kritérium úspechu určené: ÁNO

Krok 7 môže byť `SPLNENÉ` iba ak sa v izolovanom neprodukčnom prostredí prakticky potvrdí:

```text
rovnaká REQUEST_REFERENCE
+ odlišná derivation_reference
+ DBDebug=false
+ reálna MySQLi/InnoDB cesta
→ neúspešný insert alebo kolízia
→ presne zdokumentované správanie repository
→ presná RuntimeException alebo vyvrátenie statickej hypotézy
→ rollback druhého toku
→ po cleanupe 0 rezervácií + 0 behov + 0 doménových väzieb
```

### 9. Rollback určený: ÁNO

Rollback reprodukcie musí pred spustením obsahovať:

- jedinečný prefix `root-cause-repro-*`,
- odstránenie doménových väzieb, behov a rezervácie v poradí podľa FK,
- postcheck počtov `0 + 0 + 0`,
- zákaz použitia produkčnej DB,
- samostatný cleanup pri úspechu aj výnimke,
- zachovanie logu výsledku bez tajomstiev.

Rollback tejto zatvorenej inicializácie nie je potrebný: okrem tohto INI záznamu nevzniká žiadny nový pracovný artefakt ani zmena prostredia.

## Výsledok brány

```text
GATE=CLOSED
BLOKUJÚCI_BOD=5 Prostredie prakticky overené; 6 Závislosti kroku dostupné
CHÝBAJÚCI_DÔKAZ=Reálna izolovaná neprodukčná MySQL/MariaDB databáza so schémou M1–M8, dvoma nezávislými spojeniami, potvrdenými oprávneniami, transakciami a cleanupom
POVOLENÝ_ĎALŠÍ_ÚKON=Iba praktické overenie už existujúceho neprodukčného DB prostredia; nie jeho návrh, vytvorenie, konfigurácia ani spustenie reprodukcie
```

## Stav Kroku 7

```text
KROK_7=NEOTVORENÝ
DÔVOD=Inicializačná brána sa neotvorila
ĎALŠIE_KROKY=ZAKÁZANÉ
```
