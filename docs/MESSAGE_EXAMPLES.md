# Esempi di Messaggi e Interazioni

Questo documento mostra alcuni esempi di flussi di interazione tra l'utente e l'assistente virtuale VettyBMAD.

## Scenario 1: Richiesta di Appuntamento

1.  **Utente:** Clicca sul pulsante "Nuovo Appuntamento".
2.  **Sistema:** Mostra il calendario.
3.  **Utente:** Seleziona una data (es. 25 Ottobre 2024) e un orario (es. 10:30).
4.  **Sistema:** Invia la richiesta al backend.
5.  **Chat (Utente):** `Richiesta inviata: ven 25/10/2024 alle 10:30`
6.  **Chat (Bot):** `Conferma ricevuta! Il tuo appuntamento Ã¨ in attesa di approvazione. Riceverai una notifica qui quando verrÃ  confermato.`

---

## Scenario 2: Messaggio Generico dell'Utente

1.  **Utente:** Scrive "Ciao, il mio cane ha la tosse" e preme Invio.
2.  **Chat (Utente):** `Ciao, il mio cane ha la tosse`
3.  **Sistema:** Invia il messaggio al backend, che lo registra e notifica il veterinario su Telegram.
4.  **Chat (Bot):** `Grazie per il tuo messaggio. Un operatore ti risponderÃ  il prima possibile. Per urgenze, usa il pulsante EMERGENZA.`

---

## Scenario 3: Risposta del Veterinario (da Telegram)

1.  **Veterinario (su Telegram):** Risponde al messaggio precedente: "Ok, tienilo al caldo e monitoralo. Se domani non migliora, prenota una visita."
2.  **Sistema (Backend):** Riceve la risposta da Telegram e la invia al frontend dell'utente.
3.  **Chat (Bot):** `Ok, tienilo al caldo e monitoralo. Se domani non migliora, prenota una visita.`

---

## Scenario 4: Errore di Invio Messaggio

1.  **Utente:** Scrive un messaggio, ma la connessione a internet Ã¨ assente.
2.  **Chat (Utente):** Mostra il messaggio con un'icona di errore (es. un punto esclamativo rosso).
3.  **Sistema:** Mostra un banner o una notifica che informa della perdita di connessione.

---

## Scenario 5: Notifica di Conferma Appuntamento

1.  **Veterinario (tramite un'interfaccia o comando):** Conferma l'appuntamento richiesto nello Scenario 1.
2.  **Sistema (Backend):** Aggiorna lo stato dell'appuntamento.
3.  **Chat (Bot):** `GENTILE CLIENTE, il suo appuntamento di ven 25/10/2024 alle 10:30 Ã¨ stato CONFERMATO.`
