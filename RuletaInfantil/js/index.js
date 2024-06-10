//Referencias a objetos
const ruleta = document.getElementById("ruleta");
let opcionesContainer;
let opciones = Array.from(document.getElementsByClassName("opcion"));
const root = document.documentElement;
const formContainer = document.getElementById("formContainer");
const modal = document.querySelector("dialog");
const totalElement = document.getElementById("porcentaje");
const botonCancelar = document.getElementById("cancelar");
const botonAceptar = document.getElementById("aceptar");
const botonAgregar = document.getElementById("agregar");
const ganadorTextoElement = document.getElementById("ganadorTexto");

/** Texto de la opción ganadora */
let ganador = "";
/** Para el setInterval que hace que el cartel de ganador anime los "..." */
let animacionCarga;
/** Estado actual de la ruleta true => Bloquea el mouse; */
let sorteando = false;
/** Contiene la lista de colores posibles para el gráfico */
const colores = [
	"228B22", "32CD32", "FFD700", "800080", "8B4513", "F5F5DC", "808000", "40E0D0", "50C878", "FFD700", "8FBC8F", "98FB98", "FF7F50", "E6E6FA"
];

/** Cambia la escala para hacer la herramienta pseudo responsive (faltaría un event listener al cambio de width para que sea bien responsive) */
let escala = screen.width < 550 ? screen.width * 0.7 : 400;
root.style.setProperty("--escala", escala + "px");

/** Contiene la suma actual de probabilidades en base 100 */
let suma = 0;

/** Instancias de conceptos que se cargan al iniciar la app */
const uno = {
	nombre: "Uno",
	probabilidad: 25
}
const dos = {
	nombre: "Dos",
	probabilidad: 25
}
const tres = {
	nombre: "Tres",
	probabilidad: 25
}
const cuatro = {
	nombre: "Cuatro",
	probabilidad: 25
}

let conceptos = [uno, dos, tres, cuatro];

// Cargar sonidos
const spinSound = new Audio('../sonido_girar.mp3');
const stopSound = new Audio('../sonido_detener.mp3');

/** Pone a girar la ruleta y hace el sorteo del resultado */
function sortear() {
	sorteando = true;
	ganadorTextoElement.textContent = ".";
	animacionCarga = setInterval(() => {
		switch (ganadorTextoElement.textContent) {
			case ".":
				ganadorTextoElement.textContent = ".."
				break;
			case "..":
				ganadorTextoElement.textContent = "..."
				break;
			default:
				ganadorTextoElement.textContent = "."
				break;
		}
	}, 500);
	/** Numero del 0 al 1 que contiene al ganador del sorteo */
	const nSorteo = Math.random();
	/** Cantidad de grados que debe girar la ruleta */
	const giroRuleta = (1 - nSorteo) * 360 + 360 * 10; //10 vueltas + lo aleatorio;
	root.style.setProperty('--giroRuleta', giroRuleta + "deg");
	ruleta.classList.toggle("girar", true);

	// Reproducir sonido de giro
	spinSound.play();

	/** Acumulador de probabilidad para calcular cuando una probabilidad fue ganadora */
	let pAcumulada = 0;
	conceptos.forEach(concepto => {
		if (nSorteo * 100 > pAcumulada && nSorteo * 100 <= pAcumulada + concepto.probabilidad) {
			ganador = concepto.nombre;
		};
		pAcumulada += concepto.probabilidad;
	});
}

/** Desacopla lo que ocurre al terminar de girar la ruleta de la función girar */
ruleta.addEventListener("animationend", () => {
	ruleta.style.transform = "rotate(" + getCurrentRotation(ruleta) + "deg)";
	ruleta.classList.toggle("girar", false);
	sorteando = false;
	ganadorTextoElement.textContent = ganador;
	clearInterval(animacionCarga);

	// Reproducir sonido al detenerse
	spinSound.pause();
	stopSound.play();
});

/** Crea todas las partes del elemento ruleta según la lista de conceptos */
function ajustarRuleta() {
	// Primero borro la ruleta anterior y creo una nueva.
	if (opcionesContainer) ruleta.removeChild(opcionesContainer);
	opcionesContainer = document.createElement("div");
	opcionesContainer.id = "opcionesContainer";
	ruleta.appendChild(opcionesContainer);
	let pAcumulada = 0;
	conceptos.forEach((concepto, i) => {
		//Creo triangulos de colores
		const opcionElement = document.createElement("div");
		opcionElement.classList.toggle("opcion", true);
		opcionElement.style = `
            --color: #${colores[i % colores.length]};
            --deg:${probabilidadAGrados(pAcumulada)}deg;
            ${getPosicionParaProbabilidad(concepto.probabilidad)}`
		opcionElement.addEventListener("click", () => onOpcionClicked(i));
		opcionesContainer.appendChild(opcionElement);
		//Creo textos
		const nombreElement = document.createElement("p");
		nombreElement.textContent = concepto.nombre;
		nombreElement.classList.add("nombre");
		nombreElement.style = `width : calc(${concepto.probabilidad} * var(--escala) * 1.5 / 80);
            transform: rotate(${probabilidadAGrados(concepto.probabilidad) / 2 + probabilidadAGrados(pAcumulada)}deg)`;
		opcionesContainer.appendChild(nombreElement);
		//Creo separadores
		const separadorElement = document.createElement("div");
		separadorElement.style = `transform: rotate(${probabilidadAGrados(pAcumulada)}deg)`;
		separadorElement.classList.add("separador");
		opcionesContainer.appendChild(separadorElement);
		pAcumulada += concepto.probabilidad;
		//Reseteo la posición y el cartel
		ruleta.style.transform = "rotate(0deg)";
		ganadorTextoElement.textContent = "¡Click en Girar para iniciar!";
	});
}

//Eventos de botones
document.getElementById("sortear").addEventListener("click", () => {
	if (!sorteando) sortear();
});

function onOpcionClicked(i) {
	// Borro los elementos de la lista
	Array.from(formContainer.children).forEach(element => formContainer.removeChild(element));
	// Creo items de lista para cada probabilidad
	conceptos.forEach(concepto => {
		agregarConfiguracionProbabilidad(concepto);
	});
	modal.showModal();
	verificarValidezFormulario();
}

botonAceptar.addEventListener("click", () => {
	conceptos = Array.from(formContainer.children).map(opcion =>
		nuevaProbabilidad = {
			nombre: opcion.children[0].tagName === "LABEL" ? opcion.children[0].textContent : opcion.children[0].value,
			pInicial: 0,
			probabilidad: parseFloat(opcion.children[1].value)
		});
	ajustarRuleta();
	modal.close();
});

botonCancelar.addEventListener("click", () => {
	modal.close();
});

/** Revisa si  los porcentajes de probabilidades suman a 100% */
function verificarValidezFormulario() {
	suma = 0;
	Array.from(formContainer.children).forEach(opcion => {
		suma += parseFloat(opcion.children[1].value);
	});
	botonAceptar.disabled = suma !== 100; // Deshabilito el botón aceptar si la suma es distinto de 100
	totalElement.textContent = suma.toString();
}

// Botón "+" en el formulario de probabilidades
document.getElementById("agregar").addEventListener("click", () => {
	agregarConfiguracionProbabilidad();
});

function agregarConfiguracionProbabilidad(probabilidad = undefined) {
	const opcionContainer = document.createElement("div");
	let opcionLabel;
	const opcionInput = document.createElement("input");
	const eliminarBoton = document.createElement("button");
	if (probabilidad) {
		opcionLabel = document.createElement("label");
		opcionLabel.textContent = probabilidad.nombre;
		opcionLabel.for = probabilidad.nombre;
		opcionInput.value = probabilidad.probabilidad;
		opcionLabel.type = "text";
	} else {
		opcionLabel = document.createElement("input");
	}
	opcionInput.type = "number";
	eliminarBoton.textContent = "X";
	opcionInput.addEventListener("change", () => verificarValidezFormulario());
	opcionContainer.appendChild(opcionLabel);
	opcionContainer.appendChild(opcionInput);
	opcionContainer.appendChild(eliminarBoton);
	formContainer.appendChild(opcionContainer);
	eliminarBoton.addEventListener("click", (event) => {
		event.target.parentNode.parentNode.removeChild(event.target.parentNode); //También puede ser formContainer.removeChild(event.target.parentNode)
		verificarValidezFormulario();
	});
}

/** Desde una probabilidad en % devuelve un clip-path que forma el ángulo correspondiente a esa probabilidad */
function getPosicionParaProbabilidad(probabilidad) {
	if (probabilidad === 100) {
		return ''
	}
	if (probabilidad >= 87.5) {
		const x5 = Math.tan(probabilidadARadianes(probabilidad)) * 50 + 50;
		return `clip-path: polygon(50% 0%, 100% 0, 100% 100%, 0 100%, 0 0, ${x5}% 0, 50% 50%)`;
	}
	if (probabilidad >= 75) {
		const y5 = 100 - (Math.tan(probabilidadARadianes(probabilidad - 75)) * 50 + 50);
		return `clip-path: polygon(50% 0%, 100% 0, 100% 100%, 0 100%, 0% ${y5}%, 50% 50%)`;
	}
	if (probabilidad >= 62.5) {
		const y5 = 100 - (0.5 - (0.5 / Math.tan(probabilidadARadianes(probabilidad)))) * 100;
		return `clip-path: polygon(50% 0%, 100% 0, 100% 100%, 0 100%, 0% ${y5}%, 50% 50%)`;
	}
	if (probabilidad >= 50) {
		const x4 = 100 - (Math.tan(probabilidadARadianes(probabilidad)) * 50 + 50);
		return `clip-path: polygon(50% 0, 100% 0, 100% 100%, ${x4}% 100%, 50% 50%)`;
	}
	if (probabilidad >= 37.5) {
		const x4 = 100 - (Math.tan(probabilidadARadianes(probabilidad)) * 50 + 50);
		return `clip-path: polygon(50% 0, 100% 0, 100% 100%, ${x4}% 100%, 50% 50%)`;
	}
	if (probabilidad >= 25) {
		const y3 = Math.tan(probabilidadARadianes(probabilidad - 25)) * 50 + 50;
		return `clip-path: polygon(50% 0, 100% 0, 100% ${y3}%, 50% 50%)`;
	}
	if (probabilidad >= 12.5) {
		const y3 = (0.5 - (0.5 / Math.tan(probabilidadARadianes(probabilidad)))) * 100;
		return `clip-path: polygon(50% 0, 100% 0, 100% ${y3}%, 50% 50%)`;
	}
	if (probabilidad >= 0) {
		const x2 = Math.tan(probabilidadARadianes(probabilidad)) * 50 + 50;
		return `clip-path: polygon(50% 0, ${x2}% 0, 50% 50%)`;
	}
}

/** Inicia ejecución */
ajustarRuleta();
