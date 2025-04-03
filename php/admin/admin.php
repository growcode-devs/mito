<?php
session_start();
// require_once __DIR__ . '/config.php';

require_once __DIR__ . '/../api_data/get_top_songs.php';

define('SESSION_TIMEOUT', 1800);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
  header('Location: login.php');
  exit;
}

// Verificar si la sesión ha expirado
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
  session_unset();
  session_destroy();
  header('Location: login.php?message=session_expired');
  exit;
}

// Actualizar actividad de la sesión
$_SESSION['last_activity'] = time();

//canciones
$data = get_top_ten();

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrador</title>
  <link rel="stylesheet" href="../../assets/css/admin_panel.css">
</head>

<body>
  <header>
    <h1>Panel de Administración</h1>
    <a href="logout.php">Cerrar sesión</a>
  </header>
  <main>
    <!-- <div class="form-container">
      <h2>Subir Canciones</h2>
      <form action="admin_upload.php" method="POST" enctype="multipart/form-data">
        <label for="title">Título de la Canción:</label>
        <input type="text" id="title" name="title" placeholder="Título de la canción" required>

        <label for="song_file">Archivo MP3:</label>
        <input type="file" id="song_file" name="song_file" accept="audio/mp3" required>

        <button type="submit" class="submit-btn">Subir Canción</button>
      </form>
    </div> -->

    <table>
      <thead>
        <th>Top</th>
        <th>Nombre</th>
        <th>Preview</th>
        <th>Letra</th>
      </thead>
      <tbody>
        <?php foreach ($data as $key => $value) {
          $top = $value['top'];
          $name = $value['name'];
          $id = $value['spotify_id'];
          $path = $value['song_path'] ?? null;
          $is_preview = (bool) $path;
          $lyric = $value['lyric_path'] ?? null;
          $is_lyric = (bool)$lyric;

          echo "<tr>
            <td class='td-top'>$top</td>
            <td class='td-name'>$name</td>
            <td class='td-btn'><button class='add-btn' onclick='show_song_modal(\"$name\", \"$id\", \"$is_preview\", \"$is_lyric\")'>Editar</button></td>
            <td class='td-btn'><button class='add-btn' onclick='show_lyric_modal(\"$name\", \"$id\", \"$is_lyric\", \"$is_preview\")'>Editar</button></td>
        </tr>";
        } ?>

      </tbody>
    </table>
    <div id="modal-container">


      <div id="upload-modal">

        <div id="form-container">
          <h2 class="modal-title">Subir Canciones</h2>
          <form id="modal-form" action="admin_upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
              <label for="title">Título de la Canción:</label>
              <input type="text" id="title" name="title" placeholder="Título de la canción"
                value="" readonly>
            </div>
            <div id="preview-row" class="form-row">
              <label for="preview-player">Preview:</label>
              <audio id="preview-player" src="" type="audio/mpeg" controls>
              </audio>
            </div>
            <div class="form-row">

              <label id="file-label" for="song_file">Archivo MP3:</label>
              <input type="file" id="song_file" name="song_file" accept="audio/mp3" required>
            </div>
            <input type="hidden" id="spotify_id" name="spotify_id" value="">
            <input type="hidden" id="update" name="update" value="">
            <div id="form-btns">

              <input type="submit" class="submit-btn" value="Subir canción">
              <button id="close-modal" class="close-upload-modal">Salir</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div id="lyric-modal-container">
      <div id="lyric-upload-modal">

        <div class="form-container">
          <h2 class="modal-title">Subir Letras de Canciones</h2>
          <form action="admin_upload_lyrics.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
              <label for="lyric_title">Título de la Canción:</label>
              <input type="text" id="lyric-title" name="title" placeholder="Debe coincidir con el nombre de la canción en Spotify" required>
            </div>
            <div id="lyric-row" class="form-row">
              <label for="lyric">Letra actual:</label>
              <button id="lyric-btn">

                <a id="dl-lyric" target="_blank" href="" rel="noopener noreferrer">Letra actual</a>
              </button>
            </div>
            <div class="form-row">
              <label id="lyric-label" for="lyrics_file">Archivo de Letras (.txt):</label>
              <input type="file" id="lyrics_file" name="lyrics_file" accept=".txt" required>
            </div>
            <input type="hidden" id="lyric-spotify_id" name="spotify_id" value="">
            <input type="hidden" id="lyric-update" name="update" value="">

            <div id="form-btns">

              <input type="submit" class="submit-btn" value="Subir Letra">
              <button id="close-lyric-modal" class="close-upload-modal">Salir</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>

  <footer>
    &copy; 2024 Mitomanía. Todos los derechos reservados.
  </footer>

  <div id="successModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p id="modalMessage"></p>
    </div>
  </div>

  <!-- Script para el Modal -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('successModal');
      const modalMessage = document.getElementById('modalMessage');
      const closeModal = document.querySelector('.modal-content .close');
      const forms = document.querySelectorAll('form'); // Obtener ambos formularios

      <?php if (!empty($_SESSION['upload_message'])): ?>
        modalMessage.textContent = "<?= htmlspecialchars($_SESSION['upload_message']); ?>";
        modal.style.display = 'block';
        <?php unset($_SESSION['upload_message']); // Limpiar mensaje 
        ?>
      <?php endif; ?>

      closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
        forms.forEach(form => form.reset()); // Limpiar todos los formularios
      });

      window.addEventListener('click', (event) => {
        if (event.target === modal) {
          modal.style.display = 'none';
          forms.forEach(form => form.reset()); // Limpiar todos los formularios
        }
      });
    });

    //Paulos
    <?php
    // var_dump($data);
    $path_list = "const path_list={";
    foreach ($data as $value) {
      // if ((bool)$value['song_path']) {
      $path_list .= "'" . $value['spotify_id'] . "':{
          'song': \"" . $value['song_path'] . "\",
          'lyric':\"" . $value['lyric_path'] . "\"
        },";
      // }
    }
    $path_list .= "};";
    echo $path_list;

    ?>

    function show_song_modal(song_name, id, preview, exist_field) {
      const title = document.getElementById('title')
      const spotify_id = document.getElementById('spotify_id')
      const player = document.getElementById('preview-player')
      const file_label = document.getElementById('file-label')
      const preview_row = document.getElementById('preview-row')
      const update = document.getElementById('update')
      title.value = song_name
      spotify_id.value = id
      if (preview) {
        preview_row.style.display = "block"
        player.src = path_list[id].song
        file_label.innerHTML = "Elegir otro archivo:"
        update.value = "1"
      } else {
        preview_row.style.display = "none"
        update.value = exist_field
        file_label.innerHTML = "Agregar preview:"
      }
      const upload_modal = document.getElementById('modal-container')
      upload_modal.style.display = 'block'
    }
    // show_song_modal('Mi canción', '', '12')

    const colse_modal = document.getElementById('close-modal')
    const upload_modal = document.getElementById('modal-container')
    const player = document.getElementById('preview-player')
    colse_modal.addEventListener('click', function(e) {
      upload_modal.style.display = 'none'
      player.pause()
    })

    function show_lyric_modal(song_name, id, lyric, exist_field) {
      const title = document.getElementById('lyric-title')
      const spotify_id = document.getElementById('lyric-spotify_id')
      const lyric_label = document.getElementById('lyric-label')
      const lyric_row = document.getElementById('lyric-row')
      const update = document.getElementById('lyric-update')
      const lyric_file = document.getElementById('dl-lyric')
      console.log(song_name)
      title.value = song_name
      spotify_id.value = id
      if (lyric) {
        lyric_row.style.display = "block"
        lyric_file.href = path_list[id].lyric
        console.log(spotify_id)
        lyric_file.innerHTML = path_list[id].lyric.split(`${id}-`)[1]
        lyric_label.innerHTML = "Elegir otro archivo:"
        update.value = "1"
      } else {
        lyric_row.style.display = "none"
        update.value = exist_field
        lyric_label.innerHTML = "Agregar letra:"
      }
      const lyric_modal = document.getElementById('lyric-modal-container')
      lyric_modal.style.display = 'block'
    }
    // show_song_modal('Mi canción', '', '12')

    const colse_lyric_modal = document.getElementById('close-lyric-modal')
    const lyric_modal = document.getElementById('lyric-modal-container')
    colse_lyric_modal.addEventListener('click', function(e) {
      e.preventDefault()
      lyric_modal.style.display = 'none'
    })
    window.addEventListener('click', (event) => {
      if (event.target === upload_modal) {
        upload_modal.style.display = 'none'
        player.pause()
      }
      if (event.target === lyric_modal) {
        lyric_modal.style.display = 'none'
        player.pause()
      }
    });
  </script>

</body>

</html>