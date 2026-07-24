# INICIALIZÁCIA OPRAVY VALIDÁCIE KROKU 8

Dátum: 2026-07-24
Projekt: METODIKA
Repozitár: slapiar/METODIKA
Vetva: main
Východiskový commit: 7d68b262ff96ae4f1423263e8e439e7f37131686

## Povinná brána

1. Metodika načítaná: ÁNO
- Overené: aktuálny dokument `postupy/Inicializácia práce.md`.
- Úkon: nové načítanie z autoritatívnej vetvy `main`.
- Výsledok: povinnosť samostatného INI záznamu a dôkazovej brány potvrdená.
- Dôkaz: blob `201bcaef882e21e99c484b2c9acd61489e1e9914`.
- Neoverené: nič podstatné pre tento zásah.

2. Projekt a autoritatívny zdroj overený: ÁNO
- Overené: projekt METODIKA, repozitár `slapiar/METODIKA`, autoritatívna vetva `main`.
- Úkon: načítanie `PROJEKTY/ZoznamProjektov.md`.
- Výsledok: projekt a vetva potvrdené.
- Dôkaz: blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`.
- Neoverené: nič.

3. Vetva a HEAD overené: ÁNO
- Overené: workflow run 30093709757 bežal na commite `7d68b262ff96ae4f1423263e8e439e7f37131686` na vetve `main`.
- Úkon: načítanie jobu a logu GitHub Actions.
- Výsledok: východiskový stav jednoznačne potvrdený.
- Dôkaz: job 89482636003 a log runu 30093709757.
- Neoverené: nič.

4. Potrebné prístupy prakticky overené: ÁNO
- Overené: čítanie workflow logov a zápis do repozitára.
- Úkon: úspešné načítanie jobu/logu a vytvorenie tohto INI záznamu.
- Výsledok: potrebné GitHub prístupy fungujú.
- Dôkaz: commit tohto súboru.
- Neoverené: ručný `workflow_dispatch`; nie je potrebný, použije sa push trigger.

5. Prostredie prakticky overené: ÁNO
- Overené: GitHub Actions runner Ubuntu 24.04, PHP 8.4.23, Composer a PHPUnit 11.5.56.
- Úkon: analýza logu runu 30093709757.
- Výsledok: syntax prešla a 6/6 testov reportéra prešlo.
- Dôkaz: krok `Run diagnostic phase tests` v run 30093709757.
- Neoverené: session test sa nespustil, pretože prvý PHPUnit proces skončil kódom 1 na warningu.

6. Závislosti kroku dostupné: ÁNO
- Overené: locknuté závislosti, PHP 8.4, PHPUnit a patchovací skript.
- Úkon: úspešné kroky `Install locked dependencies`, `Apply exact Krok 8 patch`, `Syntax validation`.
- Výsledok: všetky technické závislosti sú dostupné.
- Dôkaz: run 30093709757.
- Neoverené: výsledok po oprave validačného príkazu.

7. Predmet a hranice zásahu určené: ÁNO
- Predmet: zmeniť iba PHPUnit CLI príkazy vo workflowe tak, aby warning o chýbajúcom coverage driveri neprepisoval úspech testov.
- Hranice: nemení sa produkčný kód, testy, phpunit konfigurácia ani diagnostická logika.
- Dotknutý súbor: `.github/workflows/krok-8-diagnostic-fix.yml`.
- Neoverené: nič.

8. Kritérium úspechu určené: ÁNO
- Oba testovacie príkazy sa vykonajú.
- 6/6 unit testov reportéra prejde.
- `DiagnosticsControllerTest` prejde.
- Workflow pokračuje finalizáciou dokumentácie a commitom Kroku 8.

9. Rollback určený: ÁNO
- Rollback: vrátenie jediného commitu úpravy workflowu.
- Produkčný rollback nie je potrebný; produkcia sa nemení.

GATE=OPEN

POVOLENÝ_ÚKON=Upraviť iba validačné PHPUnit príkazy vo workflowe Kroku 8 a znovu spustiť push-triggerovanú Validáciu.
