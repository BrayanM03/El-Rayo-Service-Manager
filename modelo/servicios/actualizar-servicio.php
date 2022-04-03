<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){

    $id_servicio = $_POST['id_serv'];
    $descripcion = $_POST["descripcion"];  
    $precio   = $_POST["precio"];   
    $estatus  = $_POST["estatus"];   
    $tipo    = $_POST["tipo"]; 
    $precio = floatval($precio);

    $insertar_serv = "UPDATE servicios SET descripcion =?, precio=?, estatus =?, img=? WHERE id =?";
    $resultado = $con->prepare($insertar_serv);
    $resultado->bind_param('sssss', $descripcion, $precio, $estatus, $tipo, $id_servicio);
    $resultado->execute();
    
    if ($resultado) {
        print_r(1);
        $resultado->close();
    }else {
        print_r(0);
    }
   
}


?>
