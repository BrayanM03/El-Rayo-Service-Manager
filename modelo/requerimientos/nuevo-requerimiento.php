<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php"); 
}
date_default_timezone_set("America/Matamoros");


if($_POST){
    
    $id_usuario = $_SESSION['id_usuario'];
    $comentario = $_POST['comentario'];
    $id_sucursal = $_SESSION['id_sucursal'];
    $estatus = 1;
    $fecha = date('Y-m-d');
    $hora = date('h:i a');
    $sel = "SELECT * FROM detalle_cambio WHERE id_usuario = ?";
    $stmt = $con->prepare($sel);
    $stmt->bind_param('s', $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    $stmt->close();

    $insert = "INSERT INTO requerimientos(id, id_usuario, comentario, id_sucursal, fecha_inicio, hora_inicio, estatus) VALUES (null, ?,?,?,?,?,?)";
    $stmt = $con->prepare($insert);
    $stmt->bind_param('ssssss',$id_usuario, $comentario, $id_sucursal, $fecha, $hora, $estatus);
    $stmt->execute();
    $id_requerimiento = $stmt->insert_id;
    $stmt->close();


    while($row = $result->fetch_assoc()){
        $id_llanta = $row['id_llanta'];
        $id_ubicacion = $row['id_ubicacion'];
        $id_destino = $row['id_destino'];
        $cantidad = $row['cantidad'];
        $id_usuario = $row['id_usuario'];
        $insert = "INSERT INTO detalle_requerimientos(id, id_llanta, id_ubicacion, id_destino, cantidad, id_usuario, estatus, id_requerimiento) VALUES (null, ?,?,?,?,?,?,?)";
        $stmt = $con->prepare($insert);
        $stmt->bind_param('sssssss', $id_llanta, $id_ubicacion, $id_destino, $cantidad, $id_usuario, $estatus, $id_requerimiento);
        $stmt->execute();
        $stmt->close();
    }

    $response = array('estatus'=>true, 'mensaje'=> 'Requesición registrada con exito');
}else{
    $response = array('estatus'=>false, 'mensaje'=> 'No se recibió un solicitud POST');

}

echo json_encode($response);

?>