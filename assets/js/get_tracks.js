let tracksData = []; // Variable global para almacenar las canciones

// Petición AJAX para obtener los datos
$.ajax({
  url: "http://localhost/growcode/web_mitomania/php/some-new-code/get_top_four.php", // Ruta al archivo PHP
  method: "GET",
  dataType: "json",
  success: (response) => {
    console.log("Respuesta AJAX recibida:", response); // Debug
    if (response.tracks && response.tracks.length > 0) {
      console.log(response);
      tracksData = response.tracks; // Guardar los datos en la variable global
      populateMusicBars(tracksData); // Generar los reproductores
    } else {
      console.error("No se encontraron canciones en la respuesta.");
    }
  },
  error: (xhr, status, error) => {
    console.error("Error en la petición AJAX:", status, error);
    console.error("Detalles del error:", xhr.responseText);
  },
});

// Función para llenar las barras de música
function populateMusicBars(tracks) {
  const bars = document.querySelectorAll(".player-bar"); // Contenedores de los reproductores

  // Procesar cada canción para llenar las barras
  tracks.slice(0, bars.length).forEach((track, index) => {
    const { name, file_path } = track; // Extraer nombre y ruta
    const audioPath = file_path; // Usar directamente la ruta proporcionada por PHP

    const bar = bars[index];
    while (bar.firstChild) {
      bar.removeChild(bar.firstChild); // Limpiar contenido previo
    }

    // Botón de reproducción
    const playButton = document.createElement("button");
    playButton.classList.add("play-pause-button");
    playButton.setAttribute("onclick", `playPreview("${audioPath}", ${index})`);
    playButton.innerHTML = '<i class="fas fa-play"></i>';
    bar.appendChild(playButton);

    // Nombre de la canción
    const trackName = document.createElement("span");
    trackName.textContent = name;
    trackName.classList.add("track-name");
    bar.appendChild(trackName);

    // Barra de progreso
    const progressBar = document.createElement("div");
    progressBar.classList.add("progress-bar");
    progressBar.id = `progress-bar-${index}`;

    const progressFill = document.createElement("div");
    progressFill.classList.add("progress-fill");
    progressBar.appendChild(progressFill);

    bar.appendChild(progressBar);
  });
}

// Control de reproducción de pistas
let currentAudio = null,
  progressInterval = null,
  isPlaying = false,
  currentTrackIndex = null;

// Función para reproducir o pausar
function playPreview(audioPath, index) {
  console.log(`Intentando reproducir: ${audioPath}`); // Log para verificar la ruta
  const playButton = $(`.play-pause-button`).eq(index);
  const progressBar = $(`#progress-bar-${index} .progress-fill`);

  if (currentTrackIndex === index && currentAudio) {
    isPlaying ? pauseAudio(playButton) : resumeAudio(playButton, progressBar);
  } else {
    if (currentAudio) resetPreviousAudio();
    startNewAudio(audioPath, index, playButton, progressBar);
  }
}

function startNewAudio(audioPath, index, playButton, progressBar) {
  currentAudio = new Audio(audioPath);

  currentAudio.addEventListener("loadedmetadata", () => {
    currentAudio.play();
    isPlaying = true;
    currentTrackIndex = index;
    playButton.html('<i class="fas fa-pause"></i>');
    startProgressBar(progressBar, currentAudio.duration);

    currentAudio.addEventListener("ended", () => {
      resetTrack();
    });
  });

  currentAudio.addEventListener("error", () => {
    console.error("Error al cargar el archivo de audio:", audioPath);
    alert("No se pudo reproducir esta canción.");
  });
}

function pauseAudio(playButton) {
  currentAudio.pause();
  isPlaying = false;
  playButton.html('<i class="fas fa-play"></i>');
  clearInterval(progressInterval);
}

function resumeAudio(playButton, progressBar) {
  currentAudio.play();
  isPlaying = true;
  playButton.html('<i class="fas fa-pause"></i>');
  startProgressBar(progressBar, currentAudio.duration);
}

function resetPreviousAudio() {
  currentAudio.pause();
  clearInterval(progressInterval);
  $(`.play-pause-button`)
    .eq(currentTrackIndex)
    .html('<i class="fas fa-play"></i>');
  $(`#progress-bar-${currentTrackIndex} .progress-fill`).css("width", "0%");
}

function resetTrack() {
  clearInterval(progressInterval);
  isPlaying = false;
  currentAudio = null;
  $(`#progress-bar-${currentTrackIndex} .progress-fill`).css("width", "0%");
  $(`.play-pause-button`)
    .eq(currentTrackIndex)
    .html('<i class="fas fa-play"></i>');
}

function startProgressBar(progressBar, duration) {
  clearInterval(progressInterval);
  progressInterval = setInterval(() => {
    const progress = (currentAudio.currentTime / duration) * 100;
    if (progress <= 100) {
      progressBar.css("width", `${progress}%`);
    } else {
      clearInterval(progressInterval);
    }
  }, 100);
}
