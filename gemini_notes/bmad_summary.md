Ho una solida conoscenza del metodo BMAD. Ecco un riassunto di quanto ho appreso:

**Concetti fondamentali del metodo BMAD:**

- **Flusso di lavoro in due fasi:**
  1.  **Fase di pianificazione (interfaccia utente Web o IDE):** questa fase si concentra sulla creazione dei file `prd.md` (documento dei requisiti del prodotto) e `architecture.md`. Coinvolge un team di agenti di intelligenza artificiale:
      - **Analyst:** esegue ricerche, brainstorming e crea una scheda di progetto.
      - **PM (Product Manager):** crea il PRD dalla scheda, definendo requisiti funzionali e non funzionali, epopee e storie utente.
      - **Esperto UX (opzionale):** crea una specifica front-end e prompt dell'interfaccia utente.
      - **Architect:** crea il documento di architettura basato sul PRD e sulle specifiche UX.
      - **QA (Test Architect):** fornisce una strategia di test iniziale e una valutazione dei rischi.
      - **PO (Product Owner):** garantisce l'allineamento tra i documenti, esegue liste di controllo e suddivide i documenti in epopee e storie.
  2.  **Ciclo di sviluppo (IDE):** questa fase si concentra sull'implementazione delle storie.
      - **SM (Scrum Master):** redige la storia successiva per l'agente di sviluppo, incorporando le informazioni dall'epopea e dall'architettura frammentate.
      - **Sviluppatore (Developer):** implementa le attività e i test per la storia.
      - **QA (Test Architect):** esamina il codice, esegue l'analisi dei rischi, la progettazione dei test e gestisce i quality gate.

- **Artefatti chiave:**
  - `docs/prd.md`: Documento dei requisiti del prodotto.
  - `docs/architecture.md`: Documento di architettura.
  - `docs/epics/`: Epopee frammentate dal PRD.
  - `docs/stories/`: Storie frammentate dalle epopee.
  - `docs/qa/assessments/`: Valutazioni QA (rischio, progettazione test, ecc.).
  - `docs/qa/gates/`: Risultati dei quality gate QA.

- **Ruoli e responsabilità degli agenti:**
  - **BMAD-Master:** un agente versatile in grado di eseguire qualsiasi attività ad eccezione dell'effettiva implementazione della storia. Ottimo per gli utenti che non vogliono passare da un agente all'altro.
  - **BMAD-Orchestrator:** un agente pesante per i pacchetti Web, non per l'uso in IDE.
  - **Analyst:** ricerca e creazione di schede di progetto.
  - **PM:** creazione del PRD.
  - **Architect:** progettazione dell'architettura.
  - **Esperto UX:** progettazione front-end.
  - **PO:** allineamento e frammentazione dei documenti.
  - **SM:** redazione della storia per lo sviluppatore.
  - **Sviluppatore:** implementazione del codice.
  - **QA (Quinn):** Test Architect, responsabile della qualità, della valutazione dei rischi e della strategia di test.

- **Comandi dell'agente QA (Quinn):**
  - `*risk`: valuta i rischi prima dello sviluppo.
  - `*design`: crea una strategia di test.
  - `*trace`: verifica la copertura dei test during lo sviluppo.
  - `*nfr`: controlla i requisiti non funzionali.
  - `*review`: esegue una valutazione completa e scrive un quality gate.
  - `*gate`: aggiorna lo stato del quality gate.

- **Configurazione:**
  - `technical-preferences.md`: un file per personalizzare il metodo con i tuoi modelli di progettazione e le tue tecnologie preferite.
  - `.bmad-core/core-config.yaml`: un file di configurazione per definire quali file l'agente di sviluppo deve sempre caricare.
