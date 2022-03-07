<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if (isset($_POST)) {
   
    $codigo = $_POST["id_marca"];
    
         $borrar_llanta= $con->prepare("DELETE FROM marcas WHERE id = ?");
         $borrar_llanta->bind_param('i', $codigo);
         $borrar_llanta->execute();
         $borrar_llanta->close();
         
        print_r(1); 
   
                  
}else{
    print_r("No se pudo establecer una conexión");
}


?>