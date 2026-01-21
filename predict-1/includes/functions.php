<?php

function getSettings($conn) {
    $result = $conn->query("SELECT * FROM settings WHERE id = 1");
    return $result->fetch_assoc();
}

function getSeoSettings($conn) {
    $result = $conn->query("SELECT * FROM seo_settings WHERE id = 1");
    return $result->fetch_assoc();
}

function getAd($conn, $slot_name) {
    $stmt = $conn->prepare("SELECT ad_code FROM ads WHERE slot_name = ? AND is_active = 1");
    $stmt->bind_param("s", $slot_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['ad_code'];
    }
    return '';
}

function getCurrentSeasonRange() {
    $year = (int)date('Y');
    $month = (int)date('n');
    $seasonStartYear = $month >= 7 ? $year : $year - 1;
    return $seasonStartYear . '/' . ($seasonStartYear + 1);
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

?>
