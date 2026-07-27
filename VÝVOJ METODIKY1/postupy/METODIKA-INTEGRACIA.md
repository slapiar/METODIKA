# Integrácia CodeIgniter 4 do METODIKY

Tento adresár obsahuje distribučnú inštaláciu CodeIgniter 4 dodanú pre projekt METODIKA.

## Prevádzkové pravidlá

- Skutočný `.env` sa nesmie commitovať.
- Priečinok `vendor/` sa generuje Composerom a nepatrí do repozitára.
- Prevádzkové dáta v `writable/` sa necommitujú; zachované sú iba ochranné a indexové súbory.
- Webový server má smerovať do adresára `public/`.
- Autoritatívnym repozitárom projektu zostáva `slapiar/METODIKA`, vetva `main`.
