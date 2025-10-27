#Prosjekt Struktur Dokument (PStrD)

Prosjektets navn er **DpostWeb**

## RESSURSRAMMEBETINGELSER:
DB som skal gis et GUI i prosjektet ligger på et Webhotell, OnNet.no.
Webhotellet har MySQL ver 8.3.28 og støtter Cron.
Jeg har en MSc i "Design of Information Systems" fra 1980. Jeg har utviklet applikajoner i og kan lese VBA kode. Jeg har god erfaring med design av Relasjonsdatabaser.
Jeg har tilgjengelig VB Studio Code, Notebook++, Access, phpMySQL ver 5.2.2, MySQL Workbench ver 8.0 CE og xampp.

## Schema
DB har et Schema som angitt i `_basis/gerhard_dposten_schema.sql`.

## Data for connection string
### Deployert versjon: 
vertsnavn (host): `hostmaster.onnet.no`
databasenavn: `gerhard_dposten`
brukernavn (MySQL-konto): `gerhard_dpbruker` (SELECT-rettigheter)
passord: ########## (fås oppgitt hvis/når nødvendig)
port: `3306`
charset: `UTF-8` (behøves ikke for connection)

### Utvikling/testing i 'xampp': 
vertsnavn (host): `localhost` 
databasenavn: `gerhard_dposten`
brukernavn (MySQL-konto): `root`
passord: `""`
port: `3306`
charset: `UTF-8` (behøves ikke for connection)

## Lagringsstruktur
Filene (så langt) er arrangert i følgende struktur:

dpostweb/
├── .htaccess                			# URL-omskriving, sikkerhetsregler
├── index.php               			# Landingside
├── login.php                			# Innlogging
├── logout.php                			# Utlogging
├── register.php             			# Brukerregistrering bruker i xuser, hvis tom tabell
│
├── admin/      						# Folder med filer kun **admin** rollen har tilgang til
│   ├── xxx.php        					# TBD
│   └── register.php         			# Brukerregistrering bruker i xuser, hvis tom tabell
│
├── assets/
│   └──  img/ 							# bilde-filer
│        ├── DpostWeb-logo.jpg       	# Site logo
│        ├── DpostWeb-logo@2x.jpg       # Site logo stor
│        └── DpostWeb-logo@4x.jpg       # Site logo størst
│
├── config/
│   ├── config.php           			# Database- og app-innstillinger
│   └── constants.php        			# Lokasjonsinnstillinger
│
├── css/  
│   └── app.css  						# css-fil
│
├── includes/
│   ├── bootstrap.php        			# Laster config, constants, functions og $mysqli = db();
│   ├── menu.php             			# Navigasjon
│   ├── header.php           			# Felles HTML-head + åpning av <body>
│   ├── footer.php           			# Felles footerelementer + lukking av <body>
│   ├── auth.php           				# rolle-/tilgangshjelpere
│   ├── xxx.php           				# TBD
│   ├── param_utils.php           		# Parameter-fil administrasjon 
│   └── functions.php        			# Hjelpefunksjoner (validering, formatering mv.)
│
├── openfil/							# Folder med filer som alle rollene har tilgang til
│   ├── change_password.php          	# Endre passord
│   └── xxx.php        					# TBD
│
├── outfil/								# Folder med print-out filer som alle rollene har tilgang til
│   └── xxx_print.php        			# TBD
│ 
└── protfil/      						# Folder med filer kun **admin** rollen har tilgang til
    ├── register.php         			# Brukerregistrering bruker i xuser, hvis tom tabell
    ├── xxx.php.php        				# TBD CRUD
    ├── param_admin.php          		# Admin av parameterfiler
    └── param_table.php        			# Admin av parameterfiler
 


Jeg er redd for å skifte ut feil kode blokker når du foreslår endringer. Derfor må du alltid sørge for enten for
1) å vise hele blokken den skal erstatte, (best for meg)
3) å lage en nedlastbar fullt oppdatert fil, eller 
2) å angi hvilke tre linjer som står FØR innsettingsstedet og hvilke tre linjer som står ETTER innsettingsstedet.

Det er mitt ansvar å holde filene på GitHub oppdaterte.