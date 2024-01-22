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
$fecha = date('Y-m-d');
$hora = date('h:i a');
$tipo = $_POST['tipo'];
$accion = $_POST['accion'];
$comentario = $_POST['comentario'];
$id_dc = $_POST['id_dc'];
$id_sesion = $_SESSION['id_usuario'];
$id_movimiento = $_POST['id_movimiento'];
$query = "SELECT COUNT(*) FROM historial_detalle_cambio WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $id_dc);
$stmt->execute();
$stmt->bind_result($total_mer);
$stmt->fetch();
$stmt->close();

if($total_mer>0){
if($tipo==1){
    $col = 'aprobado_emisor';
    $col_comentario = 'comentario_emisor';
    $col_usuario = 'usuario_emisor';
}else{
    $col = 'aprobado_receptor';
    $col_comentario = 'comentario_receptor';
    $col_usuario = 'usuario_receptor';

}
$query = "UPDATE historial_detalle_cambio SET $col = ?, $col_comentario = ?, $col_usuario =? WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('ssss', $accion, $comentario, $id_sesion, $id_dc);
$stmt->execute();
$stmt->close();
$mensaje = 'Estatus de mercancia actualizado';

//Actualizar estatus requisiciones
$query = "SELECT COUNT(*) FROM requerimientos WHERE id_movimiento = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($total_req);
    $stmt->fetch();
    $stmt->close();  
if($total_req > 0){
    $query = "SELECT id FROM requerimientos WHERE id_movimiento = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($id_requerimiento);
    $stmt->fetch();
    $stmt->close(); 
    $query = "SELECT id_llanta, aprobado_receptor, aprobado_emisor FROM historial_detalle_cambio WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $id_dc);
    $stmt->execute();
    $stmt->bind_result($id_llanta, $aprobado_receptor, $aprobado_emisor);
    $stmt->fetch();
    $stmt->close();

    if($accion == 1 && ($aprobado_emisor == 1 && $aprobado_receptor == 1)){
        $query = "UPDATE detalle_requerimientos SET estatus =6 WHERE id_llanta = ? AND id_requerimiento = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ss', $id_llanta, $id_requerimiento);
        $stmt->execute();
        $stmt->close();
    }else{
        if($accion == 1 && $tipo == 1 ){

            $query = "UPDATE detalle_requerimientos SET estatus =4 WHERE id_llanta = ? AND id_requerimiento = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('ss', $id_llanta, $id_requerimiento);
            $stmt->execute();
            $stmt->close();
        }else if($accion == 1  && $tipo == 2){
            $query = "UPDATE detalle_requerimientos SET estatus =5 WHERE id_llanta = ? AND id_requerimiento = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('ss', $id_llanta, $id_requerimiento);
            $stmt->execute();
            $stmt->close();
        }else if($accion == 2 && $tipo == 1){
            $query = "UPDATE detalle_requerimientos SET estatus =7 WHERE id_llanta = ? AND id_requerimiento = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('ss', $id_llanta, $id_requerimiento);
            $stmt->execute();
            $stmt->close();
        }else if($accion == 2 && $tipo == 2){
            $query = "UPDATE detalle_requerimientos SET estatus =8 WHERE id_llanta = ? AND id_requerimiento = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('ss', $id_llanta, $id_requerimiento);
            $stmt->execute();
            $stmt->close();
        }
    }

    
}      
//Comprobacion 
$query = "SELECT COUNT(*) FROM historial_detalle_cambio WHERE id_movimiento = ? AND (aprobado_emisor != 1 OR aprobado_receptor != 1)";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $id_movimiento);
$stmt->execute();
$stmt->bind_result($historial_aprobado);
$stmt->fetch();
$stmt->close();

if($historial_aprobado ==0){
    $query = "UPDATE movimientos SET estatus = 'Completado' WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $id_movimiento);
    $stmt->execute();
    $stmt->close();
    if($total_req > 0){
        $query = "UPDATE detalle_requerimientos SET estatus =6 WHERE id_llanta = ? AND id_requerimiento = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ss', $id_llanta, $id_requerimiento);
        $stmt->execute();
        $stmt->close();
        $query = "UPDATE requerimientos SET estatus =4 WHERE  id_movimiento = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $id_movimiento);
        $stmt->execute();
        $stmt->close();
    }
   
    $mensaje = 'Estatus de mercancia actualizado y movimiento actualizado a completado';
}

$response = array('estatus' =>true, 'mensaje' => $mensaje, 'numero_no_aprobado'=> $historial_aprobado);

}else{
    $response = array('estatus' =>false, 'mensaje' =>'No se encontro el registro de esta llanta en el detalle de cambios');
}

echo json_encode($response);

?>