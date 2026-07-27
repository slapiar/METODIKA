Bol prevedený audit s týmto výsledkom:
prvá auditná vrstva už priniesla podstatné zistenia.

Predbežná auditná tabuľka
Oblasť	Stav	Zistenie
START a nemenný vstup	HOTOVÉ	Run ukladá spoločný requestReference, fingerprint, oba derivačné identifikátory a aplikačný vstup.
Session lock pred čakaním	HOTOVÉ	Pred bariérou sa vykonáva session_write_close().
Otvorenie bariéry	ČIASTOČNE	Bariéra sa zapisuje pod zámkom a produkcia potvrdila jej otvorenie, ale load() vracia účelovo interpretovanú kópiu stavu.
Ochrana pred falošným timeoutom	ČIASTOČNE	Kontrola existuje pod exkluzívnym zámkom, no chýba samostatný regresný test pre túto vetvu.
Nezávislé vykonanie accept()	HOTOVÉ V KÓDE	Oba procesy opúšťajú file lock pred volaním aplikačnej služby.
Presná príčina chyby	NEOVERENÉ	Runner zapisuje triedu a správu výnimky iba do serverového logu; run dokument uchová iba bezpečný kód.
Unit testy run store	CHYBNÉ TVRDENIE CHECKLISTU	Existujúce testy nepokrývajú exposeOpenedBarrierToWaitingRequest(), poistku timeoutu ani skutočné dva paralelné procesy.
Produkčný replay	NEOVERENÉ	Reálny výsledok zostáva CREATED + FAILED_RUNTIME_ERROR, nie požadovaná dvojica.
Najdôležitejšie technické zistenie

FAILED_RUNTIME_ERROR vznikol vnútri aplikačného prijatia, nie pri bariére ani pri zostavovaní run dokumentu. DiagnosticsConcurrencyAcceptanceRunner totiž zachytí výnimku z celej transakcie a vráti FAILED_RUNTIME_ERROR.

Transakcia obsahuje dve odlišné operácie:

rezervácia REQUEST_REFERENCE
→ založenie historického derivačného behu

Samotná databázová unikátnosť bola potvrdená. Zostávajúci podozrivý úsek je preto najmä DerivationHistoryRepository::createInitialRun() alebo následné vloženie doménových pojmov. Táto časť zapisuje množstvo polí a dve väzby do ďalšej tabuľky.

Zároveň som potvrdila slabinu diagnostiky: vonkajší try/catch v kontroléri zahodí rozdiel medzi chybou pri zostavení vstupu a chybou aplikačnej služby a nahradí ho všeobecným ACCEPT_RUNTIME_ERROR. V produkčnom výsledku sa však objavil FAILED_RUNTIME_ERROR, takže samotné zostavenie InitialDerivationRun zrejme prešlo a výnimku zachytil až runner.

Najmenší ďalší bezpečný krok nie je oprava. Je ním získanie presného serverového logu so záznamom:

Diagnostics acceptance failed [RUNTIME_ERROR]:
<trieda výnimky>: <správa>

Bez tejto jednej informácie by sme si príčinu iba domýšľali. Repozitár som zatiaľ nemenila a nevznikol žiadny commit.

Prvý plán:
Získať presnú príčinu FAILED_RUNTIME_ERROR
nájsť záznam v produkčnom serverovom logu,
potvrdiť triedu výnimky, správu a miesto vzniku,
nič neopravovať, kým toto nemáme.
Uzavrieť audit súčasného stavu
dokončiť tabuľku HOTOVÉ / ČIASTOČNE / NEOVERENÉ / CHYBNÉ,
prejsť checklist bod po bode,
oddeliť deklarované testy od reálne pokrytých vetiev.
Určiť koreňovú príčinu
preveriť najmä:
DerivationHistoryRepository,
transakčnú hranicu,
databázové obmedzenia,
zápis doménových pojmov,
správanie rollbacku pri súbehu.
Navrhnúť najmenší bezpečný zásah
iba jednu konkrétnu opravu,
bez úprav mimo príčiny,
s jasným návratom späť,
s presným kritériom úspechu.
Doplniť chýbajúce regresné testy
chyba počas accept(),
rollback prvého procesu,
druhý proces po rollbacku,
poistka proti falošnému timeoutu,
skutočný paralelný beh dvoch procesov.
Lokálna validácia
unit testy,
integračný scenár,
kontrola výsledku CREATED + ALREADY_EXISTS,
kontrola databázovej unikátnosti a cleanupu.
Produkčné diagnostické overenie
nasadiť iba potvrdenú verziu,
vykonať jeden čistý run,
overiť všetky tri osi,
skontrolovať tombstone a sweep.
Záznam a uzavretie
aktualizovať checklist,
checkpoint,
postupy/README.md,
CHANGELOG.md,
uviesť commit a zostávajúce riziká.
Dnešné kritérium úspechu
DB unikátnosť = true
AND replay = CREATED + ALREADY_EXISTS
AND cleanup = true
AND state = COMPLETED_SUCCESS

Prvý praktický krok je teda jasný: nájsť presný produkčný log k FAILED_RUNTIME_ERROR. Bez neho by sme zase strieľali do hmly — a tej sme si včera už užili dosť.

Doplnenie plánu:
Doplnený krok plánu

3A. Rozdeliť diagnostiku chýb v executeAcceptIfReady()

Vonkajší try/catch musí rozlíšiť najmenej:

chyba pri zostavení InitialDerivationRun
≠ chyba pri načítaní payloadFingerprint
≠ chyba vo volaní aplikačnej služby accept()

Dnes sa prvé dve vetvy zlejú do:

ACCEPT_RUNTIME_ERROR

zatiaľ čo chyba vo vnútri DiagnosticsConcurrencyAcceptanceRunner sa vracia ako:

FAILED_RUNTIME_ERROR
FAILED_DATABASE_ERROR
FAILED_TYPE_ERROR
...

To znamená, že diagnostika používa dva rozdielne mechanizmy chybových kódov, ktoré nie sú významovo zladené.

Správny postup podľa mňa je:

Zostavenie vstupu oddeliť do vlastného try/catch.
Načítanie fingerprintu oddeliť alebo zaradiť do rovnakej vstupnej fázy.
Volanie acceptanceRunner()->accept() už nezakrývať všeobecným kódom.
Do run dokumentu zapisovať bezpečné, ale presné kódy, napríklad:
ACCEPT_INPUT_BUILD_FAILED
ACCEPT_PAYLOAD_FINGERPRINT_FAILED
FAILED_DATABASE_ERROR
FAILED_TYPE_ERROR
FAILED_INPUT_ERROR
FAILED_RUNTIME_ERROR

Tento zásah však nemá predbehnúť získanie existujúceho serverového logu. Najprv zistíme skutočnú príčinu včerajšieho pádu, potom upravíme diagnostiku tak, aby sa rovnaká nejasnosť už neopakovala.

Takže áno — v pôvodnom pláne tento problém zostal schovaný v širšom bode. Mal byť uvedený výslovne.

2. Opravený plán
1. Získať presnú príčinu FAILED_RUNTIME_ERROR
vyhľadať príslušný záznam v produkčnom serverovom logu,
zistiť:
triedu výnimky,
správu výnimky,
miesto vzniku,
prípadnú databázovú chybu alebo porušené obmedzenie,
potvrdiť, či chyba vznikla:
v FirstAcceptanceService,
v repository adaptéri,
v transakčnej hranici,
v DerivationHistoryRepository,
alebo pri zápise doménových pojmov.

Kým nebude príčina potvrdená logom alebo reprodukovateľným testom, nebudeme ju vydávať za zistenú.

2. Dokončiť audit aktuálnej implementácie

Prejsť aktívny checklist bod po bode a vytvoriť úplnú auditnú tabuľku:

HOTOVÉ
ČIASTOČNE
NEOVERENÉ
CHYBNÉ

Pri každom bode uviesť:

čo je skutočne implementované,
čo je pokryté testom,
čo bolo potvrdené produkčným behom,
čo bolo iba deklarované,
čo po včerajších zásahoch už nemusí platiť.

Osobitne skontrolovať testovaciu maticu M01 až M26.

3. Určiť koreňovú príčinu aplikačného pádu

Preskúmať celú transakčnú cestu:

DiagnosticsController
→ DiagnosticsConcurrencyAcceptanceRunner
→ FirstAcceptanceService
→ RequestReferenceRepository
→ DerivationHistoryRepository
→ DatabaseTransactionBoundary
→ databáza

Overiť najmä:

či prvý participant vytvorí rezerváciu a následne padne pri histórii,
či sa celá transakcia správne vráti späť,
prečo druhý participant následne skončí ako CREATED,
či porušenie vzniká na:
reservation_id,
request_reference,
derivation_reference,
cudzej väzbe,
alebo v tabuľke doménových pojmov.
4. Opraviť diagnostické rozlíšenie chýb v kontroléri

V executeAcceptIfReady() odstrániť dnešné zlievanie rozdielnych chýb do jedného všeobecného kódu:

ACCEPT_RUNTIME_ERROR

Samostatne rozlíšiť najmenej:

chyba zostavenia InitialDerivationRun
≠ chyba načítania payloadFingerprint
≠ chyba volania acceptanceRunner
≠ chyba v aplikačnej službe accept()

Navrhované bezpečné diagnostické kódy:

ACCEPT_INPUT_BUILD_FAILED
ACCEPT_PAYLOAD_FINGERPRINT_FAILED
ACCEPT_RUNNER_FAILED
FAILED_DATABASE_ERROR
FAILED_TYPE_ERROR
FAILED_INPUT_ERROR
FAILED_JSON_ERROR
FAILED_RUNTIME_ERROR

Cieľom nie je ukladať citlivý text výnimky do run dokumentu. Cieľom je zachovať presný význam fázy, v ktorej chyba vznikla.

Serverový log má zároveň uchovať:

bezpečný diagnostický kód,
triedu výnimky,
správu výnimky,
fázu spracovania.
5. Navrhnúť najmenší bezpečný funkčný zásah

Až po potvrdení príčiny navrhnúť opravu samotného aplikačného pádu.

Zásah musí:

riešiť iba potvrdenú príčinu,
nemeniť metodické významy,
nerozširovať rozsah bez potreby,
zachovať atómovosť prvého prijatia,
zachovať databázovú unikátnosť,
mať jasnú možnosť návratu.

Diagnostická oprava z kroku 4 a funkčná oprava koreňovej príčiny musia zostať významovo oddelené.

6. Doplniť chýbajúce regresné testy

Doplniť alebo opraviť testy pre:

chybu pri zostavení InitialDerivationRun,
chybu pri získaní fingerprintu,
chybu vo vnútri accept(),
databázovú výnimku,
rollback prvého participantu,
správanie druhého participantu po rollbacku,
presnú dvojicu CREATED + ALREADY_EXISTS,
otvorenú bariéru počas stavu EXECUTING,
poistku proti zápisu falošného PARTNER_TIMEOUT,
dva skutočne paralelné procesy,
finalization claim získaný iba jedným participantom,
cleanup po aplikačnom páde,
tombstone a sweep.

Každý test musí overovať aktuálny kód, nie staršiu implementáciu.

7. Lokálna a integračná validácia

Spustiť:

unit testy,
feature testy,
integračný webový scenár,
súbežný scenár dvoch participantov,
regresné diagnostické testy databázy a prihlásenia.

Overiť samostatne:

DB unikátnosť
aplikačný replay
cleanup
výsledný stav
tombstone
sweep

HTTP 200 sa nebude považovať za dôkaz úspechu aplikačnej operácie.

8. Pripraviť produkčné nasadenie

Pred nasadením:

skontrolovať rozdiel zdrojov oproti poslednej verzii,
zvýšiť verziu,
vytvoriť produkčný balík,
spätne overiť obsah balíka,
potvrdiť, že neobsahuje nepotrebné vývojové alebo súkromné súbory,
zapísať presný commit a verziu.
9. Vykonať čisté produkčné diagnostické overenie

Po nasadení vykonať nový run a overiť:

A = CREATED
B = ALREADY_EXISTS

alebo opačné poradie.

Zároveň musí platiť:

barrierOpened = true
timeoutReached = false
dbUniquenessConfirmed = true
appReplayConfirmed = true
cleanupConfirmed = true
state = COMPLETED_SUCCESS

Následne overiť:

prvé načítanie výsledku,
vznik tombstone,
nastavenie readOnceConsumedAt,
fyzické odstránenie run súboru sweepom po TTL.
10. Zapísať výsledok podľa metodiky

Po úspešnej alebo neúspešnej validácii aktualizovať:

aktívny checklist,
auditnú tabuľku,
postupy/README.md,
CHANGELOG.md,
príslušný technický návrh,
nový checkpoint, ak pôjde o významný pracovný krok.

Na konci uviesť:

čo sa vykonalo,
čo sa zmenilo,
čo zostáva otvorené,
aké riziká pretrvávajú,
identifikátor commitu,
verziu produkčného balíka,
nasledujúci logický krok.
Kritérium dnešného úspechu
DB unikátnosť = true
AND aplikačný replay = CREATED + ALREADY_EXISTS
AND cleanup = true
AND state = COMPLETED_SUCCESS

A ešte jedna dôležitá hranica: oprava diagnostického rozlíšenia chýb je dnes samostatná povinná úloha, nie vedľajší detail funkčnej opravy.
