<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if (isset($_POST)) {
       
    
    $query="SELECT * FROM movimientos";

    $resultado = mysqli_query($con, $query);

    while($fila = $resultado->fetch_assoc()){
    $id= $fila["id"];
    $id = intval($id);
    $descripcion = $fila["descripcion"];
    $mercancia = $fila["mercancia"];
    $fecha = $fila["fecha"];
    $hora = $fila["hora"];
    $usuario = $fila["usuario"];
    $tipo = $fila["tipo"];
    $tipo = intval($tipo);
  

    $data["data"][] = array("id" => $id, "descripcion"=>$descripcion, "mercancia" => $mercancia,
                    "fecha" => $fecha, "hora"=>$hora, "usuario"=>$usuario, "tipo"=> $tipo);

                  
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);  

}else{
    print_r("No se pudo establecer una conexión");
}


?>