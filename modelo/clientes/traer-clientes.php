<?php

include '../conexion.php'; 
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST)) {
       
    
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
  

    $data["data"][] = array("id" => $id, "nombre"=>$nombre, "telefono" => $telefono,
                    "direccion" => $direccion, "correo"=>$correo, "credito"=>$credito,   "rfc"=>$rfc);

                  
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);  

}else{
    print_r("No se pudo establecer una conexión");
}


?>