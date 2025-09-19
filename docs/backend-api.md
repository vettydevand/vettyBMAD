# Documentazione API Backend (`test.php`)

Questo documento descrive gli endpoint API disponibili nello script `test.php`.

## File Dati

- **`appuntamenti_data.json`**: Un file JSON che agisce come un semplice database per memorizzare tutti i dati relativi agli appuntamenti e alle chat.

## Gestione della Concorrenza

Per prevenire la corruzione del file JSON a causa di scritture simultanee, lo script implementa un sistema di **file locking** (`flock`) durante tutte le operazioni di scrittura.

---

## Endpoints

### 1. Richiesta Iniziale Utente (Creazione Chat)

Crea un nuovo "appuntamento" che funge da contenitore per una sessione di chat. Viene chiamato quando l'utente invia il primo messaggio.

- **URL**: `/test.php`
- **Metodo**: `POST`
- **Payload (JSON)**:
  ```json
  {
    "clientId": "client-1678886400000-abcdef",
    "details": "Il mio cane non mangia.",
    "dateTime": "2023-03-15T12:00:00Z"
  }
  ```
- **Risposta Successo (200 OK)**:
  ```json
  {
    "success": true,
    "appointment": {
      "clientId": "client-1678886400000-abcdef",
      "dateTime": "2023-03-15T12:00:00Z",
      "details": "Il mio cane non mangia.",
      "chatHistory": [ ... ]
    }
  }
  ```

### 2. Salvataggio Messaggio del Veterinario

Salva un messaggio inviato dal veterinario (tramite un webhook esterno, es. Make.com) nella cronologia della chat di un utente specifico.

- **URL**: `/test.php`
- **Metodo**: `POST`
- **Payload (JSON)**:
  ```json
  {
    "action": "save_vet_message",
    "clientId": "client-1678886400000-abcdef",
    "text": "Ha avuto altri sintomi?"
  }
  ```
- **Risposta Successo (200 OK)**:
  ```json
  {
    "success": true,
    "message": "Vet message saved"
  }
  ```
- **Risposta Errore (404 Not Found)**: Se il `clientId` non viene trovato.

### 3. Recupero Cronologia Chat

Fornisce la cronologia completa dei messaggi per un dato `clientId`. Utilizzato dal frontend per il polling.

- **URL**: `/test.php?action=get_chat_messages&clientId=<ID_CLIENT>`
- **Metodo**: `GET`
- **Parametri Query String**:
  - `action=get_chat_messages` (obbligatorio)
  - `clientId=<ID_CLIENT>` (obbligatorio)
- **Risposta Successo (200 OK)**: Un array di oggetti messaggio.
  ```json
  [
    {
      "sender": "user",
      "text": "Il mio cane non mangia.",
      "timestamp": "2023-03-15T12:00:00Z"
    },
    {
      "sender": "vet",
      "text": "Ha avuto altri sintomi?",
      "timestamp": "2023-03-15T12:05:00Z"
    }
  ]
  ```
- **Risposta Errore (400 Bad Request)**: Se `clientId` non Ã¨ fornito.
