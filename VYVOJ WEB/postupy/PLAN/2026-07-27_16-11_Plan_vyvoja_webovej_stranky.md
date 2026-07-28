# Plán vývoja webovej stránky METODIKA

Dátum a čas: 2026-07-27 16:11 Europe/Bratislava

Stav: PRACOVNÝ — ZÁVÄZNÝ PRE NADVÄZUJÚCI VÝVOJ WEBOVEJ STRÁNKY

---

## Východisko

Tento plán nezačína projekt nanovo.

Nadväzuje na ukončený plán Krokov 1 až 15, v ktorom bola pripravená a uzavretá testovacia sústava, produkčný stav bol potvrdený Autoritou a ďalším povoleným smerom sa stala tvorba webovej stránky s predpripravenými testami.

Existujúce východisko:

```text
REPOZITÁR=slapiar/METODIKA
VETVA=main
APLIKAČNÝ_KOREŇ=/codei
FRAMEWORK=CodeIgniter 4
DATABÁZA=MariaDB 11.8.8-MariaDB-log
PHP=8.4
SERVER=Apache
ZDROJ_PROJEKTOV=/PROJEKTY/ZoznamProjektov.md
DOKUMENTAČNÁ_ZLOŽKA=/DOC
PLÁNOVACIA_ZLOŽKA=/postupy/PLAN
```

Zložka `/DOC` slúži iba na vývoj dokumentácie a kapitol dokumentácie. Plány vývoja sa ukladajú výhradne do `/postupy/PLAN/` s názvom začínajúcim dátumom a časom.

---

## Pravidlo 1 — samostatná vývojová štruktúra projektu

Každý projekt musí mať vlastnú samostatnú vývojovú štruktúru zložiek.

Ukončená práca na metodike sa evidenčne a organizačne oddeľuje do projektového koreňa:

```text
/VYVOJ METODIKY1
```

Ďalší vývoj webovej stránky sa otvára v samostatnom projektovom koreni:

```text
/VYVOJ WEB
```

`VYVOJ WEB` preberá štruktúru pracovných zložiek, nie obsah ukončenej metodickej práce. Obsah sa do nej bude zapisovať až vtedy, keď vznikne v rámci vývoja webovej stránky.

Z plánovacieho poriadku je výslovne vyňatý tento plán:

```text
/postupy/PLAN/2026-07-27_16-11_Plan_vyvoja_webovej_stranky.md
```

Tento plán zostáva na pôvodnom mieste v `/postupy/PLAN/`, pretože je aktuálnym záväzným plánom ďalšieho vývoja.

---

## Účel webovej stránky

Webová stránka má byť pracovným rozhraním, cez ktoré sa bude dať metodicky analyzovať vybraný projekt.

Používateľ nemá ručne hľadať, ktorý projekt je dostupný, ktoré súbory s ním súvisia, aký má stav a ktoré analýzy možno spustiť. Stránka má tieto informácie načítať zo štruktúry repozitára a pripraviť ich do použiteľného pracovného pohľadu.

Základný účel:

```text
vybrať projekt
→ načítať jeho registrované údaje
→ zobraziť projektovú kartu
→ ponúknuť predpripravené testy a analýzy
→ spustiť analýzu
→ zapisovať priebeh do registrov brán
→ zobraziť výsledok a ďalší krok
```

Tento web nemá byť novou metodikou. Má byť aplikačným rozhraním už vytvorenej metodiky.

---

## Rozsah prvej etapy

Prvá etapa má postaviť priechodný a overiteľný tok nad existujúcim CodeIgniter jadrom.

Do prvej etapy patrí:

1. stránka so zoznamom projektov,
2. načítanie projektov zo súboru `/PROJEKTY/ZoznamProjektov.md`,
3. detail vybraného projektu,
4. výber predpripraveného testu alebo analýzy,
5. založenie analytického behu,
6. zápis GATE stavov,
7. evidencia dôkazov,
8. zobrazenie výsledku analýzy,
9. demo nad projektom `METODIKA`.

Do prvej etapy nepatrí:

- prepisovanie dokumentácie v `/DOC`,
- prepisovanie metodických dokumentov bez samostatného plánu,
- autonómne rozhodovanie AI bez potvrdenia Autority,
- napájanie na cudzie repozitáre bez osobitného čítacieho mechanizmu,
- veľká vizuálna nadstavba pred funkčným tokom.

---

## Najbližší povolený krok

Najbližší povolený krok je organizačné oddelenie projektovej štruktúry a následná implementácia prvého stránkového toku webu.

```text
NEXT_ALLOWED_STEP=ZALOŽIŤ_VÝVOJ_WEB_A_IMPLEMENTOVAŤ_PROJECTS_ROUTE
```
