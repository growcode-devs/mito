<?php
// spotify_auth.php

// Spotify credentials
$client_id = 'cb83c5cda57342d7b33dae6046d85d88';
$client_secret = 'a0a63dba93c64eaf8729155136575797';

// Get access token
function getAccessToken() {
    global $client_id, $client_secret;  // Make the variables global so they're accessible in the function

    $url = 'https://accounts.spotify.com/api/token';
    $headers = [
        'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),
        'Content-Type: application/x-www-form-urlencoded'  // This header is required
    ];
    $data = [
        'grant_type' => 'client_credentials'
    ];

    $options = [
        'http' => [
            'header' => implode("\r\n", $headers),
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        error_log('Failed to retrieve access token.');
        return null;
    }

    $result = json_decode($response, true);
    return $result['access_token'] ?? null;  // Return null if there's no access token
}
?>
