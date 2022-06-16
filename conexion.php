<?php
require_once('config/config.php');
class conexion
{
    private $servidor;
    private $usuario;
    private $contrasena;
    private $basedatos;
    public  $conexion;

    public function __construct()
    {
        $this->servidor   = constant('HOST');
        $this->usuario      = constant('USER');
        $this->contrasena = constant('PASSWORD');
        $this->basedatos  = constant('DB');
    }

    function conectar()
    {
        $this->conexion = new PDO("mysql:host=$this->servidor;dbname=$this->basedatos", "$this->usuario", "$this->contrasena");
    }

    function cerrar()
    {
        $this->conexion->close();
    }
}
