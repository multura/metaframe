<?php

error_reporting(E_ALL);
ini_set('display_errors', 0); // важно: НЕ ломаем JSON

header('Content-Type: application/json');

// ---- BASE SAFE DETECTION ----
$base = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
$base = rtrim($base, '/');

// ---- SAFE GETID3 LOAD ----
$getID3 = null;
$getID3Path = __DIR__ . '/vendor/getid3/getid3.php';

if (file_exists($getID3Path)) {
    require_once $getID3Path;
    if (class_exists('getID3')) {
        $getID3 = new getID3();
    }
}

// ---- SAFE SCAN ----
function safeScan($dir, $ext) {
    if (!is_dir($dir)) return [];

    $files = @scandir($dir);
    if (!$files) return [];

    return array_values(array_filter($files, function($f) use ($ext) {
        return preg_match("/\.($ext)$/i", $f);
    }));
}

// ---- PHOTOS ----
$photos = array_map(function($f) use ($base) {
    return "$base/photos/$f";
}, safeScan(__DIR__ . '/photos', 'jpg|jpeg|png|webp'));

// ---- RESPONSE (ALWAYS VALID) ----
echo json_encode([
    "ok" => true,
    "base" => $base,
    "photos" => $photos,
    "debug" => [
        "getid3" => $getID3 ? "loaded" : "missing",
        "php" => PHP_VERSION
    ]
]);