<?php
require_once __DIR__ . '/../config/spotify_auth.php';



function get_spotify_top_songs()
{

    $artist_id = '5xKK5hFACprzALHQzfbRHs'; // Ajusta según la banda que quieras consultar

    // Obtener el Access Token
    $access_token = getAccessToken();

    if (!$access_token) {

        echo json_encode(['error' => 'Unable to fetch Spotify access token']);
        exit;
    }

    // URL de la API de Spotify 
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

function update_local_data()
{
    $songs_cache = file_get_contents('cache_top_songs.json');
    $data = json_decode($songs_cache, true);

    if ($data["date"] == date("Y-m-d")) {
    } else {
        $api_data = get_spotify_top_songs();
        $data["date"] = date("Y-m-d");
        $data["songs"] = $api_data;

        file_put_contents("cache_top_songs.json", json_encode($data, JSON_PRETTY_PRINT));
    }
    return $data['songs']['top_tracks'];
}

function get_top_ten()
{
    require_once __DIR__ . '/../config/config.php';
    // $ruta = __DIR__ . '/../../uploads/';
    $ruta = realpath(__DIR__ . '/../../uploads/');
    $ruta = str_replace('\\', '/', $ruta);
    $base_upload_url = str_replace('C:/xampp/htdocs', 'http://localhost', $ruta);


    $data = update_local_data();
    // $top_songs = file_get_contents("cache_top_songs.json");

    // $data = json_decode($top_songs, true);

    //Database

    $query = "SELECT * FROM songs2";
    $result = $mysqli->query($query);

    if (!$result) {
        die(json_encode(['error' => 'Error al realizar la consulta: ' . $mysqli->error]));
    }
    // var_dump($result->fetch_all(MYSQLI_ASSOC));
    $db_songs = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($data as &$value) {
        if (!$db_songs) {
            $value['song_path'] = null;
            $value['lyric_path'] = null;
            continue;
        }
        foreach ($db_songs as $db_value) {

            if ($value['spotify_id'] == $db_value['spotify_id']) { //cuando se arregle la bd cambiar al id
                // $value['preview'] = true;
                $value['song_path'] = $db_value['song_path'] ? $base_upload_url . '/songs/' . $db_value['song_path'] : $db_value['song_path'];
                $value['lyric_path'] = $db_value['lyric_path'] ? $base_upload_url . '/lyrics/' . $db_value['lyric_path'] : $db_value['lyric_path'];
                break;
            } else {
                $value['song_path'] = null;
                $value['lyric_path'] = null;
            }
        }
    }
    unset($value);
    return $data;
}
