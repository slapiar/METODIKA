# Changelog

Podstatné zmeny projektu METODIKA sa od 21. júla 2026 zapisujú priebežne do tohto súboru.

## 2026-07-21

### Pridané

- vytvorený adresár `app/` ako koreň obslužného softvéru, ktorý bude súčasťou release,
- pridaný `app/setup.php` na vytvorenie lokálneho súboru `local-config.php`,
- setup podporuje konfiguráciu databáz `u550121827_metodic` a `u550121827_mapmet`,
- setup umožňuje uložiť ďalšie lokálne API kľúče a hodnoty vo formáte `NAZOV=hodnota`,
- pridaný `.gitignore` pre `local-config`, `local-config.php`, lokálne `.env` súbory a prevádzkové logy.

### Určené

- `local-config.php` sa vytvára v koreni projektu a nie je súčasťou release,
- obslužné PHP skripty sa ukladajú do koreňa `/app`,
- podstatné zmeny sa od tohto bodu zapisujú priebežne do `CHANGELOG.md`.
