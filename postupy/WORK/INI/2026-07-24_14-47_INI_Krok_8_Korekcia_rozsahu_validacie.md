# INICIALIZÁCIA KROKU 8 — korekcia rozsahu Validácie

1. Metodika načítaná: ÁNO
   - Overené: `postupy/Inicializácia práce.md`, záväzný plán Kroku 8 a výsledok workflow runu `30094146624`.
   - Úkon: nové načítanie aktuálnych súborov a presného testového výstupu.
   - Výsledok: Krok 8 má testovať iba diagnostické fázy a neprítomnosť raw exception textu; bariéra, `load()` a UI sú mimo jeho zásahu.
   - Dôkaz: run `30094146624`, priložený log a aktuálny repozitár.
   - Neoverené: nič potrebné pre túto korekciu.

2. Projekt a autoritatívny zdroj overený: ÁNO
   - Projekt: METODIKA.
   - Repozitár: `slapiar/METODIKA`.
   - Autoritatívna vetva: `main`.
   - Dôkaz: `PROJEKTY/ZoznamProjektov.md`.

3. Vetva a HEAD overené: ÁNO
   - Vetva: `main`.
   - Východiskový HEAD: `61ba1a9a409aaad2ec5494021231a283906fe9a6`.
   - Dôkaz: checkout workflowu runu `30094146624`.

4. Potrebné prístupy prakticky overené: ÁNO
   - Čítanie logov, čítanie súborov a zápis do `main` boli prakticky overené predchádzajúcimi commitmi.
   - Produkčný, databázový ani hostingový prístup nie je potrebný.

5. Prostredie prakticky overené: ÁNO
   - GitHub Actions: Ubuntu 24.04, PHP 8.4.23, Composer 2.
   - Patch a syntax Validácia prešli.
   - Reporter testy: 6/6 prešli.
   - Dôkaz: run `30094146624`.

6. Závislosti kroku dostupné: ÁNO
   - `DiagnosticsConcurrencyFailureReporterTest.php` pokrýva všetky diagnostické fázy a klasifikácie.
   - Nový session test `testApplicationFailurePersistsOnlySafePhaseCode` overuje bezpečný kód a neprítomnosť raw výnimky.
   - Tri ostatné zlyhania patria mimo rozsahu Kroku 8.

7. Predmet a hranice zásahu určené: ÁNO
   - Povolené: zúžiť workflow testovací filter na nový session test Kroku 8 a opraviť text finalizačného záznamu.
   - Zakázané: meniť `load()`, bariéru, timeout, UI alebo funkčnú rezerváciu.

8. Kritérium úspechu určené: ÁNO
   - Prejde syntax dotknutých súborov.
   - Prejde 6 reporter testov.
   - Prejde presne `DiagnosticsControllerTest::testApplicationFailurePersistsOnlySafePhaseCode`.
   - Finalizácia zaznamená tri mimo-rozsahové zlyhania ako otvorené dôkazy, nie ako splnené.

9. Rollback určený: ÁNO
   - Vrátiť commit korekcie workflowu/finalizácie.
   - Diagnostický ani funkčný kód aplikácie sa touto korekciou nemení.

GATE=OPEN
