# INICIALIZÁCIA — diagnostika Evidence kroku 1

Dátum a čas: 2026-07-25 17:46 Europe/Bratislava
Projekt: METODIKA
Autoritatívny repozitár: `slapiar/METODIKA`
Autoritatívna vetva: `main`
Predmet kroku: doplniť webovú diagnostiku, ktorá bezpečne vytvorí a načíta testovací Evidence záznam pre existujúci krok `id=1`, bez terminálu.

## Stav brány počas overovania

1. Metodika načítaná: ÁNO
   - Overené novým načítaním `postupy/Inicializácia práce.md` z autoritatívneho repozitára.
   - Dôkaz: blob `1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19` načítaný v aktuálnej práci.
   - Neoverené: nič relevantné pre predmet kroku.

2. Projekt a autoritatívny zdroj overený: ÁNO
   - Overené cez metadata repozitára GitHub.
   - Výsledok: repozitár `slapiar/METODIKA`, predvolená a autoritatívna vetva `main`, oprávnenie push dostupné.
   - Dôkaz: aktuálne metadata GitHub repozitára načítané v tejto práci.

3. Vetva a HEAD overené: NEOVERENÉ
   - Vetva `main` potvrdená.
   - Chýba samostatne doložený aktuálny HEAD po poslednej zmene.

4. Potrebné prístupy prakticky overené: ÁNO
   - Čítanie overené načítaním aktuálnych zdrojov z `main`.
   - Zápis overený vytvorením tohto povoleného INI záznamu.

5. Prostredie prakticky overené: ÁNO
   - Používateľ doložil úspešné produkčné načítanie session, kroku a API `GET /api/gate/session/1/steps`.
   - Produkčná databáza obsahuje krok `id=1`.

6. Závislosti kroku dostupné: NEOVERENÉ
   - Model `IniEvidenceModel` a metódy `addEvidence()`/`getEvidence()` boli načítané.
   - Ešte treba načítať aktuálne Routes, diagnostický view a existujúci diagnostický controller.

7. Predmet a hranice zásahu určené: ÁNO
   - Zásah iba do diagnostickej webovej vrstvy, routy a samostatného diagnostického controlleru pre Evidence.
   - Bez migrácie, bez zmeny schémy DB, bez zásahu do existujúceho verejného API.

8. Kritérium úspechu určené: ÁNO
   - Na diagnostickej stránke bude tlačidlo `Vytvoriť testovací dôkaz`.
   - POST požiadavka vytvorí najviac jeden testovací Evidence záznam pre `step_id=1` a vráti JSON so zoznamom dôkazov.
   - Následné `GET /api/gate/step/1/evidence` vráti rovnaký záznam.

9. Rollback určený: ÁNO
   - Odstrániť pridanú diagnostickú route, formulár a nový diagnostický controller.
   - Databázový testovací záznam možno odstrániť samostatným kontrolovaným cleanup krokom; tento krok cleanup nevykonáva.

```text
GATE=CLOSED
BLOKUJÚCI_BOD=3 a 6 — aktuálny HEAD a úplný obsah dotknutých diagnostických súborov
CHÝBAJÚCI_DÔKAZ=nové načítanie Routes.php, database.php, existujúceho diagnostického controlleru a doloženie aktuálneho vzdialeného stavu
POVOLENÝ_ĎALŠÍ_ÚKON=iba dokončenie čítania a aktualizácia tohto INI záznamu
```
