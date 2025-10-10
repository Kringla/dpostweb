# AGENTS.md

Prosjektets navn er **IhlenWeb**
Denne filen gir Codex veiledning for arbeid i dette repoet, **ihlenweb**.

## Rammer og referanser

- Prosjektets styrende filer skal alltid leses **før** utvikling/endring av ny kode. Disse er:
  - `_basis/IhlenWeb_PStrD.md` — prosjektets strukturer, repo-struktur og filoversikt  
  - `_basis/IhlenWeb_Schema.sql` — SkipsDb database-schema  
  - `_includes/bootstrap.php` — bootstrap-informasjon  
  - `config/constants.php` — konstanter i prosjektet  
  - `assets/css/app.css` — prosjektets CSS-klasser

- Bruk alltid **nyeste versjon** dersom flere versjoner finnes (`v*` = høyest nummer).

- Bruk alltid karakterset SET UTF-8. Databasen bruker tegnsett utf8mb4 og kollasjon primært UTF-8_unicode_ci, sekundært UTF-8_danish_ci

- Vedlagte filer har **alltid prioritet** over filer i repo eller GitHub.

- GitHub repo: <https://github.com/Kringla/ihlenweb>

- I dette **repoet** og **GitHub repoet** skal kun filer med file-extension .sql, .md, .css, .php, .js, .jpg og .png leses og brukes. Innholdet i andre filer kan kun benyttes om avtalt i hvert enkelt tilfelle.

- Vedlagte filer skal alltid leses/benyttes uansett file-extension.

- Filene i `config/` skal **ikke** endres.

- Tabeller som starter med 'x' er såkalte **parametertabeller**

## Tillatte endringer

- Det er lov å endre filer, men ikke bryte eksisterende funksjonalitet for innlegging av data.
- Eksisterende CSS-klasser i `assets/css` skal beholdes. Nye regler skal legges i `assets/css/app.css`.
- Bruk `app.css` fremfor inline `<style>`.

## Generelle prinsipper

- **Kodekvalitet**:  
  - Fjern unødvendig kode og duplisering av klasser.  
  - Bruk `h()` for output escaping.  
  - Bruk `basename()` på filnavn hvis dynamiske bildekilder.
  - Når ferdig med kodingsoppgaven skal du:
	-	Kontrollere at løsningen tilfredsstiller kravene i CR'en, IhlenWeb_PStrD.md og denne AGENTS.md
	-   Fjerne fra repoet "temp"-filer som kun var for bruk ifm. det aktuelle kodingsarbeidet.

- **CSS**:  
  - Ikke bruk `!important` unntatt når helt nødvendig.  
  - Scope endringer (f.eks. `.card.centered-card`).

- **JavaScript**:  
  - Ikke i bruk.

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

