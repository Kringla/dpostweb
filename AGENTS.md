# AGENTS.md

Prosjektets navn er **DpostWeb**
Denne filen gir Codex veiledning for arbeid i dette repoet, **dpostweb**.

## Rammer og referanser

- Prosjektets styrende filer skal alltid leses **før** utvikling/endring av ny kode. Disse er:
  - `_basis/DpostWeb_PStrD.md` — prosjektets strukturer, repo-struktur og filoversikt  
  - `_basis/DpostWeb_Schema.sql` — SkipsDb database-schema  
  - `_includes/bootstrap.php` — bootstrap-informasjon  
  - `config/constants.php` — konstanter i prosjektet  
  - `css/app.css` — prosjektets CSS-klasser

- Bruk alltid **nyeste versjon** dersom flere versjoner finnes (`v*` = høyest nummer).

- Bruk alltid karakterset SET UTF-8. Databasen bruker tegnsett utf8mb4 og kollasjon primært UTF-8_unicode_ci, sekundært UTF-8_danish_ci

- Vedlagte filer har **alltid prioritet** over filer i repo eller GitHub.

- GitHub public repo: <https://github.com/Kringla/dpostweb>

- I dette **repoet** og **GitHub repoet** skal kun filer med file-extension .sql, .md, .css, .php, .js, .jpg og .png leses og brukes. Innholdet i andre filer kan kun benyttes om avtalt i hvert enkelt tilfelle.

- Vedlagte filer skal alltid leses/benyttes uansett file-extension.

- Filene i `config/` skal **ikke** endres.

- Tabeller som starter med  `x` eller `tblz`er såkalte **parametertabeller**

## Tillatte endringer

- Det er lov å endre filer, men ikke bryte eksisterende funksjonalitet for innlegging av data.
- Eksisterende CSS-klasser i `css/app.css` skal beholdes. Nye regler skal legges i `css/app.css`.
- Bruk `css/app.css` fremfor inline `<style>`.

## Generelle prinsipper

### Lay-out
	Filene tilstreber å tilfredsstille følgende generelle krav til layout: 
	+ Side headinger skal være midtstilt.
	+ Knapper benyttes til å starte opp handlinger (f.eks. søks- og detaljfiler), faner brukes til å vise andre meny-sider (f.eks. egen meny for administrator). 
	+ Knapper skal være like store. Knapper skal ha "knappefarge", f.eks. tilsvarende den blå fargen som er valgt for landingsside. Knapper skal ikke være i sidens fulle bredde, men være små, men likevel brede og høye nok til å ha plass til 20 karakterer i valgt "knappefont" 10px. 
	+ Felt og blokker bør være rimlig komprimerte, unngå bruk av store typer (over 12px).
	Spesielt for landingssiden:
	+ Når knapper benyttes må de ikke være duplikater 

### Utførelse av endringer:
Alle endringer skal skje basert på enten fil vedlagt i prosjektets dokumenter, en vedlagt fil, eller siste versjon av den relevante filen som ligger på https://github.com/Kringla/dpostweb.

### Kodekvalitet:  
  - Fjern unødvendig kode og duplisering av klasser. 
  - Bruk alltid `BASE_URL` som referanse til rot. 
  - Bruk `h()` for output escaping.  
  - Bruk `basename()` på filnavn hvis dynamiske bildekilder.
  - Når ferdig med kodingsoppgaven skal du:
	-	Kontnre at løsningen tilfredsstiller kravene i CR'en, DpostWeb_PStrD.md og denne AGENTS.md
	-  Fjerne fra repoet "temp"-filer som kun var for bruk ifm. det aktuelle kodingsarbeidet.

- **CSS**:  
  - Ikke bruk `!important` unntatt når helt nødvendig.  
  - Scope endringer (f.eks. `.card.centered-card`).

- **JavaScript**:  
  - Ikke i bruk.

  **Login/logout**
  - Login når filer fra `protfil/` skal åpnes
  - Automatisk logout når en går tilbake til `index.php`. Hvis innlogget en gang i sesjonen, huskes rollen.
  - Filen `logout.php`benyttes ikke.

## Testplan

- Lokalt (XAMPP):  
  - Last siden(e) direkte og verifiser layout.  
  - Hard refresh med `CTRL+F5`.

- Nettlesere: Chrome.

- Skjermbredder: opptil 1900px.


## Instruksjoner for samarbeid med ChatGPT
- Last opp denne filen i starten av en økt. 
- Be ChatGPT validere leveranser mot reglene i denne filen.
- ChatGPT skal kun foreslå endringer som er i tråd med disse reglene.

