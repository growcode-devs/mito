<?php
session_start();
require_once __DIR__ . '/config.php';

define('SESSION_TIMEOUT', 1800);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
  header('Location: /php/login.php');
  exit;
}

// Verificar si la sesión ha expirado
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
  session_unset();
  session_destroy();
  header('Location: /php/login.php?message=session_expired');
  exit;
}

// Actualizar actividad de la sesión
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrador</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
  <header>
    <h1>Panel de Administración</h1>
    <a href="/php/logout.php" style="position: absolute; top: 10px; right: 20px; color: #ff4dff; text-decoration: none;">Cerrar sesión</a>
  </header>
  <main>
    <div class="form-container">
      <h2>Subir Canciones</h2>
      <form action="admin_upload.php" method="POST" enctype="multipart/form-data">
        <label for="title">Título de la Canción:</label>
        <input type="text" id="title" name="title" placeholder="Título de la canción" required>

        <label for="song_file">Archivo MP3:</label>
        <input type="file" id="song_file" name="song_file" accept="audio/mp3" required>

        <button type="submit" class="submit-btn">Subir Canción</button>
      </form>
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


  </main>

  <!-- Script para el Modal -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('successModal');
      const modalMessage = document.getElementById('modalMessage');
      const closeModal = document.querySelector('.modal-content .close');
      const form = document.querySelector('form');

      <?php if (!empty($_SESSION['upload_message'])): ?>
        modalMessage.textContent = "<?= htmlspecialchars($_SESSION['upload_message']); ?>";
        modal.style.display = 'block';
        <?php unset($_SESSION['upload_message']); // Limpiar mensaje 
        ?>
      <?php endif; ?>

      closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
        form.reset(); // Limpiar el formulario
      });

      window.addEventListener('click', (event) => {
        if (event.target === modal) {
          modal.style.display = 'none';
          form.reset(); // Limpiar el formulario
        }
      });
    });
  </script>

</body>

</html>



</body>

</html>