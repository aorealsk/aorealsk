<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed.',
    ]);
    exit;
}

$rawInput = file_get_contents('php://input');
if ($rawInput === false || $rawInput === '') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Request body is missing.',
    ]);
    exit;
}

$decoded = json_decode($rawInput, true);
if (!is_array($decoded)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid JSON payload.',
    ]);
    exit;
}

if (!isset($decoded['answers']) || !is_array($decoded['answers'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Answers payload is missing.',
    ]);
    exit;
}

$answers = $decoded['answers'];
$timezone = new DateTimeZone('Europe/Bratislava');
$now = new DateTimeImmutable('now', $timezone);
$answers['date_submitted'] = $now->format('d.m.Y');
$answers['time_submitted'] = $now->format('G:i');

$storageDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'user_input_states';
if (!is_dir($storageDir) && !mkdir($storageDir, 0775, true) && !is_dir($storageDir)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to prepare storage directory.',
    ]);
    exit;
}

try {
    $randomSegment = bin2hex(random_bytes(4));
} catch (Exception $exception) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to generate filename.',
    ]);
    exit;
}

$filename = sprintf('submission_%s_%s.json', $now->format('Ymd_His'), $randomSegment);
$filePath = $storageDir . DIRECTORY_SEPARATOR . $filename;
$fileContents = json_encode($answers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if ($fileContents === false || file_put_contents($filePath, $fileContents) === false) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to save submission.',
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'message' => 'Answers saved successfully.',
    'file' => 'data/user_input_states/' . $filename,
]);
