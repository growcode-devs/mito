//anahenna
// Seleccionamos el canvas y configuramos el contexto
const canvas = document.getElementById('backgroundCanvas');
const ctx = canvas.getContext('2d');

// Ajustamos el tamaño del canvas a la ventana
function resizeCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}
resizeCanvas();

// Variables para las ondas
const waveCount = 4; // Número total de ondas
const colors = ['#FF4DFF', '#FFA221', '#F8FF21', '#006FFF']; // Colores estilo neón

// Generar parámetros únicos para cada línea
let waves = [];
let lineWidth = 3; // Grosor de las líneas
let spacing = 40; // Espaciado entre líneas

function generateWaveParams() {
  const isSmallScreen = window.innerWidth <= 768;

  // Ajustar grosor y espaciado según el tamaño de la pantalla
  lineWidth = isSmallScreen ? 2 : 3;
  spacing = isSmallScreen ? 20 : 40;

  waves = Array.from({ length: waveCount }, (_, i) => ({
    amplitude: isSmallScreen ? Math.random() * 60 + 30 : Math.random() * 120 + 50, // Altura única
    waveLength: isSmallScreen ? Math.random() * 0.03 + 0.01 : Math.random() * 0.02 + 0.005, // Longitud de onda
    phase: Math.random() * Math.PI * 2, // Fase inicial única
    speed: isSmallScreen ? Math.random() * 0.04 + 0.02 : Math.random() * 0.02 + 0.01, // Velocidad única
    patternOffset: Math.random() * 3 + 1, // Genera un patrón único para asimetría
  }));
}
generateWaveParams();

// Estado inicial de las ondas (invertidas o no)
let invertedStates = Array.from({ length: waveCount }, () => false);

// Efecto neón con sombras brillantes
ctx.shadowBlur = 15;

// Función para dibujar una onda
function drawWave(yOffset, color, invert, waveParams, index) {
  ctx.beginPath();
  ctx.strokeStyle = color;
  ctx.lineWidth = lineWidth; // Ajustar el grosor según el tamaño de pantalla
  ctx.shadowColor = color; // Efecto neón

  for (let x = 0; x < canvas.width; x++) {
    const asymmetryFactor = Math.sin(x * 0.003 * waveParams.patternOffset + index); // Variación asimétrica
    const y =
      canvas.height / 2 +
      yOffset +
      Math.sin((x * waveParams.waveLength) + waveParams.phase) *
        waveParams.amplitude *
        asymmetryFactor *
        (invert ? -1 : 1); // Oscilación y orientación invertida
    ctx.lineTo(x, y);
  }

  ctx.stroke();
}

// Animación principal
function animate() {
  ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpiar el canvas

  // Dibujar las ondas con sus estados actuales
  for (let i = 0; i < waveCount; i++) {
    const yOffset = i * spacing - (waveCount * spacing) / 2; // Espaciado dinámico
    drawWave(yOffset, colors[i % colors.length], invertedStates[i], waves[i], i);

    // Actualizar la fase de cada onda para animarla
    waves[i].phase += waves[i].speed;
  }

  requestAnimationFrame(animate); // Llamar de nuevo a la animación
}

// Alternar orientación de cada línea independientemente
invertedStates.forEach((_, i) => {
  setInterval(() => {
    invertedStates[i] = !invertedStates[i]; // Alternar orientación de la línea específica
  }, Math.random() * 500 + 200); // Intervalo aleatorio entre 200ms y 700ms
});

// Ajustar el tamaño del canvas y regenerar parámetros al redimensionar la ventana
window.addEventListener('resize', () => {
  resizeCanvas();
  generateWaveParams();
});

// Iniciar la animación
animate();

//anahenna
