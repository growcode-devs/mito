<?php
require_once __DIR__ . '/../config.php';
//Replace -> api call
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
            $value['path'] = $base_upload_url . $db_value['file_path'];
            break;
        } else {
            $value['preview'] = false;
        }
    }
}
unset($value);

// foreach ($data['songs']['top_tracks'] as $value) {
//     foreach ($value as $k => $v) {
//         echo $k . "=>" . $v;
//         echo '<br>';
//     }
//     echo '<br>';
//     echo '<br>';
//     echo '<br>';
// }
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



    #modal-container {
        display: none;
        height: 100vh;
        width: 100vw;
        font-family: sans-serif;
        align-content: center;
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(1px);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 100;
    }

    #upload-modal {
        position: relative;
        /* text-align: center; */
        display: flex;
        justify-content: center;
        color: #dcd8d6;
        background-color: rgba(0, 0, 0, 0.96);
        border-radius: 10px;
        width: fit-content;
        margin: auto;
        padding: 3rem;
        border: 1px solid #fff;
    }

    #modal-title {
        text-align: center;
        margin-top: 0
    }

    #modal-form {
        margin-bottom: 0;
    }

    .form-row {
        margin: 1rem 0;
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
        <?php foreach ($data['songs']['top_tracks'] as $key => $value) {
            $top = $value['top'];
            $name = $value['name'];
            $id = $value['spotify_id'];
            $is_preview = $value['preview'];
            $path = $value['path'] ?? null;

            echo "<tr>
            <td class='td-top'>$top</td>
            <td class='td-name'>$name</td>
            <td class='add-btn'><button onclick='show_upload_modal(\"$name\", \"$id\", \"$is_preview\")'>Add</button></td>
        </tr>";
        } ?>

    </tbody>
</table>



<div id="modal-container">


    <div id="upload-modal">

        <div id="form-container">
            <h2 id="modal-title">Subir Canciones</h2>
            <form id="modal-form" action="http://localhost/growcode/web_mitomania/php/admin_upload.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <label for="title">Título de la Canción:</label>
                    <input type="text" id="title" name="title" placeholder="Título de la canción"
                        value="" readonly>
                </div>
                <div id="preview-row" class="form-row">
                    <label>Preview:</label>
                    <audio id="preview-player" src="" type="audio/mpeg" controls>
                    </audio>
                </div>
                <div class="form-row">

                    <label id="file-label" for="song_file">Archivo MP3:</label>
                    <input type="file" id="song_file" name="song_file" accept="audio/mp3" required>
                </div>
                <input type="hidden" id="spotify_id" name="spotify_id" value="">
                <input type="submit" value="Subir canción">

                <button id="close-modal">Salir</button>
            </form>
        </div>
    </div>
</div>

<script>
    <?php
    $path_list = "const path_list={";
    foreach ($data['songs']['top_tracks'] as $value) {
        if ($value['preview']) {
            $path_list .= "'" . $value['spotify_id'] . "': \"" . $value['path'] . "\",";
        }
    }
    $path_list .= "}";
    echo $path_list;
    ?>

    function show_upload_modal(song_name, id, preview, path) {
        const title = document.getElementById('title')
        const spotify_id = document.getElementById('spotify_id')
        const player = document.getElementById('preview-player')
        const file_label = document.getElementById('file-label')
        const preview_row = document.getElementById('preview-row')
        title.value = song_name
        spotify_id.value = id
        if (preview) {
            preview_row.style.display = "block"
            player.src = path_list[id]
            // player.controls = true
            file_label.innerHTML = "Elegir otro archivo:"
        } else {
            preview_row.style.display = "none"
            // player.controls = false
            file_label.innerHTML = "Agregar preview:"
        }
        const upload_modal = document.getElementById('modal-container')
        upload_modal.style.display = 'block'
    }


    const colse_modal = document.getElementById('close-modal')
    colse_modal.addEventListener('click', function(e) {
        const upload_modal = document.getElementById('modal-container')
        upload_modal.style.display = 'none'
        const player = document.getElementById('preview-player')
        player.pause()
    })
</script>