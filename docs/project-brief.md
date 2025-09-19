# Project Brief: VettyBMAD - Evoluzione di un'Applicazione per Veterinari

## 1. Panoramica del Progetto

**Progetto:** VettyBMAD
**Data:** 2024-07-16
**Autore:** Gemini (nel ruolo di Analyst BMAD)
**Stato:** Inizio

Questo documento delinea il brief di progetto per l'evoluzione di un'applicazione esistente per studi veterinari. Il progetto parte da un'analisi dettagliata di un prototipo semi-funzionante e mira a trasformarlo in una soluzione professionale, bidirezionale e commercialmente valida, seguendo fedelmente la metodologia BMAD.

## 2. Contesto e Problema

Il cliente possiede un'applicazione per veterinari generata da AI che soffre di una limitazione critica: la **comunicazione è unidirezionale**. I clienti finali possono inviare richieste di appuntamento o emergenze dal sito web (un'interfaccia `gutenberg.html` su WordPress), e il veterinario riceve notifiche su Telegram tramite un'integrazione con Make.com. Tuttavia, **il veterinario non può rispondere ai clienti in modo che i messaggi appaiano nella chat del sito web**.

L'applicazione attuale simula risposte del bot, ma manca una reale interazione bidirezionale, minando l'utilità e la professionalità del servizio.

## 3. Obiettivi Principali (MVP)

L'obiettivo primario e immediato, che costituirà il nostro Minimum Viable Product (MVP), è **implementare la comunicazione bidirezionale completa**.

1.  **Ricezione Messaggi del Veterinario:** Permettere al veterinario di rispondere ai messaggi dei clienti direttamente da Telegram.
2.  **Integrazione e Archiviazione:** Configurare Make.com per inoltrare le risposte del veterinario al backend (`test.php`), associando ciascun messaggio al cliente corretto (`clientId`). Il backend dovrà archiviare questi messaggi in modo persistente e affidabile.
3.  **Visualizzazione in Chat:** Modificare l'interfaccia frontend (`gutenberg.html`) per recuperare e visualizzare in tempo reale i messaggi inviati dal veterinario all'interno della finestra di chat del cliente corrispondente.

## 4. Scope del Lavoro (Brownfield)

Questo progetto è classificato come **Brownfield**, poiché si basa su codice e infrastruttura preesistenti. L'intervento si concentrerà sulla modifica e l'estensione dei seguenti componenti:

- **Frontend:** `gutenberg.html` (HTML/CSS/JS)
- **Backend:** `test.php` (PHP)
- **Integrazione:** Blueprint di Make.com (`Integration Telegram Bot, Webhooks.blueprint.json` e `Integration Webhooks.blueprint.json`)
- **Archiviazione Dati:** Attualmente `appuntamenti_data.json` (da valutare se mantenere o sostituire con una soluzione più robusta come un database).

## 5. Visione a Lungo Termine (Oltre l'MVP)

Sebbene non rientri nello scope immediato, la visione a lungo termine (come delineato nel documento di analisi) prevede di trasformare l'applicazione in un prodotto SaaS (Software as a Service) rivendibile, strutturato in più livelli (Base, Pro, Premium). Le evoluzioni future potrebbero includere:

- Un **database relazionale** per una gestione robusta dei dati.
- Un **backend API** più strutturato (es. basato su un micro-framework).
- **WebSockets** per una chat real-time più efficiente.
- Un **dashboard web dedicato** per il veterinario.
- Integrazione con **AI conversazionale** avanzata.

## 6. Prossimi Passi (Secondo il Metodo BMAD)

1.  **Approvazione del Brief:** Questo documento deve essere approvato prima di procedere.
2.  **Fase di Pianificazione (PM):** Un Product Manager (PM) utilizzerà questo brief per creare un Product Requirements Document (PRD) dettagliato.
3.  **Fase di Architettura (Architect):** Un Architect progetterà la soluzione tecnica basandosi sul PRD.
