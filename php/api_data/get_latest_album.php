<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include(__DIR__ . '/../spotify_auth.php');

function get_spotify_latest_album()
{



    // ID del artista en Spotify
    $artist_id = '5xKK5hFACprzALHQzfbRHs'; // Ajusta según la banda que quieras consultar

    // Obtener el Access Token
    $access_token = getAccessToken();

    if (!$access_token) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unable to fetch Spotify access token']);
        exit;
    }

    // URL de la API de Spotify para obtener los álbumes
    $url = "https://api.spotify.com/v1/artists/$artist_id/albums?limit=1&market=US&include_groups=album";
    $options = [
        'http' => [
            'header' => "Authorization: Bearer $access_token",
            'method' => 'GET'
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unable to fetch album data from Spotify API']);
        exit;
    }

    $decodedResponse = json_decode($response, true);

    if (!isset($decodedResponse['items']) || empty($decodedResponse['items'])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No album data returned from Spotify API']);
        exit;
    }

    // Obtener el último álbum
    $latestAlbum = $decodedResponse['items'][0];

    // URL de la API de Spotify para las canciones del álbum
    $album_id = $latestAlbum['id'];
    $track_url = "https://api.spotify.com/v1/albums/$album_id/tracks";
    $response_tracks = file_get_contents($track_url, false, $context);

    if ($response_tracks === FALSE) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unable to fetch tracks data from Spotify API']);
        exit;
    }

    $decodedTracks = json_decode($response_tracks, true);

    if (!isset($decodedTracks['items']) || empty($decodedTracks['items'])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No track data returned from Spotify API']);
        exit;
    }

    // Formatear la salida
    $formattedAlbum = [
        'album' => [
            'name' => $latestAlbum['name'],
            'image' => $latestAlbum['images'][0]['url'] ?? null
        ],
        'tracks' => array_map(function ($track) {
            return [
                'name' => $track['name']
            ];
        }, $decodedTracks['items'])
    ];
    return $formattedAlbum;

    // Devolver el JSON al frontend
    // header('Content-Type: application/json');
    // echo json_encode($formattedAlbum);
}


$album_cache = file_get_contents('cache_latest_album.json');
$data = json_decode($album_cache, true);

if ($data["date"] == date("Y-m-d")) {
} else {
    $api_data = get_spotify_latest_album();
    $data["date"] = date("Y-m-d");
    $data["album"] = $api_data;

    file_put_contents("cache_latest_album.json", json_encode($data, JSON_PRETTY_PRINT));
}
// return $data['album']['album'];


// Devolver el JSON al frontend
header('Content-Type: application/json');
echo json_encode($data['album']);
