// Selecciona el contenedor del álbum
const album = document.querySelector('.album-container');

// Alterna la rotación al hacer clic
album.addEventListener('click', () => {
  album.style.transform = album.style.transform === 'rotateY(180deg)' ? 'rotateY(0deg)' : 'rotateY(180deg)';
});

// URL del endpoint para obtener el último álbum del artista
const API_URL = "../php/get_latest_album.php";

// Selecciona los elementos del álbum
const albumFront = document.querySelector(".album-front img");
const albumBack = document.querySelector(".album-back ul");

// Obtener datos del álbum más reciente
fetch(API_URL)
  .then((response) => response.json())
  .then((data) => {
    if (data.album && data.tracks) {
      // Actualizar carátula del álbum
      albumFront.src = data.album.image || "https://via.placeholder.com/700x700";
      albumFront.alt = data.album.name;

      // Llenar lista de canciones
      albumBack.innerHTML = ""; // Limpiar cualquier contenido existente
      data.tracks.forEach((track) => {
        const listItem = document.createElement("li");
        listItem.textContent = track.name;
        albumBack.appendChild(listItem);
      });
    } else {
      console.error("Error: Datos del álbum incompletos.");
    }
  })
  .catch((error) => {
    console.error("Error al obtener datos del álbum:", error);
  });
