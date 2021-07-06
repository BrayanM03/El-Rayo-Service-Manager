<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if (isset($_POST)) {
  

   if(isset($_POST["searchTerm"])){
    $term = $_POST["searchTerm"];
    $query="SELECT * FROM servicios WHERE descripcion LIKE '%$term%' OR precio LIKE '%$term%' OR img LIKE '%$term%'";

    $resultado = mysqli_query($con, $query);

    while($fila = $resultado->fetch_assoc()){
    $id= $fila["id"];
    $descripcion = $fila["descripcion"];
    $precio = $fila["precio"];
    $estatus = $fila["estatus"];
    $img = $fila["img"];
  

    $data[] = array("id" => $id, "descripcion"=>$descripcion, "precio" => $precio,
                    "estatus" => $estatus, "imagen" => $img);

                  
}

echo json_encode($data, JSON_UNESCAPED_UNICODE); 
   }
}else{
    print_r("No se pudo establecer una conexión");
}


?>