<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){

    
    $query = "CREATE TEMPORARY TABLE detalle_venta_temp( id INT NOT NULL, codigo VARCHAR(250) NOT NULL, modelo VARCHAR(150) NOT NULL, cantidad INT NOT NULL, precio DECIMAL(7,2) NOT NULL DEFAULT 0.00, subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00)";


    $result = mysqli_query($con, $query);

    if ($result) {
        print_r("Se realizo la consulta");
    }else{
        print_r("No se realizo nada");
    }


}


?>