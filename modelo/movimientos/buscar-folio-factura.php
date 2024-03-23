<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
$folio = $_POST['folio'];
$param_ = '%' . $_POST['folio'] . '%';

$query = "SELECT COUNT(*) FROM movimientos WHERE (id = ? OR folio_factura LIKE ?)";
$stmt = $con->prepare($query);
$stmt->bind_param('ss', $folio, $param_);
$stmt->execute();
$stmt->bind_result($total_movimientos);
$stmt->fetch();
$stmt->close();

$id_movimiento=0;
if($total_movimientos > 0){
    $query = "SELECT m.*, p.nombre as proveedor FROM movimientos m INNER JOIN proveedores p ON p.id = m.proveedor_id WHERE (m.id = ? OR m.folio_factura LIKE ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ss', $folio, $param_);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stmt->free_result();
    $stmt->close();
    $total_facturas =0;
    $data = array();
    foreach ($resultado as $key => $value) {
        $total_facturas++;
        $data[] = $value;
        $id_movimiento = $value['id'];
    }

    if($total_movimientos == 1){
        $mensaje = 'Se encontró 1 factura';
        $resultado = 1;
        $estatus = true;
    }else if ($total_movimientos > 1){
        $mensaje = "Se encontró $total_facturas factura";
        $resultado = $total_facturas;
        $estatus = true;
    }
}else{
    $data=[];
    $estatus = false;
    $mensaje = "No se encontrarón resultados";
    $resultado = 0;
}

$arreglo_resp = array('data'=>$data, 'post'=>$_POST, 'estatus'=>$estatus, 'mensaje'=>$mensaje, 'total_facturas'=>$resultado, 'id_movimiento'=>$id_movimiento);
echo json_encode($arreglo_resp);