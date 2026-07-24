# INICIALIZÁCIA — odstránenie omylom obnoveného workflowu Kroku 8

1. Metodika načítaná: ÁNO
   - Overené aktuálnym čítaním `postupy/Inicializácia práce.md`.
2. Projekt a autoritatívny zdroj overený: ÁNO
   - Projekt `METODIKA`, repozitár `slapiar/METODIKA`, autoritatívna vetva `main`.
3. Vetva a HEAD overené: ÁNO
   - Aktuálny `main` obsahuje commit `da191dd...`; pred ním už existuje automatický finálny commit Kroku 8 `6bcfd98...`.
4. Potrebné prístupy prakticky overené: ÁNO
   - Čítanie aj zápis do repozitára boli v tomto kroku prakticky potvrdené.
5. Prostredie prakticky overené: ÁNO
   - Nevykonáva sa runtime, databázový ani produkčný zásah; ide iba o odstránenie jednorazového workflow súboru.
6. Závislosti kroku dostupné: ÁNO
   - `postupy/README.md` potvrdzuje Kroky 1–8 ako `SPLNENÉ` a Krok 9 ako jediný ďalší povolený krok.
7. Predmet a hranice zásahu určené: ÁNO
   - Odstrániť iba `.github/workflows/krok-8-diagnostic-fix.yml`, ktorý bol po úspešnej finalizácii omylom znovu vytvorený.
   - Nemeniť vykonateľný kód, testy, dokumentáciu Kroku 8 ani produkciu.
8. Kritérium úspechu určené: ÁNO
   - Workflow súbor neexistuje na `main`; finálny commit Kroku 8, pracovný záznam, register a `CHANGELOG.md` zostanú zachované.
9. Rollback určený: ÁNO
   - V prípade omylu obnoviť odstránený workflow z commitu `da191dd...`; jeho opätovné spúšťanie však nie je súčasťou rollbacku.

```text
GATE=OPEN
```
