# DpostWeb_UI_Checklist_and_DesignTokens.md

**Prosjekt:** DpostWeb
**Omfang:** Sammendrag av Trinn 2–4 (Søk, CRUD, UI/Layout)
**Formål:** Hurtigsjekk og felles designvariabler (tokens) for implementasjon og QA.

---

## 1. Kvalitetssjekkliste for utvikling og test

### 🔍 A. Funksjonalitet

| Punkt | Kontroll                                                 | Status |
| ----- | -------------------------------------------------------- | ------ |
| 1     | Fritekstsøk i artikler fungerer og ekskluderer annonser  | ☐      |
| 2     | Søk i billedtekst returnerer bilder med korrekt fotograf | ☐      |
| 3     | Annonser kan listes per annonsør                         | ☐      |
| 4     | CRUD-operasjoner fungerer for alle definert tabeller     | ☐      |
| 5     | Tilgangskontroll hindrer uautorisert CRUD                | ☐      |
| 6     | Sletting krever bekreftelse og beskytter relasjoner      | ☐      |
| 7     | Utfylling og lagring håndterer æ/ø/å korrekt (UTF‑8)     | ☐      |
| 8     | Ingen inline CSS/JS – alt i `app.css` / `includes`       | ☐      |

### 🎨 B. Layout og design

| Punkt | Kontroll                                    | Status |
| ----- | ------------------------------------------- | ------ |
| 1     | Global bredde 1900px, innhold 1600px        | ☐      |
| 2     | Knapper like store og konsekvent farget     | ☐      |
| 3     | Overskrifter midtstilte, spacing konsekvent | ☐      |
| 4     | Tabeller med sticky header og zebra-striper | ☐      |
| 5     | Ingen horisontal scroll < 768px             | ☐      |
| 6     | Hovedfont sans-serif, 16px base             | ☐      |
| 7     | Kontrast ≥ 4.5:1 for tekst                  | ☐      |
| 8     | Fokusrammer synlige ved Tab-navigasjon      | ☐      |

### ⚙️ C. Ytelse og teknisk

| Punkt | Kontroll                                      | Status |
| ----- | --------------------------------------------- | ------ |
| 1     | Bilder komprimert og cachebar                 | ☐      |
| 2     | Ingen blocking CSS/JS                         | ☐      |
| 3     | Bruk av prepared statements (SQL)             | ☐      |
| 4     | Ingen PHP-feil/varsler i logg                 | ☐      |
| 5     | Hard refresh (Ctrl+F5) gir konsistent visning | ☐      |
| 6     | Kontrollert i Chrome og Edge                  | ☐      |

---

## 2. Design Tokens (grunnverdier)

Disse verdiene brukes som faste referanser i CSS og layout.

### 🎨 Farger

| Navn                | Hex       | Bruk                     |
| ------------------- | --------- | ------------------------ |
| `--color-primary`   | `#0D3B66` | Primær (knapper, lenker) |
| `--color-secondary` | `#F1F3F5` | Sekundær bakgrunn        |
| `--color-accent`    | `#F4D35E` | Aksent/fokus             |
| `--color-text`      | `#111111` | Hovedtekst               |
| `--color-error`     | `#C1121F` | Feilmelding              |
| `--color-success`   | `#2E8B57` | Suksessmelding           |

### 🧱 Spacing

| Token       | Verdi | Beskrivelse           |
| ----------- | ----- | --------------------- |
| `--space-1` | 4px   | Grunn-enhet           |
| `--space-2` | 8px   | Små marginer          |
| `--space-3` | 16px  | Standard padding      |
| `--space-4` | 24px  | Seksjonsavstand       |
| `--space-5` | 32px  | Stor vertikal spacing |

### 🔤 Typografi

| Token                   | Verdi                                     |
| ----------------------- | ----------------------------------------- |
| `--font-base`           | `system-ui, Arial, Helvetica, sans-serif` |
| `--font-size-base`      | 16px                                      |
| `--font-size-h1`        | 2.0rem                                    |
| `--font-size-h2`        | 1.6rem                                    |
| `--font-size-h3`        | 1.3rem                                    |
| `--line-height-text`    | 1.5                                       |
| `--line-height-heading` | 1.2                                       |

### 📏 Layout-bredder

| Token               | Verdi  | Beskrivelse             |
| ------------------- | ------ | ----------------------- |
| `--width-global`    | 1900px | Maksimal sidebredde     |
| `--width-container` | 1600px | Maksimal innholdsbredde |
| `--width-form`      | 800px  | Maksimal formbredde     |

### 🔘 Knapper og UI

| Token                | Verdi | Bruk               |
| -------------------- | ----- | ------------------ |
| `--button-height`    | 40px  | Minimumshøyde      |
| `--border-radius`    | 4px   | Standard avrunding |
| `--transition-speed` | 0.2s  | Hover-effekter     |

Eksempel i CSS:

```css
:root {
  --color-primary: #0D3B66;
  --color-secondary: #F1F3F5;
  --space-3: 16px;
  --font-base: system-ui, Arial, sans-serif;
}
```

---

## 3. QA-sjekk før release

1. Kjør alle tester i QA-skjemaet (del 1–3).
2. Sammenlign søke- og CRUD-funksjonene mot manuelle SQL-spørringer.
3. Verifiser at layout følger tokens og AGENTS.md-regler.
4. Kontroller UTF‑8-visning av æ/ø/å i artikler, billedtekster og annonser.
5. Test responsive tilstander (≤480px, 768px, 1024px, 1900px).
6. Bekreft at alle roller får riktig tilgangsnivå.

---

## 4. Vedlegg / referanser

* DpostWeb_PReqD_Trinn2_Søk.md
* DpostWeb_PReqD_Trinn3_CRUD.md
* DpostWeb_PReqD_Trinn4_UI_Layout.md
* DpostWeb_PStrD.md
* AGENTS.md

---
