# Register stavov dokumentov v `postupy/`

## Účel

Tento súbor je záväzným registrom aktuálneho pracovného stavu dokumentov uložených v adresári `postupy/`.

Dokument v tomto adresári nie je autoritatívnou definíciou iba preto, že obsahuje ucelený návrh alebo záver. Jeho platnosť určuje stav uvedený v tomto registri a prípadný odkaz na autoritatívny dokument.

## Povolené stavy

```text
PRACOVNÝ
PRACOVNÝ — ZÁVÄZNÝ
PRACOVNÝ — ZÁVÄZNÝ / UZAVRETÝ_SPLNENÝ
POTVRDENÝ-NA-PRENESENIE
ČIASTOČNE-PREVZATÝ
PREKONANÝ
NEPLATNÝ
ARCHIVOVANÝ
```

## Aktuálny stav po uzavretí plánu — 2026-07-27

```text
PROJEKT=METODIKA
REPOZITÁR=slapiar/METODIKA
VETVA=main
PLÁN=postupy/PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md
KROKY_1_AŽ_15=SPLNENÉ
VŠETKY_GATE=OPEN_NAPLNENÉ_A_UZAVRETÉ
PRODUKČNÝ_RELEASE=1.1.17
PRODUKČNÝ_RELEASE_COMMIT=cc9d48d95ff982b4ec7510e86e1d03f0734cf9de
PRODUKČNÝ_ZIP_BLOB=486267e8d812d5dfee568c21c23663074e0e33d3
PRODUKČNÝ_VÝSLEDOK=POTVRDENÝ_AUTORITOU
PRODUKCIA_CLEAN=true
DIAGNOSTIKA=OFF
FEATURE_FLAGS=OFF
PLÁN=SPLNENÝ
NEXT_ALLOWED_STEP=TVORBA_WEBOVEJ_STRÁNKY_S_PREDPRIPRAVENÝMI_TESTAMI
```

Autorita po Kroku 15 potvrdila, že produkčný výsledok plánu bol splnený. Tým sa evidenčne uzatvára celý plán Krokov 1 až 15. Všetky GATE sú vedené ako otvorené, naplnené a uzavreté podľa príslušných pracovných záznamov.

## Aktuálny register

| Dokument | Stav | Autoritatívny cieľ alebo poznámka |
|---|---|---|
| `PLAN/2026-07-26_06-07_Plan_dokoncenia_testovacej_sustavy.md` | PRACOVNÝ — ZÁVÄZNÝ / UZAVRETÝ_SPLNENÝ | Rámcový plán Krokov 11 až 15 je splnený. Kroky 1 až 15 sú uzavreté, všetky GATE sú `OPEN_NAPLNENÉ_A_UZAVRETÉ`, produkčný release je `1.1.17` a produkčný výsledok je potvrdený Autoritou. |
| `WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Krok 11 je splnený. Lokálna a integračná Validácia testovacej sústavy prešla, checklist 1–13 a matica M01–M26 sú evidenčne vyriešené. |
| `WORK/2026-07-27_10-20_Krok_11_Klasifikacia_a_testovacia_specifikacia.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Úplná klasifikácia, implementačný diff, testovacia špecifikácia a evidencia Fáz 11.B–11.F. |
| `WORK/INI/2026-07-27_11-46_INI_Krok_12_Jeden_release_a_uplny_audit_balika.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Krok 12 je splnený. Release `1.1.16` bol vytvorený, auditovaný a pripravený podľa plánu. |
| `PLAN/2026-07-27_11-51_Plan_Kroku_12_release_a_audit_balika.md` | PRACOVNÝ — ZÁVÄZNÝ PRE KROK 12 / UZAVRETÝ_SPLNENÝ | Podrobný vykonávací plán Fáz 12.A–12.F je splnený. |
| `WORK/2026-07-27_11-53_Inicializacia_analyza_a_plan_Kroku_12.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Úplný záznam inicializácie, analýzy, release a auditu balíka Kroku 12. |
| `WORK/INI/2026-07-27_13-59_INI_Krok_13_Jeden_produkcny_run.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Krok 13 je po potvrdení Autoritou evidenčne splnený. Produkčný výsledok je potvrdený Autoritou, opravný release `1.1.17` je určený ako produkčný stav. |
| `WORK/2026-07-27_15-00_Krok_13_Riadeny_STOP_po_produkcnom_nasadeni.md` | PRACOVNÝ / PREKONANÝ_KONEČNÝM_POTVRDENÍM | Pôvodný riadený STOP po produkčnom nasadení je evidenčne prekonaný následným potvrdením Autority, že produkčný výsledok bol splnený. Historický záznam zostáva zachovaný. |
| `WORK/INI/2026-07-27_15-07_INI_Krok_14_Tombstone_sweep_a_produkcny_cleanup.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Krok 14 je splnený. Tombstone, sweep, nulové zvyšky, vypnuté flagy a čistá produkcia sú potvrdené. |
| `WORK/2026-07-27_15-16_Krok_14_Produkčný_cleanup_a_vypnutie_diagnostiky.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Produkčný cleanup a vypnutie diagnostiky sú uzavreté. |
| `WORK/INI/2026-07-27_15-31_INI_Krok_15_ReValidacia_registre_a_zaverecne_uzavretie.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Brána Kroku 15 je otvorená, naplnená a uzavretá záverečným registračným zápisom. |
| `WORK/2026-07-27_15-38_Krok_15_ReValidacia_registre_a_zaverecne_uzavretie.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Záverečný checkpoint Kroku 15. Plán je splnený a ďalším povoleným smerom je tvorba webovej stránky s predpripravenými testami. |
| `2026-07-23_12-27_Copilot-checklist a testovacia matica.md` | PRACOVNÝ / UZAVRETÝ_SPLNENÝ | Checklist a testovacia matica sú po ukončení plánu vedené ako splnené v rozsahu pripravenom pre ďalšiu webovú stránku s predpripravenými testami. |

## Pravidlo pokračovania

Od tohto checkpointu sa už neotvárajú Kroky 1 až 15 pôvodného plánu. Ďalšia práca pokračuje tvorbou webovej stránky s predpripravenými testami.

```text
NEXT_ALLOWED_STEP=TVORBA_WEBOVEJ_STRÁNKY_S_PREDPRIPRAVENÝMI_TESTAMI
```
