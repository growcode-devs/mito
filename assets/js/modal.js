function openLyricsModal(event) {
    const modal = document.getElementById("lyricsModal");
    const lyricsText = modal.querySelector(".modal-text");

    const button = event.currentTarget;
    const lyricsUrl = button.dataset.lyricsUrl;

    // Verificar si hay una URL de letras, si no, mostrar mensaje por defecto
    if (lyricsUrl) {
        lyricsText.innerHTML = `Cargando letras... <br> <a href="${lyricsUrl}" target="_blank">Ver en otra pestaña</a>`;
    } else {
        lyricsText.innerHTML = "Letra no disponible para esta canción.";
    }

    modal.style.display = "flex";
}

// Cerrar el modal al hacer clic en la "X" o fuera del modal
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("lyricsModal");
    const closeButton = modal.querySelector(".close-button");

    // Cerrar al hacer clic en la "X"
    closeButton.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Cerrar al hacer clic fuera del modal
    window.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});
