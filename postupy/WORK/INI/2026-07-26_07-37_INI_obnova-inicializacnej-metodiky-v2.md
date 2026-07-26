# INICIALIZÁCIA KROKU: Obnova inicializačnej metodiky v2.0 po chybnom merge

## Kontrolná matica (Dôkaz je povinný, inak = NEOVERENÉ)
1. Metodika načítaná: ÁNO | Dôkaz: aktuálny `postupy/Inicializácia práce.md`, blob `4c69dbd23fccb5c01250ec5be86dec63dadb67ca`; potvrdená verzia v2.0, blob `44729126508a0c9151fb2358badcb1445a425bd6`
2. Projekt a autoritatívny zdroj: ÁNO | Dôkaz: `slapiar/METODIKA`, vetva `main`, cesta `postupy/Inicializácia práce.md`
3. Vetva a HEAD: NEOVERENÉ | Dôkaz: aktuálny HEAD sa potvrdí po vytvorení tohto INI
4. Prístupy (read/write): NEOVERENÉ | Dôkaz: čítanie potvrdené; zápis sa potvrdí týmto commitom a read-backom
5. Prostredie a runtime: ÁNO | Dôkaz: úkon je výlučne vzdialená obnova Markdown súboru cez GitHub API; `/codei` runtime je mimo rozsahu
6. Závislosti kroku: ÁNO | Dôkaz: dostupný aktuálny súbor, potvrdený historický blob v2.0, register a changelog
7. Predmet a hranice zásahu: ÁNO | Dôkaz: obnova iba `postupy/Inicializácia práce.md` a povinné zosúladenie `postupy/README.md`, `CHANGELOG.md` a WORK záznamu
8. Kritérium úspechu: ÁNO | Dôkaz: výsledný blob `postupy/Inicializácia práce.md` musí byť zhodný s `44729126508a0c9151fb2358badcb1445a425bd6`; read-back musí potvrdiť úplných 94 riadkov
9. Rollback plán: ÁNO | Dôkaz: obnoviť dnešný chybný blob `4c69dbd23fccb5c01250ec5be86dec63dadb67ca` a novým evidenčným zápisom zosúladiť register a changelog

## Stav Brány
GATE = CLOSED
BLOKUJÚCI_BOD = 3, 4
POVOLENÝ_ĎALŠÍ_ÚKON = Iba overenie aktuálneho HEAD, zápisu a read-backu tohto INI

## Matica plnenia pokynov
| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---|---|
| ID-01 až ID-14 | 1 | 1 |
