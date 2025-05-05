<?php

include '../conexion.php';
include 'Empleado.php';
include '../helpers/response_helper.php';
$con= $conectando->conexion(); 

if($_POST){
    $tipo = $_POST['tipo'];
    $id_incidencia = $_POST['id'];

    if($tipo == 1){
        $update = 'UPDATE incidencias SET estatus = 0 WHERE id = ?';
        $stmt = $con->prepare($update);
        $stmt->bind_param('s', $id_incidencia);
        $stmt->execute();
        $stmt->close();
        $mensaje = 'Incidencia desactivada con exito';
    }else if($tipo==2){
        $update = 'DELETE FROM incidencias WHERE id = ?';
        $stmt = $con->prepare($update);
        $stmt->bind_param('s', $id_incidencia);
        $stmt->execute();
        $stmt->close();
        $mensaje = 'Incidencia eliminada con exito';

    }else if($tipo==3){
        $update = 'UPDATE incidencias SET estatus = 1 WHERE id = ?';
        $stmt = $con->prepare($update);
        $stmt->bind_param('s', $id_incidencia);
        $stmt->execute();
        $stmt->close();
        $mensaje = 'Incidencia activada con exito';

    }else{
        responder(false, "El id del tipo no coincide", 'success', []);
        die();
    }

    responder(true, $mensaje, 'success', []);
   
}else{

    responder(false, "No hay solicitud post", 'success', []);
}