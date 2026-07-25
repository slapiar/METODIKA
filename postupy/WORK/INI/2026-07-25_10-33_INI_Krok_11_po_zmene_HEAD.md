# INICIALIZÁCIA — Krok 11 po zmene autoritatívneho HEAD

## Stav brány

```text
GATE=CLOSED
```

## Dôvod novej inicializácie

Pôvodná brána Kroku 11 bola otvorená nad autoritatívnym HEAD:

```text
39f054bd54d931cc6cc68b1d4c4cc3d65a30d1b0
```

Po otvorení brány a počas Validácie pribudol na autoritatívnej vetve `main` nový commit:

```text
a0018df22ecdcb3017939dc2077e442468c88080
Doplnenie o stránku supervízora metodiky 1
```

Podľa bodu 10 dokumentu `postupy/Inicializácia práce.md` zmena HEAD po otvorení brány ruší jej platnosť a vyžaduje novú inicializáciu.

## Stav rozpracovaných artefaktov

```text
PR=#3
VETVA=krok11-uplna-validacia
PÔVODNÝ_BASE=39f054bd54d931cc6cc68b1d4c4cc3d65a30d1b0
STAV_ARTEFAKTOV=NA_ROZHODNUTIE
VALIDÁCIA_NA_STAROM_BASE=NEAUTORITATÍVNA
RELEASE=ZAKÁZANÝ
KROK_12_GATE=CLOSED
```

Na pracovnej vetve vznikli testovacie artefakty:

- `.github/workflows/krok-11-full-validation.yml`,
- `codei/tests/integration/krok11-http-e2e.sh`,
- `codei/tests/session/DiagnosticsAuthenticationFlowTest.php`,
- rozpracovaný presný patch zastaraných očakávaní v `DiagnosticsControllerTest.php`.

Žiadny z týchto artefaktov sa po zmene autoritatívneho HEAD automaticky nepovažuje za platný výsledok Kroku 11. O ich ponechaní, úprave, rebase alebo odstránení sa rozhodne až po úplnom načítaní a overení nového stavu.

## Aktuálny vzdialený stav

```text
projekt=METODIKA
repozitár=slapiar/METODIKA
autoritatívna_vetva=main
aktuálny_HEAD=a0018df22ecdcb3017939dc2077e442468c88080
technický_koreň=/codei
čas_overenia=2026-07-25 10:33 Europe/Bratislava
```

Rozdiel oproti HEAD pôvodnej brány tvorí jeden commit a šesť súborov:

| Súbor | Stav | Zmena |
|---|---|---:|
| `codei/app/Config/Boot/development.php` | modified | +4 / −0 |
| `codei/app/Config/Boot/production.php` | modified | +3 / −0 |
| `codei/app/Config/Database.php` | modified | +1 / −1 |
| `codei/app/Config/Routes.php` | modified | +5 / −0 |
| `codei/app/Controllers/GateSupervisor.php` | added | +116 / −0 |
| `codei/app/app/Models/IniStepRegisterModel.php` | added | +36 / −0 |

Zmeny sa dotýkajú bootovania prostredia, databázovej konfigurácie, routovania a nových aplikačných komponentov. Preto priamo zasahujú do predpokladov environmentálneho, session, databázového a HTTP E2E overenia Kroku 11.

## Inicializačná tabuľka

| Bod | Stav | Čo bolo overené | Konkrétny výsledok a dôkaz | Zostáva neoverené |
|---|---|---|---|---|
| 1. Metodika načítaná | ÁNO | Úplný aktuálny obsah `postupy/Inicializácia práce.md`, najmä bod 10 a STOP pravidlo | blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79`; zmenený HEAD ruší pôvodnú bránu | nič |
| 2. Projekt a autoritatívny zdroj overený | ÁNO | Projekt METODIKA, repozitár `slapiar/METODIKA`, vetva `main`, koreň `/codei` | aktuálny vzdialený repozitár a projektový register | nič |
| 3. Vetva a HEAD overené | ÁNO | Nový autoritatívny HEAD a rozdiel od pôvodnej brány | `39f054bd... → a0018df2...`; jeden commit, šesť dotknutých súborov | nič |
| 4. Potrebné prístupy prakticky overené | ÁNO | GitHub čítanie, zápis a administrátorské oprávnenie | vytvorenie tohto INI; predchádzajúce praktické čítanie, zápis a Actions operácie | spätné načítanie tohto INI |
| 5. Prostredie prakticky overené | NEOVERENÉ | Predchádzajúce dôkazy prostredia boli platné pre starší boot a konfiguráciu | nový commit mení oba boot súbory a `Database.php` | úplný obsah zmien; načítanie `.env`; aktuálny PHP/Composer/MariaDB beh; migrácie; izolácia; rollback; cleanup |
| 6. Závislosti kroku dostupné | NEOVERENÉ | Zistený zoznam šiestich zmenených súborov a existencia PR #3 | nové routes, kontrolér a model menia zdrojový stav testovaný Krokom 11 | úplný obsah všetkých nových a súvisiacich súborov; modelové a databázové závislosti; syntax; kompatibilita s PR #3 |
| 7. Predmet a hranice zásahu určené | ÁNO | Predmetom je iba obnova platnosti inicializácie Kroku 11 po zmene HEAD | bez testu, návrhu, rebase, merge, release alebo produkcie pred `GATE=OPEN` | nič |
| 8. Kritérium úspechu určené | ÁNO | Najprv potvrdiť úplný nový stav a kompatibilitu všetkých deviatich blokov; následne zachovať pôvodné binárne kritérium Kroku 11 | `DB_UNIQUENESS=true AND OUTCOMES=CREATED+ALREADY_EXISTS AND CLEANUP=true AND STATE=COMPLETED_SUCCESS` | praktické splnenie až po novej otvorenej bráne |
| 9. Rollback určený | ÁNO | Rozpracovaný PR sa nezlučuje; starý validačný beh sa nepoužije; podľa dôkazu sa vetva rebaseuje, opraví alebo odstráni | produkčný commit Kroku 10 sa automaticky nevracia | presné rozhodnutie o PR #3 po overení bodov 5 a 6 |

## Vyhodnotenie brány

```text
1=ÁNO
2=ÁNO
3=ÁNO
4=ÁNO
5=NEOVERENÉ
6=NEOVERENÉ
7=ÁNO
8=ÁNO
9=ÁNO
GATE=CLOSED
```

```text
BLOKUJÚCI_BOD=5. Prostredie a 6. Závislosti kroku
CHÝBAJÚCI_DÔKAZ=úplné načítanie a praktické overenie zmien commitu a0018df2... a ich kompatibility s deviatimi blokmi Kroku 11 a PR #3
POVOLENÝ_ĎALŠÍ_ÚKON=iba čítanie a overovanie nového autoritatívneho stavu bez testu predmetu Kroku 11, bez zmeny kódu, konfigurácie, pracovnej vetvy alebo produkcie
```

## STOP záznam zmeny predpokladu

```text
STOP
PORUŠENÝ_BOD=10. Implementácia až po analýze — po otvorení brány sa zmenil autoritatívny HEAD
ČO_BOLO_VYKONANÉ_PRED_ZISTENÍM_ZMENY=analýza deviatich blokov, vytvorenie troch testovacích artefaktov, PR #3 a prvý neúspešný validačný beh nad starým base
KTORÁ_BRÁNA_MALA_CHYBE_ZABRÁNIŤ=opätovná kontrola HEAD pred zápisom a Validáciou
PREČO_NEZABRÁNILA=nový commit pribudol na main súbežne až po otvorení brány a po začatí práce na oddelenej vetve
STAV_VZNIKNUTÝCH_ARTEFAKTOV=na rozhodnutie; nie sú automaticky neplatné, ale nie sú autoritatívne validované
ROLLBACK_ALEBO_NÁPRAVA=nepoužiť výsledky behov zo starého base; nezhlučovať PR #3; úplne načítať nový stav; vytvoriť novú bránu; až po GATE=OPEN rozhodnúť o rebase, oprave alebo odstránení artefaktov
```

## Tabuľka stavu čítania a vykonávania metodických pokynov

| ID | Metodický pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Povinné nové načítanie vzdialeného autoritatívneho repozitára | 1 | 1 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 1 |
| 4 | Praktické overenie prístupov | 1 | 1 |
| 5 | Obnova projektového kontextu | 1 | 0 |
| 6 | Overenie skutočného stavu | 1 | 0 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 0 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 0 |
| 10 | Implementácia až po analýze | 1 | 0 |
| 11 | Spätné načítanie po zápise | 1 | 0 |
| 12 | Validácia výsledku | 1 | 0 |
| 13 | Záznam metodického úkonu | 1 | 1 |
| 14 | Ukončenie pracovného kroku | 1 | 0 |