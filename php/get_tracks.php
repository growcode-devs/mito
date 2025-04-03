<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/spotify_auth.php';

// Nueva base URL
$base_upload_url = 'https://www.mitomaniachile.com/media/';
$base_lyrics_url = 'https://www.mitomaniachile.com/lyrics/'; // Nueva URL base para las letras

// Obtener las canciones más escuchadas de Spotify
function getSpotifyTopTracks($artist_id, $access_token) {
    $url = "https://api.spotify.com/v1/artists/$artist_id/top-tracks?market=CL";
    $options = [
        'http' => [
            'header' => "Authorization: Bearer $access_token",
            'method' => 'GET',
        ],
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return ['error' => 'Unable to fetch data from Spotify API'];
    }

    $decodedResponse = json_decode($response, true);

    if (!isset($decodedResponse['tracks']) || empty($decodedResponse['tracks'])) {
        return ['error' => 'No tracks data returned from Spotify API'];
    }

    return array_slice($decodedResponse['tracks'], 0, 4); // Devuelve las primeras 4 canciones
}

// ID del artista en Spotify
$artist_id = '5xKK5hFACprzALHQzfbRHs'; // Reemplaza con el ID real
$access_token = getAccessToken();

if (!$access_token) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unable to fetch Spotify access token']);
    exit;
}

// Obtener las canciones de Spotify
$spotifyTracks = getSpotifyTopTracks($artist_id, $access_token);

// Verificar y combinar las canciones con las de la base de datos
$tracksWithDetails = [];

foreach ($spotifyTracks as $track) {
    $name = $track['name'];
    $album_image = $track['album']['images'][0]['url'] ?? null;

    // Buscar en la base de datos
    $stmt = $mysqli->prepare("SELECT file_path FROM songs WHERE title = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $found = $stmt->fetch();
    $stmt->close();

    // Buscar la letra en la base de datos
    $stmt = $mysqli->prepare("SELECT file_path FROM lyrics WHERE title = ?");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->bind_result($lyrics_file);
    $lyrics_found = $stmt->fetch();
    $stmt->close();

    // Construir la respuesta
    $tracksWithDetails[] = [
        'name' => $name,
        'album_image' => $album_image,
        'file_path' => $found ? $base_upload_url . basename($file_path) : null, // Construye el nuevo URL para la canción
        'lyrics_file' => $lyrics_found ? $base_lyrics_url . basename($lyrics_file) : null, // Construye el nuevo URL para la letra
    ];
}

// Cerrar la conexión a la base de datos
$mysqli->close();

// Devolver el JSON al frontend
header('Content-Type: application/json');
echo json_encode(['tracks' => $tracksWithDetails]);
?>
