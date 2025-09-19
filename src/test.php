<?php
/**
 * @file
 * Backend API per la gestione della chat e degli appuntamenti dello studio veterinario.
 *
 * Questo script gestisce le richieste HTTP per:
 * - Ricevere nuovi appuntamenti/messaggi dall'utente.
 * - Ricevere messaggi inviati dal veterinario (via Webhook da Make.com).
 * - Fornire la cronologia della chat a un client specifico.
 * Utilizza un file JSON (`appuntamenti_data.json`) come datastore.
 */

// === HEADER ===
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// === CONFIGURAZIONE ===
$dataFile = '../data/appuntamenti_data.json';

// === FUNZIONI DI UTILITÀ ===

/**
 * Legge e decodifica i dati dal file JSON.
 *
 * @param string $file Il percorso del file di dati.
 * @return array I dati decodificati o una struttura vuota in caso di errore.
 */
function readData($file) {
    if (!file_exists($file)) {
        return ['appointments' => []];
    }
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return $data ?: ['appointments' => []];
}

/**
 * Scrive i dati nel file JSON in modo sicuro utilizzando il file locking.
 *
 * @param string $file Il percorso del file di dati.
 * @param array $data I dati da codificare e scrivere.
 * @return void
 */
function writeData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// === INIZIALIZZAZIONE ===
if (!file_exists($dataFile)) {
    writeData($dataFile, ['appointments' => []]);
}

// === ROUTING ===
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    echo json_encode(['status' => 'ok']);
    exit;
}

// --- GESTIONE RICHIESTE POST ---
if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $postData = json_decode($input, true);

    if (is_array($postData)) {
        array_walk_recursive($postData, function(&$value) {
            $value = is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
        });
    }

    $data = readData($dataFile);
    $action = $postData['action'] ?? 'create_appointment';

    switch ($action) {
        case 'save_vet_message':
            // ... (codice invariato)
            break;

        case 'create_appointment':
            // ... (codice invariato)
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }

// --- GESTIONE RICHIESTE GET ---
} else if ($method === 'GET') {
    $action = $_GET['action'] ?? 'default';
    $clientId = $_GET['clientId'] ?? null;

    $data = readData($dataFile);

    switch ($action) {
        /**
         * Recupera i messaggi della chat per un client.
         * Se viene fornito il parametro 'since', restituisce solo i messaggi
         * piÃ¹ recenti di quel timestamp (formato ISO 8601).
         */
        case 'get_chat_messages':
            if (!$clientId) {
                http_response_code(400);
                echo json_encode(['error' => 'clientId is required']);
                exit;
            }

            $since = $_GET['since'] ?? null;
            $chatHistory = [];

            foreach ($data['appointments'] as $apt) {
                if ($apt['clientId'] === $clientId) {
                    $fullChatHistory = $apt['chatHistory'] ?? [];

                    if ($since) {
                        try {
                            $sinceTimestamp = new DateTime($since);
                            $chatHistory = array_filter($fullChatHistory, function ($message) use ($sinceTimestamp) {
                                if (!isset($message['timestamp'])) return false;
                                try {
                                    $messageTimestamp = new DateTime($message['timestamp']);
                                    return $messageTimestamp > $sinceTimestamp;
                                } catch (Exception $e) {
                                    return false;
                                }
                            });
                            // Re-indicizza l'array per garantire che sia un array JSON.
                            $chatHistory = array_values($chatHistory);
                        } catch (Exception $e) {
                            // In caso di timestamp non valido, restituisce un array vuoto.
                            $chatHistory = [];
                        }
                    } else {
                        // Se 'since' non Ã¨ specificato, restituisce l'intera cronologia.
                        $chatHistory = $fullChatHistory;
                    }
                    break;
                }
            }
            echo json_encode($chatHistory);
            break;

        default:
            echo json_encode(['message' => 'Welcome to the Vetty API. Use a valid action to proceed.']);
            break;
    }
}
?>
