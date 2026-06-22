<?php

header('Content-Type: application/json');

$lat = 44.039815;
$lon = 43.070427;

// Open-Meteo weather
$url = "https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$lon&current_weather=true";

$data = @file_get_contents($url);
$data = json_decode($data, true);

$w = $data['current_weather'] ?? null;

echo json_encode([
    "city" => "Moscow",
    "lat" => $lat,
    "lon" => $lon,
    "temp" => $w['temperature'] ?? null,
    "wind" => $w['windspeed'] ?? null,
    "code" => $w['weathercode'] ?? null
]);