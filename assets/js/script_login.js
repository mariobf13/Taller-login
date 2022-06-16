var modalExitosa = new bootstrap.Modal(document.getElementById("modalSuccess"));
var modalError = new bootstrap.Modal(document.getElementById("modalError"));

(() => {
	document.getElementById("btnInciarSesion").addEventListener("click", () => {
		limpiarValidadores();
		inciarSesion();
	});
})();

async function inciarSesion() {
	let usuario = {};
	let texto = "";
	let username = document.getElementById("username").value;
	let password = document.getElementById("password").value;

	let validador = validarCampos(password, username);

	if (validador) {
		usuario.username = username;
		usuario.password = password;
		const request = await fetch("./api/login.php", {
			method: "POST",
			headers: {
				Accept: "application/json",
				"Content-Type": "application/json",
			},
			body: JSON.stringify(usuario),
		});
		const respuesta = await request.json();
		if (request.ok) {
			if (respuesta.usernameExiste == 1) {
				//si el usuario existe
				if (respuesta.usernameActivo == 1) {
					//si usuario esta activo
					if (respuesta.passCoincide == 1) {
						texto = "setTimeout(function (){";
						texto +=
							'window.location.href="./views/vista' +
							respuesta.tipoUsuario +
							'.html"';
						texto += "}, 1000);";
						document.getElementById("vistaTipo").innerHTML = texto;
					} else {
						//si el pass no coincide con el username
						console.log("Password no coincide en BD");
						document.getElementById("passNoCoincide").style.display = "flex";
					}
				} else {
					//si usuario no esta activo
					console.log("Usuario no esta activo en BD");
					document.getElementById("userPorValidar").style.display = "flex";
					modalError.toggle();
				}
			} else {
				//si el usuario no existe
				console.log("Usuario no existe en BD");
				document.getElementById("userNoExiste").style.display = "flex";
			}
		} else {
			console.log(
				"Error al guardar los datos: " + request.status + " cuerpo: " + request.body
			);
		}
	}
}

function validarCampos(password, username) {
	let validRagexInput = /^[a-zA-Z0-9\s]*$/;

	let validador = true;

	if (!(password.length >= 6) || !(password.length <= 20) || password === "") {
		validador = false;
		passwordInvalida();
		console.log("error password invalido");
	}

	if (!validRagexInput.test(username) || username === "") {
		validador = false;
		usuarioInvalido();
		console.log("error username invalido");
	}

	return validador;
}

function passwordInvalida() {
	document.getElementById("passInvalid").style.display = "flex";
}

function usuarioInvalido() {
	document.getElementById("userInvalid").style.display = "flex";
}

function limpiarValidadores() {
	document.getElementById("userInvalid").style.display = "none";
	document.getElementById("userNoExiste").style.display = "none";
	document.getElementById("passInvalid").style.display = "none";
	document.getElementById("passNoCoincide").style.display = "none";
	document.getElementById("userPorValidar").style.display = "none";
}

function limpiarInput() {
	document.getElementById("username").value = "";
	document.getElementById("password").value = "";
}
