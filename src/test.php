<?php
/**
 * Backend API per la gestione della chat e degli appuntamenti dello studio veterinario.
 * Questo script gestisce le richieste HTTP per:
 * - Ricevere nuovi appuntamenti/messaggi dall'utente (via Webhook da Make.com).
 * - Ricevere messaggi inviati dal veterinario (via Webhook da Make.com).
 * - Fornire la cronologia della chat a un client specifico.
 * Utilizza un file JSON (`appuntamenti_data.json`) come database semplice.
 */

// === HEADER ===
// Imposta gli header per consentire le richieste cross-origin (CORS) e definire il tipo di contenuto.
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permette a qualsiasi origine di accedere.
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Metodi HTTP consentiti.
header('Access-Control-Allow-Headers: Content-Type'); // Header consentiti nella richiesta.

// === CONFIGURAZIONE ===
$dataFile = '../data/appuntamenti_data.json'; // Nome del file usato come database.

// === FUNZIONI DI UTILITÀ PER I DATI ===

/**
 * Legge i dati dal file JSON.
 * Se il file non esiste, restituisce una struttura dati vuota.
 * Gestisce anche il caso in cui il JSON sia corrotto o illeggibile.
 * @param string $file Il percorso del file da cui leggere.
 * @return array I dati decodificati come array associativo.
 */
function readData($file) {
    if (!file_exists($file)) {
        return ['appointments' => []];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: ['appointments' => []]; // Fallback in caso di JSON non valido.
}

/**
 * Scrive i dati nel file JSON in modo sicuro.
 * Utilizza il file locking (flock) per prevenire scritture concorrenti che potrebbero corrompere il file.
 * Se il lock non puÃ² essere acquisito, usa un fallback (file_put_contents) che Ã¨ meno sicuro ma funzionale.
 * @param string $file Il percorso del file su cui scrivere.
 * @param array $data L'array di dati da codificare in JSON e salvare.
 */
function writeData($file, $data) {
    $fp = fopen($file, 'w');
    if (flock($fp, LOCK_EX)) { // Acquisisce un lock esclusivo
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT)); // JSON_PRETTY_PRINT per leggibilitÃ 
        flock($fp, LOCK_UN); // Rilascia il lock
    } else {
        // Fallback nel caso (improbabile) che flock fallisca.
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)); 
    }
    fclose($fp);
}

// === INIZIALIZZAZIONE ===
// Se il file dati non esiste, lo crea con una struttura iniziale.
if (!file_exists($dataFile)) {
    writeData($dataFile, ['appointments' => []]);
}

// === ROUTING PRINCIPALE ===
$method = $_SERVER['REQUEST_METHOD'];

// Gestisce le richieste pre-flight CORS inviate dai browser.
if ($method === 'OPTIONS') {
    echo json_encode(['status' => 'ok']);
    exit;
}

// --- GESTIONE RICHIESTE POST ---
if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $postData = json_decode($input, true);

    // Controlla se Ã¨ una richiesta strutturata con un campo 'action'
    if (isset($postData['action'])) {
        $data = readData($dataFile);

        switch ($postData['action']) {
            /**
             * Azione per salvare un messaggio inviato dal veterinario.
             * Questo webhook viene chiamato da Make.com quando il veterinario risponde.
             */
            case 'save_vet_message':
                $clientId = $postData['clientId'] ?? null;
                $text = $postData['text'] ?? null;

                if (!$clientId || !$text) {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'clientId and text are required']);
                    exit;
                }

                $updated = false;
                // Itera su tutti gli appuntamenti per trovare quello associato al clientId.
                // NOTA: Si assume che un client abbia una sola conversazione attiva.
                foreach ($data['appointments'] as &$apt) {
                    if ($apt['clientId'] === $clientId) {
                        $apt['chatHistory'][] = [
                            'sender' => 'vet',
                            'text' => $text,
                            'timestamp' => date('c') // Formato ISO 8601
                        ];
                        $updated = true;
                        break; // Trovato e aggiornato, esce dal ciclo.
                    }
                }

                if ($updated) {
                    writeData($dataFile, $data); // Salva i dati aggiornati.
                    echo json_encode(['success' => true, 'message' => 'Vet message saved']);
                } else {
                    http_response_code(404); // Not Found
                    echo json_encode(['error' => 'Appointment not found for clientId']);
                }
                break;
        }
    } else { 
        /**
         * Gestione fallback per il webhook originale che crea un nuovo appuntamento/chat.
         * Questo viene triggerato quando un utente inizia una nuova conversazione dal frontend.
         */
        $data = readData($dataFile);
        $appointment = [
            'clientId' => $postData['clientId'] ?? uniqid('client-'),
            'dateTime' => $postData['dateTime'] ?? date('c'),
            'details' => $postData['details'] ?? '',
            'chatHistory' => [
                [
                    'sender' => 'user', // Il primo messaggio Ã¨ sempre dell'utente
                    'text' => $postData['details'] ?? '',
                    'timestamp' => date('c')
                ]
            ]
        ];
        $data['appointments'][] = $appointment;
        writeData($dataFile, $data);
        echo json_encode(['success' => true, 'appointment' => $appointment]);
    }

// --- GESTIONE RICHIESTE GET ---
} else if ($method === 'GET') {
    $action = $_GET['action'] ?? 'default';
    $clientId = $_GET['clientId'] ?? null;

    $data = readData($dataFile);

    switch ($action) {
        /**
         * Azione per recuperare la cronologia della chat per un client specifico.
         * Chiamato dal frontend in polling per aggiornare l'interfaccia della chat.
         */
        case 'get_chat_messages':
            if (!$clientId) {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'clientId is required']);
                exit;
            }
            
            $chatHistory = [];
            // Cerca l'appuntamento (e quindi la chat) per il clientId fornito.
            foreach ($data['appointments'] as $apt) {
                if ($apt['clientId'] === $clientId) {
                    $chatHistory = $apt['chatHistory'] ?? [];
                    break;
                }
            }
            echo json_encode($chatHistory);
            break;

        // Default case per richieste GET non riconosciute.
        default:
            echo json_encode(['message' => 'Welcome to the Vetty API']);
    }
}
?>
