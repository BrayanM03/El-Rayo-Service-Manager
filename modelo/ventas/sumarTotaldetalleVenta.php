<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$iduser = $_SESSION["id_usuario"];

if(isset($_POST)){

$consulta = mysqli_query($con, "SELECT * FROM productos_temp$iduser");
$total = 0; // total declarado antes del bucle

if ($consulta) {
    while($row = mysqli_fetch_assoc($consulta))
    {
      $total = $total + $row['importe']; // Sumar variable $total + resultado de la consulta 
    }
    
    print_r($total);
}else{
    print_r($total);
}


}

?>