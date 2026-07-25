# INICIALIZÁCIA — Kontrola predchádzajúcich úkonov po zmene `main`

## Stav brány a výsledku

```text
GATE=OPEN
VALIDÁCIA=VALID
VÝSLEDOK=SPLNENÉ
```

## Nadväznosť

Predchádzajúce dva pokusy sú zachované ako historické STOP záznamy:

- `postupy/WORK/INI/2026-07-25_12-16_INI_Kontrola_predchadzajucich_ukonov_a_obnova_autority.md`,
- `postupy/WORK/INI/2026-07-25_12-23_INI_Obnova_metodickeho_pravidla_na_aktualnom_HEAD.md`.

Prvý pokus bol zastavený po zistení neúplne načítaného vzdialeného stavu. Druhý pokus správne zastavila bezprostredná kontrola HEAD pred implementáciou, keď sa autoritatívny `main` zmenil o päť commitov. Tento tretí záznam je jedinou autoritatívnou bránou a výsledkom metodického úkonu.

## Predmet metodického úkonu

Do `postupy/Inicializácia práce.md` doplniť záväzné pravidlo, že pred vykonaním každého bodu nového projektového kroku sa najprv preverí jeho predchádzajúce vykonanie v tom istom projekte. Existujúci výsledok sa použije iba po novom načítaní, overení presnej zhody a kontinuity. Ak nie je dostupný, vykoná sa najprv bezpečný pokus o obnovu alebo sprístupnenie z vlastnej preukázanej administrátorskej autority.

Administrátorská autorita nie je oprávnením domýšľať výsledok, obchádzať bezpečnostné hranice, rozširovať zásah mimo otvorenej brány ani meniť produkciu bez osobitne potvrdeného oprávnenia a rollbacku.

## Vzdialený stav

```text
projekt=METODIKA
repozitár=slapiar/METODIKA
autoritatívna_vetva=main
HEAD_PRED_INI=af6c72c94f723527775942fea078557f4d8a427d
HEAD_PO_VYTVORENÍ_INI=fdab6cb8e7b419c81fea1b77f84e827ce300a092
HEAD_PRI_FINÁLNEJ_VALIDÁCII=8cf59d31b816d44d2985c7af3ba813b13991ad12
technický_koreň=/codei
čas_inicializácie=2026-07-25 12:35 Europe/Bratislava
```

Bezprostredné porovnanie `8cf59d31... → main` pred finálnym zápisom vrátilo `identical`, `ahead_by=0`, `behind_by=0`.

## Načítané a použité dôkazy

- celý pôvodný `postupy/Inicializácia práce.md`, blob `262fa71b93fd8059426f8e0fc430a2d9cb623e79`,
- celý záväzný plán `postupy/PLAN/2026-07-25_05-18_Plan_prace.md`, blob `cc121bddaf480c53474477a43d4eda3dcf11d623`,
- `PROJEKTY/ZoznamProjektov.md`, blob `5e6102ee0d19fbdd8e0ba489a993eb64f366046d`,
- autoritatívny INI Kroku 11 `postupy/WORK/INI/2026-07-25_09-29_INI_Krok_11_Uplna_lokalna_a_integracna_validacia.md`, blob `3914ae9980c08f05cb6b0c28248bb583dade4a83`,
- pôvodný register `postupy/README.md`, blob `1a9aee8d63ef80dec5937e3be6e90bfc1e9eea83`,
- pôvodný aktuálny `CHANGELOG.md`, blob `e4feda4d6732b17afcc40fa52586956eb7db5c58`,
- zmeny mimo predmetu v `header.php`, `Routes.php`, `gate_dashboard.php`, `gate-dashboard.js` a `gate-session.js`, ktoré boli načítané, oddelené a ponechané bez zásahu,
- vytvorenie tohto INI commitom `fdab6cb8e7b419c81fea1b77f84e827ce300a092` a jeho vzdialený read-back, blob `99260d6356aa5e1e8b2c98231d56868ffabfd403`.

## Inicializačná tabuľka

| Bod | Stav | Čo bolo overené | Konkrétny výsledok a dôkaz | Zostáva neoverené |
|---|---|---|---|---|
| 1. Metodika načítaná | ÁNO | Celý pôvodný dokument, body 0–14, STOP a základné pravidlo; po zápise celý výsledný oddiel | pôvodný blob `262fa71b...`; výsledný blob `1f32fd91...` | nič |
| 2. Projekt a autoritatívny zdroj overený | ÁNO | METODIKA, `slapiar/METODIKA`, `main`, `/codei` | projektový register a vzdialené metadata | nič |
| 3. Vetva a HEAD overené | ÁNO | HEAD pred INI, pred implementáciou, pri súbežných zmenách a pred finálnou Validáciou | STOP záznamy; `8cf59d31... → main=identical` | nič |
| 4. Potrebné prístupy prakticky overené | ÁNO | Administrátorské čítanie, zápis a vzdialený read-back | `admin=true`, `push=true`; všetky uvedené commity a bloby | nič |
| 5. Prostredie prakticky overené | ÁNO | Pre dokumentačný úkon bolo potrebné iba vzdialené GitHub prostredie | čítanie, zápisy, porovnania a read-backy boli úspešné | nič |
| 6. Závislosti kroku dostupné | ÁNO | Metodika, register, changelog, WORK a INI priestor | všetky cieľové súbory boli načítané a aktualizované | nič |
| 7. Predmet a hranice zásahu určené | ÁNO | Iba metodika a jej povinná evidencia | bez zmeny vykonateľného kódu, DB, workflowu, produkcie a bez uzavretia Kroku 11 | nič |
| 8. Kritérium úspechu určené | ÁNO | Jednoznačný príkaz, read-back, register, changelog a WORK záznam | všetky kritériá sú v pracovnom zázname vyhodnotené ako splnené | nič |
| 9. Rollback určený | ÁNO | Vrátenie iba commitov tohto metodického úkonu novou opravnou udalosťou | história STOP záznamov sa zachováva | nič |

## Vyhodnotenie brány

```text
1=ÁNO
2=ÁNO
3=ÁNO
4=ÁNO
5=ÁNO
6=ÁNO
7=ÁNO
8=ÁNO
9=ÁNO
GATE=OPEN
```

## Vykonaný výsledok

Do `postupy/Inicializácia práce.md` bol medzi body 5 a 6 doplnený oddiel `Povinná kontrola predchádzajúcich úkonov a obnova dôkazov`.

Záväzný výsledok prikazuje:

1. pri každom bode nového kroku najprv preveriť jeho predchádzajúce vykonanie v projekte,
2. neopakovať už vykonaný bod iba preto, že je znovu uvedený,
3. použiť existujúci dôkaz až po novom načítaní, presnej zhode a overenej kontinuite,
4. pri nedostupnosti najprv bezpečne obnoviť alebo sprístupniť dôkaz z preukázanej administrátorskej autority,
5. nepoužiť autoritu na domýšľanie výsledku, obchádzanie hraníc ani neoprávnený zásah,
6. ponechať nepreukázaný bod ako `NIE` alebo `NEOVERENÉ`, kým otvorená brána nepovolí nové vykonanie.

## Dôkazy zápisu a evidencie

```text
METODIKA_COMMIT=06eac77e998f8ac164566717c86ca50e8ccef3c2
METODIKA_BLOB=1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19
REGISTER_FINÁLNY_COMMIT=d03d2d53c78e711352c39630c79980b6fbd5368f
REGISTER_FINÁLNY_BLOB=5ed9e3b500380f2844bd8c8965c0efd826942504
CHANGELOG_COMMIT=b945c39b86a9ac7c4e35f5964385d73a245546ad
CHANGELOG_BLOB=c734e169f5f5eb10ede659c343a332c5957f7103
WORK_FINALIZAČNÝ_COMMIT=8cf59d31b816d44d2985c7af3ba813b13991ad12
WORK_FINÁLNY_BLOB=42e9065d9d6edb3891fe93186f4f4defd2f77703
READ_BACK_VŠETKÝCH_ZÁPISOV=ÚSPEŠNÝ
```

Pracovný výsledok a Validácia sú v:

`postupy/WORK/2026-07-25_12-42_Doplnenie_kontroly_predchadzajucich_ukonov.md`

## Validácia

```text
PRAVIDLO_ZAPÍSANÉ=true
PREDCHÁDZAJÚCE_VYKONANIE_SA_KONTROLUJE=true
KONTINUITA_DÔKAZU_SA_OVERUJE=true
BEZPEČNÁ_OBNOVA_Z_AUTORITY_ZAPÍSANÁ=true
AUTORITA_NENAHRÁDZA_DÔKAZ=true
REGISTER_A_CHANGELOG_AKTUALIZOVANÉ=true
STOP_HISTÓRIA_ZACHOVANÁ=true
ZÁSAH_DO_VYKONATEĽNÉHO_KÓDU=false
ZÁSAH_DO_PRODUKCIE=false
VALIDÁCIA=VALID
VÝSLEDOK=SPLNENÉ
```

## Rollback

Rollback spočíva vo vrátení metodického oddielu a jeho evidenčných záznamov novým opravným commitom. História STOP záznamov sa nemaže ani potichu neprepisuje.

## Nasledujúci logický krok

```text
Samostatne inicializovať správne uzavretie už vykonaného Kroku 11. Pri každom jeho bode najprv preveriť a použiť existujúce dôkazy z predchádzajúcich krokov. Nový výkon je prípustný iba tam, kde sa predchádzajúce vykonanie nepodarí preukázať ani bezpečne obnoviť.
```

## Tabuľka stavu čítania a vykonávania metodických pokynov

| ID | Metodický pokyn | R | W |
|---:|---|:---:|:---:|
| 0 | Povinné nové načítanie vzdialeného autoritatívneho repozitára | 1 | 1 |
| 1 | Obnova univerzálnej metodiky | 1 | 1 |
| 2 | Identifikácia projektu | 1 | 1 |
| 3 | Určenie autoritatívneho zdroja | 1 | 1 |
| 4 | Praktické overenie prístupov | 1 | 1 |
| 5 | Obnova projektového kontextu | 1 | 1 |
| 6 | Overenie skutočného stavu | 1 | 1 |
| 7 | Vymedzenie predmetu a rozsahu práce | 1 | 1 |
| 8 | Analýza pred návrhom | 1 | 1 |
| 9 | Návrh najmenšieho bezpečného riešenia | 1 | 1 |
| 10 | Implementácia až po analýze | 1 | 1 |
| 11 | Spätné načítanie po zápise | 1 | 1 |
| 12 | Validácia výsledku | 1 | 1 |
| 13 | Záznam metodického úkonu | 1 | 1 |
| 14 | Ukončenie pracovného kroku | 1 | 1 |