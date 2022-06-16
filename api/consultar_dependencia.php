<?php

require_once("../conexion.php");
if (isset($_GET)) {
    $conexion = new conexion();
    $conexion->conectar();
    $dependencias = array();
    try {
        $sql = "SELECT * FROM `dependencia_programa`";
        $stmt = $conexion->conexion->prepare($sql);
        $stmt->execute();
        while ($dependencia = $stmt->fetch(PDO::FETCH_OBJ)) {
            array_push($dependencias, $dependencia);
        }
    } catch (PDOException $e) {
        error_log("Error al hacer la consulta" . $e);
    }

    echo json_encode($dependencias);
}
