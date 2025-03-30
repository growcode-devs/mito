<?php
require_once __DIR__ . '/../config.php';
$base_upload_url = 'http://localhost/growcode/web_mitomania/uploads/';
$top_songs = file_get_contents("cache_top_songs.json");
$data = json_decode($top_songs, true);

//Database
$query = "SELECT * FROM songs";
$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(['error' => 'Error al realizar la consulta: ' . $mysqli->error]));
}
// var_dump($result->fetch_all(MYSQLI_ASSOC));
$db_songs = $result->fetch_all(MYSQLI_ASSOC);


foreach ($data['songs']['top_tracks'] as &$value) {
    foreach ($db_songs as $db_value) {

        if ($value['name'] == $db_value['title']) { //cuando se arregle la bd cambiar al id
            $value['preview'] = true;
            $value['file_path'] = $base_upload_url . $db_value['file_path'];
            break;
        } else {
            $value['preview'] = false;
        }
    }
}
unset($value);
header('Content-Type: application/json');
// echo json_encode($data);
echo json_encode(['tracks' => array_slice($data['songs']['top_tracks'], 0, 4)]);
