<?php
// Database connection
$host = 'lamp-db';
$dbname = 'micn';
$user = 'root';
$pass = 'root_password';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// 4-hour block definitions (ordered for display: 3a→7a→...→11p→3a)
// 'rollover' = true means end is on the next calendar day
$BLOCKS = [
    ['start' => 3,  'end' => 7,  'label' => '3a–7a',  'rollover' => false],
    ['start' => 7,  'end' => 11, 'label' => '7a–11a', 'rollover' => false],
    ['start' => 11, 'end' => 15, 'label' => '11a–3p', 'rollover' => false],
    ['start' => 15, 'end' => 19, 'label' => '3p–7p',  'rollover' => false],
    ['start' => 19, 'end' => 23, 'label' => '7p–11p', 'rollover' => false],
    ['start' => 23, 'end' => 3,  'label' => '11p–3a', 'rollover' => true],
];

function blocksOverlap($shiftStart, $shiftEnd, $blockStartDT, $blockEndDT) {
    return $shiftStart < $blockEndDT && $shiftEnd > $blockStartDT;
}

switch ($action) {
    case 'list':
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-t');

        // Fetch all shifts in range
        $stmt = $pdo->prepare("SELECT * FROM shifts WHERE shift_start < ? AND shift_end > ? ORDER BY shift_start");
        $stmt->execute([$end, $start]);
        $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $events = [];

        // Add actual shifts
        foreach ($shifts as $s) {
            $events[] = [
                'id' => $s['id'],
                'title' => $s['nurse_name'] ?: 'Open',
                'start' => $s['shift_start'],
                'end' => $s['shift_end'],
                'backgroundColor' => $s['color'] ?: '#4cc9f0',
                'borderColor' => $s['color'] ?: '#4cc9f0',
                'textColor' => '#fff',
                'extendedProps' => [
                    'shift_type' => $s['shift_type'],
                    'status' => $s['status'],
                    'notes' => $s['notes'],
                    'nurse_name' => $s['nurse_name'],
                    'is_peds' => (bool)$s['is_peds'],
                    'is_open' => false
                ]
            ];
        }

        // Generate OPEN blocks for each day in range
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($startDate, $interval, $endDate);

        global $BLOCKS;

        foreach ($period as $day) {
            $dayStr = $day->format('Y-m-d');
            $nextDay = (clone $day)->modify('+1 day')->format('Y-m-d');

            foreach ($BLOCKS as $i => $block) {
                $blockStartDT = $dayStr . ' ' . sprintf('%02d:00:00', $block['start']);
                // Handle overnight block (23→03 crosses midnight)
                if ($block['rollover']) {
                    $blockEndDT = $nextDay . ' ' . sprintf('%02d:00:00', $block['end']);
                } else {
                    $blockEndDT = $dayStr . ' ' . sprintf('%02d:00:00', $block['end']);
                }

                // Check if any shift covers this block
                $covered = false;
                foreach ($shifts as $s) {
                    if (blocksOverlap($s['shift_start'], $s['shift_end'], $blockStartDT, $blockEndDT)) {
                        $covered = true;
                        break;
                    }
                }

                if (!$covered) {
                    $events[] = [
                        'id' => 'open-' . $dayStr . '-' . $i,
                        'title' => 'OPEN',
                        'start' => $blockStartDT,
                        'end' => $blockEndDT,
                        'backgroundColor' => 'rgba(76, 201, 240, 0.12)',
                        'borderColor' => 'rgba(76, 201, 240, 0.3)',
                        'textColor' => 'rgba(76, 201, 240, 0.6)',
                        'display' => 'auto',
                        'extendedProps' => [
                            'shift_type' => 'open-block',
                            'status' => 'open',
                            'notes' => '',
                            'nurse_name' => '',
                            'is_open' => true
                        ]
                    ];
                }
            }
        }

        echo json_encode($events);
        break;

    case 'create':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO shifts (nurse_name, shift_start, shift_end, shift_type, status, notes, color, is_peds) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['nurse_name'] ?? '',
            $data['shift_start'],
            $data['shift_end'],
            $data['shift_type'] ?? 'custom',
            $data['status'] ?? 'scheduled',
            $data['notes'] ?? '',
            $data['color'] ?? '#4cc9f0',
            !empty($data['is_peds']) ? 1 : 0
        ]);
        echo json_encode(['id' => $pdo->lastInsertId(), 'success' => true]);
        break;

    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? $_GET['id'] ?? 0;

        // Always update all fields
        $stmt = $pdo->prepare("UPDATE shifts SET nurse_name = ?, shift_start = ?, shift_end = ?, shift_type = ?, status = ?, notes = ?, color = ?, is_peds = ? WHERE id = ?");
        $stmt->execute([
            $data['nurse_name'] ?? '',
            $data['shift_start'] ?? null,
            $data['shift_end'] ?? null,
            $data['shift_type'] ?? 'custom',
            $data['status'] ?? 'scheduled',
            $data['notes'] ?? '',
            $data['color'] ?? '#4cc9f0',
            !empty($data['is_peds']) ? 1 : 0,
            $id
        ]);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM shifts WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'quick-add':
        $data = json_decode(file_get_contents('php://input'), true);
        $start = $data['shift_start'];
        $end = $data['shift_end'];

        $startHour = date('H', strtotime($start));
        $duration = (strtotime($end) - strtotime($start)) / 3600;

        $type = 'custom';
        if ($duration >= 11.5 && $duration <= 12.5) {
            $type = ($startHour == 7) ? '12hr-day' : '12hr-noc';
        } elseif ($duration >= 7.5 && $duration <= 8.5) {
            $type = '8hr';
        } elseif ($duration >= 3.5 && $duration <= 4.5) {
            $type = '4hr';
        }

        $color = '#4cc9f0';
        if ($type === '12hr-day') $color = '#4cc9f0';
        if ($type === '12hr-noc') $color = '#1b3a4b';
        if ($type === '8hr') $color = '#274c5b';
        if ($type === '4hr') $color = '#5fa8d3';

        $stmt = $pdo->prepare("INSERT INTO shifts (nurse_name, shift_start, shift_end, shift_type, status, color) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['nurse_name'] ?? 'Open', $start, $end, $type, 'scheduled', $color]);
        echo json_encode(['id' => $pdo->lastInsertId(), 'success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}
