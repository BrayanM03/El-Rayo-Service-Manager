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
    $id_detalle = $_POST["id_detalle"];
    $tipo = $_POST["tipo"];
    if($tipo == 1){
        $estatus = 3;
    }else if($tipo == 2){
        $estatus = 2;
    }else if ($tipo == 3){
        $estatus = 1;
    }else if ($tipo == 4){
        $estatus =1;
    }else{
        $estatus =1;
    }
    $aprob = "UPDATE detalle_requerimientos SET estatus = $estatus WHERE id = ?";
    $stmt = $con->prepare($aprob);
    $stmt->bind_param('s', $id_detalle);
    $stmt->execute();
    $stmt->close();

    $aprob = "SELECT id_requerimiento FROM detalle_requerimientos WHERE id = ?";
    $stmt = $con->prepare($aprob);
    $stmt->bind_param('s', $id_detalle);
    $stmt->execute();
    $stmt->bind_result($id_requerimiento);
    $stmt->fetch();
    $stmt->close();

    $response = array('estatus'=>true, 'mensaje'=>'Actualizado correctamente', 'post'=>$_POST, 'id_requerimiento'=>$id_requerimiento);
    echo json_encode($response);

}


?>