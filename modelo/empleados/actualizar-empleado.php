<?php
include '../conexion.php';
require_once '../empleados/Empleado.php';
include '../helpers/response_helper.php';

$con= $conectando->conexion(); 
$empleado= new Empleado($con);
$documentos_response = $empleado->obtenerDatosEmpleado($_POST['id_empleado']);
$data = $documentos_response['data'];

if($data['foto_perfil']==1){
    $ext = $data['extension'];
}else{
    $ext=0;
}
$actualizar_dg_response = $empleado->actualizarDatosGenerales($_POST, $_FILES, $ext);

echo json_encode($actualizar_dg_response);

?>