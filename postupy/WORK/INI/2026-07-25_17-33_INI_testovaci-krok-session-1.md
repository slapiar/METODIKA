# INICIALIZÁCIA KROKU – testovací krok session 1

1. Metodika načítaná: ÁNO
   - Overené: aktuálny obsah `postupy/Inicializácia práce.md`.
   - Úkon: priame načítanie zo vzdialeného repozitára.
   - Výsledok: potvrdená povinnosť samostatného INI záznamu a deviatich doložených bodov.
   - Dôkaz: blob `1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19`.
   - Neoverené: nič relevantné pre tento úzky krok.

2. Projekt a autoritatívny zdroj overený: ÁNO
   - Overené: repozitár `slapiar/METODIKA`, koreň aplikácie `codei/`.
   - Úkon: priame čítanie aktuálnych súborov z GitHubu.
   - Výsledok: potvrdený projekt METODIKA a dotknutá aplikácia CodeIgniter.
   - Dôkaz: aktuálne súbory `DiagnosticsController.php`, `Routes.php`, `database.php`, `IniStepModel.php`.
   - Neoverené: produkčný filesystem nie je predmetom zápisu; nasadenie vykoná release mechanizmus používateľa.

3. Vetva a HEAD overené: ÁNO
   - Overené: autoritatívna vetva `main`, aktuálny vzdialený HEAD `45df1142865a57fd6e50849e76dee44870864655`.
   - Úkon: priame vyhľadanie aktuálnych súborov v repozitári.
   - Výsledok: všetky dotknuté súbory pochádzajú z rovnakého HEAD.
   - Dôkaz: GitHub blob URL výsledkov obsahuje uvedený commit.
   - Neoverené: lokálny pracovný strom používateľa; pre zápis cez GitHub Contents API nie je potrebný.

4. Potrebné prístupy prakticky overené: ÁNO
   - Overené: čítanie aj zápis do repozitára.
   - Úkon: načítanie dotknutých súborov a vytvorenie tohto INI záznamu.
   - Výsledok: prístupy fungujú.
   - Dôkaz: commit vytvorený zápisom tohto súboru.
   - Neoverené: hosting a databáza; ich zmena nie je súčasťou implementácie.

5. Prostredie prakticky overené: ÁNO
   - Overené: aktuálna diagnostická stránka už úspešne vytvorila session `id=1` a načítala ju cez API.
   - Úkon: výsledky produkčného testu dodané používateľom v tomto pracovnom vlákne.
   - Výsledok: databázové pripojenie, tabuľka `ini_sessions` a session 1 fungujú.
   - Dôkaz: odpovede `count=1` a `/api/gate/session/1`.
   - Neoverené: tabuľka `ini_steps`; práve tá je predmetom nasledujúceho testu.

6. Závislosti kroku dostupné: ÁNO
   - Overené: `IniStepModel`, tabuľkové polia používané modelom, session `id=1`, diagnostická stránka, CSRF route mechanizmus.
   - Úkon: priame načítanie `codei/app/Models/IniStepModel.php`, `Routes.php` a view.
   - Výsledok: potrebné kódové závislosti existujú.
   - Dôkaz: blob modelu `be4da46c21b636794903fd373b32434da4784d7b`.
   - Neoverené: skutočná existencia tabuľky `ini_steps`; bude potvrdená výsledkom testu.

7. Predmet a hranice zásahu určené: ÁNO
   - Predmet: pridať na diagnostickú stránku tlačidlo, route a bezpečný controller pre vytvorenie jedného testovacieho kroku viazaného na session 1.
   - Hranice: bez migrácie, bez zmeny databázovej schémy, bez zásahu do produkcie, bez úpravy existujúcej session.
   - Dôkaz: tento INI záznam.
   - Neoverené: nič.

8. Kritérium úspechu určené: ÁNO
   - Po stlačení tlačidla vznikne krok so `session_id=1`, `step_number=1`, názvom `Inicializácia`, stavom `valid` a vyplneným `validated_at`.
   - Odpoveď bude platný JSON s `ok=true`, `step_id` a načítaným zoznamom krokov session 1.
   - Následný GET `/api/gate/session/1/steps` vráti vytvorený záznam.
   - Dôkaz: tento INI záznam.
   - Neoverené: výsledok až po release a produkčnom teste.

9. Rollback určený: ÁNO
   - Kódový rollback: revert commitov tohto kroku.
   - Dátový rollback: odstrániť testovací riadok z `ini_steps` podľa vráteného `step_id`, ak bude potrebné.
   - Dôkaz: tento INI záznam.
   - Neoverené: konkrétne `step_id` vznikne až pri teste.

GATE=OPEN
