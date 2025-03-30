<?php
$directorio = __DIR__ . '/../../uploads/';
$archivos = scandir($directorio);

// Filtrar para eliminar . y ..
// $archivos = array_diff($archivos, ['.', '..']);

foreach ($archivos as $archivo) {
    // echo $archivo . "<br>";
    if (str_contains($archivo, '123')) {
        echo "La palabra '67e08fffe05c6' fue encontrada en $archivo";
        echo "<br>";
        $archivo_a_eliminar = $directorio . $archivo;
        echo $archivo_a_eliminar;
        echo "<br>";
        echo "<br>";

        if (file_exists($archivo_a_eliminar)) {
            if (unlink($archivo_a_eliminar)) {
                echo "Archivo eliminado con éxito";
            } else {
                echo "Error al eliminar el archivo";
            }
        }
        break;
    }
}
