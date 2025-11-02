# CR Endre Fartoydetaljer ver 1.md

## Mål
Målet er å utvide innholdt av nettstedet **DpostWeb** med data om innhold av et biblioteks bøker.

## Rammer
- Følg reglene og retningslinjene i relevante prosjekt-dokumenters innhold:
  + `AGENTS.md`
  + `DpostWeb_PReqD_Trinn4_UI_Layout.md`
  + `DpostWeb_Schema.sql`
  + `openfil/fartoyer.php` og `openfil/detalg/utgaver.php` som eksempler på ønsket resultat.
- Mye brukt relasjon mellom tabeller er `tblfartobj.FTIDObj` = `tblzfarttype.FTID` og `tblfarttid.FTIDTid` = `tblzfarttype.FTID`.

- Endringen gjelder `openfil/detalj/fartoy_detalj.php`, og kan berøre `openfil/fartoyer.php`.

## Nåværende oppførsel
Funksjonaliteten eksisterer bare delvis på det nåværende.

## Ønsket oppførsel
Jeg ønsker at `openfil/detalj/fartoy_detalj.php` skal ha funksjonalitet og layout som følger: 
1. Siden skal ha en overskrift og et card som er delt i to, venstre del 15 - 20 % av containerbredden.
2. I venstre del skal det være en tabell ('TabA'). Klikking på radene oppdaterer innholdet i høyre del.
3. TabA skal:
	- Vise tre kolonner basert på alle forekomster av data fra `tblfarttid` som er lik `ObjID`i raden som ble klikket på i `openfil/fartoyer.php`. De tre kolonnene er:
		+ `TypeFork` (fra `tblzfarttype`) konkatinert med `FartNavn` fra (fra `tblfartnavn`) med space i mellom.
		+ `YearTid` konkatinert med '/' og `MndTid`, sortert fra lavest til høyest
		+ Et felt kalt 'Hendelse' som kan ha fem verdier: 
			-- 'Nybygg' når `Objekt` = True
			-- 'Ombygd' når `Objekt` = False og `Bygging` = True og `Eierskifte` = False og `Navning` = False
			-- 'Ny eier' når `Objekt` = False og `Bygging` = False og `Eierskifte` = True og `Navning` = False
			-- 'Ny eier/navn' når `Objekt` = False og `Bygging` = False og `Eierskifte` = True og `Navning` = True
			-- 'Ny eier/navn/ombygd' når `Objekt` = False og `Bygging` = True og `Eierskifte` = True og `Navning` = True
			-- 'Nyttnavn/ombygd' når `Objekt` = False og `Bygging` = True og `Eierskifte` = False og `Navning` = True
			-- 'Nytt navn' når `Objekt` = False og `Bygging` = False og `Eierskifte` = False og `Navning` = True
	False
	- TabA skal lande på raden der `tblfarttid.ObjID` = `ObjID`i raden som ble klikket på i `openfil/fartoyer.php` og `tblfarttid.FartID` = `FartID`i raden som ble klikket på i `openfil/fartoyer.php`
4. Høyre felt skal:
	- Vise data fra `tblfartnavn`, `tblfartobj` og `tblfartspes` for valgt rad i TabA, sekvensielt ovenfra og nedover.	
		+ Data fra `tblfartnavn` som skal vises på enkelstående linjer, er 
			-- `TypeFork` (fra `tblzfarttype`) konkatinert med `FartNavn` og eventuelt `Tilnavn`. Felt tittel: 'Navn'
			-- `OrgNavn` (fra `tblorganisasjon`). Felt tittel: 'Forening'
			-- `Picfile`. Klikkbart til konkatinering av 'https:ihlen.net/installs/DPfilerPIC/' og verdien av `Picfile`. Felt tittel: 'Bilde'
			+ Data fra `tblfartobj` som skal vises på enkelstående linjer, er 
			-- `TypeFork` (fra `tblzfarttype`) konkatinert med `FartObjNavn` med space i mellom. Felt tittel: 'Navn', og `IMO` (fra `tblorganisasjon`). Felt tittel: 'IMO nr'
			-- `StroketYear` konkatinert med `StroketGrunn` med space i mellom. Felt tittel: 'Strøket'
			+ Data fra `tblfartspes` som skal vises på enkelstående linjer , er 
			-- `VerftNavn` (fra `tblVerft`) og `Byggenr`. Felt tittel: 'Verft'
			-- `TypeFunksjon` (fra `tblzFartFunk`), Felt tittel: 'Funksjon', space, `FunkDetalj`, uten tittel.
			-- `DriftMiddel` (fra `tblzFartDrift`). Felt tittel: 'Fremdrift'
			-- `Rigg` med felt tittel: 'Rigg'
			-- `TypeSkrog` (fra `tblzFartSkrog`). Felt tittel: 'Skrogtype'
			-- `Tonnasje` med felt tittel: 'Tonn (Brt)'
			-- `MotorType` med felt tittel: 'Motor' konkatinert med space, `MotorEff` (uten tittel), ' HK, Max fart ca ' og `MaxFart` (uten tittel).  
			-- `Lengde` med felt tittel: 'Lengde/Bredde/dypg (fot)' konkatinert med '/', `Bredde`(uten tittel), '/', `Dypg`(uten tittel).  
	- Felles for dette feltet er at linjer kun vises hvis ett av feltene på hver linje har innhold.
	- Høyre felt starter med å vise data der `tblfarttid.ObjID` = `ObjID`i raden som ble klikket på i `openfil/fartoyer.php` og `tblfarttid.FartID` = `FartID`i raden som ble klikket på i `openfil/fartoyer.php` med tilhørende `tblfartspes` der `tblfartspes.SpesID` = `tblfarttid.SpesID`
	- Oppdateres med nye data basert på klikket rad i TabA.

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

