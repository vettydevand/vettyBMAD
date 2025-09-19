# Guida all'Installazione - VettyBMAD

Questo documento fornisce le istruzioni per configurare ed eseguire l'applicazione VettyBMAD, sia in un ambiente di sviluppo locale che in produzione.

---

## Guida Rapida per Sviluppo Locale

Questo metodo è il più semplice e veloce per avviare l'applicazione sul tuo computer per test e sviluppo. Utilizza il server web integrato di PHP.

### Prerequisiti

- [PHP](https://www.php.net/manual/en/install.php) (versione 7.4 o successiva) installato e accessibile dalla linea di comando.
- Il codice sorgente del progetto scaricato sul tuo computer.

### Passi

1.  **Apri il Terminale:**
    Apri la tua applicazione terminale (Terminal, PowerShell, CMD, ecc.).

2.  **Naviga nella Directory del Progetto:**
    Usando il comando `cd`, spostati nella cartella principale di questo progetto (la cartella dove si trova questo file e la directory `src`).

3.  **Avvia il Server PHP:**
    Esegui questo comando:

    ```bash
    php -S localhost:8000 -t src/
    ```

    - `php -S localhost:8000`: Avvia un server web sulla porta 8000.
    - `-t src/`: Imposta la directory `src` come root del server. Questo è fondamentale per far funzionare correttamente i percorsi dei file.

4.  **Apri l'Applicazione nel Browser:**
    Apri il tuo browser web e visita il seguente indirizzo:

    [http://localhost:8000/gutenberg.html](http://localhost:8000/gutenberg.html)

5.  **Verifica Permessi (se necessario):**
    Se riscontri errori quando invii un messaggio, potrebbe essere un problema di permessi. Assicurati che la directory `data/` e il file `data/appuntamenti_data.json` siano scrivibili dal processo PHP.
    Nella maggior parte dei casi per lo sviluppo locale, questo non è un problema.

Ora dovresti vedere l'interfaccia della chat funzionante e pronta per essere testata.

---

## Scenari di Deploy in Produzione

(Il resto della guida per WordPress/Aruba rimane qui...)

### Scenario 1: Aggiornamento per il Cliente Esistente (Aruba WP Hosting)

...

### Scenario 2: Installazione da Zero (Nuova Istanza WordPress o Altro)

...
