<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if (isset($_POST)) {
    $iduser = $_SESSION['id_usuario'];   
    $codigo = $_POST["id"];
    
         $borrar_llanta= $con->prepare("DELETE FROM cotizacion_temp$iduser WHERE id = ?");
         $borrar_llanta->bind_param('i', $codigo);
         $borrar_llanta->execute();
         $borrar_llanta->close();
         
        print_r(1); 
   
                  
}else{
    print_r("No se pudo establecer una conexión");
}


?>