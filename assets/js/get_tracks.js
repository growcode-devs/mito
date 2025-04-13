let tracksData = []; // Variable global para almacenar las canciones

// Control de reproducción de pistas
let currentAudio = null,
  progressInterval = null,
  isPlaying = false,
  currentTrackIndex = null;

// Petición AJAX para obtener los datos
$.ajax({
  //   url: "../../php/api_data/get_top_ten.php", // Ruta al archivo PHP
  url: "php/api_data/get_top_ten.php", // Ruta al archivo PHP
  method: "GET",
  dataType: "json",
  success: (response) => {
    console.log("Respuesta AJAX recibida:", response); // Debug
    if (response.tracks && response.tracks.length > 0) {
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

//Set default album image
function resetAlbumImage() {
  const imageElement = document.querySelector(".music-lyrics-image");
  imageElement.src = "./assets/img/default_musica.png";
}
//Set track album image
//if not exists, set default
function updateAlbumImage(albumImage) {
  const imageElement = document.querySelector(".music-lyrics-image");
  imageElement.src = albumImage || "./assets/img/default_musica.png";
}

// Función para llenar las barras de música
function populateMusicBars(tracks) {
  const bars = document.querySelectorAll(".player-bar"); // Contenedores de los reproductores

  tracks.slice(0, bars.length).forEach((track, index) => {
    const { name, song_path, album_image, url, lyric_path } = track; // Extraer datos relevantes
    const audioPath = song_path; // Usar directamente la ruta proporcionada por PHP

    const bar = bars[index];
    while (bar.firstChild) {
      bar.removeChild(bar.firstChild); // Limpiar contenido previo
    }

    // Botón de reproducción
    const playButton = document.createElement("button");
    playButton.classList.add("play-pause-button");
    if (audioPath) {
      playButton.setAttribute(
        "onclick",
        `playPreview("${audioPath}", '${album_image}', ${index})`
      );
      playButton.innerHTML = '<i class="fas fa-play"></i>';
    } else {
      playButton.disabled = true;
      playButton.innerHTML = '<i class="fas fa-ban"></i>';
    }
    bar.appendChild(playButton);

    // Botón de Spotify (siempre presente, pero deshabilitado si no hay URL)
    const spotifyButton = document.createElement("a");
    spotifyButton.classList.add("spotify-button");
    spotifyButton.innerHTML = '<i class="fab fa-spotify"></i>';
    if (url) {
      spotifyButton.href = url;
      spotifyButton.target = "_blank"; // Abrir en nueva pestaña
    } else {
      spotifyButton.classList.add("disabled-icon"); // Clase para estilos deshabilitados
    }
    bar.appendChild(spotifyButton);

    // Botón de Letras - Ahora abre el modal en lugar de ir a una URL
    const lyricsButton = document.createElement("button");
    lyricsButton.classList.add("lyrics-button");
    lyricsButton.innerHTML = '<i class="fas fa-file-alt"></i>';
    lyricsButton.dataset.lyricsFile = lyric_path || ""; // Guardar la URL de la letra si existe
    lyricsButton.addEventListener("click", openLyricsModal);
    bar.appendChild(lyricsButton);

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

// Función para abrir el modal con la letra
function openLyricsModal(event) {
  const modal = document.getElementById("lyricsModal");
  const lyricsText = modal.querySelector(".modal-text");
  const button = event.currentTarget;
  const lyricsFile = button.dataset.lyricsFile;

  if (lyricsFile) {
    fetch(lyricsFile)
      .then((response) => response.text())
      .then((text) => {
        lyricsText.innerHTML = text;
        // lyricsText.innerHTML = `<pre>${text}</pre>`;
      })
      .catch((error) => {
        console.error("Error al cargar la letra:", error);
        lyricsText.innerHTML = "Error al cargar la letra.";
      });
  } else {
    lyricsText.innerHTML = "Letra no disponible.";
  }

  modal.style.display = "flex";
}

/*
Needs ->  clearInterval()
          setInterval()
*/
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
/*
Needs ->  clearInterval()
          resetAlbumImage()
*/
function resetTrack() {
  clearInterval(progressInterval);
  isPlaying = false;
  currentAudio = null;
  $(`#progress-bar-${currentTrackIndex} .progress-fill`).css("width", "0%");
  $(`.play-pause-button`)
    .eq(currentTrackIndex)
    .html('<i class="fas fa-play"></i>');
  resetAlbumImage();
}

/*
Needs ->  clearInterval()
*/
function pauseAudio(playButton) {
  currentAudio.pause();
  isPlaying = false;
  playButton.html('<i class="fas fa-play"></i>');
  clearInterval(progressInterval);
}

/*
Needs ->  clearInterval()
          resetAlbumImage()
*/
function resetPreviousAudio() {
  currentAudio.pause();
  clearInterval(progressInterval);
  $(`.play-pause-button`)
    .eq(currentTrackIndex)
    .html('<i class="fas fa-play"></i>');
  $(`#progress-bar-${currentTrackIndex} .progress-fill`).css("width", "0%");
  // resetAlbumImage();
}

/*
Needs -> startProgressBar()
*/
function resumeAudio(playButton, progressBar) {
  currentAudio.play();
  isPlaying = true;
  playButton.html('<i class="fas fa-pause"></i>');
  startProgressBar(progressBar, currentAudio.duration);
}

/*
Needs ->  updateAlbumImage()
          pauseAudio()
          resumeAudio()
          resetPreviousAudio()
          startNewAudio()
*/
function playPreview(audioPath, albumImage, index) {
  console.log(`Intentando reproducir: ${audioPath}`);
  const playButton = $(`.play-pause-button`).eq(index);
  const progressBar = $(`#progress-bar-${index} .progress-fill`);

  updateAlbumImage(albumImage);

  if (currentTrackIndex === index && currentAudio) {
    isPlaying ? pauseAudio(playButton) : resumeAudio(playButton, progressBar);
  } else {
    if (currentAudio) resetPreviousAudio();
    startNewAudio(audioPath, index, playButton, progressBar, albumImage);
  }
}

/* 
Needs ->  startProgressBar()
          resetTrack()
          resetAlbumImage()
 */
function startNewAudio(
  audioPath,
  index,
  playButton,
  progressBar,
  albumImage = null
) {
  currentAudio = new Audio(audioPath);
  currentAudio.addEventListener("loadedmetadata", () => {
    updateAlbumImage(albumImage);

    console.log(`Metadata cargada para: ${audioPath}`);
    currentAudio.play();
    isPlaying = true;
    currentTrackIndex = index;
    playButton.html('<i class="fas fa-pause"></i>');
    startProgressBar(progressBar, currentAudio.duration);

    currentAudio.addEventListener("ended", () => {
      resetTrack();
    });
  });

  currentAudio.addEventListener("error", (e) => {
    console.error("Error al cargar el archivo de audio:", audioPath);
    console.error("Detalles del error:", e);
    alert("No se pudo reproducir esta canción.");
    resetAlbumImage();
  });
}
