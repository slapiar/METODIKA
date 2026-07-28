# Krok 13 — riadený STOP po produkčnom nasadení

Dátum: 2026-07-27 15:00 Europe/Bratislava

## Stav dokumentu

```text
PRACOVNÝ
KROK_13=UZAVRETÝ_RIADENÝM_STOP
GATE_KROKU_13=OPEN_NAPLNENÁ_A_UZAVRETÁ_PO_RIADENOM_STOP
```

## Predmet záznamu

Tento dokument uzatvára skutočný priebeh Kroku 13 bez domýšľania
nevykonaných alebo nedoložených častí produkčného scenára.

## Východisko

- autoritatívny repozitár: `slapiar/METODIKA`,
- vetva: `main`,
- auditovaný nasadzovaný release: `1.1.16`,
- release commit: `fb243698b3811ddf66ad772f89c2e171aa5bc3de`,
- SHA-256 ZIP-u:
  `04742c8b9075acc1fc280326e653ca6b7be57f514a0dc756c141560d5d062668`,
- pôvodný INI:
  `postupy/WORK/INI/2026-07-27_13-59_INI_Krok_13_Jeden_produkcny_run.md`.

## Vykonaný produkčný priebeh

1. Autorita nasadila release `1.1.16` priamo na Hostinger.
2. Overenie diagnostickým tokenom prešlo.
3. Databázová diagnostika `diagnostics/database` fungovala.
4. Vzhľad bol po nasadení bez CSS.
5. Po doplnení `METODIKA_GATE_ENABLED=1` sa zobrazili štyri GATE tlačidlá.
6. Priame porovnanie vzdialeného `main` s `1.1.15` preukázalo chýbajúci
   segment `public/` v URL spoločného CSS.
7. Autorita opravila cestu priamo na Hostingeri a potvrdila správny vzhľad.
8. Rovnaká oprava bola zapísaná do repozitára v commite `bd94a535...`.
9. V repozitári bol následne vytvorený opravný release `1.1.17`,
   release commit `cc9d48d95ff982b4ec7510e86e1d03f0734cf9de`.

## Dôkazné oddelenie

### Potvrdené

```text
DEPLOYMENT_1.1.16=true
TOKEN_VERIFICATION=PASS
DATABASE_DIAGNOSTICS=PASS
GATE_BUTTONS_VISIBLE_AFTER_ENV_UPDATE=true
CSS_ROOT_CAUSE=CONFIRMED
CSS_PRODUCTION_HOTFIX=PASS
CORRECTIVE_RELEASE_1.1.17=CREATED_AND_PUSHED
```

### Nepotvrdené

```text
runId=NEZAZNAMENANÉ
barrierOpened=NEZAZNAMENANÉ
timeoutReached_A=NEZAZNAMENANÉ
timeoutReached_B=NEZAZNAMENANÉ
outcomes=NEZAZNAMENANÉ
dbUniquenessConfirmed=NEZAZNAMENANÉ
appReplayConfirmed=NEZAZNAMENANÉ
cleanupConfirmed=NEZAZNAMENANÉ
overallSuccess=NEPOTVRDENÉ
state_COMPLETED_SUCCESS=NEPOTVRDENÉ
```

Úspech základnej diagnostiky nie je dôkazom úspechu celého súbežného
produkčného scenára.

## Rozhodnutie

Krok 13 sa uzatvára riadeným STOP. Nie je označený ako
`COMPLETED_SUCCESS`, pretože povinný výsledok produkčného scenára nemá
úplný dôkaz. Produkcia však nezostala v nefunkčnom stave: token,
databázová diagnostika aj vzhľad po priamej oprave fungovali.

```text
KROK_13=UZAVRETÝ_RIADENÝM_STOP
GATE_KROKU_13=OPEN_NAPLNENÁ_A_UZAVRETÁ_PO_RIADENOM_STOP
ROLLBACK_1.1.16=false
PRODUCTION_VERSION_AFTER_HOTFIX=1.1.16_S_PRIAMOU_OPRAVOU_CSS
REPOSITORY_RELEASE=1.1.17
DEPLOYMENT_1.1.17=NEPOTVRDENÉ
```

## Prechodová podmienka

Záväzný plán povoľuje otvorenie Kroku 14 po úspešnom produkčnom rune alebo
po riadenom STOP. Táto podmienka je splnená.

Pred prvým úkonom Kroku 14 sa musí nanovo načítať celý aktuálny vzdialený
súbor `postupy/Inicializácia práce.md` a vytvoriť samostatný INI Kroku 14.

```text
NEXT_ALLOWED_STEP=KROK_14_S_VLASTNÝM_INI_A_GATEM
KROK_14=NEOTVORENÝ
```
