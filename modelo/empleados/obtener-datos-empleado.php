<?php
include '../conexion.php';
require_once '../empleados/Empleado.php';
include '../helpers/response_helper.php';
$con= $conectando->conexion(); 

$id_empleado = $_POST['id_empleado'];
$empleado= new Empleado($con);
$documentos_response = $empleado->obtenerDocumentosXEmpleado($id_empleado);
$response = array(
    'documentos'=>$documentos_response['data']);
    
    responder(true, 'Se encontrarón datos', 'success', $response);