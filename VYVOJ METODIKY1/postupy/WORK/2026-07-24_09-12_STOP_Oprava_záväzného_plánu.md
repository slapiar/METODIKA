# STOP — oprava záväzného plánu

## Stav dokumentu

```text
PRACOVNÝ
```

## Väzba

Tento záznam dokumentuje používateľom nariadený `STOP` a opravu záväzného plánu:

`postupy/PLAN/2026-07-24_08-04_Plán_práce.md`

## Zistená chyba

Pôvodný plán nesprávne zaradil externé produkčné dôkazy do zmrazenia východiskového stavu. Ak nebol dostupný produkčný log, tombstone alebo stav feature flagov, krok A1 zostal otvorený a zároveň znemožnil systematické pokračovanie.

Takýto model vytváral riziko:

- viacerých pootvorených krokov,
- čakania bez uzavretého výsledku,
- straty aktuálneho miesta práce,
- miešania repozitárového auditu s produkčnou prevádzkou.

## Vykonaný úkon

Záväzný plán bol prepísaný na striktne lineárny rad 15 krokov.

Každý krok musí byť pred otvorením ďalšieho uzavretý ako:

```text
SPLNENÉ
UZAVRETÉ S OBMEDZENÍM
ZASTAVENÉ ROZHODOVACOU BRÁNOU
```

Externý dôkaz má samostatný Krok 5. Jeho nedostupnosť už nenechá prácu visieť; krok sa uzavrie s obmedzením a ďalší presne určený krok reprodukuje príčinu mimo produkcie.

## Následok pre doterajšiu prácu

Doterajšie záznamy:

- `WORK/2026-07-24_A1_Východiskový_stav.md`,
- `WORK/2026-07-24_08-57_A1_Pokračovanie_a_produkčná_blokácia.md`

zostávajú historickým dôkazom pôvodného postupu, ale ich pracovná logika je prekonaná opraveným plánom.

## Aktuálne miesto

```text
Krok 1 — Zmrazenie repozitárového východiska
```

Krok 1 sa musí vykonať nanovo podľa opravenej definície a uzavrieť pred začatím Kroku 2.

## Validácia opravy plánu

Opravený plán:

- povoľuje iba jeden otvorený krok,
- oddeľuje repozitárové a produkčné dôkazy,
- určuje uzatvárateľný výsledok pri nedostupnom externom dôkaze,
- zakazuje preskakovanie a paralelné otváranie krokov,
- zachováva pôvodné technické problémy, testy, rozhodovacie brány a kritériá úspechu.
