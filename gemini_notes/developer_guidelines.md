# Linee Guida per lo Sviluppo (Developer Gemini)

Questo documento contiene le buone pratiche e le direttive da seguire durante il ciclo di sviluppo.

## 1. Processo di Lavoro

- **Utilizzo di File di Appoggio**: Creare e utilizzare file di supporto (in `gemini_notes/`) per pianificare, prendere appunti e registrare decisioni. Questo migliora la tracciabilitÃ e la trasparenza.
- **Commit e Push Frequenti**: Non aspettare la fine di una fase per salvare il lavoro. Eseguire commit atomici e regolari con messaggi chiari che descrivono la modifica apportata. Questo facilita il tracciamento delle modifiche e il recupero in caso di errori.
- **Auto-Correzione**: Prima di considerare una fase conclusa, rivalutare il lavoro svolto alla luce delle linee guida e del feedback ricevuto. Apportare le correzioni necessarie prima di procedere.

## 2. QualitÃ del Codice

- **Commenti Dettagliati**: Commentare sempre il codice in modo approfondito. Spiegare il "perchÃ©" delle scelte implementative, non solo il "cosa". I commenti devono rendere il codice comprensibile a un altro sviluppatore (o a sÃ© stessi in futuro).
- **Documentazione Accurata**: Ogni nuova funzionalitÃ o modifica significativa deve essere accompagnata da una documentazione chiara.
- **Collegamento della Documentazione**: La nuova documentazione deve essere linkata e resa raggiungibile dai documenti principali (es. `architecture.md`) per garantire che non rimanga isolata.

## 3. Gestione Errori e Feedback

- **Apprendimento dagli Errori**: Ogni errore o fallimento (es. un comando fallito, un'interpretazione errata) va analizzato per estrarre una lezione e, se opportuno, aggiornare queste linee guida.
- **Internalizzazione del Feedback**: Il feedback dell'utente Ã¨ una fonte primaria di miglioramento. Ogni suggerimento va recepito e trasformato in un'azione concreta o in una nuova linea guida.
