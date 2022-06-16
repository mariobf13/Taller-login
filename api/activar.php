<?php
error_reporting(E_ALL); //error/Exepcion engine
ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);
ini_set('error_log', '../php_log.log');
require_once("../conexion.php");

if (isset($_GET)) {
    $key = $_GET['key'];

    if (verificarKey($key)) {
        echo '<!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Document</title>
                <link
                    href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap"
                    rel="stylesheet"
                />
        
                <link
                    rel="stylesheet"
                    href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
                />
                <link rel="stylesheet" href="../assets/css/style.css" />
            </head>
            <body>
                <div class="container-fluid bg-light h-75">
                    <div class="container mt-5 shadow-lg p-3 mb-5 bg-body rounded">
                        <div class="d-flex justify-content-center">
                            <img src="../assets/img/confim1.jpg" alt="confirmacio_img" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <h5>Usuario activado con exito!</h5>
                        </div>
                    </div>
                </div>
                <script>
                    setTimeout(function () {
                        window.location.href = "../index.html";
                    }, 4000);
                </script>
            </body>
        </html>';
    } else {
        echo '<!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="UTF-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Document</title>
                <link
                    href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap"
                    rel="stylesheet"
                />
        
                <link
                    rel="stylesheet"
                    href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
                />
                <link rel="stylesheet" href="../assets/css/style.css" />
            </head>
            <body>
                <div class="container-fluid bg-light h-75">
                    <div class="container mt-5 shadow-lg p-3 mb-5 bg-body rounded">
                        <div class="d-flex justify-content-center">
                            <img src="../assets/img/error1.jpg" alt="confirmacio_img" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <h5>No se pudo activar usuario!</h5>
                        </div>
                    </div>
                </div>
                <script>
                    setTimeout(function () {
                        window.location.href = "../index.html";
                    }, 4000);
                </script>
            </body>
        </html>';
    }
}

function verificarKey($key)
{
    $verificacion = false;
    try {
        $conexion = new conexion();
        $conexion->conectar();
        $sql = "SELECT username, id FROM `usuarios` WHERE hash = '$key'";
        $stmt = $conexion->conexion->prepare($sql);
        if ($stmt->execute()) {
            $usuario = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($usuario)) {
                $key_bd = hash('sha256', $usuario->username);
                if ($key_bd == $key) {
                    actualizarEstado($usuario->id);
                    $verificacion = true;
                    error_log("Metodo verificarKey::Key existe en BD");
                }
            } else {
                error_log("Metodo verificarKey::Key no existe en la BD");
            }
        } else {
            error_log("Metodo verificarKey::No se Ejecuto la consulta a BD");
        }
    } catch (PDOException $e) {
        error_log("Metodo vverificarKey::Exepcion a la BD" . $e);
    }
    return $verificacion;
}

function actualizarEstado($id)
{
    try {
        $conexion = new conexion();
        $conexion->conectar();
        $sql = "UPDATE usuarios SET activo = '1' WHERE id = '$id'";
        $stmt = $conexion->conexion->prepare($sql);
        if ($stmt->execute()) {
            error_log("Metodo actualizarEstado::Usuario activado en BD");
        } else {
            error_log("Metodo actualizarEstado::No se pudo activar usuario en BD");
        }
    } catch (PDOException $e) {
        error_log("Metodo actualizarEstado::Exepcion a la BD" . $e);
    }
}
