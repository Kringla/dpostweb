# DpostWeb_PReqD_Trinn4_UI_Layout.md

**Prosjekt:** DpostWeb
**Database:** Dpostendb
**Trinn:** 4 – Utseende, layout og UI-standarder
**Dato:** (settes ved innsjekk)

---

## 1. Formål

Dette dokumentet definerer **utseende-, layout- og UI-standarder** for DpostWeb.
Målet er å sikre et konsistent, tilgjengelig og vedlikeholdbart grensesnitt på tvers av alle skjermer i prosjektet, i tråd med prosjektets retningslinjer.

---

## 2. Overordnede prinsipper

* **Enkelhet:** Minimal visuell støy, tydelige primærhandlinger.
* **Konsistens:** Samme typografi, spacing og knappestørrelser på tvers av moduler.
* **Lesbarhet:** Linjelengde og kontrast prioriteres.
* **Tilgjengelighet:** WCAG AA som utgangspunkt (kontrast ≥ 4.5:1, fokustilstander, semantikk).
* **Responsivitet:** Mobil først, deretter tablet/desktop.

---

## 3. Breakpoints og rammestørrelser

* **Global wrapper (page width):** maks **1900px**.
* **Innholdscontainere:** maks **1600px** (optimal lesbarhet på desktop).
* **Breakpoints (veiledende):**

  * `≤ 480px` (mobil smal)
  * `481–768px` (mobil bred / liten tablet)
  * `769–1024px` (tablet)
  * `1025–1440px` (laptop)
  * `1441–1900px` (desktop stor)

**Container-regel:** Innhold sentreres med `margin: 0 auto;` og har standard padding `1.2em`.

---

## 4. Typografi

* **Fontfamilie:** Sans-serif (f.eks. `system-ui`, fallback `Arial`, `Helvetica`).
* **Grunnstørrelse:** 16px.
* **Linjehøyde:** 1.5–1.65 (tekst) / 1.2 (overskrifter).
* **Overskrifter:**

  * `h1`: 2.0rem
  * `h2`: 1.6rem
  * `h3`: 1.3rem
* **Vekt:**

  * Titler: 600–700
  * Brødtekst: 400–500
* **Fremheving:** bruk `<strong>`/`<em>` med måte; unngå rene fargeendringer som eneste signal.

---

## 5. Farger og kontrast (palett)

* **Primær:** maritim blå (eks.: `#0D3B66`)
* **Sekundær:** lys grå (eks.: `#F1F3F5`)
* **Aksent:** dempet gul/oransje for fokuserte elementer (eks.: `#F4D35E`)
* **Tekst:** nesten-sort (eks.: `#111`)
* **Lenker:** primærfarge med understrek ved hover/fokus
* **Feil/Advarsel/Suksess:** standardiserte varianter med tydelige kontraster

**Kontrast:** Minst 4.5:1 for tekst mot bakgrunn.

---

## 6. Spacing og grid

* **Basisenhet:** 4px (4/8/12/16/24/32).
* **Seksjoner:** vertikal margin 24–32px.
* **Kort/Paneler:** padding 16–24px.
* **Grid:** fleksibel `grid`/`flex` etter behov, med kolonne-gap 16–24px.
* **Sticky topp/bunn:** kun der det styrker navigasjon; unngå overlapping.

---

## 7. Komponenter

### 7.1 Knapper

* **Størrelser:** like høyder (min 40px), konsekvent horisontal padding.
* **Primærknapp:** fylt med primærfarge, hvit tekst.
* **Sekundær:** svak kantlinje/lys bakgrunn.
* **Tilstand:** `:hover`, `:focus-visible`, `:disabled`.
* **Ikon + tekst:** ikon til venstre, 8px gap.

### 7.2 Tabeller

* **Sticky header** ved lange lister.
* **Zebra-striper** for lesbarhet.
* **Kolonner:** min-bredder pr. datakategori (år/utgave/side, tittel, navn).
* **Responsivitet:** horisontal scroll på mobil, ikke bryt kolonnehierarki.
* **Interaksjon:** klikkbare rader for detaljvisning (med `cursor: pointer`).

### 7.3 Skjemaer

* **Label over felt** (enkelt språk).
* **Hjelpetekst** under felt der nødvendig.
* **Validering:** inline feilmelding, kort og spesifikk – ikke rød farge alene; bruk ikon/tekst.
* **Feltbredde:** fylle container opptil 600–800px for lesbarhet.
* **Gruppering:** metadata / relasjoner / innhold i seksjoner.

### 7.4 Navigasjon og brødsmulesti

* Toppmenyen markerer aktiv seksjon (skjules på landingssiden for å unngå duplikater).
* Brødsmulesti er midlertidig deaktivert og vises ikke.

---

## 8. Ikoner og illustrasjoner

* **Ikonstil:** enkle linjeikoner, konsistent linjetykkelse.
* **Størrelser:** 16/20/24px etter kontekst.
* **Alternativtekst:** alle meningsbærende ikoner/bilder har `alt`-tekst.

---

## 9. Tilgjengelighet (A11y)

* **Tastaturnavigasjon:** alle interaktive elementer kan fokuseres med Tab.
* **Fokusstil:** tydelig fokusramme (`outline`) med høy kontrast.
* **ARIA:** `aria-label` for skjemaer/søk, `role` kun ved behov.
* **Skjermleser:** skjul visuelle elementer via `visually-hidden`-mønster der nødvendig.
* **Feilmeldinger:** kobles med `aria-describedby`.

---

## 10. Responsiv oppførsel

* **Mobil:** vertikal stacking, full bredde på felt/knapper.
* **Tablet:** to-kolonne grid der mulig.
* **Desktop:** flere kolonner; behold 1600px container.
* **Tabeller:** `overflow-x: auto` på små skjermer.

---

## 11. Ytelse

* Minimal blocking CSS/JS.
* Bilder skaleres/tommes for unødvendige bytes.
* Cache headers på statiske ressurser.
* Unngå tunge webfonter hvis ikke nødvendig.

---

## 12. Feilhåndtering og tomme tilstander

* **Tomme søkeresultat:** kort forklarende tekst og forslag.
* **Feilpanel:** konsekvent komponent med tittel, beskrivelse og forslag til tiltak.
* **Loading-indikatorer:** spinner/progressbar med `aria-live="polite"`.

---

## 13. Test og verifikasjon

| Mål             | Metode                         | Forventning                     |
| --------------- | ------------------------------ | ------------------------------- |
| Kontrast        | Verifiser i dev-verktøy        | ≥ 4.5:1 på tekst                |
| Fokustilstander | Tast Tab gjennom UI            | Synlig fokus på alle kontroller |
| Responsivitet   | Manuell resize + devtools      | Ingen brudd i layout            |
| Tabeller        | Test sticky header/scroll      | Kolonner lesbare ≤ 480px        |
| Skjemaer        | Test validering og hjelpetekst | Feil tydelige, ikke kun farge   |

---

## 14. Endringshåndtering

* Endringer i stil/komponenter beskrives som **hele blokker** eller **patch med 3 linjer før/etter**.
* All CSS samles i `css/app.css` eller modulære `.css`-filer etter avtale.
* Ingen inline CSS i `.php`.

---

## 15. Plass for videre spesifikasjon

* Komponentbibliotek (kort, paneler, tabs, badges).
* Mørk modus (tema-variabler).
* Utskriftsstil (print CSS) for artikler/detaljer.

---

## 16. Vedlegg / referanser

* DpostWeb_PReqD.md (overordnet plan)
* DpostWeb_PStrD.md (struktur)
* AGENTS.md (regler og krav)
* DpostWeb_PReqD_Trinn2_Søk.md (lesesøk)
* DpostWeb_PReqD_Trinn3_CRUD.md (CRUD)

---
