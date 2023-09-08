<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if (isset($_POST)) {
    $iduser = $_SESSION['id_usuario'];   
    $tipo_cotizacion = $_POST['tipo_cotizacion'];
         $borrar_llanta= $con->prepare("DELETE FROM detalle_nueva_cotizacion WHERE tipo = ?");
         $borrar_llanta->bind_param('s', $tipo_cotizacion);
         $borrar_llanta->execute();
         $borrar_llanta->close();
         
        print_r(1); 
   
                  
}else{
    print_r("No se pudo establecer una conexión");
}


?>