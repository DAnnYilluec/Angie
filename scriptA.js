var nombres = [];
var nombresDisponibles = [];
var nombresSeleccionados = [];
var nombresNoDisponibles = [];

// Función para cargar los nombres desde la base de datos
function cargarNombres() {
    // Realizar una solicitud AJAX al servidor
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'obtener_seguidores.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Parsear la respuesta JSON
            nombres = JSON.parse(xhr.responseText);
            // Copiar los nombres a la lista de nombres disponibles
            nombresDisponibles = nombres.slice();
        } else {
            console.error('Error al cargar los nombres: ' + xhr.statusText);
        }
    };
    xhr.onerror = function () {
        console.error('Error de red al cargar los nombres.');
    };
    xhr.send();
}

// Llamar a la función para cargar los nombres al inicio
cargarNombres();

function girarRuleta() {
    // Reproducir sonido de girar
    var audioGirar = document.getElementById("sonidoGirar");
    audioGirar.play();
    var ruleta = document.getElementById('ruleta');
    ruleta.style.animation = "girar 2s linear infinite";
    // Limpiar los nombres seleccionados
    nombresSeleccionados = [];
    // Esperar un tiempo antes de detener la ruleta
    setTimeout(detenerRuleta, 10000);
}

function detenerRuleta() {
    var audioGirar = document.getElementById("sonidoGirar");
    audioGirar.pause();
    audioGirar.currentTime = 0;

    // Reproducir sonido de detener
    var audioDetener = document.getElementById("sonidoDetener");
    audioDetener.play();

    var ruleta = document.getElementById('ruleta');
    ruleta.style.animation = "none";

    var nombre1 = obtenerNombreAleatorio();
    var nombre2 = obtenerNombreAleatorio();
    var resultadoDiv = document.getElementById('resultado');
    var resultadoMDiv = document.getElementById('resultadom');

    resultadoDiv.innerHTML = "<span style='color: #2E8307'>"+nombre1+" </span>" ;
    resultadoMDiv.innerHTML = "<span style='color: #FF0000'> "+nombre2+"</span>";
}


function obtenerNombreAleatorio() {
    if (nombresDisponibles.length === 0) {
        console.log('Ya se han seleccionado todos los nombres.');
        return null;
    }

    var indiceAleatorio = Math.floor(Math.random() * nombresDisponibles.length);
    var nombreAleatorio = nombresDisponibles.splice(indiceAleatorio, 1)[0];
    nombresSeleccionados.push(nombreAleatorio);
    nombresNoDisponibles.push(nombreAleatorio);
    actualizarNombresNoDisponibles();
    return nombreAleatorio;
}

function actualizarNombresNoDisponibles() {
    var resultado2Div = document.getElementById('resultado2');
    resultado2Div.innerHTML = "Nombres no disponibles: " + nombresNoDisponibles.join(", ");
}
