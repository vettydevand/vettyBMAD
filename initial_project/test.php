<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Debug sempre - per qualsiasi richiesta
file_put_contents('debug_post.txt', print_r($_POST, true));
file_put_contents('debug_input.txt', file_get_contents('php://input'));
file_put_contents('debug_method.txt', $_SERVER['REQUEST_METHOD']);

// File dati appuntamenti
$dataFile = 'appuntamenti_data.json';

// Inizializza file dati se non esiste
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([
        'appointments' => [],
        'events' => [],
        'last_update' => time()
    ]));
}

// Leggi dati esistenti
$data = json_decode(file_get_contents($dataFile), true);
if (!$data) {
    $data = ['appointments' => [], 'events' => [], 'last_update' => time()];
}

// Gestione richieste
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    echo json_encode(['status' => 'options_ok']);
    exit;
}

if ($method === 'POST') {
    // ===== WEBHOOK DA MAKE =====
    $input = file_get_contents('php://input');
    $makeData = json_decode($input, true);
    
    if (!$makeData) {
        echo json_encode(['error' => 'Invalid JSON', 'input' => $input]);
        exit;
    }
    
    // Gestisci appuntamenti (cerca appointmentId)
    if (isset($makeData['appointmentId'])) {
        // Nuovo appuntamento da Make
        $appointment = [
            'appointmentId' => $makeData['appointmentId'],
            'clientId' => $makeData['clientId'] ?? 'unknown',
            'date' => $makeData['date'] ?? date('Y-m-d'),
            'time' => $makeData['time'] ?? '10:00',
            'status' => $makeData['status'] ?? 'pending',
            'note' => $makeData['note'] ?? '',
            'timestamp' => $makeData['timestamp'] ?? date('c')
        ];
        
        $data['appointments'][] = $appointment;
        
        // Aggiungi evento calendario
        $data['events'][] = [
            'title' => 'Occupato',
            'start' => $makeData['date'] . 'T' . $makeData['time'],
            'allDay' => false,
            'display' => 'background',
            'appointmentId' => $appointment['appointmentId']
        ];
        
        // Aggiorna timestamp
        $data['last_update'] = time();
        
        // Salva dati
        file_put_contents($dataFile, json_encode($data));
        
        echo json_encode(['success' => true, 'message' => 'Appointment saved', 'id' => $makeData['appointmentId']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No appointmentId found', 'received' => $makeData]);
    }
    
} else if ($method === 'GET') {
    // ===== RICHIESTE DALL'APPLICAZIONE =====
    
    $action = $_GET['action'] ?? 'default';
    
    switch ($action) {
        case 'events':
            // Ritorna eventi per FullCalendar
            echo json_encode($data['events']);
            break;
            
        case 'appointments':
            // Ritorna appuntamenti per un client specifico
            $clientId = $_GET['clientId'] ?? '';
            if ($clientId) {
                $userAppointments = array_filter($data['appointments'], 
                    function($apt) use ($clientId) {
                        return $apt['clientId'] === $clientId;
                    });
                echo json_encode(array_values($userAppointments));
            } else {
                echo json_encode($data['appointments']);
            }
            break;
            
        case 'status':
            // Controlla aggiornamenti (per polling)
            $lastCheck = $_GET['since'] ?? 0;
            $hasUpdates = $data['last_update'] > $lastCheck;
            
            echo json_encode([
                'hasUpdates' => $hasUpdates,
                'lastUpdate' => $data['last_update'],
                'timestamp' => time()
            ]);
            break;
            
        case 'available_slots':
            // Ritorna slot disponibili per una data
            $date = $_GET['date'] ?? date('Y-m-d');
            
            // Genera slot disponibili (9:00-17:00, ogni 30 min)
            $allSlots = [];
            for ($h = 9; $h <= 17; $h++) {
                if ($h < 17) {
                    $allSlots[] = sprintf('%02d:00', $h);
                    $allSlots[] = sprintf('%02d:30', $h);
                } else {
                    $allSlots[] = '17:00';
                }
            }
            
            // Rimuovi slot già prenotati
            $bookedSlots = [];
            foreach ($data['appointments'] as $apt) {
                if ($apt['date'] === $date && $apt['status'] !== 'rejected') {
                    $bookedSlots[] = $apt['time'];
                }
            }
            
            $availableSlots = array_diff($allSlots, $bookedSlots);
            
            echo json_encode([
                'date' => $date,
                'available' => array_values($availableSlots),
                'booked' => $bookedSlots
            ]);
            break;
            
        default:
            echo json_encode(['debug' => 'GET request received', 'method' => $method, 'action' => $action]);
    }
}
?>