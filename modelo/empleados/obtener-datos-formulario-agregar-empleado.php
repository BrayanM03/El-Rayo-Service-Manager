<?php
include '../conexion.php';
require_once '../sucursales/Sucursal.php';
require_once '../empleados/Empleado.php';
require_once '../configuraciones/configuracion_usuarios/Usuario.php';
include '../helpers/response_helper.php';
$con= $conectando->conexion(); 
$sucursal = new Sucursal($con);
$empleado= new Empleado($con);
$usuario= new Usuario($con);

$sucursal_response = $sucursal->obtenerSucursales();
$puestos_response = $empleado->obtenerPuestos();
$usuario_response = $usuario->obtenerUsuarios();
$response = array(
'sucursales'=>$sucursal_response['data'],
'usuarios'=>$usuario_response['data'],
'puestos'=>$puestos_response['data']);

responder(true, 'Se encontrarón datos', 'success', $response);

?>