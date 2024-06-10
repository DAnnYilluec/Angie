const wheel = document.getElementById("wheel");
const spinButton = document.getElementById("spinButton");
const addSegmentButton = document.getElementById("addSegmentButton");
let degree = 0;

// Funcion para girar la rueda
function spinWheel() {
    degree = Math.floor(5000 + Math.random() * 5000); // gira entre 5000 y 10000 grados
    wheel.style.transition = "transform 10s ease-out";
    wheel.style.transform = `rotate(${degree}deg)`;
    // restablece para un nuevo giro
    setTimeout(() => {
        wheel.style.transition = "none";
        wheel.style.transform = `rotate(${degree % 360}deg)`;
    }, 10000);
}

// Funcion para añadir un segmento
function addSegment() {
    const newSegment = document.createElement("div");
    newSegment.classList.add("segment");
    const segmentText = document.createElement("div");
    segmentText.classList.add("segment-text");
    segmentText.textContent = prompt("Introduce el texto para el nuevo segmento:");
    const deleteButton = document.createElement("button");
    deleteButton.textContent = "Borrar";
    deleteButton.classList.add("deleteButton");
    deleteButton.addEventListener("click", () => {
        newSegment.remove();
        updateSegments();
    });
    segmentText.appendChild(deleteButton);
    newSegment.appendChild(segmentText);
    wheel.appendChild(newSegment);
    updateSegments();
}

// Funcion para actualizar los segmentos
function updateSegments() {
    const segments = document.querySelectorAll(".segment");
    const totalSegments = segments.length;
    const angle = 360 / totalSegments;

    segments.forEach((segment, index) => {
        const color = `hsl(${index * angle}, 100%, 50%)`;
        segment.style.borderBottom = `500px solid ${color}`;
        segment.style.transform = `rotate(${index * angle}deg)`;
    });
}

// Añade el evento click a los botones de eliminar actuales
function initializeDeleteButtons() {
    const deleteButtons = document.querySelectorAll(".deleteButton");
    deleteButtons.forEach(button => {
        button.addEventListener("click", (event) => {
            const segment = event.target.closest(".segment");
            segment.remove();
            updateSegments();
        });
    });
}

spinButton.addEventListener("click", spinWheel);
addSegmentButton.addEventListener("click", addSegment);

// Inicializa los segmentos y botones de eliminar
initializeDeleteButtons();
updateSegments();
