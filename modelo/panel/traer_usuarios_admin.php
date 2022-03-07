<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$iduser = $_SESSION["id_usuario"];

if(isset($_POST)){

$consulta = mysqli_query($con, "SELECT * FROM usuarios WHERE rol LIKE 1 OR rol LIKE 2");


if ($consulta) {
    while($row = mysqli_fetch_assoc($consulta))
    {
      $id_usuario_admi = $row['id'] . " " . $row["nombre"] ; // Sumar variable $total + resultado de la consulta 
      $desc_notifi = $row["nombre"] . "acaba de realizar una venta";
      $estatus = 1; 
      $fecha = date("Y-m-d"); 
      $hora = date("h:i a");
      $refe = 0;  
      $queryInsertarNoti = "INSERT INTO registro_notificaciones (id, id_usuario, descripcion, estatus, fecha, hora, refe) VALUES (null,?,?,?,?,?,?)";
            $resultados = $con->prepare($queryInsertarNoti);
            $resultados->bind_param('isissi',$id_usuario_admi, $desc_notifi, $estatus, $fecha, $hora, $refe);
            $resultados->execute();
            $resultados->close();
    }
    
    
}else{
    
}


}

?>