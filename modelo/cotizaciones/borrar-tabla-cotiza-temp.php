<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if (isset($_POST)) {
    $iduser = $_SESSION['id_usuario'];   
    
         $borrar_llanta= $con->prepare("TRUNCATE TABLE cotizacion_temp$iduser");
         $borrar_llanta->execute();
         $borrar_llanta->close();
         
        print_r(1); 
   
                  
}else{
    print_r("No se pudo establecer una conexión");
}


?>