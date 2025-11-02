# CR Nytt Publikasjoner ver 1.md

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
- Siden skal helst ha tilsvarende funksjonalitet og lay-out som for eksempel i `openfil/tema_liste.php`.
- En tabell til venstre som viser innholdet av `tblpublikasjon`. Jeg vil se kolonner med konkatinering av feltene `PubTittel` og `PubSubTittel` separert med ' - ', feltet `ForlagNavn` fra relevant `tblforlag`, `PubForfNavn` fra relevant `tblpubforfatter` (kun første hvis flere forfattere) , samt feltet `UtgittYear`.  
- Til høyre, øverst, ønskes synliggjort detaljert informasjon fra `tblpublikasjon` med alle forfatterne.

2. Filens lagringssted/navn skal være `openfil/publikasjoner.php`

3. Navbar oppdateres.

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

