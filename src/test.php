<?php
/**
 * @file
 * Backend API per la gestione della chat e degli appuntamenti dello studio veterinario.
 */

// === HEADERS CORS ===
// Consente alla pagina su github.io di comunicare con questo server localhost.
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gestisce la richiesta "pre-flight" OPTIONS inviata dai browser.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// === HEADER ===
header('Content-Type: application/json');


// === CONFIGURAZIONE ===
$dataFile = '../data/appuntamenti_data.json';

// === FUNZIONI DI UTILITÀ ===

/**
 * Legge e decodifica i dati dal file JSON.
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
 * Scrive i dati nel file JSON in modo sicuro.
 */
function writeData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// === INIZIALIZZAZIONE ===
if (!file_exists($dataFile)) {
    if (!is_dir(dirname($dataFile))) {
        mkdir(dirname($dataFile), 0777, true);
    }
    writeData($dataFile, ['appointments' => []]);
}


// === ROUTING ===
$method = $_SERVER['REQUEST_METHOD'];

// --- GESTIONE RICHIESTE POST ---
if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $postData = json_decode($input, true);

    if (!is_array($postData)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        exit;
    }
    
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
                $data['appointments'][$appointmentIndex]['chatHistory'][] = $newMessage;
            } else {
                $newAppointment = [
                    'clientId' => $clientId,
                    'dateTime' => $timestamp, 
                    'details' => $text,
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
                            $chatHistory = array_values($chatHistory);
                        } catch (Exception $e) {
                            $chatHistory = [];
                        }
                    } else {
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