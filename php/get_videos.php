<?php
require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

$api_key = $_ENV['API_KEY'];

header('Content-Type: application/json');
$url = "https://www.googleapis.com/youtube/v3/search?key=$api_key&channelId=UCY_aA4xy4BG7rJm1goIFqpA&part=snippet&order=viewCount&type=video&maxResults=3";

$contenido = file_get_contents("cache_videos.json");
$data = json_decode($contenido, true);

if ($data["date"] == date("Y-m-d")) {
    echo json_encode([
        "status" => 200,
        "success" => true,
        "data" => $data["videos"],
        "origin" => "storage"
    ]);
} else {
    $response = @file_get_contents($url);

    if ($response === false) {
        $error = error_get_last();
        echo json_encode([
            "status" => 500,
            "success" => false,
            "error" => $error['message']
        ]);
        exit;
    }
    $api_data = json_decode($response, true);
    $videos = [];
    foreach ($api_data["items"] as $api_element) {
        array_push($videos, $api_element["id"]["videoId"]);
    }
    $data["videos"] = $videos;
    $data["date"] = date("Y-m-d");

    file_put_contents("cache_videos.json", json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode([
        "status" => 200,
        "success" => true,
        "data" => $videos,
        "origin" => "API"
    ]);
}

// print_r($data);

// $data["date"] = date("Y-m-d");
