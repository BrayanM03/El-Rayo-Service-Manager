<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

require_once('../movimientos/mover_clientes.php');

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){


    $nombre    = $_POST["nombre"];  
    $credito   = $_POST["credito"];   
    $telefono  = $_POST["telefono"];   
    $correo    = $_POST["correo"];   
    $rfc       = $_POST["rfc"];   
    $direccion = $_POST["direccion"];   
    $latitud   = $_POST["latitud"];  
    $longitud  = $_POST["longitud"];
    $asesor = $_POST["asesor"];

    $asesor_nombre = traerAsesor($asesor, $con);
    $cambios = "Se agregó nuevo cliente: $nombre - Asesor: $asesor_nombre";
    InsertarMovimiento("insercion", $cambios, $con);

    
    $insertar_cliente = "INSERT INTO clientes(id, Nombre_Cliente, Telefono, Direccion, Correo, Credito, RFC, Latitud, Longitud, id_asesor) VALUES(null, ?,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($insertar_cliente);
    $resultado->bind_param('sisssssss', $nombre, $telefono, $direccion, $correo, $credito, $rfc, $latitud, $longitud, $asesor);
    $resultado->execute();
    
    if ($resultado) {
        print_r(1);
    }else {
        print_r(0);
    }
   
}


?>
