<?php
// === INTESTAZIONI CORS ===
// Permetti l'accesso da qualsiasi origine. Per produzione, dovresti limitarlo.
header("Access-Control-Allow-Origin: *"); 
// Specifica i metodi HTTP permessi.
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Specifica le intestazioni personalizzate permesse.
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Il browser invia una richiesta OPTIONS "preflight" per verificare i permessi CORS.
// Dobbiamo rispondere con successo a questa richiesta, altrimenti bloccherà le successive.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204); // No Content - è la risposta standard per OPTIONS
    exit();
}

// === GESTIONE RICHIESTA ===

// Simula un database di appuntamenti/messaggi in un file JSON
$dataFile = __DIR__ . '/../data/appuntamenti_data.json';

/**
 * Legge i messaggi dal file JSON.
 * @param string $clientId
 * @return array
 */
function getMessages($clientId) {
    global $dataFile;
    if (!file_exists($dataFile)) return [];

    $allData = json_decode(file_get_contents($dataFile), true);
    return isset($allData[$clientId]) ? $allData[$clientId] : [];
}

/**
 * Salva un messaggio nel file JSON.
 * @param string $clientId
 * @param array $message
 */
function saveMessage($clientId, $message) {
    global $dataFile;
    $allData = [];
    if (file_exists($dataFile)) {
        $allData = json_decode(file_get_contents($dataFile), true);
    }

    if (!isset($allData[$clientId])) {
        $allData[$clientId] = [];
    }
    $allData[$clientId][] = $message;

    // Assicura che la directory esista
    if (!is_dir(dirname($dataFile))) {
        mkdir(dirname($dataFile), 0777, true);
    }

    file_put_contents($dataFile, json_encode($allData, JSON_PRETTY_PRINT));
}

$action = $_GET['action'] ?? null;
$clientId = $_GET['clientId'] ?? null;

header('Content-Type: application/json');

switch ($action) {
    case 'get_chat_messages':
        if (!$clientId) {
            http_response_code(400);
            echo json_encode(['error' => 'clientId mancante']);
            exit;
        }

        $allMessages = getMessages($clientId);
        $since = $_GET['since'] ?? null;

        if ($since) {
            $filteredMessages = array_filter($allMessages, function ($msg) use ($since) {
                return $msg['timestamp'] > $since;
            });
            echo json_encode(array_values($filteredMessages));
        } else {
            echo json_encode($allMessages);
        }
        break;

    case 'create_appointment':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['clientId']) || !isset($input['details'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dati di input non validi']);
            exit;
        }

        $userMessage = [
            'sender' => 'user',
            'text' => $input['details'],
            'timestamp' => $input['dateTime']
        ];
        saveMessage($input['clientId'], $userMessage);

        // Risposta automatica simulata dal veterinario
        $vetResponse = [
            'sender' => 'vet',
            'text' => 'Grazie per la sua richiesta. La contatteremo il prima possibile.',
            'timestamp' => (new DateTime())->format('c') // Timestamp attuale
        ];
        saveMessage($input['clientId'], $vetResponse);

        http_response_code(201); // Created
        echo json_encode($vetResponse); // Restituisce la risposta del vet
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Azione non trovata']);
        break;
}
