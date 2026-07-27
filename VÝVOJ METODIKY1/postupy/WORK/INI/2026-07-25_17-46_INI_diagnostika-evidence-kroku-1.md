# INICIALIZÁCIA — diagnostika Evidence kroku 1

Dátum a čas: 2026-07-25 17:46 Europe/Bratislava
Projekt: METODIKA
Autoritatívny repozitár: `slapiar/METODIKA`
Autoritatívna vetva: `main`
Východiskový vzdialený HEAD kroku: `4a152f393f1255ca153a701e79f79860bb070bd1`
Predmet kroku: doplniť webovú diagnostiku, ktorá bezpečne vytvorí a načíta testovací Evidence záznam pre existujúci krok `id=1`, bez terminálu.

## Výsledok povinného overovania

1. Metodika načítaná: ÁNO
   - Overené novým načítaním `postupy/Inicializácia práce.md` z autoritatívneho repozitára.
   - Dôkaz: blob `1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19`.
   - Neoverené: nič relevantné pre predmet kroku.

2. Projekt a autoritatívny zdroj overený: ÁNO
   - Overené cez aktuálne metadata GitHub repozitára.
   - Výsledok: `slapiar/METODIKA`, autoritatívna vetva `main`, push oprávnenie dostupné.
   - Neoverené: nič relevantné pre predmet kroku.

3. Vetva a HEAD overené: ÁNO
   - Vetva `main` potvrdená cez metadata repozitára a všetky dotknuté súbory boli nanovo načítané z `main`.
   - Východiskový HEAD po vytvorení INI záznamu: `4a152f393f1255ca153a701e79f79860bb070bd1`.
   - Neoverené: nič relevantné pre predmet kroku.

4. Potrebné prístupy prakticky overené: ÁNO
   - Čítanie overené načítaním modelu, API controlleru, routes, diagnostického view a existujúceho diagnostického controlleru.
   - Zápis overený vytvorením a aktualizáciou tohto INI záznamu.
   - Neoverené: nasadenie na produkciu zostáva samostatným úkonom používateľa.

5. Prostredie prakticky overené: ÁNO
   - Používateľ doložil úspešný produkčný výsledok session, kroku a `GET /api/gate/session/1/steps`.
   - Produkčná databáza obsahuje krok `id=1`, ktorý je povinnou závislosťou Evidence.
   - Neoverené: Evidence zápis — je predmetom následného testu po nasadení.

6. Závislosti kroku dostupné: ÁNO
   - `IniEvidenceModel` používa tabuľku `ini_evidence` a polia `step_id`, `type`, `content`, `created_at`.
   - `GateSupervisor::addEvidence()` a `getEvidence()` aj API routes už existujú.
   - Aktuálne `Routes.php`, `database.php` a `DiagnosticsGateStepController.php` boli načítané v celom relevantnom rozsahu.
   - Neoverené: nič relevantné pre implementáciu.

7. Predmet a hranice zásahu určené: ÁNO
   - Pridať samostatný diagnostický Evidence controller, jednu CSRF chránenú POST route a jeden formulár na diagnostickú stránku.
   - Bez migrácie, bez zmeny schémy DB a bez zmeny verejného API.

8. Kritérium úspechu určené: ÁNO
   - Tlačidlo `Vytvoriť testovací dôkaz` bude viditeľné na diagnostickej stránke.
   - Vytvorí najviac jeden jednoznačne označený testovací Evidence záznam pre `step_id=1`.
   - JSON odpoveď vráti `ok`, `created`, `evidence_id`, `count` a `evidence`.
   - `GET /api/gate/step/1/evidence` následne vráti rovnaký záznam.

9. Rollback určený: ÁNO
   - Odstrániť pridanú route, formulár a nový controller.
   - Databázový testovací záznam možno odstrániť samostatným kontrolovaným cleanup krokom; tento krok cleanup nevykonáva.

```text
GATE=OPEN
BLOKUJÚCI_BOD=ŽIADNY
CHÝBAJÚCI_DÔKAZ=ŽIADNY PRE IMPLEMENTÁCIU
POVOLENÝ_ĎALŠÍ_ÚKON=implementácia diagnostiky Evidence v určených hraniciach
```
