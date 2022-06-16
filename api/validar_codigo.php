<?php
error_reporting(E_ALL); //error/Exepcion engine
ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);
ini_set('error_log', '../php_log.log');
require_once("../conexion.php");


if (isset($_POST)) {
    $data = json_decode(file_get_contents('php://input'), true); //se recibe la informacion desde front
    $codigo = $data['codigo'];
    $resultado = validarCodigo($codigo);
    if ($resultado->existe == 1) {
        actualizarEstadoCodigo($resultado->id_codigo);
    }
    echo json_encode($resultado);
}

function validarCodigo($codigo)
{
    try {
        $conexion = new conexion();
        $conexion->conectar();

        $sql = "SELECT id, id_usuario, estado FROM `cod_recuperacion` WHERE codigo_recuperacion = '$codigo'";

        $stmt = $conexion->conexion->prepare($sql);

        $stmt->execute();

        $respuesta = $stmt->fetch(PDO::FETCH_OBJ);

        if (!empty($respuesta)) {
            error_log("Metodo validarCodig::Codigo existe existe en BD " . $respuesta->id);
            $object = (object) [
                'id_codigo' => $respuesta->id,
                'existe' => 1,
                'estado' => $respuesta->estado,
                'id_usuario' => $respuesta->id_usuario
            ];
            return $object;
        } else {
            error_log("Metodo validarCodig::Codigo no ya existe en BD ");
            $object = (object) [
                'id_codigo' => 0,
                'existe' => 0,
                'estado' => 0,
                'id_usuario' => 0
            ];
            return $object;
        }
    } catch (PDOException $e) {
        error_log("Metodo validarUsuario::Exepcion a la BD" . $e);
    }
}

function actualizarEstadoCodigo($id)
{
    try {
        $conexion = new conexion();
        $conexion->conectar();
        $sql = "UPDATE `cod_recuperacion` SET estado = '1' WHERE id = '$id'";
        $stmt = $conexion->conexion->prepare($sql);
        if ($stmt->execute()) {
            error_log("Metodo actualizarEstadoCodigo::Estado del codigo actualizado en BD");
        } else {
            error_log("Metodo actualizarEstadoCodigo::No se pudo actualizar estado codigo en BD");
        }
    } catch (PDOException $e) {
        error_log("Metodo actualizarEstadoCodigo::Exepcion a en consulta a BD " . $e);
    }
}
