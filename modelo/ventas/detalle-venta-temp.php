<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){

    $iduser = $_SESSION["id_usuario"];

    $consultar = "SELECT * FROM productos_temp$iduser";
    $result = $con->query($consultar);

    while($fila= $result->fetch_assoc()){
        
        $id           =            $fila["id"];
        $codigo       =        $fila["codigo"];
        $descripcion  =   $fila["descripcion"];
        $modelo       =        $fila["modelo"];
        $cantidad     =      $fila["cantidad"];
        $precio       =        $fila["precio"];
        $importe      =       $fila["importe"];


        $data["data"][] = array("id" => $id, 
                        "codigo" => $codigo,
                        "descripcion"=>$descripcion, 
                        "modelo" => $modelo, 
                        "cantidad" => $cantidad,
                        "precio"=>$precio, 
                        "importe"=>$importe);
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);


}


?>