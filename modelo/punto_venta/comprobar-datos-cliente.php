<?php
    session_start();
    include '../conexion.php';
require_once '../clientes/Cliente.php';

    $con= $conectando->conexion(); 
    
date_default_timezone_set("America/Matamoros");
$id_cliente = $_POST['id_cliente'];
$catalogo = new Clientes($con);
$creditos_vencidos_arreglo = $catalogo->traer_creditos_vencidos($id_cliente);
echo json_encode($creditos_vencidos_arreglo);
/* if(!$producto_arreglo['estatus']){
    responder(false, $producto_arreglo['mensaje'], 'danger', [], true);
} */