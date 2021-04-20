<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$iduser = $_SESSION["id_usuario"];

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST["data"])) {

    $vaciarTabla = "TRUNCATE TABLE productos_temp$iduser";

    $consulta = mysqli_query($con, $vaciarTabla);


if ($consulta) {
    print_r(1);
    
}else{
    print_r(0);
}


}

?>