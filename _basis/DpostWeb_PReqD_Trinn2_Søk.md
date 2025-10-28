# DpostWeb_PReqD_Trinn2_Søk.md

**Prosjekt:** DpostWeb
**Database:** Dpostendb
**Trinn:** 2 – Søkegrensesnitt og visning (lesetilgang)
**Dato:** (settes ved innsjekk)

---

## 1. Formål

Dette dokumentet definerer de tre primære søkene for DpostWebs landingsside i trinn 2.
Formålet er å gi alle brukere (*role=user*) tilgang til artikler, bilder og annonser i databasen uten innlogging, med enkel og enhetlig navigasjon.
Dokumentet beskriver funksjonelle krav, datakilder, visningsstruktur og testmål.

---

## 2. Søkeoversikt

| Nr | Søkenavn                  | Type         | Datakilde                                | Kommentar                |
| -- | ------------------------- | ------------ | ---------------------------------------- | ------------------------ |
| 1  | Fritekstsøk i artikler    | Tekst        | `tblArtikkel` (+ `tblPerson`, `tblTema`) | Utelater annonser        |
| 2  | Fritekstsøk i billedtekst | Tekst        | `tblBilde` (+ `tblForfatter`)            | Kun feltet `billedtekst` |
| 3  | Annonser per annonsør     | Valg / klikk | `tblAnnonse` (+ `tblTema`)               | Oversikt + detaljer      |

> **Utvidelse:** Nye søk og knapper kan legges til under pkt. 2.4 senere i prosjektet.

---

## 2.1 Søke 1 – Artikler (uten annonser)

**Formål:**
Gi brukeren raskt fritekstsøk i redaksjonelt innhold uten annonser.

**Input:**
Ett fritekstfelt.

**Kilder:**

* `tblArtikkel` → `tittel`, `undertittel`, `ingress`, `tekst`
* `tblPerson` → `navn` (forfatter)
* `tblTema` → `temanavn`

**Sortering:** `utgave_år DESC`, `utgave_nr ASC`, `side ASC`

**Resultatvisning:**

| Felt               | Eksempel            |
| ------------------ | ------------------- |
| År / Utgave / Side | 1932 nr 4 s. 12     |
| Tittel             | “Postgangen i nord” |
| Forfatter          | Johan Ihlen         |
| Tema               | Samferdsel          |

**Detaljvisning:**
Full tekst m/ metadata + eventuelle bilder.

---

## 2.2 Søke 2 – Billedtekster

**Formål:**
Finne bilder basert på ord/uttrykk i billedtekst.

**Input:**
Fritekst + valgfri avgrensning (år/periode)

**Kilder:**

* `tblBilde` → `billedtekst`, `tittel`, `utgave_år`, `utgave_nr`
* `tblForfatter` → `navn` der `Fotograf = 1`

**Sortering:** `utgave_år DESC`, `billedtekst ASC`

**Resultatvisning:**

| Felt        | Eksempel                                  |
| ----------- | ----------------------------------------- |
| År / Utgave | 1928 nr 7                                 |
| Fotograf    | P. Andresen                               |
| Billedtekst | “Postskipet Polarlys ankommer Hammerfest” |

**Detaljvisning:**
Større bilde m/ billedtekst, fotograf, utgaveinfo, lenke til artikkel.

---

## 2.3 Søke 3 – Annonser per annonsør

**Formål:**
Gi oversikt over annonsører og deres annonser.

**Input:**
Fritekstfelt (delnavn) + valgfri filtrering på år/tema.

**Kilder:**

* `tblAnnonse` → `firma`, `tittel`, `annonsetekst`, `utgave_år`, `utgave_nr`, `side`, `tema_id`
* `tblTema` → `temanavn`

**Trinn 1 – Annonsøroversikt:**

| Annonsør                | Antall annonser |
| ----------------------- | --------------- |
| A/S Wilhelmsen Linje    | 47              |
| Den Norske Amerikalinje | 33              |
| Fred. Olsen Reederei    | 21              |

**Trinn 2 – Annonseliste:**

| År / Utgave / Side | Tittel                | Tema         |
| ------------------ | --------------------- | ------------ |
| 1934 nr 5 s. 7     | “Sjøfolk søkes til …” | Rekruttering |

**Detaljvisning:**
Full annonsetekst + bilde (mulig) + metadata.

---

## 2.4 Plass for ytterligere søkealternativer (utvidelse)

Her reserveres plass for nye knapper/søk som senere kan implementeres, f.eks.:

* “Artikler per tema”
* “Utgaver per årgang”
* “Bilder per fotograf”

---

## 3. Datakart

| Tabell         | Relevante felt                                                                                        | Relasjoner brukes i søk                            |
| -------------- | ----------------------------------------------------------------------------------------------------- | -------------------------------------------------- |
| `tblArtikkel`  | `tittel`, `undertittel`, `ingress`, `tekst`, `utgave_nr`, `utgave_år`, `side`, `person_id`, `tema_id` | `person_id → tblPerson.id`, `tema_id → tblTema.id` |
| `tblBilde`     | `billedtekst`, `tittel`, `fotograf_id`, `utgave_nr`, `utgave_år`                                      | `fotograf_id → tblForfatter.id (Fotograf=1)`       |
| `tblAnnonse`   | `firma`, `tittel`, `annonsetekst`, `tema_id`, `utgave_nr`, `utgave_år`, `side`                        | `tema_id → tblTema.id`                             |
| `tblForfatter` | `navn`, `Fotograf`                                                                                    | kobles via id                                      |
| `tblTema`      | `temanavn`                                                                                            | kobles via id                                      |

---

## 4. UI og layout

* Global max-width 1900 px (AGENTS-standard)
* Innholdskontainere 1600 px for lesbarhet

```css
.container { max-width: 1600px; margin: 0 auto; padding: 1.2em; }
```

---

## 5. Test og verifikasjon

| Mål                       | Metode                                                | Forventet resultat                                                                         |
| ------------------------- | ----------------------------------------------------- | ------------------------------------------------------------------------------------------ |
| Fritekstsøk i artikler    | Søk etter ord som forekommer i `tekst`                | Resultater matcher manuelt oppslag i Dpostendb (eks: søk «postgang» → “Postgangen i nord”) |
| Fritekstsøk i billedtekst | Søk etter navn eller stedsnavn fra kjent foto         | Viser billedteksten og fotografen korrekt                                                  |
| Annonser per annonsør     | Velg navn fra liste (f.eks. «Wilhelmsen»)             | Lister kun annonser fra valgt firma                                                        |
| Responsivitet             | Endre vindusstørrelse                                 | Ingen horisontal scroll, overskrifter forblir midtstilte                                   |
| Datakonsistens            | Sammenlign antall poster i søkeresultat med SQL COUNT | Avvik = 0                                                                                  |

**Verifisering:**

* Sammenligning av tilfeldige søk mot direkte SQL-spørringer i Dpostendb.
* Visuell sjekk av layout i Chrome og Edge.
* Ingen feil i UTF-8 enkoding (æ ø å skal vises korrekt).

---

## 6. Vedlegg / referanser

* `gerhard_dposten_schema.sql`
* `DpostWeb_PReqD.md` (Trinn 1–4 oversikt)
* `DpostWeb_PStrD.md` (prosjektstruktur)
* `AGENTS.md` (layout- og kvalitetskrav)

---
