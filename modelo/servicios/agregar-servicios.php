<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){


    $descripcion = $_POST["descripcion"];  
    $precio   = $_POST["precio"];   
    $estatus  = $_POST["estatus"];   
    $tipo    = $_POST["tipo"]; 
    $precio = floatval($precio);
    $traerLastID = "SELECT MAX(id) AS total FROM servicios";
    $resp = $con->prepare($traerLastID);
    $resp->execute();
    $resp->bind_result($last_id);
    $resp->fetch();
    $resp->close();

    $codigo = "SERV" . (intval($last_id) + 1);

    
    $insertar_serv = "INSERT INTO servicios(id, codigo, descripcion, precio, estatus, img) VALUES(null, ?,?,?,?,?)";
    $resultado = $con->prepare($insertar_serv);
    $resultado->bind_param('sssss', $codigo, $descripcion, $precio, $estatus, $tipo);
    $resultado->execute();
    
    if ($resultado) {
        print_r(1);
        $resultado->close();
    }else {
        print_r(0);
    }
   
}


?>
