<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}


if(isset($_POST)){
    $id_credito = $_POST["id-credito"];
    $abono = $_POST["abono"];
    $fecha = date("Y-m-d");
  
   
    $insertar_abono = "INSERT INTO abonos(id, id_credito, fecha, abono)
                         VALUES(null,?,?,?)";
    $resultado = $con->prepare($insertar_abono);                     
    $resultado->bind_param('isd', $id_credito, $fecha, $abono);
    $resultado->execute();
    $resultado->close();

    
    $traerdata = "SELECT pagado, restante, total FROM creditos WHERE id = ?";
    $result = $con->prepare($traerdata);
    $result->bind_param('i',$id_credito);
    $result->execute();
    $result->bind_result($pagado, $restante, $total);
    $result->fetch();
    $result->close();

    $pagado_update = $pagado + $abono;
    $restante_update = $restante - $abono;
    
 

    $actualizar = "UPDATE creditos SET pagado = ?, restante = ?, estatus= 2 WHERE id = ?";
    $res = $con->prepare($actualizar);
    $res->bind_param('ddi', $pagado_update, $restante_update, $id_credito);
    $res->execute();
    $res->close();
    

    $traerdatadenew = "SELECT pagado, restante FROM creditos WHERE id = ?";
    $result = $con->prepare($traerdatadenew);
    $result->bind_param('i',$id_credito);
    $result->execute();
    $result->bind_result($pagado2, $restante2);
    $result->fetch();
    $result->close();

    $data = array("pagado_nuevo"=> $pagado2, "restante_nuevo"=>$restante2);

    echo json_encode($data, JSON_UNESCAPED_UNICODE); 
    
}


?>