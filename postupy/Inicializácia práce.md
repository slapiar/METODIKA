# METODIKA: Inicializačný a vykonávací protokol (v2.0)

Tento dokument definuje nepriechodnú vykonávaciu bránu (`GATE`) pred akýmkoľvek zásahom do projektu.

---

## 1. INICIALIZAČNÁ BRÁNA (Gatekeeper)

Pred vykonaním akejkoľvek práce vytvorte súbor v `postupy/WORK/INI/YYYY-MM-DD_HH-MM_INI_<popis>.md`.
Je to **jediný** povolený artefakt pred otvorením brány (`GATE=OPEN`).

### Šablóna INI záznamu:

```text
# INICIALIZÁCIA KROKU: [Názov kroku]

## Kontrolná matica (Dôkaz je povinný, inak = NEOVERENÉ)
1. Metodika načítaná: [ÁNO/NIE/NEOVERENÉ] | Dôkaz: [commit/súbor/blob ID]
2. Projekt a autoritatívny zdroj: [ÁNO/NIE/NEOVERENÉ] | Dôkaz: [URL/repozitár/cesta]
3. Vetva a HEAD: [ÁNO/NIE/NEOVERENÉ] | Dôkaz: [hash commitu]
4. Prístupy (read/write): [ÁNO/NIE/NEOVERENÉ] | Dôkaz: [výsledok testu prístupu]
5. Prostredie a runtime: [ÁNO/NIE/NEOVERENÉ] | Dôkaz: [verzia, stav env]
6. Závislosti kroku: [ÁNO/NIE/NEOVERENÉ] | Dôkaz: [dostupnosť balíkov/služieb]
7. Predmet a hranice zásahu: [ÁNO/NIE/NEOVERENÉ] | Dôkaz: [súbory v rozsahu]
8. Kritérium úspechu: [ÁNO/NIE/NEOVERENÉ] | Dôkaz: [akceptačné kritériá]
9. Rollback plán: [ÁNO/NIE/NEOVERENÉ] | Dôkaz: [postup návratu]

## Stav Brány
GATE = [OPEN / CLOSED]
BLOKUJÚCI_BOD = [Číslo bodu / Žiadny]
POVOLENÝ_ĎALŠÍ_ÚKON = [Iba overenie chýbajúceho bodu OR Pokračovanie na krok 2]

## Matica plnenia pokynov
| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---|---|
| ID-01 až ID-14 | 1 | 1 |
```

---

## 2. PRAVIDLÁ INICIALIZÁCIE (GATE Logic)

1. **Stav GATE:**
   - Všetky body 1–9 sú `ÁNO` s doloženým DÔKAZOM $\rightarrow$ `GATE=OPEN`.
   - Akýkoľvek bod `NIE` / `NEOVERENÉ` alebo chýbajúci dôkaz $\rightarrow$ `GATE=CLOSED`.
2. **Pravidlo STOP pri `GATE=CLOSED`:**
   - Sú zakázané: návrhy riešení, úpravy kódu, zápis iných súborov, commit, spúšťanie migrácií/nasadení.
   - Povolené je **výhradne**: vykonávanie overovacích úkonov pre získanie chýbajúcich dôkazov bodov 1–9.
3. **Pravidlo čítania stavu (No-Memory Rule):**
   - Vždy sa číta **aktuálny vzdialený stav** (remote repository/HEAD).
   - Pamäť konverzácie, lokálne medzipamäte a interné registre sa považujú za **neoverené**.

---

## 3. FYZICKÝ PRACOVNÝ POSTUP (Po GATE=OPEN)

Po otvorení brány postupujte striktne v tomto poradí:

```text
[1. Načítanie stavu] -> [2. Analýza príčiny] -> [3. Minimálny návrh] -> [4. Implementácia] 
   -> [5. Spätné načítanie] -> [6. Validácia] -> [7. Záznam a Registre] -> [8. Checkpoint]
```

1. **Načítanie stavu a Plánu:**
   - Overte existenciu denných plánov v `postupy/PLAN/`.
   - Overte, či rovnaký úkon nebol vykonaný v predošlých krokoch. Ak áno, znovupoužite dôkaz (ak platí pre aktuálny HEAD).
2. **Analýza & Návrh:**
   - Najprv určite príčinu, až potom riešenie.
   - Zvoľte **najmenší možný bezpečný zásah** s pripraveným rollbackom.
3. **Implementácia a Spätné načítanie (Read-After-Write):**
   - Po každom zápise vykonajte opätovné načítanie vzdialeného súboru a overte správnosť, úplnosť a syntax.
4. **Záznam vykonanej práce:**
   - Záznam zapíšte do `postupy/WORK/YYYY-MM-DD_HH-MM_<krok>.md`.
   - V prípade zmeny metodických súborov aktualizujte `CHANGELOG.md` a príslušné registre.
5. **Ukončenie kroku:**
   - Uveďte: vykonané zmeny, otvorené riziká, commit ID, cestu k INI súboru a nasledujúci krok.

---

## 4. PROTOKOL PRI PORUŠENÍ (STOP Protocol)

Ak prácu začnete bez `GATE=OPEN` alebo s vymysleným dôkazom:
1. Okamžite zastavte prácu (žiadne opravy „za chodu“).
2. Do logu/konzoly zapíšte blok porušenia:

```text
STOP: PORUŠENIE BEZPEČNOSTNEJ BRÁNY
PORUŠENÝ_BOD = [Číslo bodu]
PREDČASNÝ_ZÁSAH = [Popis]
PRÍČINA = [Prečo brána nezabránila]
NÁPRAVA/ROLLBACK = [Postup obnovy do bezpečného stavu]
```
