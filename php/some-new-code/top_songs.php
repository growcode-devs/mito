<?php
require_once __DIR__ . '/get_top_songs.php';

header('Content-Type: application/json');
$songs_cache = file_get_contents('cache_top_songs.json');
$data = json_decode($songs_cache, true);


if ($data["date"] == date("Y-m-d")) {
    echo json_encode([
        "status" => 200,
        "success" => true,
        "data" => $data["songs"],
        "origin" => "storage"
    ]);
} else {
    $api_data = get_spotify_top_songs();
    $data["date"] = date("Y-m-d");
    $data["songs"] = $api_data;

    file_put_contents("cache_top_songs.json", json_encode($data, JSON_PRETTY_PRINT));
    // echo json_encode($api_data);
    echo json_encode([
        "status" => 200,
        "success" => true,
        "data" => $api_data,
        "origin" => "api"
    ]);
}
