<?php

include '../conexion.php';
include '../empleados/Empleado.php';
include '../nomina/Nomina.php';
include '../helpers/response_helper.php';
$con= $conectando->conexion(); 
$empleado = new Empleado($con);
$nomina = new Nomina($con);
$sucursales = isset($_POST['sucursales']) ? $_POST['sucursales'] : false;
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_final = $_POST['fecha_final'];
$semana = $_POST['semana'];
if($sucursales){
    $empleados = $empleado->obtenerEmpleados($sucursales);
    $prenomina_empleados = $nomina->obtenerPrenomina($fecha_inicio, $fecha_final, $semana, $sucursales, $empleados['data']);
    echo json_encode($prenomina_empleados);
}else{
    responder(false, 'Selecciona una o mas sucursales', []);
}



