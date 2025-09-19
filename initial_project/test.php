<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$dataFile = 'appuntamenti_data.json';

function readData($file) {
    if (!file_exists($file)) {
        return ['appointments' => []];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: ['appointments' => []];
}

function writeData($file, $data) {
    $fp = fopen($file, 'w');
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
    } else {
        // Fallback or error handling
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)); 
    }
    fclose($fp);
}

if (!file_exists($dataFile)) {
    writeData($dataFile, ['appointments' => []]);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    echo json_encode(['status' => 'ok']);
    exit;
}

if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $postData = json_decode($input, true);

    if (isset($postData['action'])) {
        $data = readData($dataFile);

        switch ($postData['action']) {
            case 'save_vet_message':
                $clientId = $postData['clientId'] ?? null;
                $text = $postData['text'] ?? null;

                if (!$clientId || !$text) {
                    http_response_code(400);
                    echo json_encode(['error' => 'clientId and text are required']);
                    exit;
                }

                $updated = false;
                foreach ($data['appointments'] as &$apt) {
                    if ($apt['clientId'] === $clientId) {
                        $apt['chatHistory'][] = [
                            'sender' => 'vet',
                            'text' => $text,
                            'timestamp' => date('c')
                        ];
                        $updated = true;
                        break;
                    }
                }

                if ($updated) {
                    writeData($dataFile, $data);
                    echo json_encode(['success' => true, 'message' => 'Vet message saved']);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Appointment not found for clientId']);
                }
                break;
        }
    } else { // Fallback for original Make.com webhook
        $data = readData($dataFile);
        $appointment = [
            'clientId' => $postData['clientId'] ?? uniqid('client-'),
            'dateTime' => $postData['dateTime'] ?? date('c'),
            'details' => $postData['details'] ?? '',
            'chatHistory' => [
                [
                    'sender' => 'user',
                    'text' => $postData['details'] ?? '',
                    'timestamp' => date('c')
                ]
            ]
        ];
        $data['appointments'][] = $appointment;
        writeData($dataFile, $data);
        echo json_encode(['success' => true, 'appointment' => $appointment]);
    }

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
            
            $chatHistory = [];
            foreach ($data['appointments'] as $apt) {
                if ($apt['clientId'] === $clientId) {
                    $chatHistory = $apt['chatHistory'] ?? [];
                    break;
                }
            }
            echo json_encode($chatHistory);
            break;

        default:
            echo json_encode(['message' => 'Welcome to the API']);
    }
}
?>