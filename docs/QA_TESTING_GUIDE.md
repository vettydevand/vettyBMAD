# Guida al Test di Quality Assurance (QA)

Questa guida descrive i passaggi necessari per testare l'interfaccia di chat dell'assistente virtuale VettyBMAD, assicurando che le modifiche al backend e al frontend funzionino come previsto.

## Prerequisiti

1.  **Repository clonato:** Assicurati di avere una copia locale del repository `vettyBMAD`.
2.  **PHP installato:** Per eseguire il server di backend, Ã¨ necessario avere PHP installato sulla propria macchina.
3.  **Accesso a un browser web.**

## Configurazione dell'Ambiente di Test

Per testare l'interfaccia in modo isolato, utilizziamo GitHub Pages per servire il file HTML del frontend. Questo ci permette di verificare il comportamento del frontend quando viene caricato da un dominio diverso da quello del backend, simulando uno scenario di produzione realistico.

### 1. Avviare il Server di Backend Locale

Il backend Ã¨ un semplice server PHP. Per avviarlo, esegui questo comando dalla root del tuo progetto:

```bash
php -S localhost:8000
```

Questo comando avvierÃ un server web sulla porta 8000. Lascia questo terminale in esecuzione per tutta la durata del test.

### 2. Preparare il Frontend per GitHub Pages

Abbiamo un ramo dedicato chiamato `gh-pages` per ospitare il nostro frontend di test.

1.  **Passa al ramo `main` e assicurati che sia aggiornato:**

    ```bash
    git checkout main
    git pull origin main
    ```

2.  **Crea o aggiorna il ramo `gh-pages`:**

    ```bash
    git checkout -b gh-pages
    git pull origin gh-pages # Opzionale, se il ramo esiste giÃ
    ```

3.  **Copia e rinomina il file del frontend:**

    ```bash
    cp src/gutenberg.html ./index.html
    ```

4.  **Modifica `index.html` per puntare al backend locale:**
    Apri il file `index.html` e cambia la riga `const API_URL = 'test.php';` in:

    ```javascript
    const API_URL = 'http://localhost:8000/src/test.php';
    ```

5.  **Esegui il commit e il push delle modifiche:**
    ```bash
    git add index.html
    git commit -m "docs: Update test frontend for QA session"
    git push -u origin gh-pages
    ```

### 3. Attivare GitHub Pages sul Repository

Se Ã¨ la prima volta che usi GitHub Pages su questo repository, devi attivarlo.

1.  Vai alla pagina principale del tuo repository su GitHub (es. `https://github.com/tuo-utente/vettyBMAD`).
2.  Clicca sulla scheda **"Settings"** (Impostazioni).
3.  Nel menu a sinistra, clicca su **"Pages"**.
4.  Nella sezione "Build and deployment", sotto "Source", seleziona **"Deploy from a branch"**.
5.  Sotto "Branch", seleziona il ramo **`gh-pages`** e lascia la cartella su **`/(root)`**.
6.  Clicca su **"Save"**.

Dopo pochi minuti, il tuo sito sarÃ pubblicato all'URL indicato nella parte superiore della pagina (solitamente `https://tuo-utente.github.io/vettyBMAD/`).

## Esecuzione del Test Manuale

Una volta che l'ambiente Ã¨ configurato, procedi con i seguenti passaggi.

1.  **Apri l'URL di GitHub Pages** nel tuo browser.
2.  **Verifica il Caricamento Iniziale:** La cronologia della chat dovrebbe caricarsi senza sfarfallio (flickering).
3.  **Invia un Nuovo Messaggio:** Digita un messaggio e premi "Invia". Il messaggio deve apparire istantaneamente nella chat, allineato a destra.
4.  **Verifica la Persistenza:** Ricarica la pagina. Il messaggio inviato deve essere presente nella cronologia.
5.  **Simula una Risposta del Veterinario:** Esegui questo comando `curl` nel tuo terminale per inviare un messaggio come se fosse il veterinario:
    ```bash
    # Assicurati di usare un identificativo diverso per il `clientId` per simulare un altro utente
    curl -X POST http://localhost:8000/src/test.php -d '{"action": "create_appointment", "clientId": "vet-reply-test", "details": "Grazie per il suo messaggio, la ricontatteremo a breve.", "dateTime": "'$(date -u +"%Y-%m-%dT%H:%M:%SZ")'"}'
    ```
6.  **Osserva l'Aggiornamento Automatico:** Entro 5-10 secondi, il messaggio del veterinario dovrebbe apparire nella chat nel browser, allineato a sinistra, senza che la pagina si ricarichi o che lo scroll si resetti.

## Criteri di Successo

Il test ha successo se tutti i seguenti punti sono verificati:

- L'interfaccia della chat rimane stabile e fluida.
- I nuovi messaggi (sia dell'utente che simulati) appaiono automaticamente senza bisogno di ricaricare la pagina.
- Non si verificano sfarfallii o reset della posizione di scroll durante gli aggiornamenti.
