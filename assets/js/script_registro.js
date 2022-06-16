var modalExitosa = new bootstrap.Modal(document.getElementById("modalSuccess"));
var modalError = new bootstrap.Modal(document.getElementById("modalError"));

(() => {
	listarDependencias();
	listarTipos();
	document.getElementById("btnRegistrar").addEventListener("click", () => {
		limpiarValidadores();
		cargarUsuario();
	});
})();

async function cargarUsuario() {
	let usuario = {};
	let nombre = document.getElementById("nombres").value;
	let apellido = document.getElementById("apellidos").value;
	let identificacion = document.getElementById("identificacion").value;
	let email = document.getElementById("email").value;
	let tipo = document.getElementById("tipoUsuario").value;
	let username = document.getElementById("username").value;
	let password = document.getElementById("password").value;
	let validPassword = document.getElementById("validPassword").value;
	let dependencia_programa = document.getElementById("tipoDependencia").value;
	let activo = 0;
	let validador = validarCampos(
		nombre,
		apellido,
		identificacion,
		email,
		tipo,
		dependencia_programa,
		password,
		validPassword,
		username
	);
	if (validador) {
		usuario.nombres = nombre;
		usuario.apellidos = apellido;
		usuario.identificacion = Number.parseInt(identificacion);
		usuario.email = email;
		usuario.tipo_id = tipo;
		usuario.username = username;
		usuario.password = password;
		usuario.programa_dependencia_id = dependencia_programa;
		usuario.activo = activo;
		const request = await fetch("../api/registrar_usuario.php", {
			method: "POST",
			headers: {
				Accept: "application/json",
				"Content-Type": "application/json",
			},
			body: JSON.stringify(usuario),
		});
		const respuesta = await request.json();
		if (request.ok) {
			console.log(typeof respuesta.validador);
			console.log(respuesta.validador);
			if (respuesta.username == 0 && respuesta.email == 0) {
				console.log("Datos guardados con exito");
				limpiarInput();
				limpiarValidadores();
				setTimeout(function () {
					window.location.href = "../index.html";
				}, 5000);
				modalExitosa.toggle();
			} else if (respuesta.username == 1) {
				console.log("Usuario ya estiste en BD");
				document.getElementById("userRegistrado").style.display = "flex";
				modalError.toggle();
			} else if (respuesta.email == 1) {
				console.log("Correo ya estiste en BD");
				document.getElementById("emailRegistrado").style.display = "flex";
				modalError.toggle();
			}
		} else {
			console.log(
				"Error al guardar los datos: " + request.status + " cuerpo: " + request.body
			);
		}
	}
}

async function listarTipos() {
	let texto = "<option selected>Seleccione un Tipo de Usuario</option>";
	const request = await fetch("../api/consultar_tipos.php", {
		method: "GET",
		headers: {
			Accept: "application/json",
			"Content-Type": "application/json",
		},
	});
	const tipos = await request.json();
	tipos.forEach((tipo) => {
		texto += `<option value="${tipo.id}">${tipo.nombre}</option>`;
	});
	document.getElementById("tipoUsuario").innerHTML = texto;
}

async function listarDependencias() {
	let texto = "<option selected>Seleccione un programa o dependencia</option>";
	const request = await fetch("../api/consultar_dependencia.php", {
		method: "GET",
		headers: {
			Accept: "application/json",
			"Content-Type": "application/json",
		},
	});
	const dependencias = await request.json();
	dependencias.forEach((dependencia) => {
		texto += `<option value="${dependencia.id}">${dependencia.nombre}</option>`;
	});
	document.getElementById("tipoDependencia").innerHTML = texto;
}

function validarCampos(
	nombre,
	apellido,
	identificacion,
	email,
	tipo,
	dependencia_programa,
	password,
	validPassword,
	username
) {
	let validRagexInput = /^[a-zA-Z0-9\s]*$/;
	let validRegexMail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
	let validador = true;

	if (!validRagexInput.test(nombre) || !(nombre.length <= 100) || nombre === "") {
		validador = false;
		nombreInvalido();
		console.log("error nombre invalido");
	}

	if (!validRagexInput.test(apellido) || !(apellido.length <= 100) || apellido === "") {
		validador = false;
		apellidoInvalido();
		console.log("error apellido invalido");
	}
	if (!(identificacion.length <= 20) || !(identificacion.length > 0)) {
		indentificacionInvalida();
		validador = false;
		console.log("error identifcacion invalido");
	}
	if (!validRegexMail.test(email) || email === "") {
		validador = false;
		emailInvalido();
		console.log("error email invalido");
	}
	if (isNaN(tipo)) {
		validador = false;
		tipoInvalida();
		console.log("error en tipo invalido");
	}
	if (isNaN(dependencia_programa)) {
		validador = false;
		dependenciaInvalida();
		console.log("error en tipo invalido");
	}
	if (!(password.length >= 6) || !(password.length <= 20) || password === "") {
		validador = false;
		passwordInvalida();
		console.log("error password invalido");
	}
	if (!(validPassword.length >= 6) || !(validPassword.length <= 20) || validPassword === "") {
		validador = false;
		passwordValidInvalida();
		console.log("error validar password invalido");
	}
	if (!validRagexInput.test(username) || username === "") {
		validador = false;
		usuarioInvalido();
		console.log("error username invalido");
	}
	if (!(password === validPassword)) {
		validador = false;
		coincidenciaInvalida();
		console.log("las contraselas no coninciden");
	}

	return validador;
}

function nombreInvalido() {
	document.getElementById("nombreInvalid").style.display = "flex";
}

function apellidoInvalido() {
	document.getElementById("apellidoInvalid").style.display = "flex";
}

function indentificacionInvalida() {
	document.getElementById("indentificacionInvalid").style.display = "flex";
}

function tipoInvalida() {
	document.getElementById("tipoInvalid").style.display = "flex";
}

function dependenciaInvalida() {
	document.getElementById("dependenciaInvalid").style.display = "flex";
}

function emailInvalido() {
	document.getElementById("emailInvalid").style.display = "flex";
}

function usuarioInvalido() {
	document.getElementById("userInvalid").style.display = "flex";
}

function passwordInvalida() {
	document.getElementById("passInvalid").style.display = "flex";
}

function passwordValidInvalida() {
	document.getElementById("passValidInvalid").style.display = "flex";
}
function coincidenciaInvalida() {
	document.getElementById("coincidenciaInvalid").style.display = "flex";
}

function limpiarValidadores() {
	document.getElementById("nombreInvalid").style.display = "none";
	document.getElementById("apellidoInvalid").style.display = "none";
	document.getElementById("indentificacionInvalid").style.display = "none";
	document.getElementById("tipoInvalid").style.display = "none";
	document.getElementById("dependenciaInvalid").style.display = "none";
	document.getElementById("emailInvalid").style.display = "none";
	document.getElementById("userInvalid").style.display = "none";
	document.getElementById("passInvalid").style.display = "none";
	document.getElementById("passValidInvalid").style.display = "none";
	document.getElementById("userRegistrado").style.display = "none";
	document.getElementById("emailRegistrado").style.display = "none";
	document.getElementById("coincidenciaInvalid").style.display = "none";
}

function limpiarInput() {
	document.getElementById("nombres").value = "";
	document.getElementById("apellidos").value = "";
	document.getElementById("identificacion").value = "";
	document.getElementById("email").value = "";
	document.getElementById("tipoUsuario").value = "";
	document.getElementById("username").value = "";
	document.getElementById("password").value = "";
	document.getElementById("validPassword").value = "";
	document.getElementById("tipoDependencia").value = "";
}
