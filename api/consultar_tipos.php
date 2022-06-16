<?php

require_once("../conexion.php");
if (isset($_GET)) {
    $conexion = new conexion();
    $conexion->conectar();
    $tipos_usuarios = array();
    try {
        $sql = "SELECT * FROM `tipo_usuario`";
        $stmt = $conexion->conexion->prepare($sql);
        $stmt->execute();
        while ($tipo = $stmt->fetch(PDO::FETCH_OBJ)) {
            array_push($tipos_usuarios, $tipo);
        }
    } catch (PDOException $e) {
        error_log("Error al hacer la consulta" . $e);
    }

    echo json_encode($tipos_usuarios);
}
