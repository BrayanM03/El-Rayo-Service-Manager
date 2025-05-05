<?php
include '../conexion.php';
require_once '../sucursales/Sucursal.php';
require_once '../empleados/Empleado.php';
include '../helpers/response_helper.php';
$con= $conectando->conexion(); 
$sucursal = new Sucursal($con);
$empleado= new Empleado($con);
$data= array('empleados'=>$empleado->obtenerEmpleados(), 'sucursales'=>$sucursal->obtenerSucursales(), 'puestos'=>$empleado->obtenerPuestos());


responder(true, 'Se encontrarón datos', 'success', $data);

?>