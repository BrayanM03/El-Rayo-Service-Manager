<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){

    $id = $_POST["id"];
    $nombre    = $_POST["nombre"];  
    $credito   = $_POST["credito"];   
    $telefono  = $_POST["telefono"];   
    $correo    = $_POST["correo"];   
    $rfc       = $_POST["rfc"];   
    $direccion = $_POST["direccion"];   
    $latitud   = $_POST["latitud"];  
    $longitud  = $_POST["longitud"];
    $asesor    = $_POST["asesor"];

   

    
    $update_cliente = "UPDATE clientes SET Nombre_Cliente = ?, Telefono = ?, Direccion = ?, Correo = ?, Credito = ?, RFC = ?, Latitud = ?, Longitud = ?, id_asesor = ? WHERE id = ?";
    $resultado = $con->prepare($update_cliente);
    $resultado->bind_param('sisssssssi', $nombre, $telefono, $direccion, $correo, $credito, $rfc, $latitud, $longitud, $asesor, $id);
    $resultado->execute();
    
    if ($resultado) {
        print_r(1);
    }else {
        print_r(0);
    }
   
}


?>
