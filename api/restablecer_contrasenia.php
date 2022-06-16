<?php
error_reporting(E_ALL); //error/Exepcion engine
ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);
ini_set('error_log', '../php_log.log');
require_once("../conexion.php");


if (isset($_POST)) {
    $data = json_decode(file_get_contents('php://input'), true); //se recibe la informacion desde front
    $id = $data['id'];
    $password = $data['password'];

    actualizarContraseña($id, $password);
}

function actualizarContraseña($id, $password)
{
    try {
        $conexion = new conexion();
        $conexion->conectar();
        $password_encryp = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE `usuarios` SET password = '$password_encryp' WHERE id = '$id'";
        $stmt = $conexion->conexion->prepare($sql);
        if ($stmt->execute()) {
            error_log("Metodo actualizarContraseña::Contraseña actualizada en BD");
        } else {
            error_log("Metodo actualizarEstadoCodigoactualizarContraseña::No se pudo actualizar contraseña en BD");
        }
    } catch (PDOException $e) {
        error_log("Metodo actualizarContraseña::Exepcion a en consulta a BD " . $e);
    }
}
