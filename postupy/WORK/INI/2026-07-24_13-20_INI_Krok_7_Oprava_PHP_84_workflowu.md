# INICIALIZÁCIA KROKU 7 — Oprava PHP 8.4 vo workflowe

## Identifikácia

```text
dátum a čas: 2026-07-24 13:20 Europe/Bratislava
projekt: METODIKA
činnosť: oprava runtime GitHub Actions workflowu pre Krok 7
autoritatívny repozitár: slapiar/METODIKA
autoritatívna vetva: main
HEAD pri overení: 9ab1df7
workflow run: 30088813520
job: 89467181640
```

## Povinná brána

1. Metodika načítaná: ÁNO
   - Dôkaz: aktuálny dokument `postupy/Inicializácia práce.md` a záväzný Krok 7 boli načítané pred zásahom.

2. Projekt a autoritatívny zdroj overený: ÁNO
   - Dôkaz: projekt METODIKA, repozitár `slapiar/METODIKA`, vetva `main`.

3. Vetva a HEAD overené: ÁNO
   - Dôkaz: používateľský push presunul `main` na commit `9ab1df7`.

4. Potrebné prístupy prakticky overené: ÁNO
   - Dôkaz: čítanie workflowu, workflow runu a logov; zápis tohto INI záznamu.

5. Prostredie prakticky overené: ÁNO
   - Dôkaz: GitHub Actions runner `ubuntu-24.04`; MariaDB 11.4 service container sa inicializoval a bol healthy; zlyhal iba Composer na PHP 8.3.6.

6. Závislosti kroku dostupné: ÁNO
   - Dôkaz: `composer.lock` vyžaduje PHP >= 8.4.1; oficiálny `shivammathur/setup-php@v2` podporuje PHP 8.4 a rozšírenia `mysqli`, `intl`, `mbstring`, `xml`, `curl`.

7. Predmet a hranice zásahu určené: ÁNO
   - Predmet: nahradiť systémovú inštaláciu PHP 8.3 krokom, ktorý explicitne pripraví PHP 8.4 s potrebnými rozšíreniami a Composerom.
   - Hranice: iba `.github/workflows/krok-7-root-cause-reproduction.yml`; bez zmeny aplikácie, databázovej schémy, produkcie alebo reprodukčného príkazu.

8. Kritérium úspechu určené: ÁNO
   - Workflow vypíše PHP 8.4.x, `composer install` prejde a následné databázové kroky sa vykonajú.

9. Rollback určený: ÁNO
   - Obnovenie predchádzajúceho blobu workflowu `09761cb31baf08b2a81280da566ce2b8296c646d`; databázový service container po behu automaticky zaniká.

```text
GATE=OPEN
POVOLENÝ_ÚKON=Upraviť výhradne PHP setup vo workflowe Kroku 7 a spätne načítať výsledok
```
