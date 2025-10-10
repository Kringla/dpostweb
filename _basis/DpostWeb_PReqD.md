#Prosjekt Krav Dokument (PRD)

Prosjektets navn er **IhlenWeb**

##PROSJEKTMÅL:
Det skal utvikles et web-basert brukergrensesnitt (GUI) for en MySQL database som heter **ihlendb** (DB).

##OVERORDNETE KRAV:
GUI skal kunne brukes i alle vanlig forekommende browsere som Edge, Chrome, Opera o.l.
GUI skal kunne brukes av to forskjellige brukergrupper, administratorer (admin) og brukere (user), opptil 3 samtidige.
Det skal opprettes adgangskontroll med e-post/passord i egen tabell i DB.
Rollen kalt **admin** skal kunne SØKE i, LESE, ENDRE, SLETTE og LEGGE til data i DB.
Rollen kalt **user** skal kun kunne SØKE i og LESE data i DB.
**admin** skal i tillegg kunne legge til og slette user.
Enkelhet i kode og visuelt inntrykk er viktig.

##DATABASENS INNHOLD:
Databasen inneholder opplysninger om en families eiendeler og forvaltning av slike, fra matlaging til administrasjon av hytte.

##RESSURSBRUK:
Jeg er leder av utviklingen, men koder ikke selv.
Du er min rådgiver og gir anbefalinger om utviklingsprosessen.
Du er den som utvikler koden som er nødvendig for at prosjektets mål blir nådd.

##RESSURSRAMMEBETINGELSER:
DB ligger på et Webhotell. Webhotellet har MySQL ver 8.3.28 og støtter Cron.
Jeg har en MSc i "Design of Information Systems" fra 1980. Jeg har utviklet applikajoner i og kan lese VBA kode. Jeg har god erfaring med design av Relasjonsdatabaser.
* Jeg har tilgjengelig VB Studio Code, Notebook++, Access, phpMySQL ver 5.2.2, MySQL Workbench ver 8.0 CE og Laragon
DB har et Schema som angitt i `_basis/ihlendb_schema.sql`.
Du kan få adgang til Webhotell, MySQL og min OneDrive ev. Dropbox eller Google Drive.

##ARBEIDSPROSESS:
Arbeidet skal utføres i henhold til en trinnvis utviklingsplan.

### TRINN 1 – Oppsett og grunnstruktur
Mål: Etablere grunnlaget for utvikling, inkludert tilgangskontroll.
Backend: PHP 8.3.x (fordi det støttes av webhotellet)
Database: MySQL 8.3.28 (eksisterende)
Frontend: HTML, CSS (enkelt), og litt JavaScript (validering, interaktivitet)
Autentisering: users-tabell med feltene user_id, email, password, role (admin eller user), isactive, created_at, lastused
Påloggingsside (`login.php`) og utlogging (`logout.php`)
Sesjonsstyring for brukerroller (admin / user)

### TRINN 2 – Lesetilgang for brukere (user)
Mål: Brukergrensesnitt for lesetilgang.
*Adgang til DB bekreftet.
En nettside for søk og lesing av oppskrifter (`oppskrifter` + tilhørende navn, spesifikasjoner og historikk)
Knapper for forskjellige typer søk, initielt tre knapper.
Kun lesetilgang
Ferdig testet

### TRINN 3 – Utseende og justeringer
Strukturering av siten
Definisjon av søk, tildeling til landinssidens knapper
Utseende landingsside
CSS utvikling

### TRINN 4 - Utseende generelt, nye krav til innhold
Redefinisjon av søk
Utseende alle sider
CSS videreutvikling
Nye linker