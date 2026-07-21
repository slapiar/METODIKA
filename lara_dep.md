# Deploy plan: METODIKA -> lara (Hostinger)

Dátum prípravy: 2026-07-21
Vetva: joyee-priority
Cieľ: /home/u550121827/domains/dremont.in/public_html/lara

## Cieľ
Prepojiť repozitár tak, aby sa nasadzoval iba obsah priečinka `app/` do cieľového priečinka `lara/`.

## Čo bolo dohodnuté
- Zdroj kódu bude mimo web root: `/home/u550121827/repos/metodika-src`
- Web root aplikácie: `/home/u550121827/domains/dremont.in/public_html/lara`
- Nasadenie bude cez skript: `git pull + rsync`
- Citlivé súbory sa nemajú prepisovať z repozitára (najmä `db.env`)

## Jednorazové kroky na serveri
```bash
mkdir -p /home/u550121827/repos
cd /home/u550121827/repos
git clone -b joyee-priority git@github.com:slapiar/METODIKA.git metodika-src
mkdir -p /home/u550121827/domains/dremont.in/public_html/lara
```

## Deploy skript na serveri
Vytvoriť súbor `/home/u550121827/deploy-metodika.sh`:

```bash
#!/usr/bin/env bash
set -e

SRC="/home/u550121827/repos/metodika-src/app/"
DST="/home/u550121827/domains/dremont.in/public_html/lara/"

cd /home/u550121827/repos/metodika-src
git fetch origin
git checkout joyee-priority
git pull --ff-only origin joyee-priority

rsync -av --delete \
  --exclude ".git" \
  --exclude "db.env" \
  "$SRC" "$DST"
```

Následne:
```bash
chmod +x /home/u550121827/deploy-metodika.sh
/home/u550121827/deploy-metodika.sh
```

## Každé ďalšie nasadenie
1. Lokálne: commit + push do `joyee-priority`
2. Na serveri:
```bash
/home/u550121827/deploy-metodika.sh
```

## Dôležitá poznámka k bezpečnosti
- V `app/db.env` je chybná koncová úvodzovka pri `DB_PASSWORD` (extra `"`).
- Heslo zdieľané v texte považovať za kompromitované a zmeniť ho v DB aj v produkčnom configu.

## Zajtra spolu dokončíme
- Oprava `app/db.env`
- Pridanie deploy skriptu aj do repozitára
- Krátka deploy sekcia do README
- Rýchly test nasadenia krok za krokom
