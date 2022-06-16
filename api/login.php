<?php
error_reporting(E_ALL); //error/Exepcion engine
ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);
ini_set('error_log', '../php_log.log');
require_once("../conexion.php");



if (isset($_POST)) {
    $data = json_decode(file_get_contents('php://input'), true); //se recibe la informacion desde front

    $password = $data['password'];
    $username = $data['username'];

    $resultado = validarSesion($username, $password);

    echo json_encode($resultado);
}

/**
 * La funcion validarUusario($usuario) recibe como parametro un usuario y valida
 * si en el servidor ya hay un usuario con ese nombre, esta funcion devuelve 0 si no hay
 * y 1 si lo hay esto se se manda como json para que el front valide y si acepte la peticion
 * de creacion
 * */
function validarSesion($username, $password)
{
    try {
        $conexion = new conexion();
        $conexion->conectar();

        $sql = "SELECT username,id, password,activo, tipo_id FROM `usuarios` WHERE username = '$username'";

        $stmt = $conexion->conexion->prepare($sql);

        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_OBJ);

        if (!empty($usuario)) {
            if ($usuario->activo == 1) {
                if (password_verify($password, $usuario->password)) {
                    //si las pass no coinciden
                    error_log("Metodo validarSesion::Password coincide en BD");
                    $nombreTipo = cargarVistaTipoUsuario($usuario->tipo_id);
                    $object = (object) [
                        'usernameExiste' =>  1,
                        'usernameActivo' =>  1,
                        'passCoincide' =>  1,
                        'tipoUsuario' =>  $nombreTipo,
                    ];
                    return $object;
                } else {
                    //si las pass no coinciden
                    error_log("Metodo validarSesion::Password no coincide en BD");
                    $object = (object) [
                        'usernameExiste' =>  1,
                        'usernameActivo' =>  1,
                        'passCoincide' =>  0,
                        'tipoUsuario' =>  "",
                    ];
                    return $object;
                }
            } else {
                // usuario existe pero no esta activo
                error_log("Metodo validarSesion::Usuario no esta activo en BD");
                $object = (object) [
                    'usernameExiste' =>  1,
                    'usernameActivo' =>  0,
                    'passCoincide' =>  0,
                    'tipoUsuario' =>  "",
                ];
                return $object;
            }
        } else {
            error_log("Metodo validarSesion::Usuario no existe en BD " . $username);
            $object = (object) [
                'usernameExiste' =>  0,
                'usernameActivo' =>  0,
                'passCoincide' =>  0,
                'tipoUsuario' =>  "",
            ];
            return $object;
        }
    } catch (PDOException $e) {
        error_log("Metodo validarSesion::Exepcion a la BD" . $e);
    }
}




/**
 * Metodo para registrar usuario en BD
 */

function cargarVistaTipoUsuario($tipo_id)
{
    try {
        $conexion = new conexion();
        $conexion->conectar();
        $sql = "SELECT nombre FROM tipo_usuario WHERE id = '$tipo_id'";
        $stmt = $conexion->conexion->prepare($sql);
        if ($stmt->execute()) {
            $tipo = $stmt->fetch(PDO::FETCH_OBJ);
            return  $tipo->nombre;
        } else {
            error_log("Metodo cargarVistaTipoUsuario::No se pudo cargar tipo de usuario de BD");
        }
    } catch (PDOException $e) {
        error_log("Metodo cargarVistaTipoUsuario::Exepcion a en consulta a BD " . $e);
    }
}
