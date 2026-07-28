# Cleanup názvov ciest bez diakritiky

Tento pomocný návod patrí k technickému cleanupu názvov ciest bez diakritiky.

## Cieľ

Odstrániť diakritiku z názvov adresárov a súborov bez zmeny významu dokumentov.

## Postup

V koreňovom adresári repozitára spustiť najprv kontrolu:

```bash
python3 tools/rename_paths_without_diacritics.py --dry-run
```

Ak výpis neukáže kolíziu názvov, vykonať zmenu:

```bash
python3 tools/rename_paths_without_diacritics.py --apply
```

Potom skontrolovať stav:

```bash
git status --short
```

## Čo skript robí

- prejde gitom sledované súbory,
- premenuje iba cesty s diakritikou,
- medzery ponechá,
- veľkosť písmen ponechá,
- aplikuje nové názvy v textových súboroch,
- nemení produkciu,
- nemení databázu.
