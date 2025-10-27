#Prosjekt Krav Dokument (PRD)

Prosjektets navn er **DpostWeb**

##PROSJEKTMÅL:
Det skal utvikles et web-basert brukergrensesnitt (GUI) for en MySQL database som heter **dposten** (DB).

##OVERORDNETE KRAV:
GUI skal kunne brukes i alle vanlig forekommende browsere som Edge, Chrome, Opera o.l.
GUI skal kunne brukes av tre forskjellige brukergrupper (roller), administratorer (admin), superbrukere (super) og brukere (user), opptil 3 samtidige.
Det eksiterer adgangskontroll med rolle, e-post/passord som er lagretegen tabell i DB.
Rollen kalt **admin** kan SØKE i, LESE, ENDRE, SLETTE og LEGGE til data i DB, samt endre struktur.
Rollen kalt **super** kan SØKE i, LESE, ENDRE, SLETTE og LEGGE til data i DB.
Rollen kalt **user** kan SØKE i og LESE data i DB.
**admin** skal i tillegg kunne legge til og slette user.
Enkelhet i kode og visuelt inntrykk er viktig.

##DATABASENS INNHOLD:
Databasen inneholder opplysninger om innholdet av de 125 utgavene av tidsskriftet **Dampskibsposten**.

### Hovedhensikten
Hovedhensikten med DpostWeb er å gjøre databasens innhold lettest mulig tilgjengelig for alle brukere gjennom definerte søkemuligheter.
Det skal finnes CRUD muligheter kun for rollene admin og super.

### Overordnet om databasens innhold 
Tidsskriftet inneholder i hovedsak skrevne tekstavsnitt kalt **artikler** og **bilder**. I tillegg inneholder noen av utgavene **annonser** meed angivelse av annonsør. Alle artikler kan ha navngitte forfattere og kildehenvisninger, og alle bilder kan ha navngitte fotografer (Fotografer er lagret sammen med forfatterne i en felles tabell). Artiklene er kategorisert som 'Artikler', 'Notiser', 'Anmeldelser'. Anmeldelsene viser til bøker som er anmeldt. 
Felles for alle artikler, annonser og bilder er at: 
- De har oppgitt utgave nr og side der de forekommer. Bildene har i tillegg oppgitt hvilken artikkel de forekommer i.
- De er omhandler saker tilhørende ett eller flere tema **tema**, **personer**, **organisasjoner** og/eller **fartøy**.

### Eksisterende løsning
Det eksisterer en løsning på https://ihlen.net/dampskibsposten, som er en mindre god løsning. DpostWeb skal avløse denne løsningen. Den eksisterende løsningen illustrerer noe av de ønskete søkemulighetene.

### Databasens struktur
DB har et Schema som angitt i `_basis/gerhard_dposten_schema.sql`.



##RESSURSBRUK:
Jeg er leder av utviklingen, men koder ikke selv.
Du er min rådgiver og gir anbefalinger om utviklingsprosessen.
Du er den som utvikler koden som er nødvendig for at prosjektets mål blir nådd.

##RESSURSRAMMEBETINGELSER:
- DB ligger på et Webhotell. Webhotellet har MySQL ver 8.3.28 og støtter Cron.
- Jeg har en MSc i "Design of Information Systems" fra 1980. Jeg har utviklet applikajoner i og kan lese VBA kode. Jeg har god erfaring med design av Relasjonsdatabaser.
- Jeg har tilgjengelig VB Studio Code, Notebook++, Access, phpMySQL ver 5.2.2, MySQL Workbench ver 8.0 CE og Laragon
- Du kan få adgang til Webhotell, MySQL og det offentlig tilgjengelige repoet **dpostweb** som finnes og til enhver tid oppdateres på GitHub.

##ARBEIDSPROSESS:
Arbeidet skal utføres i henhold til en trinnvis utviklingsplan.

### TRINN 1 – Oppsett og grunnstruktur
Mål: Etablere grunnlaget for utvikling, inkludert tilgangskontroll.
- Backend: PHP 8.3.x (fordi det støttes av webhotellet)
- Database: MySQL 8.3.28 (eksisterende)
- Frontend: HTML, CSS, og litt JavaScript (validering, interaktivitet)
- Autentisering: `xuser`-tabell med feltene `user_id`, `email`, `password`, `role` ('admin', 'super' eller 'user'),  `isactive`, `created_at`, `lastused`
- Påloggingsside (`login.php`) og utlogging (`logout.php`)
- Sesjonsstyring for brukerroller (admin / user)

### TRINN 2 – Lesetilgang for brukere (user)
Mål: Brukergrensesnitt for lesetilgang.
- Adgang til DB bekreftet.
- Nettside for søk og lesing av artikler
- Knapper for forskjellige typer søk, initielt tre knapper.
- Kun lesetilgang
- Ferdig testet

### TRINN 3 – Utseende og justeringer
- Strukturering av siten
- Definisjon av søk, tildeling til landingssidens knapper
- Utseende landingsside og CSS utvikling

### TRINN 4 - Utseende generelt, nye krav til innhold
- Mulig redefinisjon av søk
- Utseende alle sider
- CSS videreutvikling
- Mulige nye linker