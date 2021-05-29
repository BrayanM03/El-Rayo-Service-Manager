<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$iduser = $_SESSION["id_usuario"];


if(isset($_POST["borrar"])){
    $idproduct = $_POST["id"];
$consulta = mysqli_query($con, "DELETE FROM productos_temp$iduser WHERE id = $idproduct");


if ($consulta) {
    print_r(1);
    
}else{
    print_r(0);
}


}

if(isset($_POST["reinicio"])){
    $consulta = mysqli_query($con, "DELETE FROM productos_temp$iduser");
}

?>