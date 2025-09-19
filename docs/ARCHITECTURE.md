# Architettura del Progetto VettyBMAD

Questo documento descrive lo stato attuale dell'architettura del progetto e la visione per la sua evoluzione futura. Cattura le decisioni prese, il debito tecnico esistente e i piani per la sua risoluzione, in linea con la metodologia BMAD.

## Architettura Attuale (As-Is)

### Panoramica

L'architettura attuale Ã¨ suddivisa funzionalmente tra due rami Git principali:

- **`main`**: Contiene tutto il codice sorgente dell'applicazione (`src/`), i dati (`data/`) e la documentazione del progetto (`docs/`). Questo ramo rappresenta la fonte della veritÃ per lo sviluppo.
- **`gh-pages`**: Attualmente utilizzato come **ambiente di staging/QA temporaneo** per i test end-to-end dell'interfaccia frontend.

### Debito Tecnico e Workaround

L'uso attuale del ramo `gh-pages` Ã¨ un **workaround deliberato** introdotto per facilitare la fase di _Measure_ del nostro primo ciclo di sviluppo. Presenta i seguenti problemi noti:

1.  **Duplicazione del Codice:** Il file `index.html` sul ramo `gh-pages` Ã¨ una copia manuale e modificata di `src/gutenberg.html` dal ramo `main`. Questo introduce il rischio di disallineamento e rende la manutenzione complessa e soggetta a errori.

2.  **Conflitto di Scopo:** Il ramo `gh-pages` Ã¨, per convenzione di GitHub, associato al sito pubblico del progetto. L'uso attuale espone un'interfaccia di test non rifinita come "vetrina" del progetto, il che Ã¨ fuorviante e non professionale.

## Architettura Desiderata (To-Be)

### Visione

L'obiettivo Ã¨ evolvere verso un'architettura che separi nettamente lo sviluppo, la documentazione e la pubblicazione, automatizzando il piÃ¹ possibile per eliminare la gestione manuale e gli errori.

### Struttura Futura

1.  **Ramo `main`**: ContinuerÃ ad essere la fonte della veritÃ per il codice e la documentazione sorgente (i file Markdown in `docs/`).

2.  **Sito Statico per la Documentazione**: La cartella `docs/` sarÃ trattata come la sorgente per un sito di documentazione statico.

3.  **Ramo `gh-pages`**: ConterrÃ **esclusivamente** l'output HTML compilato del sito di documentazione. Questo ramo non sarÃ piÃ¹ modificato manualmente.

4.  **Automazione con GitHub Actions**: VerrÃ implementato un workflow di GitHub Actions che, ad ogni push sul ramo `main`:
    a. Esegue un job che utilizza un generatore di siti statici (es. Jekyll, MkDocs) per convertire il contenuto di `docs/` in un sito web HTML.
    b. Esegue il commit e il push automatico dell'output HTML generato sul ramo `gh-pages`.

### Vantaggi dell'Architettura Futura

- **Single Source of Truth**: La documentazione viene scritta e mantenuta in un unico posto (`main`).
- **Nessuna Duplicazione**: Il sito pubblicato Ã¨ un prodotto di build, non una copia manuale.
- **Coerenza**: Il sito del progetto Ã¨ sempre sincronizzato con l'ultima versione della documentazione.
- **ProfessionalitÃ**: Il ramo `gh-pages` serve una documentazione pulita e navigabile, agendo come una vera landing page per il progetto.

## Piano di Transizione

Il debito tecnico attuale sarÃ affrontato in un ciclo BMAD dedicato, immediatamente successivo a quello corrente. Le attivitÃ includeranno la scrittura del workflow della GitHub Action e la pulizia iniziale del ramo `gh-pages`.
