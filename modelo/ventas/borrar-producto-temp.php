<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$iduser = $_SESSION["id_usuario"];
$idproduct = $_POST["id"];

if(isset($_POST)){

$consulta = mysqli_query($con, "DELETE FROM productos_temp$iduser WHERE id = $idproduct");


if ($consulta) {
    print_r(1);
    
}else{
    print_r(0);
}


}

?>