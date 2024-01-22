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
$fecha = date("Y-m-d");
$hora = date("h:i a");

if(isset($_POST)){

    $id_detalle = $_POST["id_detalle"];
    $traercantida_d = "SELECT r.id, r.id_movimiento FROM detalle_requerimientos dr INNER JOIN requerimientos r ON dr.id_requerimiento = r.id WHERE dr.id = ?";
    $result = $con->prepare($traercantida_d);
    $result->bind_param('s', $id_detalle);
    $result->execute();
    $result->bind_result($id_requerimiento, $id_movimiento);
    $result->fetch();
    $result->close();

    $traercantidad = "SELECT COUNT(*) FROM detalle_requerimientos WHERE id = ?";
        $result = $con->prepare($traercantidad);
        $result->bind_param('s', $id_detalle);
        $result->execute();
        $result->bind_result($total_detalles);
        $result->fetch();
        $result->close();

    if($total_detalles > 0){

        $traercantidad = "SELECT COUNT(*) FROM detalle_requerimientos WHERE id_requerimiento = ?";
        $result = $con->prepare($traercantidad);
        $result->bind_param('s', $id_requerimiento);
        $result->execute();
        $result->bind_result($total_detalles_actuales);
        $result->fetch();
        $result->close();

        if($total_detalles_actuales == 1){
            $upd = "UPDATE requerimientos SET estatus = 2 WHERE id = ?";
            $stmt = $con->prepare($upd);
            $stmt->bind_param('s', $id_requerimiento);
            $stmt->execute();
            $stmt->close();
        }

        $upd = "UPDATE detalle_requerimientos SET estatus = 2 WHERE id = ?";
        $stmt = $con->prepare($upd);
        $stmt->bind_param('s', $id_detalle);
        $stmt->execute();
        $stmt->close();
        $res = array('estatus'=>true, 'mensaje'=>'Llanta desaprobada, estatus actualizado correctamente', 'id_requerimiento'=> $id_requerimiento);
    }  else{
        $res = array('estatus'=>false, 'mensaje'=>'No existe el id del registro');
    } 
}else{
    $res = array('estatus'=>false, 'mensaje'=>'No hay petición POST');
}

echo json_encode($res);

?>