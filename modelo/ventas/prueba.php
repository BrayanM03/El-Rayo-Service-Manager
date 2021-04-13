<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion();


$id_venta = 99;


$detalle = $con->prepare("SELECT detalle_venta.Cantidad,llantas.Descripcion, llantas.Marca, detalle_venta.precio_Unitario, detalle_venta.Importe FROM detalle_venta INNER JOIN llantas ON detalle_venta.id_llanta = llantas.id WHERE id_Venta = ?");
$detalle->bind_param('i', $id_venta);
$detalle->execute();
$resultado = $detalle->get_result();



/* obtener los valores */
while($fila = $resultado->fetch_assoc()) {
  
    echo $fila["Cantidad"];
    echo $fila["Descripcion"];
    echo $fila["Marca"];
    echo $fila["precio_Unitario"];
    echo $fila["Importe"];


}

?>