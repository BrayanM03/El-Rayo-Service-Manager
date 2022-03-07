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

   

    
    $insertar_cliente = "INSERT INTO clientes(id, Nombre_Cliente, Telefono, Direccion, Correo, Credito, RFC, Latitud, Longitud) VALUES(null, ?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($insertar_cliente);
    $resultado->bind_param('sissssss', $nombre, $telefono, $direccion, $correo, $credito, $rfc, $latitud, $longitud);
    $resultado->execute();
    
    if ($resultado) {
        print_r(1);
    }else {
        print_r(0);
    }
   
}


?>
