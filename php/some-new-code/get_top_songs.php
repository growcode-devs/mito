<?php
require_once __DIR__ . '/../spotify_auth.php';

function get_spotify_top_songs()
{
    header('Content-Type: application/json');

    $artist_id = '5xKK5hFACprzALHQzfbRHs'; // Ajusta según la banda que quieras consultar

    // Obtener el Access Token
    $access_token = getAccessToken();

    if (!$access_token) {

        echo json_encode(['error' => 'Unable to fetch Spotify access token']);
        exit;
    }

    // URL de la API de Spotify para obtener los álbumes
    $url = "https://api.spotify.com/v1/artists/$artist_id/top-tracks";
    $options = [
        'http' => [
            'header' => "Authorization: Bearer $access_token",
            'method' => 'GET'
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {

        echo json_encode(['error' => 'Unable to fetch album data from Spotify API']);
        exit;
    }

    $decodedResponse = json_decode($response, true);

    // if (!isset($decodedResponse['items']) || empty($decodedResponse['items'])) {

    //     echo json_encode(['error' => 'No album data returned from Spotify API']);
    //     exit;
    // }
    $top_songs = [];
    foreach ($decodedResponse['tracks'] as $key => $value) {
        $top_songs['top_tracks'][] = [
            'top' => $key + 1,
            'name' => $value['name'],
            'album_name' => $value['album']['name'],
            'album_image' => $value['album']['images'][0]['url'],
            'url' => $value['external_urls']['spotify'],
            'spotify_id' => $value['id']
        ];
    }

    return $top_songs;
    // echo json_encode($top_songs);
    // echo json_encode($decodedResponse);
}
// get_spotify_top_songs();
