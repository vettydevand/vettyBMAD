# Panoramica del Progetto VettyBMAD

**VettyBMAD** Ã¨ un'applicazione di assistente virtuale progettata per studi veterinari. Lo scopo Ã¨ ottimizzare la comunicazione con i clienti, semplificare la gestione degli appuntamenti e fornire un canale diretto per le emergenze.

## Obiettivi del Progetto

- **Migliorare l'Efficienza:** Ridurre il carico di lavoro del personale di segreteria automatizzando le richieste di appuntamento e le domande frequenti.
- **Aumentare la Soddisfazione del Cliente:** Offrire ai clienti un modo moderno, rapido e asincrono per comunicare con lo studio, accessibile 24/7.
- **Centralizzare le Comunicazioni:** Integrare le conversazioni web con uno strumento di comunicazione interna (Telegram) per permettere al personale di rispondere da un unico posto.
- **Tracciare le Interazioni:** Mantenere uno storico delle conversazioni e degli appuntamenti per ogni cliente.

## FunzionalitÃ Chiave

- **Chat Bidirezionale in Tempo Reale:** Gli utenti possono inviare messaggi dal sito web, e il personale puÃ² rispondere direttamente da Telegram.
- **Prenotazione Appuntamenti:** Un'interfaccia calendario permette agli utenti di selezionare una data e un orario, inviando una richiesta che il personale puÃ² confermare o modificare.
- **Notifiche di Emergenza:** Un pulsante dedicato consente agli utenti di inviare una notifica di emergenza direttamente al canale Telegram del personale.
- **Interfaccia Adattiva:** Il frontend Ã¨ progettato per essere integrato facilmente in qualsiasi pagina web.

## Filosofia di Sviluppo: Il Metodo BMAD

Questo progetto segue un approccio iterativo e data-driven chiamato **BMAD**: Build, Measure, Analyze, Document.

- **Build (Costruisci):** Sviluppiamo funzionalitÃ in piccoli cicli rapidi.
- **Measure (Misura):** Raccogliamo dati e feedback su ciÃ² che abbiamo costruito.
- **Analyze (Analizza):** Analizziamo i dati per identificare problemi e opportunitÃ .
- **Document (Documenta):** Documentiamo le decisioni, il codice e i piani futuri.

Per maggiori dettagli, consulta il documento sulla [Metodologia BMAD applicata a questo progetto](./BMAD_METHODOLOGY.md).

## Architettura e Struttura

L'applicazione si basa su una semplice architettura a tre componenti:

1.  **Frontend:** Un file HTML/JavaScript (`src/gutenberg.html`).
2.  **Backend API:** Un singolo file PHP (`src/test.php`).
3.  **Database (File JSON):** Un file JSON (`data/appuntamenti_data.json`) per lo storage.
4.  **Integrazione Esterna (Make/Telegram):** Gestione delle notifiche tramite webhook.

### Struttura del Repository

- **/src**: Codice sorgente dell'applicazione.
- **/data**: Dati dell'applicazione.
- **/docs**: Documentazione del progetto.
- **/archive**: Versioni storiche del codice.

## Installazione

Per le istruzioni dettagliate su come installare e aggiornare l'applicazione, fare riferimento alla [Guida di Installazione](./INSTALL_GUIDE.md).
