# CR Nytt Annonsor ver 1.md

## Mål
Målet er å utvide innholdt av nettstedet **DpostWeb** med data om innhold av et biblioteks bøker.

## Rammer
- Følg reglene i `AGENTS.md`.
- Relevante prosjekt-dokumenters innhold:
  + `AGENTS.md`
  + `DpostWeb_PReqD_Trinn4_UI_Layout.md`
  + `DpostWeb_Schema.sql`
  + `openfil/tema_liste.php` og `openfil/utgaver.php` som eksempler på ønsket resultat.

- Endringene gjelder ikke eksisterende filer, og men at nye lages.

## Nåværende oppførsel
Funksjonaliteten eksisterer ikke på det nåværende.

## Ønsket oppførsel
1. Jeg ønsker en fil funksjonalitet og layout som følger: 
En side som viser innholdet av `tblAnnonsor` i en tabell til venstre med konkatinering av feltene `AnnNavn` og `AnnAvd` separert med ', ', konkatinering av feltene `Postnr` og `Poststed`, samt feltet `WebSide` som skal åpne i ny fane når klikket. og . Siden skal helst ha tilsvarende funksjonalitet og lay-out som for eksempel i `openfil/tema_liste.php`. Til høyre, under et øverste felt med detaljert informasjon, ønsker jeg to tabeller:
- En tabell som viser de radene i `tblAnnonse` som er relevante i forhold til valgt rad i `tblAnnonsor`. 
- En tabell under denne som viser deler av et view basert på `tblxAnnonseBlad` og `tblBlad` der feltene `Year`, `BladNr`, `Side` og `Lenke` vises. Feltet `Lenke` skal ha samme klikk - funksjonalitet som i `openfil/utgaver.php` som er relevant i forhold til valgt rad i tabellen over.

## Gjennomføring
- Les denne CR'en, og still spørsmål om uklarheter, eller mulige logiske tilleggskrav.
- Du har frie hender til å komme med forslag til struktur-endringer, samt utviklingssteg.
- Be om svar, eller, hvis ingen spørsmål, be om å få gå videre.

## Endringer
- `css/app.css` kan oppdateres men uten å endre inneholdet av eksisterende klasser som de eksisterende php-filene inneholder. 
- List konkrete endringer som er gjort i kode eller struktur.
- Spesifiser gjerne i hvilken fil/klasse/SQL-tabell som berøres.

## Test
Hvordan endringen skal verifiseres:
- Lokalt (XAMPP / annen stack).
- Nettlesere / enheter.

## Sikkerhet og kvalitet
- Påminnelse om regler fra 'Rammer' (over).

