<?php

session_start();
include '../conexion.php';
require_once '../catalogo/Catalogo.php';
$con= $conectando->conexion(); 
$catalogo = new Catalogo($con);

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if($_POST){ 
    
    $id_sucursal = $_POST['id_sucursal'];
    $id_sucursal_contraria =isset($_POST['id_sucursal_contraria']) ? $_POST['id_sucursal_contraria'] : null;
    /* $count = "SELECT COUNT(DISTINCT(hdc.id_movimiento)) FROM historial_detalle_cambio hdc 
    INNER JOIN movimientos m ON m.id = hdc.id_movimiento WHERE (hdc.id_ubicacion = ? AND (hdc.aprobado_emisor = 0 OR hdc.aprobado_receptor =0)) OR ((hdc.aprobado_receptor = 0 OR hdc.aprobado_emisor = 0 )AND hdc.id_destino = ?)";
    */ 
    $id_llanta = isset($_POST['id_llanta']) ? $_POST['id_llanta'] : 0;

    if($id_llanta!=0){
        $response_stock_destino = $catalogo->obtenerStock($id_llanta, $id_sucursal);
        $stock_destino = $response_stock_destino['data'];
    }else{
        $stock_destino =0;
    }

    if ($id_sucursal_contraria) {
        // Si ambas sucursales han sido seleccionadas
        $count = "SELECT COUNT(DISTINCT(hdc.id_movimiento)) 
                  FROM historial_detalle_cambio hdc 
                  INNER JOIN movimientos m ON hdc.id_movimiento = m.id
                  WHERE (hdc.id_ubicacion = ? AND (hdc.aprobado_emisor = 0)) 
                  OR (hdc.id_destino = ? AND (hdc.aprobado_receptor = 0))";

        $stmt = $con->prepare($count);
        $stmt->bind_param('ss', $id_sucursal, $id_sucursal);
    } else {
        // Si solo una sucursal ha sido seleccionada
        $count = "SELECT COUNT(DISTINCT(hdc.id_movimiento)) 
                  FROM historial_detalle_cambio hdc 
                  INNER JOIN movimientos m ON hdc.id_movimiento = m.id
                  WHERE (hdc.id_ubicacion = ? AND (hdc.aprobado_emisor = 0)) 
                  OR (hdc.id_destino = ? AND (hdc.aprobado_receptor = 0))";

        $stmt = $con->prepare($count);
        $stmt->bind_param('ss', $id_sucursal, $id_sucursal);
    }
    /* $stmt = $con->prepare($count);
    $stmt->bind_param('ssss', $id_sucursal, $id_sucursal_contraria, $id_sucursal_contraria, $id_sucursal); */
    $stmt->execute();
    $stmt->bind_result($total_cambios);
    $stmt->fetch();
    $stmt->close();

    

   echo json_encode(array('estatus'=>true, 'total_cambios'=>0, 'query'=>$count, 'stock_destino'=>$stock_destino)); //$total_cambios
}