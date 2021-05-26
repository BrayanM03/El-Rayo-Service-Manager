<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}


if(isset($_POST)){

    $id = $_POST["id"];
    

    $borrar_credito= $con->prepare("DELETE FROM abonos WHERE id_credito = ?");
    $borrar_credito->bind_param('i', $id);
    $borrar_credito->execute();
    $borrar_credito->close();

    $borrar_cred = $con->prepare("DELETE FROM creditos WHERE id = ?");
    $borrar_cred->bind_param('i', $id);
    $borrar_cred->execute();
    $borrar_cred->close();
    
   print_r(1); 

}

?>