<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/GeminiClient.php';

header('Content-Type: application/json');

$home = $_GET['home'] ?? '';
$away = $_GET['away'] ?? '';

if (empty($home) || empty($away)) {
    echo json_encode(['error' => 'Missing team names']);
    exit;
}

$match_hash = md5(strtolower(trim($home)) . " vs " . strtolower(trim($away)));

// Check cache
$stmt = $conn->prepare("SELECT result_json, created_at FROM prediction_cache WHERE match_hash = ?");
$stmt->bind_param("s", $match_hash);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Cache for 24 hours
    if (strtotime($row['created_at']) > (time() - 86400)) {
        echo $row['result_json'];
        exit;
    } else {
        // Delete expired cache
        $conn->query("DELETE FROM prediction_cache WHERE match_hash = '$match_hash'");
    }
}

$settings = getSettings($conn);
if (empty($settings['gemini_api_key'])) {
    echo json_encode(['error' => 'API Key not configured']);
    exit;
}

$client = new GeminiClient($settings['gemini_api_key']);

try {
    $prediction = $client->getPrediction($home, $away);
    $prediction_json = json_encode($prediction);

    // Save to cache
    $stmt = $conn->prepare("INSERT INTO prediction_cache (match_hash, home_team, away_team, result_json) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $match_hash, $home, $away, $prediction_json);
    $stmt->execute();

    echo $prediction_json;
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
