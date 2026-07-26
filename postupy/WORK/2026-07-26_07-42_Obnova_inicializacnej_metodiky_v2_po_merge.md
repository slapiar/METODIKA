# Obnova inicializačnej metodiky v2.0 po chybnom merge

Dátum a čas: 2026-07-26 07:42 Europe/Bratislava

## Stav úkonu

```text
SPLNENÉ — HLAVNÝ SÚBOR OBNOVENÝ
EVIDENCIA=ROZPRACOVANÁ
```

## Zadanie

Skontrolovať a opraviť `postupy/Inicializácia práce.md` po probléme so zlučovaním vetiev tak, aby presne zodpovedal poslednej potvrdenej verzii v2.0.

## Inicializačná brána

- INI: `postupy/WORK/INI/2026-07-26_07-37_INI_obnova-inicializacnej-metodiky-v2.md`
- commit vytvorenia INI: `b6f6523d6fa6aa2af91b3cdd08f6c7bc852a57d9`
- commit otvorenia brány: `d6d58405df687b8da8716d5fd0c71a85b197fa0a`
- výsledok: `GATE=OPEN`

## Zistená príčina

Merge commit `1cd574cdd715b35fe0797cb597d00efdec3903ea` spojil starší úvod dokumentu so štandardizovaným telom v2.0. Výsledkom bol hybridný blob `4c69dbd23fccb5c01250ec5be86dec63dadb67ca`, ktorý obsahoval starý názov, blok `POTVRDENÝ-NA-PRENESENIE` a starší úvodný odsek.

## Vykonaná oprava

Celý súbor `postupy/Inicializácia práce.md` bol nahradený presným obsahom potvrdenej verzie v2.0 z commitu `6e566cc0e6df6a2b9c0cfdaaa4fb7827d8a6b4df`.

```text
COMMIT_OPRAVY=0c794628db7842635b10162a8f89a2b37a3900f9
VÝSLEDNÝ_BLOB=44729126508a0c9151fb2358badcb1445a425bd6
RIADKOV=94
ZHODA_S_POTVRDENOU_VERZIOU=true
```

## Read-after-write a Validácia

Vzdialené spätné načítanie potvrdilo:

- správny nadpis `METODIKA: Inicializačný a vykonávací protokol (v2.0)`,
- úplných 94 riadkov,
- presný blob `44729126508a0c9151fb2358badcb1445a425bd6`,
- úplnú GATE šablónu, GATE logiku, fyzický pracovný postup a STOP protokol,
- odstránenie starého úvodu a merge hybridu.

## Hranice

Nezmenili sa `/codei`, testy, migrácie, databáza, release verzia, ZIP balíky ani produkcia.

## Rollback

Obnoviť chybný merge blob `4c69dbd23fccb5c01250ec5be86dec63dadb67ca` iba v prípade výslovného rozhodnutia Autority; následne novou historickou udalosťou zosúladiť register a changelog.

## Nasledujúci krok

Dokončiť povinnú evidenciu opravy v `postupy/README.md`, `CHANGELOG.md` a uzavrieť pôvodný INI záznam opravy.
