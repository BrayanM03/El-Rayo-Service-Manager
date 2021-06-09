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
    $query="SELECT * FROM clientes WHERE Nombre_Cliente LIKE '%$term%'";

    $resultado = mysqli_query($con, $query);

    while($fila = $resultado->fetch_assoc()){
    $id= $fila["id"];
    $nombre = $fila["Nombre_Cliente"];
    $telefono = $fila["Telefono"];
    $direccion = $fila["Direccion"];
    $correo = $fila["Correo"];
    $credito = $fila["Credito"];
    $rfc = $fila["RFC"];
  

    $data[] = array("id" => $id, "nombre"=>$nombre, "telefono" => $telefono,
                    "direccion" => $direccion, "correo"=>$correo, "credito"=>$credito,   "rfc"=>$rfc);

                  
}

echo json_encode($data, JSON_UNESCAPED_UNICODE); 
   }else{
    $query="SELECT * FROM clientes";

    $resultado = mysqli_query($con, $query);

    while($fila = $resultado->fetch_assoc()){
    $id= $fila["id"];
    $nombre = $fila["Nombre_Cliente"];
    $telefono = $fila["Telefono"];
    $direccion = $fila["Direccion"];
    $correo = $fila["Correo"];
    $credito = $fila["Credito"];
    $rfc = $fila["RFC"];
  

    $data[] = array("id" => $id, "nombre"=>$nombre, "telefono" => $telefono,
                    "direccion" => $direccion, "correo"=>$correo, "credito"=>$credito,   "rfc"=>$rfc);

                  
}

echo json_encode($data, JSON_UNESCAPED_UNICODE); 
   }
    
 

}else{
    print_r("No se pudo establecer una conexión");
}


?>