<?php

//Cambiar origins en producción!!!
header("Access-Control-Allow-Origin: *"); //!!!
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';

// Ruta base de los archivos subidos
// $base_upload_url = 'https://srv1074-files.hstgr.io/2e7a29f86ffe80fc/files/public_html/uploads/';
$base_upload_url = 'http://localhost/growcode/web_mitomania/uploads/';

// Consulta para obtener las canciones más escuchadas desde la base de datos
$query = "SELECT title AS name, file_path FROM songs ORDER BY id DESC LIMIT 4";
$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(['error' => 'Error al realizar la consulta: ' . $mysqli->error]));
}

// Extraer las canciones en un arreglo
$tracks = [];
while ($row = $result->fetch_assoc()) {
    $tracks[] = [
        'name' => $row['name'],
        'file_path' => $base_upload_url . $row['file_path'], // Construir la URL completa
    ];
}

// Cerrar conexión a la base de datos
$mysqli->close();

// Establecer el encabezado como JSON y devolver la respuesta
header('Content-Type: application/json');
echo json_encode(['tracks' => $tracks]);
