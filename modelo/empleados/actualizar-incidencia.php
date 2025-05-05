<?php
include '../conexion.php';
require_once '../empleados/Empleado.php';
include '../helpers/response_helper.php';
$con= $conectando->conexion(); 

$tipo = $_POST['tipo_peticion'];
$empleado= new Empleado($con);
if($tipo=='traer'){
    $id_incidencia = $_POST['id'];
    $incidencia = $empleado->obtenerIncidencia($id_incidencia);
    $response = array(
        'data_incidencia'=>$incidencia);
        $mensaje = 'Se encontrarón datos';
}else if($tipo =='actualizar'){
    $response = $empleado->actualizarIncidencia($_POST);
    $mensaje = 'Remisión actualizada con exito';

}

responder(true, $mensaje, 'success', $response);

?>