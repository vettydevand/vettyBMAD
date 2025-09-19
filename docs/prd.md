# Product Requirements Document (PRD): VettyBMAD MVP

## 1. Introduzione

**Prodotto:** VettyBMAD - Assistente Veterinario Intelligente
**Obiettivo:** MVP - Implementazione della Chat Bidirezionale
**Data:** 2024-07-16
**Autore:** Gemini (nel ruolo di Product Manager BMAD)

Questo documento definisce i requisiti per la prima versione (MVP) del progetto VettyBMAD. L'obiettivo è trasformare l'attuale prototipo unidirezionale in un'applicazione di chat bidirezionale funzionale, dove i veterinari possono rispondere ai clienti direttamente da Telegram e le risposte sono visibili sull'interfaccia web del cliente.

Questo PRD servirà come guida per gli agenti Architect e Developer per la progettazione e l'implementazione della soluzione.

## 2. Obiettivi e Finalità

- **Obiettivo Principale:** Abilitare la comunicazione bidirezionale tra cliente (via web) e veterinario (via Telegram).
- **Successo dell'MVP:** L'MVP sarà considerato un successo quando un veterinario potrà rispondere a un messaggio ricevuto su Telegram e il cliente potrà leggere quella risposta nella propria interfaccia di chat sul sito web in un tempo ragionevole.
- **Valore per l'Utente:** Fornire un canale di comunicazione diretto e funzionale, aumentando la fiducia e l'efficienza del servizio veterinario offerto.

## 3. Requisiti Funzionali (FR)

### FR1: Integrazione Telegram -> Backend

- **FR1.1:** Il sistema (Make.com) deve intercettare i messaggi di risposta inviati dal veterinario su Telegram.
- **FR1.2:** Il sistema deve associare in modo affidabile la risposta del veterinario al `clientId` del cliente originale. Si raccomanda un meccanismo che non richieda al veterinario di inserire manualmente l'ID, ma che lo derivi dal contesto del messaggio a cui si sta rispondendo.
- **FR1.3:** Il sistema deve inviare i dati del messaggio (testo, `clientId`, mittente "vet", timestamp) all'endpoint del backend (`test.php`) tramite una richiesta POST.
- **FR1.4:** Il payload inviato al backend deve avere un formato chiaro per distinguere un messaggio di chat da altre azioni (es. `{"type": "chat_message", ...}`).

### FR2: Gestione Messaggi nel Backend

- **FR2.1:** Il backend (`test.php`) deve essere in grado di ricevere e processare le richieste POST contenenti i messaggi di chat dal webhook di Make.com.
- **FR2.2:** Il backend deve archiviare in modo persistente i messaggi della chat. L'archiviazione deve includere il `clientId`, il testo del messaggio, il mittente (`sender`: "user" o "vet") e un `timestamp`.
- **FR2.3:** Il backend deve esporre un nuovo endpoint API (es. `GET /test.php?action=get_chat_messages&clientId=...`) che, dato un `clientId`, restituisca l'intera cronologia della conversazione per quel cliente, ordinata cronologicamente.

### FR3: Visualizzazione Messaggi nel Frontend

- **FR3.1:** L'interfaccia frontend (`gutenberg.html`) deve implementare un meccanismo per recuperare periodicamente (polling) i nuovi messaggi dall'endpoint `get_chat_messages`.
- **FR3.2:** I messaggi recuperati dal backend devono essere visualizzati dinamicamente nell'area della chat.
- **FR3.3:** I messaggi devono essere visivamente distinti in base al mittente (es. messaggi del cliente allineati a destra, messaggi del veterinario a sinistra).
- **FR3.4:** La logica esistente che simula le risposte del bot (`setTimeout` in `sendMessage`) deve essere rimossa o disattivata per evitare la visualizzazione di messaggi fittizi.

## 4. Requisiti Non Funzionali (NFR)

- **NFR1 (Performance):** I nuovi messaggi inviati dal veterinario devono apparire nell'interfaccia del cliente entro 10-15 secondi dall'invio.
- **NFR2 (Affidabilità):** Il sistema di archiviazione dei messaggi deve essere robusto per evitare la perdita di dati. Sebbene un file JSON sia accettabile per l'MVP, si raccomanda all'Architect di valutare e documentare i rischi e di proporre una migrazione a un database come passo successivo prioritario.
- **NFR3 (Sicurezza):** L'endpoint `get_chat_messages` non deve esporre pubblicamente le chat. Sebbene per l'MVP l'autenticazione non sia un requisito bloccante, l'accesso tramite `clientId` è il minimo indispensabile. L'Architect deve evidenziare i rischi di sicurezza e pianificare l'introduzione di un sistema di autenticazione.
- **NFR4 (Manutenibilità):** Il codice aggiunto (specialmente in `test.php`) deve essere chiaro, commentato e seguire le best practice per facilitare future evoluzioni.

## 5. Epopee e Storie Utente

### Epopea 1: Comunicazione Bidirezionale

_Come utente finale, voglio poter ricevere risposte dal veterinario nella chat del sito, in modo da avere una conversazione fluida e completa._

- **Storia 1.1 (Backend):** Come sviluppatore, voglio modificare il backend per ricevere, archiviare e servire i messaggi inviati dal veterinario, in modo che possano essere recuperati dal frontend.
- **Storia 1.2 (Integrazione):** Come sviluppatore, voglio configurare l'integrazione (Make.com) per catturare le risposte del veterinario su Telegram e inviarle al backend con il corretto `clientId`.
- **Storia 1.3 (Frontend):** Come utente finale, voglio vedere i messaggi di risposta del veterinario apparire nella mia finestra di chat quasi in tempo reale.

## 6. Fuori dallo Scope (per l'MVP)

Le seguenti funzionalità, sebbene desiderabili, sono esplicitamente **fuori dallo scope** di questo MVP:

- Migrazione a un database relazionale (es. MySQL).
- Implementazione di WebSockets in sostituzione del polling.
- Creazione di un dashboard web dedicato per il veterinario.
- Qualsiasi funzionalità delle versioni "Pro" o "Premium" (gestione avanzata appuntamenti, AI, ecc.).
- Un sistema di autenticazione e autorizzazione completo per gli utenti.
- Rebranding completo del prodotto in "Vetty Assistant".
- Rimozione di elementi non professionali (es. "QIIZ ALIMENTARE").

Questi punti saranno considerati per le iterazioni successive.
