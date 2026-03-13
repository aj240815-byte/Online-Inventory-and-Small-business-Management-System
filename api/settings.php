<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$settingsFile = __DIR__ . '/../config/settings.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($settingsFile)) {
        $json = file_get_contents($settingsFile);
        echo $json ?: '{}';
    } else {
        echo json_encode([]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit();
    }

    if (!is_dir(dirname($settingsFile))) {
        mkdir(dirname($settingsFile), 0777, true);
    }

    file_put_contents($settingsFile, json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true]);
    exit();
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);

