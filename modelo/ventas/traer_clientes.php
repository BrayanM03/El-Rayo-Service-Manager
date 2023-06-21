<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
$id_usuario = intval($_SESSION['id_usuario']); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if (isset($_POST)) {
    
    
   if(isset($_POST["searchTerm"])){
    $term = $_POST["searchTerm"];
    $termX = '%'.$term .'%';
    $query= "SELECT COUNT(*) FROM clientes WHERE Nombre_Cliente LIKE ?"; // WHERE id_asesor = ? OR id_autorizado = ?
    $resultado = $con->prepare($query);
    $resultado->bind_param('s', $termX);
    $resultado->execute();
    $resultado->bind_result($total_clientes);
    $resultado->fetch();
    $resultado->close();

   
    if($total_clientes > 0){
        $query="SELECT * FROM clientes WHERE  Nombre_Cliente LIKE '%$term%'"; // WHERE id_asesor = ? OR id_autorizado = ?

        $resultado = $con->query($query);
    
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
    echo "Nada";
};

   }else{

    $query= "SELECT COUNT(*) FROM clientes";
    $resultado = $con->prepare($query);
    //$resultado->bind_param('ii', $id_usuario, $id_usuario);
    $resultado->execute();
    $resultado->bind_result($total_clientes);
    $resultado->fetch();
    $resultado->close();
    if($total_clientes > 0){

        $querySuc = "SELECT * FROM clientes"; // WHERE id_asesor = ?  OR id_autorizado = ?
        $respon = $con->query($querySuc);
    
        while ($fila = $respon->fetch_assoc()) {
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
        echo "Nada";
    };
    

   /*  while($fila = $resultado->fetch_assoc()){
    $id= $fila["id"];
    $nombre = $fila["Nombre_Cliente"];
    $telefono = $fila["Telefono"];
    $direccion = $fila["Direccion"];
    $correo = $fila["Correo"];
    $credito = $fila["Credito"];
    $rfc = $fila["RFC"];
  

    $data[] = array("id" => $id, "nombre"=>$nombre, "telefono" => $telefono,
                    "direccion" => $direccion, "correo"=>$correo, "credito"=>$credito,   "rfc"=>$rfc);

                  
} */

//echo json_encode($data, JSON_UNESCAPED_UNICODE); 
   }
    
 

}else{
    print_r("No se pudo establecer una conexión");
}


?>