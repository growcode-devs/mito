<?php
require_once __DIR__ . '/config.php';
//Replace -> api call
$example_tracks = file_get_contents("example-tracks.json");
$data = json_decode($example_tracks, true);

//Database
$query = "SELECT * FROM songs";
$result = $mysqli->query($query);

if (!$result) {
    die(json_encode(['error' => 'Error al realizar la consulta: ' . $mysqli->error]));
}
$songs_id = [];
foreach ($result as $ket => $value) {
}

?>

<style>
    body {
        background-color: #000220;
        color: white;
    }



    table {
        padding: 0 3rem;
        width: 100%;
        table-layout: auto;
    }

    th {
        background-color: #0d1317 !important;
        height: 4rem;
        color: #b100c9 !important;
        text-align: center;
        vertical-align: middle;
        font-size: 1.2rem;
        word-wrap: break-word !important;
        /* width: 100%; */
    }

    .td-top,
    .add-btn {
        text-align: center;
    }


    td {
        /* width: 100%; */
        /* background-color: rgba(13, 19, 23, 0.8) !important; */
        background-color: transparent !important;
        word-wrap: break-word !important;
        vertical-align: middle;
    }

    .td-original a,
    .td-short a {
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        font-size: 1.1rem;
        color: #101d42;
        word-break: break-all;
        /* word-wrap: break-word; */
        white-space: normal;
        /* Asegúrate de que no haya margen excesivo */
        /* display: inline; */
    }

    td button i {
        font-size: 1.2rem;
        color: #0d1317;
    }

    tr {
        background-color: #6564db;
        transition: background-color 0.3s ease;
    }

    tr:hover {
        background-color: #89d2dc;
    }

    .table-title {
        text-align: center;
        margin-top: 5rem;
    }

    #modal-form label,
    #modal-form input {
        width: 100%;
    }

    #upload-modal {
        width: 80%;
        margin: auto;
        padding: 1rem 2rem;
        border: 1px solid #fff;
    }
</style>
<h1>Tu top 10 actual</h1>
<table>
    <thead>
        <th>Top</th>
        <th>Nombre</th>
        <th>Editar Preview</th>
    </thead>
    <tbody>
        <?php foreach ($data as $key => $value) {
            $top = $key + 1;
            $name = $value['name'];
            $id = $value['id'];

            echo "<tr>
            <td class='td-top'>$top</td>
            <td class='td-name'>$name</td>
            <td class='add-btn'><button onclick='show_upload_modal(\"$name\", \"$id\")'>Add</button></td>
        </tr>";
        } ?>

    </tbody>
</table>

<div id="modal-container">


    <div id="upload-modal" style="display: none;">

        <div id="form-container" style="display: block;">
            <h2>Subir Canciones</h2>
            <form id="modal-form" action="http://localhost/growcode/web_mitomania/php/admin_upload.php" method="POST" enctype="multipart/form-data">
                <label for="title">Título de la Canción:</label>
                <input type="text" id="title" name="title" placeholder="Título de la canción"
                    value="" readonly>

                <label for="song_file">Archivo MP3:</label>
                <input type="file" id="song_file" name="song_file" accept="audio/mp3" required>

                <button type="submit" class="submit-btn">Subir Canción</button>
            </form>
        </div>
        <button id="close-modal">Salir</button>
    </div>
</div>

<script>
    function show_upload_modal(song_name, id) {
        const title = document.getElementById('title')
        title.value = song_name
        const upload_modal = document.getElementById('upload-modal')
        upload_modal.style.display = 'block'
    }

    function db_songs() {
        //return example
        const songs_preview = {
            'Mitomanos': null,
            'Nunca Quise Ir al Colegio': 'cancion.mp3',
            'Pero Nunca Llegaste': null,
            'Si Me Quisieras': null,
            'Quiero Bailar Contigo': null,
            'Isla Negra': null,
            '¿A Qué Hora?': 'name.mp3',
            'Helado de Invierno': null,
            'La Respuesta': null,
            '¿Dónde Estarán Mis Amigos?': null,
        }
        return songs_preview
    }
    const colse_modal = document.getElementById('close-modal')
    colse_modal.addEventListener('click', function(e) {
        const upload_modal = document.getElementById('upload-modal')
        upload_modal.style.display = 'none'
    })
</script>