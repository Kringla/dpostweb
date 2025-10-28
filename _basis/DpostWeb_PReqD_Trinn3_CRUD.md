# DpostWeb_PReqD_Trinn3_CRUD.md

**Prosjekt:** DpostWeb
**Database:** Dpostendb
**Trinn:** 3 – CRUD-funksjonalitet for Super og Admin
**Dato:** (settes ved innsjekk)

---

## 1. Formål

Dette dokumentet beskriver krav og spesifikasjoner for trinn 3 i utviklingsplanen for DpostWeb.
Trinnet omfatter etablering av **CRUD-funksjonalitet** (Create, Read, Update, Delete) for rollene `super` og `admin`.

Målet er å muliggjøre vedlikehold av data i de sentrale tabellene i Dpostendb, med vekt på enkelhet, sikkerhet og konsistent layout i henhold til AGENTS.md.

---

## 2. Omfang

### 2.1 Roller og rettigheter

| Rolle     | Tilgang                               | Kommentar                                          |
| --------- | ------------------------------------- | -------------------------------------------------- |
| **user**  | Kun lesetilgang (Trinn 2)             | Ingen CRUD                                         |
| **super** | Full CRUD i fagtabeller               | Artikler, bilder, annonser, personer, tema, fartøy |
| **admin** | Full CRUD + brukeradm + paramtabeller | I tillegg kan redigere `xuser` og struktur         |

### 2.2 CRUD-omfang

CRUD implementeres for følgende tabeller:

| Tabell            | Beskrivelse             | CRUD-ansvarlig rolle |
| ----------------- | ----------------------- | -------------------- |
| `tblArtikkel`     | Artikler og notiser     | super/admin          |
| `tblBilde`        | Foto og illustrasjoner  | super/admin          |
| `tblAnnonse`      | Annonser                | super/admin          |
| `tblTema`         | Temakoder               | admin                |
| `tblForfatter`    | Personer/fotografer     | super/admin          |
| `tblOrganisasjon` | Rederier, institusjoner | admin                |
| `tblFartøy`       | Skip/fartøy             | super/admin          |
| `xuser`           | Brukeradministrasjon    | admin                |
| `tblz*`           | Parameter-tabeller      | admin                |

---

## 3. Grunnleggende krav (fra AGENTS.md)

* Ingen kode i `config/` skal endres.
* Endringer skjer alltid som **hele blokker** eller som **patcher med 3 linjer før/etter**.
* Alle PHP-sider skal bruke `BASE_URL` og `h()` for output escaping.
* Skjermbredder: maks 1900 px (global), 1600 px (innhold).
* UTF-8 og `utf8mb4_unicode_ci` skal brukes i alle filer og queries.
* Kun `.php`, `.sql`, `.css`, `.js`, `.md`, `.jpg`, `.png` skal inngå i repoet.
* Ingen inline CSS; all styling i `css/app.css`.

---

## 4. Brukergrensesnitt

### 4.1 Plassering i struktur

* CRUD-sider for **super** ligger i `/admin/`.
* CRUD-sider for **admin** ligger i `/protfil/`.
* Felleskomponenter (listevisning, form, validering) i `/includes/`.

### 4.2 Layout og navigasjon

* Alle CRUD-sider følger samme layoutmal med toppmeny, heading og knappesett.
* Listevisning: tabell med paginering, sortering etter `id` eller `utgave_år`/`tittel`.
* Handlingsknapper: `Ny`, `Endre`, `Slett`, `Vis`.
* Skjemaer: inndelt i logiske seksjoner (metadata, relasjoner, innhold).
* Validering: enkel, feltbasert, med melding under feltet.

### 4.3 Navigasjonseksempel

```
protfil/
├── crud_artikkel.php
├── crud_bilde.php
├── crud_annonse.php
├── crud_fartoy.php
├── crud_param.php
└── brukeradmin.php
```

---

## 5. Datamodell og redigerbare felt

| Tabell         | Primærnøkkel | Redigerbare felt                                                                                      | Relasjoner                               |
| -------------- | ------------ | ----------------------------------------------------------------------------------------------------- | ---------------------------------------- |
| `tblArtikkel`  | `id`         | `tittel`, `undertittel`, `ingress`, `tekst`, `utgave_nr`, `utgave_år`, `side`, `person_id`, `tema_id` | `person_id→tblPerson`, `tema_id→tblTema` |
| `tblBilde`     | `id`         | `tittel`, `billedtekst`, `utgave_år`, `utgave_nr`, `fotograf_id`, `artikkel_id`                       | `fotograf_id→tblForfatter`               |
| `tblAnnonse`   | `id`         | `firma`, `tittel`, `annonsetekst`, `tema_id`, `utgave_nr`, `utgave_år`, `side`                        | `tema_id→tblTema`                        |
| `tblTema`      | `id`         | `temanavn`                                                                                            | –                                        |
| `tblForfatter` | `id`         | `navn`, `Fotograf`                                                                                    | –                                        |
| `tblFartøy`    | `id`         | `navn`, `byggår`, `verft`, `type`, `status`                                                           | –                                        |
| `xuser`        | `user_id`    | `email`, `role`, `isactive`, `lastused`                                                               | –                                        |

---

## 6. Sikkerhet og validering

* CRUD-operasjoner krever aktiv innlogget bruker med `$_SESSION['role']` = super/admin.
* Sletting skal kreve bekreftelse (JavaScript confirm + serverside sjekk).
* Feil logges til `logs/dpostweb.log`.
* SQL skal kjøres med prepared statements (mysqli/PDO).
* Input skal valideres med PHP `filter_var` og escapes i HTML med `h()`.

---

## 7. Test og verifikasjon

| Testmål           | Metode                            | Forventet resultat                                  |
| ----------------- | --------------------------------- | --------------------------------------------------- |
| CRUD for artikler | Opprett, endre, slett artikkel    | Endring synlig i database og i søk (Trinn 2)        |
| CRUD for bilder   | Opprett nytt bilde                | Visning oppdatert i billedsøket                     |
| CRUD for annonser | Rediger annonsetekst              | Endring reflekteres i "Annonser per annonsør"-søket |
| CRUD for brukere  | Legg til ny bruker                | Brukeren kan logge inn etterpå                      |
| Tilgangskontroll  | Prøv CRUD uten rolle              | Tilgang nektes og redirect til login.php            |
| Sletting          | Slett artikkel med koblede bilder | Systemet nekter sletting uten bekreftelse           |
| Dataformat        | Test æ/ø/å og spesialtegn         | Alle tegn vises korrekt (UTF-8)                     |

**Verifisering:**

* Testes lokalt (XAMPP) og på OnNet.
* Kontrollspørringer kjøres i MySQL Workbench.
* Sammenligning mellom GUI og direkte SQL COUNT for validering.
* Layout testes i Chrome og Edge.

---

## 8. Utvidelser (plass for fremtidige moduler)

* CRUD for param-tabeller (f.eks. fartøytyper, materialer, funksjoner).
* Utvidet bildebehandling (opplasting / kobling mot DigitaltMuseum).
* Historikk/logg for endringer i artikler og bilder.
* Mulighet for eksport til CSV/PDF for admin.

---

## 9. Vedlegg / referanser

* `DpostWeb_PReqD.md` (overordnet plan)
* `DpostWeb_PStrD.md` (struktur og mapper)
* `AGENTS.md` (retningslinjer og regler)
* `gerhard_dposten_schema.sql` (databasegrunnlag)
* `DpostWeb_PReqD_Trinn2_Søk.md` (søke-UI for lesetilgang)

---
