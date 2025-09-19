# Documentazione Logica Frontend (`gutenberg.html`)

Questo documento spiega il funzionamento dello script JavaScript incorporato in `gutenberg.html`.

## Obiettivo

L'obiettivo principale dello script Ã¨ fornire un'interfaccia di chat funzionale che permetta a un utente di comunicare con lo studio veterinario. La comunicazione Ã¨ asincrona e persistente tra le sessioni.

## Componenti Chiave

### 1. Gestione dell'IdentitÃ del Client (`clientId`)

- Al primo caricamento della pagina, lo script controlla se esiste un `vettyClientId` nel `localStorage` del browser.
- **Se non esiste**, ne genera uno nuovo combinando un timestamp, un numero casuale e una stringa fissa (`client-`). Questo ID viene quindi salvato nel `localStorage`.
- **Se esiste**, viene utilizzato quello.

Questo meccanismo garantisce che un utente mantenga la stessa identitÃ (e quindi la stessa cronologia di chat) anche se chiude e riapre il browser.

### 2. Invio di Messaggi (`sendMessage`)

1.  **Rendering Ottimistico**: Quando l'utente invia un messaggio, questo viene immediatamente aggiunto all'interfaccia grafica (UI) con lo stile "user". Questo dÃ una sensazione di reattivitÃ immediata.
2.  **Richiesta Asincrona**: Subito dopo, una richiesta `fetch` di tipo `POST` viene inviata al backend (`test.php`).
3.  **Payload**: Il corpo della richiesta contiene il `clientId`, il testo del messaggio (`details`) e un `timestamp`.

### 3. Ricezione di Messaggi (Polling con `fetchMessages`)

Per mantenere la chat aggiornata con le risposte del veterinario, lo script utilizza una tecnica di **polling**.

1.  **Funzione `fetchMessages`**: Questa funzione esegue una richiesta `GET` all'endpoint `/test.php?action=get_chat_messages&clientId=...`.
2.  **Intervallo di Esecuzione**: La funzione `fetchMessages` viene eseguita:
    - Una volta al caricamento della pagina (`DOMContentLoaded`).
    - Successivamente, ogni **5 secondi** tramite `setInterval`.
3.  **Aggiornamento dell'UI**: Quando la funzione riceve la cronologia dei messaggi dal backend, pulisce completamente l'area della chat e la ri-renderizza con tutti i messaggi ricevuti. Applica la classe `.user` o `.vet` a seconda del campo `sender` di ogni messaggio.

### 4. Ciclo di Vita

- **Inizio**: L'utente apre la pagina. Viene generato/recuperato un `clientId`. La cronologia esistente viene caricata. Il polling si avvia.
- **Invio**: L'utente scrive e invia un messaggio. Il messaggio appare subito e viene inviato al server.
- **Attesa**: Il polling continua in background.
- **Ricezione**: Quando il veterinario risponde, il backend aggiorna il file `appuntamenti_data.json`. Alla successiva esecuzione del polling, `fetchMessages` riceve la nuova cronologia, e il messaggio del veterinario appare nell'UI.
- **Fine**: Quando l'utente chiude la pagina, un `event listener` su `beforeunload` interrompe l'intervallo di polling per liberare risorse.
