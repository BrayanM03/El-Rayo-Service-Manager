<?php

include '../conexion.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}
$id_abono = $_POST['id_abono'];
$id_movimiento = $_POST['id_movimiento'];
$query = "SELECT total, pagado, restante FROM movimientos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i',$id_movimiento);
    $stmt->execute();
    $result = $stmt->bind_result($importe_total, $pagado, $restante);
    $stmt->fetch();
    $stmt->close();

    $query = "SELECT monto FROM abonos_cuentas WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i',$id_abono);
    $stmt->execute();
    $data = $stmt->bind_result($monto_a_descontar);
    $stmt->fetch();
    $stmt->close();

    $nuevo_pagado = $pagado - $monto_a_descontar;
    $nuevo_restante = $restante + $monto_a_descontar;

    $update = "UPDATE movimientos SET estado_factura = 2, pagado = ?, restante = ? WHERE id = ?";
    $respp = $con->prepare($update);
    $respp->bind_param('sss', $nuevo_pagado, $nuevo_restante, $id_movimiento);
    $respp->execute();
    $respp->close();

$borrar_abono= $con->prepare("DELETE FROM abonos_cuentas WHERE id = ?");
$borrar_abono->bind_param('i', $id_abono);
$borrar_abono->execute();
$borrar_abono->close();



$response = array('estatus'=> true, 'mensaje'=>'Abono eliminado correctamente');
echo json_encode($response);