<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

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
