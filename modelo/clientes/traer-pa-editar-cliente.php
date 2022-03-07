<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){


    $id   = $_POST["id"]; 

   

    
    $traer_cliente = "SELECT * FROM clientes WHERE id =?";
    $resultado = $con->prepare($traer_cliente);
    $resultado->bind_param('i', $id);
    $resultado->execute();
    $resultados = $resultado->get_result();
    $fila = $resultados->fetch_assoc();
    $resultado->close();
 
         $id = $fila["id"];
         $nombre = $fila["Nombre_Cliente"];
         $telefono = $fila["Telefono"];
         $direccion = $fila["Direccion"];
         $correo = $fila["Correo"];
         $credito = $fila["Credito"];
         $rfc = $fila["RFC"];
         $latitud = $fila["Latitud"];
         $longitud = $fila["Longitud"];
         

         $data = array("id" => $id, "nombre" => $nombre, "telefono" => $telefono, "direccion" => $direccion,
                        "correo"=> $correo, "credito" => $credito, "rfc" => $rfc, "latitud" => $latitud, "longitud" => $longitud);
     
         echo json_encode($data, JSON_UNESCAPED_UNICODE);
  
   
}


?>
