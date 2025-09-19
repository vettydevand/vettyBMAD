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
    // Se il file non esiste, lo crea con una struttura base.
    if (!is_dir(dirname($dataFile))) {
        mkdir(dirname($dataFile), 0777, true);
    }
    writeData($dataFile, ['appointments' => []]);
}


// === ROUTING ===
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    // Gestisce le richieste pre-flight CORS
    echo json_encode(['status' => 'ok']);
    exit;
}

// --- GESTIONE RICHIESTE POST ---
if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $postData = json_decode($input, true);

    // Validazione e sanitizzazione di base dell'input
    if (!is_array($postData)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        exit;
    }
    
    // Sanitizzazione ricorsiva per prevenire XSS
    array_walk_recursive($postData, function(&$value) {
        $value = is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
    });

    $data = readData($dataFile);
    $action = $postData['action'] ?? null;
    $clientId = $postData['clientId'] ?? null;

    if (!$clientId) {
        http_response_code(400);
        echo json_encode(['error' => 'clientId is required']);
        exit;
    }

    // Trova l'indice dell'appuntamento per il clientId specificato
    $appointmentIndex = -1;
    foreach ($data['appointments'] as $index => $apt) {
        if ($apt['clientId'] === $clientId) {
            $appointmentIndex = $index;
            break;
        }
    }

    switch ($action) {
        case 'save_vet_message':
        case 'create_appointment':
            $text = $postData['details'] ?? '';
            $timestamp = $postData['dateTime'] ?? (new DateTime())->format(DateTime::ATOM);
            $sender = ($action === 'save_vet_message') ? 'vet' : 'user';

            if (empty($text)) {
                 http_response_code(400);
                 echo json_encode(['error' => 'Message details are required.']);
                 exit;
            }
            
            $newMessage = [
                'sender' => $sender,
                'text' => $text,
                'timestamp' => $timestamp
            ];

            if ($appointmentIndex !== -1) {
                // Appuntamento esistente: aggiungi messaggio alla cronologia
                $data['appointments'][$appointmentIndex]['chatHistory'][] = $newMessage;
            } else {
                // Nuovo appuntamento: crea una nuova entry
                $newAppointment = [
                    'clientId' => $clientId,
                    'dateTime' => $timestamp, 
                    'details' => $text, // Il primo messaggio è anche il dettaglio iniziale
                    'chatHistory' => [$newMessage]
                ];
                $data['appointments'][] = $newAppointment;
            }
            
            writeData($dataFile, $data);
            echo json_encode(['status' => 'success', 'message' => 'Message saved.']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid or missing action']);
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
         * più recenti di quel timestamp (formato ISO 8601).
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
                        // Se 'since' non è specificato, restituisce l'intera cronologia.
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