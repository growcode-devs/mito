<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['lyrics_file'])) {
    $title = $_POST['title'];
    $song_id = $_POST['spotify_id'];
    $file = $_FILES['lyrics_file'];
    $upload_dir = __DIR__ . '/../../uploads/lyrics/';
    $allowed_extensions = ['txt']; // Only TXT files allowed

    // echo "<pre>";
    // print_r($_FILES);
    // echo "</pre>";

    // Check if the destination folder exists; if not, create it
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            die("Error: Unable to create upload directory.");
        }
    }

    // Validate file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        die("Error: Only .txt files are allowed.");
    }

    // Generate a unique filename
    $file_name = $song_id . '-' . basename($file['name']);
    $file_path = $upload_dir . $file_name;

    //Eliminar el existente cuando es actualización
    if ($_POST['update']) {
        // $upload_dir = __DIR__ . '/../uploads/songs/';
        $archivos = scandir($upload_dir);

        foreach ($archivos as $archivo) {
            // echo $archivo . "<br>";
            if (str_contains($archivo, $song_id)) {
                $archivo_a_eliminar = $upload_dir . $archivo;


                if (file_exists($archivo_a_eliminar)) {
                    if (!unlink($archivo_a_eliminar)) {
                        $_SESSION['upload_message'] = "Error: No se pudo eliminar el archivo existente";
                        header('Location: admin.php');
                        exit;
                    }
                }
                break;
            }
        }
    }

    // Move the uploaded file to the correct location
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        die("Error: Failed to move uploaded file to $file_path.");
    }
    if ($_POST['update']) {
        // Guardar información en la base de datos
        $stmt = $mysqli->prepare("UPDATE songs2 SET lyric_path = ? WHERE spotify_id = ?");
        $stmt->bind_param('ss',  $file_name, $song_id);
        if ($stmt->execute()) {
            $_SESSION['upload_message'] = "Letra actualizada exitosamente.";
        } else {
            $_SESSION['upload_message'] = "Error al guardar en la base de datos: " . $stmt->error;
        }
    } else {
        $stmt = $mysqli->prepare("INSERT INTO songs2 (title, lyric_path, spotify_id) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $title, $file_name, $song_id);
        if ($stmt->execute()) {
            $_SESSION['upload_message'] = "Letra subida exitosamente.";
        } else {
            $_SESSION['upload_message'] = "Error al guardar en la base de datos: " . $stmt->error;
        }
    }
    // echo "File uploaded successfully to $file_path.<br>";

    // // Ensure database connection exists
    // if (!$mysqli) {
    //     die("Error: Database connection failed.");
    // }

    // // Save data to the database
    // $stmt = $mysqli->prepare("INSERT INTO lyrics (title, file_path) VALUES (?, ?)");
    // if (!$stmt) {
    //     die("Database error: " . $mysqli->error);
    // }

    // $stmt->bind_param('ss', $title, $file_name);
    // if ($stmt->execute()) {
    //     echo "Success: Lyrics uploaded and saved to database.";
    // } else {
    //     die("Database error: " . $stmt->error);
    // }

    $stmt->close();
} else {
    die("Error: No file uploaded.");
}
$mysqli->close();
header('Location: admin.php');
exit;
