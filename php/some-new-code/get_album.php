<?php
require_once __DIR__ . '/get_spotify_latest_album.php';
header('Content-Type: application/json');
$album_cache = file_get_contents('cache_album.json');
$data = json_decode($album_cache, true);
// var_dump($data);
// var_dump($data["date"] == date("Y-m-d"));

if ($data["date"] == date("Y-m-d")) {
    echo json_encode([
        "status" => 200,
        "success" => true,
        "data" => $data["latest_album"],
        "origin" => "storage"
    ]);
} else {
    $api_data = get_spotify_album();
    $data["date"] = date("Y-m-d");
    $data["latest_album"] = $api_data;

    file_put_contents("cache_album.json", json_encode($data, JSON_PRETTY_PRINT));
    // echo json_encode($api_data);
    echo json_encode([
        "status" => 200,
        "success" => true,
        "data" => $api_data,
        "origin" => "api"
    ]);
}
