# Doplnenie kontroly predchádzajúcich úkonov a obnovy dôkazov

## Stav

```text
VYKONANÉ — EVIDENCIA SA DOKONČUJE V TOM ISTOM PRACOVNOM KROKU
```

## Inicializačný dôkaz

Autoritatívna brána tohto metodického úkonu je:

`postupy/WORK/INI/2026-07-25_12-35_INI_Kontrola_predchadzajucich_ukonov_po_zmene_main.md`

Predchádzajúce dva pokusy boli metodicky zastavené po zistení neúplného alebo zmeneného vzdialeného HEAD a zostávajú zachované ako historické STOP záznamy.

## Vykonaný zásah

Do `postupy/Inicializácia práce.md` bol medzi body 5 a 6 doplnený oddiel `Povinná kontrola predchádzajúcich úkonov a obnova dôkazov`.

Oddiel záväzne určuje, že:

1. pred vykonaním každého bodu nového projektového kroku sa najprv preverí, či už nebol vykonaný a doložený v niektorom predchádzajúcom kroku toho istého projektu,
2. už vykonaný bod sa neopakuje iba preto, že je znovu uvedený v pláne,
3. existujúci výsledok sa použije až po novom načítaní, overení presnej zhody a kontinuity jeho predpokladov voči aktuálnemu HEAD, prostrediu, konfigurácii a rozsahu,
4. ak dôkaz nie je priamo dostupný, najprv sa vykoná bezpečný pokus o jeho obnovenie alebo sprístupnenie prostredníctvom vlastnej preukázanej administrátorskej autority,
5. administrátorská autorita neumožňuje domýšľanie výsledku, obchádzanie bezpečnostných hraníc, rozšírenie zásahu mimo otvorenej brány ani zmenu produkcie bez osobitne potvrdeného oprávnenia a rollbacku,
6. nepreukázaný ani neobnovený bod zostáva `NIE` alebo `NEOVERENÉ` a môže sa vykonať nanovo iba podľa otvorenej brány, záväzného plánu a potvrdených hraníc zásahu.

## Dôkazy

```text
PÔVODNÝ_BLOB_METODIKY=262fa71b93fd8059426f8e0fc430a2d9cb623e79
NOVÝ_BLOB_METODIKY=1f32fd9144a4cc3ca94ca8219a1d5dcb4518ee19
IMPLEMENTAČNÝ_COMMIT=06eac77e998f8ac164566717c86ca50e8ccef3c2
READ_BACK=ÚSPEŠNÝ
```

Pôvodný obsah metodiky bol pred zápisom presne rekonštruovaný a jeho gitový blob sa zhodoval s autoritatívnym blobom `262fa71b...`. Nový vzdialený read-back potvrdil očakávaný blob `1f32fd91...` a jediný vložený oddiel medzi bodmi 5 a 6.

## Hranice zásahu

Týmto metodickým úkonom sa nemenili:

- vykonateľný kód,
- databáza ani migrácie,
- workflowy,
- produkčné prostredie,
- technické výsledky Kroku 11,
- stav uzavretia Kroku 11.

## Rollback

Rollback spočíva vo vrátení metodického oddielu a jeho evidenčných záznamov novým opravným commitom. História STOP záznamov sa nemaže ani potichu neprepisuje.

## Otvorené do dokončenia evidencie

- aktualizácia `postupy/README.md`,
- aktualizácia `CHANGELOG.md`,
- doplnenie konečných commitov a blobov do tohto WORK záznamu a INI,
- záverečná Validácia a výpis tabuľky R/W.
