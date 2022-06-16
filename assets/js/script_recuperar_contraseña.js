var modalExitosa = new bootstrap.Modal(document.getElementById("modalSuccess"));
var modalContraseñaExitosa = new bootstrap.Modal(document.getElementById("contraseñaCambiada"));
var modalError = new bootstrap.Modal(document.getElementById("modalError"));

(() => {
	document.getElementById("btnEnviarRecuperar").addEventListener("click", () => {
		limpiarValidadores();
		enviarRecuperacion();
	});

	document.getElementById("btnValidarCodigo").addEventListener("click", () => {
		limpiarValidadores();
		enviarCodigo();
	});
})();

async function enviarRecuperacion() {
	let usuario = {};
	let email = document.getElementById("email").value;
	let username = document.getElementById("username").value;
	let validador = validarCampos(email, username);
	if (validador) {
		usuario.email = email;
		usuario.username = username;

		const request = await fetch("../api/recuperar_contrasenia.php", {
			method: "POST",
			headers: {
				Accept: "application/json",
				"Content-Type": "application/json",
			},
			body: JSON.stringify(usuario),
		});
		const respuesta = await request.json();
		if (request.ok) {
			if (respuesta.username == 0 && respuesta.email == 0) {
				console.log("Usuarios no existen en BD");
				document.getElementById("emailNoExiste").style.display = "flex";
				document.getElementById("usuarioNoExiste").style.display = "flex";
			} else if (respuesta.username == 1 || respuesta.email == 1) {
				console.log("Codigo enviado al correo");
				limpiarValidadores();
				limpiarInput();
				cambiarSeccionIntroducirCodigo();
				modalExitosa.toggle();
			}
		} else {
			console.log(
				"Error al guardar los datos: " + request.status + " cuerpo: " + request.body
			);
		}
	}
}

async function enviarCodigo() {
	let elemento = {};
	let codigo = document.getElementById("codigo_recuperacion").value;
	let validador = validarCampoCodigo(codigo);
	if (validador) {
		elemento.codigo = codigo;
		const request = await fetch("../api/validar_codigo.php", {
			method: "POST",
			headers: {
				Accept: "application/json",
				"Content-Type": "application/json",
			},
			body: JSON.stringify(elemento),
		});
		const respuesta = await request.json();

		if (request.ok) {
			if (respuesta.existe == 0) {
				console.log("Codigo no existen en BD");
				document.getElementById("codigoNoExiste").style.display = "flex";
			} else if (respuesta.existe == 1) {
				console.log("Codigo existe");
				if (respuesta.estado == 0) {
					console.log("Codigo validado");
					limpiarValidadores();
					limpiarInput();
					cambiarSeccionRecuperarContraseña();
					const accion = await document
						.getElementById("btnRecuperar")
						.addEventListener("click", async () => {
							limpiarValidadores();
							let usuario = {};
							let password = document.getElementById("passRecuperar").value;
							let validPassword = document.getElementById("validPassRecuperar").value;

							let validadorContraseña = validarContraseñas(password, validPassword);
							if (validadorContraseña) {
								usuario.id = respuesta.id_usuario;
								usuario.password = password;
								const request = await fetch("../api/restablecer_contrasenia.php", {
									method: "POST",
									headers: {
										Accept: "application/json",
										"Content-Type": "application/json",
									},
									body: JSON.stringify(usuario),
								});
								if (request.ok) {
									console.log("contraseña cambiada con exito!");
									setTimeout(function () {
										window.location.href = "../index.html";
									}, 3000);
									modalContraseñaExitosa.toggle();
								}
							}
						});
				} else {
					console.log("codigo ya fue usado");
					document.getElementById("codigoUsado").style.display = "flex";
				}
			}
		} else {
			console.log(
				"Error al guardar los datos: " + request.status + " cuerpo: " + request.body
			);
		}
	}
}

function validarCampos(email, username) {
	let validRagexInput = /^[a-zA-Z0-9\s]*$/;
	let validRegexMail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
	let validador = true;

	if (!validRegexMail.test(email)) {
		validador = false;
		emailInvalido();
		console.log("error email invalido");
	}
	if (!validRagexInput.test(username)) {
		validador = false;
		usuarioInvalido();
		console.log("error username invalido");
	}
	if (username === "" && email === "") {
		validador = false;
		camposBlanco();
		console.log("error username y email en blaco");
	}

	return validador;
}

function validarContraseñas(password, validPassword) {
	let validador = true;

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

	if (!(password === validPassword)) {
		validador = false;
		coincidenciaInvalida();
		console.log("las contraselas no coninciden");
	}

	return validador;
}

function validarCampoCodigo(codigo) {
	let validador = true;

	if (codigo.length != 6) {
		validador = false;
		codigoInvalido();
		console.log("Logitud del codigo incorrecta");
	}

	if (codigo == "") {
		validador = false;
		camposBlanco();
		console.log("campo codigo en blanco");
	}

	return validador;
}

function emailInvalido() {
	document.getElementById("emailInvalid").style.display = "flex";
}

function usuarioInvalido() {
	document.getElementById("userInvalid").style.display = "flex";
}

function passwordInvalida() {
	document.getElementById("passRecuperarInvalid").style.display = "flex";
}

function passwordValidInvalida() {
	document.getElementById("passValidRecuperarInvalid").style.display = "flex";
}
function coincidenciaInvalida() {
	document.getElementById("coincidenciaInvalidRecuperarPass").style.display = "flex";
}

function camposBlanco() {
	document.getElementById("campoBlanco1").style.display = "flex";
	document.getElementById("campoBlanco2").style.display = "flex";
	document.getElementById("campoBlancoCod").style.display = "flex";
}

function codigoInvalido() {
	document.getElementById("codInvalid").style.display = "flex";
}

function limpiarValidadores() {
	document.getElementById("emailInvalid").style.display = "none";
	document.getElementById("userInvalid").style.display = "none";
	document.getElementById("campoBlanco1").style.display = "none";
	document.getElementById("campoBlanco2").style.display = "none";
	document.getElementById("emailNoExiste").style.display = "none";
	document.getElementById("usuarioNoExiste").style.display = "none";
	document.getElementById("codInvalid").style.display = "none";
	document.getElementById("campoBlancoCod").style.display = "none";
	document.getElementById("codigoNoExiste").style.display = "none";
	document.getElementById("codigoUsado").style.display = "none";
	document.getElementById("passRecuperarInvalid").style.display = "none";
	document.getElementById("passValidRecuperarInvalid").style.display = "none";
	document.getElementById("coincidenciaInvalidRecuperarPass").style.display = "none";
}

function limpiarInput() {
	document.getElementById("email").value = "";
	document.getElementById("username").value = "";
	document.getElementById("codigo_recuperacion").value = "";
}

function cambiarSeccionIntroducirCodigo() {
	document.getElementById("seccion_recuperacion_envio").style.display = "none";
	document.getElementById("seccion_introducir_codigo").style.display = "grid";
	document.getElementById("seccion_recuperar").style.display = "none";
}

function cambiarSeccionRecuperarContraseña() {
	document.getElementById("seccion_recuperacion_envio").style.display = "none";
	document.getElementById("seccion_introducir_codigo").style.display = "none";
	document.getElementById("seccion_recuperar").style.display = "grid";
}
