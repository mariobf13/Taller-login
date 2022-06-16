<?php
error_reporting(E_ALL); //error/Exepcion engine
ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);
ini_set('error_log', '../php_log.log');
require_once("../conexion.php");
require_once('../config/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../libs/vendor/autoload.php';


if (isset($_POST)) {
    $data = json_decode(file_get_contents('php://input'), true); //se recibe la informacion desde front

    $nombres = $data['nombres'];
    $apellidos = $data['apellidos'];
    $identificacion = $data['identificacion'];
    $email = $data['email'];
    $tipo_id = $data['tipo_id'];
    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT);
    $programa_dependencia_id = $data['programa_dependencia_id'];
    $activo = $data['activo'];
    $hash = hash('sha256', $username);

    $resultado = validarUsuario($username, $email);

    if ($resultado->username == 0 && $resultado->email == 0) {
        enviarMail($email, $nombres, $apellidos, $hash);
        registrarUsuario(
            $tipo_id,
            $programa_dependencia_id,
            $identificacion,
            $nombres,
            $apellidos,
            $email,
            $username,
            $password,
            $activo,
            $hash
        );
    }
    echo json_encode($resultado);
}

/**
 * La funcion validarUusario($usuario) recibe como parametro un usuario y valida
 * si en el servidor ya hay un usuario con ese nombre, esta funcion devuelve 0 si no hay
 * y 1 si lo hay esto se se manda como json para que el front valide y si acepte la peticion
 * de creacion
 * */
function validarUsuario($username, $email)
{
    try {
        $conexion = new conexion();
        $conexion->conectar();
        $sql = "SELECT username, id FROM `usuarios` WHERE username = '$username'";
        $sql2 = "SELECT email, id FROM `usuarios` WHERE email = '$email'";
        $stmt = $conexion->conexion->prepare($sql);
        $stmt2 = $conexion->conexion->prepare($sql2);
        $stmt->execute();
        $stmt2->execute();
        $usuario = $stmt->fetch(PDO::FETCH_OBJ);
        $mail = $stmt2->fetch(PDO::FETCH_OBJ);
        if (empty($usuario) || empty($mail)) {
            error_log("Metodo validarUsuario::Usuario o correo ya existe en BD");
            $object = (object) [
                'username' => empty($usuario) ? 0 : 1,
                'email' => empty($mail) ? 0 : 1
            ];
            return $object;
        } else {
            error_log("Metodo validarUsuario:: Usuario y Correo ya existe en BD " . $usuario->username);
            $object = (object) [
                'username' =>  1,
                'email' =>  1
            ];
            return $object;
        }
    } catch (PDOException $e) {
        error_log("Metodo validarUsuario::Exepcion a la BD" . $e);
    }
}

/**
 * Metodo para enviar email al usuario registrado
 * 
 */

function enviarMail($email, $nombres, $apellidos, $hash)
{
    try {
        //se crea el objeto de PHPMailer y se establecen los parametro para envio
        $mail = new PHPMailer(true);
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  //servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'proyecto.desarrollo.web.correo@gmail.com'; //Correo de envio
        $mail->Password = 'zbmvbqwxgifgixfg'; //Contraseña del correo
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Encriptacion
        $mail->Port = 465; //Puerto del servidor
        $mail->setFrom('proyecto.desarrollo.web.correo@gmail.com', 'Administrador Web'); //Remitente
        $mail->addAddress($email, $nombres . " " . $apellidos); //Correo a quien se le envia
        $mail->isHTML(true);
        $mail->Subject = 'Validacion de cuenta de usuario';  //Asunto del correo
        //Codigo HTML que se quiere que se muestre
        $mail->Body = ' 
        <html>
	<head>
		<title>Activacion de usuario</title>
	</head>
	<body>
		<div
			style="
				float: left;
				background-color: #ffffff;
				padding: 10px 30px 10px 30px;
				border: 1px solid #f6f6f6;
			"
		>
			<div class="adM"></div>
			<div style="float: left; max-width: 470px">
				<div class="adM"></div>
				<p
					style="
						line-height: 21px;
						font-family: Helvetica, Verdana, Arial, sans-serif;
						font-size: 12px;
					"
				>
					<strong
						style="
							line-height: 21px;
							font-family: Helvetica, Verdana, Arial, sans-serif;
							font-size: 18px;
						"
						>Confirma la activacion</strong
					>
				</p>
				<div
					style="
						line-height: 21px;
						min-height: 100px;
						font-family: Helvetica, Verdana, Arial, sans-serif;
						font-size: 12px;
					"
				>
					<p
						style="
							line-height: 21px;
							font-family: Helvetica, Verdana, Arial, sans-serif;
							font-size: 12px;
						"
					>
						Gracias por registrate
					</p>
					<p
						style="
							line-height: 21px;
							font-family: Helvetica, Verdana, Arial, sans-serif;
							font-size: 12px;
						"
					>
						Valida tu cuenta ingresando al siguiente link:
					</p>
					<p
						style="
							line-height: 21px;
							font-family: Helvetica, Verdana, Arial, sans-serif;
							font-size: 12px;
							margin-bottom: 25px;
							background-color: #f7f9fc;
							padding: 15px;
						"
					>
						<strong>Confirmar: </strong
						><a
							style="color: #4371ab; text-decoration: none"
							href="' . constant('URL') . 'api/activar.php?key=' . $hash . '"' . '
							target="_blank"
							>Click para activar cuenta</a
						>
					</p>
					<p
						style="
							line-height: 21px;
							font-family: Helvetica, Verdana, Arial, sans-serif;
							font-size: 12px;
						"
					>
						Muchas Gracias,<br />Desarrollo Web
					</p>
					<div class="yj6qo"></div>
					<div class="adL"></div>
				</div>
				<div class="adL"></div>
			</div>
			<div class="adL"></div>
		</div>
	</body>
</html>';
        if (!$mail->send()) { //se valida si se envio correctamente
            error_log("Metodo enviarMail::Errora enviar email " . $mail->ErrorInfo);
        } else {
            error_log("Metodo enviarMail::Email enviado con exito");
        }
    } catch (Exception $e) {
        error_log("Metodo enviarMail::Exepcion a en consulta a BD " . $e);
    }
}


/**
 * Metodo para registrar usuario en BD
 */

function registrarUsuario($tipo_id, $programa_dependencia_id, $identificacion, $nombres, $apellidos, $email, $username, $password, $activo, $hash)
{
    try {
        $conexion = new conexion();
        $conexion->conectar();
        $sql = "INSERT INTO usuarios(tipo_id,
                programa_dependencia_id,
                identificacion,
                nombres, 
                apellidos,
                email,
                username,
                password,
                activo,
                hash
                ) VALUES ('$tipo_id',
                '$programa_dependencia_id',
                '$identificacion',
                '$nombres',
                '$apellidos',
                '$email',
                '$username',
                '$password',
                '$activo',
                '$hash');";
        $stmt = $conexion->conexion->prepare($sql);
        if ($stmt->execute()) {
            error_log("Metodo ingresarUsuario::Usuario registrado en BD");
        } else {
            error_log("Metodo ingresarUsuario::No se pudo registrar usuario en BD");
        }
    } catch (PDOException $e) {
        error_log("Metodo ingresarUsuario::Exepcion a en consulta a BD " . $e);
    }
}
