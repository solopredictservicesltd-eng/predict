<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/GeminiClient.php';

header('Content-Type: application/json');

$q = $_GET['q'] ?? '';

if (strlen($q) < 3) {
    echo json_encode([]);
    exit;
}

$settings = getSettings($conn);
if (empty($settings['gemini_api_key'])) {
    echo json_encode([]);
    exit;
}

$client = new GeminiClient($settings['gemini_api_key']);

try {
    $results = $client->getTeamSuggestions($q);
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
