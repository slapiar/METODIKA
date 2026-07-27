# INICIALIZÁCIA KROKU: Krok 14 — tombstone, sweep a úplný produkčný cleanup

Dátum: 2026-07-27 15:07 Europe/Bratislava

## Kontrolná matica (Dôkaz je povinný, inak = NEOVERENÉ)

1. Metodika načítaná: ÁNO | Dôkaz: celý aktuálny vzdialený súbor
   `postupy/Inicializácia práce.md` bol po zlúčení Kroku 13 nanovo načítaný
   z `main`; blob `4b73ad567bbc78a30e7cc2066b3fcada73b0022d`.
2. Projekt a autoritatívny zdroj: ÁNO | Dôkaz: `slapiar/METODIKA`, vetva
   `main`; záväzný rámcový plán
   `postupy/PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md`;
   Krok 13 je uzavretý riadeným STOP.
3. Vetva a HEAD: ÁNO | Dôkaz:
   `main=b3766f0cd11bb457bdbcafcec78c6d08f6669025`; vzdialený read-back
   potvrdil evidenciu Kroku 13 a `RELEASE_VERSION=1.1.17`.
4. Prístupy (read/write): ÁNO | Dôkaz: GitHub konektor vykonal čítanie,
   zápis, PR a merge Kroku 13; Autorita preukázala nasadenie, úpravu `.env`
   a priamu kontrolu produkčného webu na Hostingeri pri release `1.1.16`.
   Produkčné úkony vykonáva Autorita obvyklým webovým rozhraním; SSH nie je
   podmienkou.
5. Prostredie a runtime: ÁNO | Dôkaz: produkcia po nasadení `1.1.16`
   úspešne overila diagnostický token a `diagnostics/database`; databázové
   spojenie fungovalo a po priamej oprave CSS sa stránka načítala správne.
   Aktuálne diagnostické flagy sú
   `METODIKA_DIAGNOSTICS_ENABLED=1`,
   `METODIKA_CONCURRENCY_WEB_ENABLED=1` a
   `METODIKA_GATE_ENABLED=1`.
6. Závislosti kroku: ÁNO | Dôkaz: release
   `releases/metodika-codei-hostinger-1.1.17.zip` je na `main`, Git blob
   `486267e8d812d5dfee568c21c23663074e0e33d3`, SHA-256
   `c2ea8b5dced1d47e7d4b47cfe5294154eb1ee04b975ff1ea10c7669a3506ea22`,
   813 súborov; ZIP obsahuje
   `asset_url('public/assets/css/app.css')`. Produkčný read-only panel
   `DiagnosticsProductionStateInspector` zobrazuje nasadenú verziu, tri
   flagy, migrácie, počty šiestich sledovaných tabuliek a počty
   JSON/lock/temp/iných run-store súborov. Implementácia tombstone, sweep,
   run-store a príslušné testy majú rovnaké Git bloby ako validovaný Krok 11.
7. Predmet a hranice zásahu: ÁNO | Dôkaz: presne jedno nasadenie release
   `1.1.17`, jeden read-only odpis produkčného panelu, vyhodnotenie nulových
   zvyškov a následné vypnutie troch diagnostických flagov. Žiadny nový
   diagnostický run, žiadne tlačidlo GATE, žiadna migrácia, funkčná oprava
   ani ďalší release. Ak ktorýkoľvek sledovaný počet nie je nula, nasleduje
   STOP pred vypnutím diagnostiky a presná identifikácia zvyškov; žiadne
   plošné mazanie naslepo.
8. Kritérium úspechu: ÁNO | Dôkaz: záväzný Krok 14 vyžaduje
   `TOMBSTONE_CONTRACT=true`, `SWEEP=true`, `RUNSTORE_FILES=0`,
   `TEMP_FILES=0`, `DB_TEST_ROWS=0`, `GATE_TEST_ROWS=0`,
   `FEATURE_FLAGS=OFF`, `DIAGNOSTIC_MODE=OFF` a
   `PRODUCTION_CLEAN=true`.
9. Rollback plán: ÁNO | Dôkaz: release `1.1.17` nemení migrácie ani
   databázovú schému. Pri regresii sa znovu nasadí auditovaný ZIP `1.1.16`
   a obnoví sa jediná potvrdená produkčná oprava cesty CSS s `public/`.
   Pri probléme po vypnutí flagov sa obnovia ich predchádzajúce hodnoty `1`.
   Keďže tento krok nevykonáva plošné mazanie naslepo, databázový rollback
   sa bez konkrétne identifikovaného zásahu nespúšťa.

## Znovupoužitý dôkaz tombstone a sweep

Nasledujúce vykonateľné súbory a testy sú medzi finálnym validačným merge
Kroku 11 `4cb2fe0a...` a release `1.1.17` byte-identické:

```text
DiagnosticsController.php                         247583d54c508b20b7ca4a58cd8b814a47a74842
DiagnosticsConcurrencyRunStore.php                dfac24e9f7d452637a5eb02a4e9ea25f0bec9a1f
DiagnosticsConcurrencyTombstoneCleaner.php        65d309d7c06b8b575b6a86cd6523c52602406c82
DiagnosticsProductionStateInspector.php           6a80f9fcc1bf4b06b3f293e96f3a52ce7e3cb40e
DiagnosticsControllerTest.php                     566d9df27968be3948c795ab5febc2fe9e06bcc5
DiagnosticsConcurrencyTombstoneCleanerTest.php    974fa1f649707505f3ca967f5de6741eda6203e9
```

Preto platí už vykonaný dôkaz Kroku 11 o `readOnceConsumedAt`, redukcii na
tombstone, `deleteAfter`, sweep a odstránení JSON/lock/temp súborov. Krok 14
ho neopakuje; na produkcii overí iba skutočný nulový koncový stav.

## Povinné vykonávacie poradie

1. Autorita nasadí presne release `1.1.17`.
2. Diagnostické flagy zatiaľ zostanú zapnuté.
3. Po tokenovom prihlásení sa na `diagnostics/database` načíta read-only
   panel „Aktuálny produkčný stav“.
4. Zaznamená sa:
   - nasadená verzia `1.1.17`,
   - aktuálny čas odpisu,
   - počty riadkov v `ini_sessions`, `ini_steps`, `ini_evidence`,
     `question_derivation_request_reservations`,
     `question_derivation_runs` a
     `question_derivation_run_domain_terms`,
   - počty JSON, lock, temp a iných run-store súborov.
5. Ak je ktorýkoľvek uvedený počet nenulový alebo `NEZISTENÉ`, nastane STOP.
   Flagy sa ešte nevypnú a nič sa nemaže naslepo.
6. Ak sú všetky počty nula, vykoná sa logout diagnostickej session.
7. V `.env` sa nastavia:

   ```text
   METODIKA_DIAGNOSTICS_ENABLED=0
   METODIKA_CONCURRENCY_WEB_ENABLED=0
   METODIKA_GATE_ENABLED=0
   ```

8. Priamy prístup na `diagnostics/database` musí po vypnutí skončiť
   fail-closed odpoveďou bez diagnostického obsahu.
9. Výsledok sa zapíše do WORK záznamu, rámcového plánu, registra a
   CHANGELOG-u; až potom možno Krok 14 uzavrieť.

## Stav Brány

```text
GATE=OPEN
BLOKUJÚCI_BOD=ŽIADNY
POVOLENÝ_ĎALŠÍ_ÚKON=NASADENIE_RELEASE_1.1.17_AUTORITOU_A_READ_ONLY_ODPIS_PRODUKCNEHO_STAVU
KROK_14=OTVORENÝ
KROK_15=ZATVORENÝ
```

## Matica plnenia pokynov

| ID pokynu | R (Prečítané) | W (Zapísané) |
|---|---:|---:|
| ID-01 až ID-14 | 1 | 1 |

---

## Produkčný výsledok a uzavretie — 2026-07-27 15:16 Europe/Bratislava

Autorita vykonala povinné produkčné poradie pre release `1.1.17`. Po
read-only kontrole nulového koncového stavu pokračovala povolenou vetvou,
odhlásila diagnostickú session a nastavila:

```text
METODIKA_DIAGNOSTICS_ENABLED=0
METODIKA_CONCURRENCY_WEB_ENABLED=0
METODIKA_GATE_ENABLED=0
```

Následný priamy prístup na `diagnostics/database` už diagnostický obsah
nezobrazil. Tým je doložený požadovaný fail-closed stav. Technický dôkaz
kontraktu tombstone a sweep zostáva znovupoužitý z Kroku 11, pretože
príslušná implementácia a testy sú v release `1.1.17` byte-identické.

```text
DEPLOYED_RELEASE=1.1.17
TOMBSTONE_CONTRACT=true
SWEEP=true
RUNSTORE_FILES=0
TEMP_FILES=0
DB_TEST_ROWS=0
GATE_TEST_ROWS=0
FEATURE_FLAGS=OFF
DIAGNOSTIC_MODE=OFF
DIAGNOSTICS_DATABASE_CONTENT=HIDDEN_FAIL_CLOSED
PRODUCTION_CLEAN=true
```

## Konečný stav brány

```text
GATE=OPEN
GATE_STATUS=OPEN_NAPLNENÁ_A_UZAVRETÁ_PO_VYKONANÍ
BLOKUJÚCI_BOD=ŽIADNY
KROK_14=SPLNENÝ
KROK_15=NEOTVORENÝ
NEXT_ALLOWED_STEP=KROK_15_S_VLASTNÝM_INI_A_GATEM
```
