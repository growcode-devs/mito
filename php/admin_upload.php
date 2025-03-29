<?php
session_start();
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['song_file'])) {
    $title = $_POST['title'];
    $file = $_FILES['song_file'];
    $upload_dir = __DIR__ . '/../uploads/';
    $allowed_extensions = ['mp3'];

    // Inicializar mensaje vacío
    $_SESSION['upload_message'] = '';

    // Validar extensión del archivo
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        $_SESSION['upload_message'] = "Error: Solo se permiten archivos MP3.";
        header('Location: admin.php');
        exit;
    }

    // Validar tamaño del archivo (máximo 5 MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        $_SESSION['upload_message'] = "Error: El archivo es demasiado grande (máximo 5 MB).";
        header('Location: admin.php');
        exit;
    }

    // Generar un nombre único para el archivo
    $file_name = uniqid() . '-' . basename($file['name']);
    $file_path = $upload_dir . $file_name;

    // Mover el archivo al directorio de uploads
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        $_SESSION['upload_message'] = "Error: No se pudo subir el archivo.";
        header('Location: admin.php');
        exit;
    }

    // Guardar información en la base de datos
    $stmt = $mysqli->prepare("INSERT INTO songs (title, file_path) VALUES (?, ?)");
    $stmt->bind_param('ss', $title, $file_name);
    if ($stmt->execute()) {
        $_SESSION['upload_message'] = "Canción subida exitosamente.";
    } else {
        $_SESSION['upload_message'] = "Error al guardar en la base de datos: " . $stmt->error;
    }
    $stmt->close();
}

$mysqli->close();
header('Location: admin.php');
exit;
?>
