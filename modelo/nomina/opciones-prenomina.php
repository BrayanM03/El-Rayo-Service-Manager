<?php

include '../conexion.php';
include '../nomina/Nomina.php';

$con= $conectando->conexion(); 
$nomina = new Nomina($con);

if($_POST['tipo']== 'carga'){
    $prenomina_empleados = $nomina->obtenerPrenominaGuardada();
    echo json_encode($prenomina_empleados);
}

if($_POST['tipo'] == 'edicion'){
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $tipo_incd = $_POST['tipo_incidencia'];
    $id = $_POST['id'];
    $id_prenomina = $_POST['id_prenomina'];

    $prenomina_empleados = $nomina->actualizarIncidenciaPrenomina($id, $id_prenomina, $descripcion, $monto, $tipo_incd);
    echo json_encode($prenomina_empleados);
}

if($_POST['tipo']== 'limpiar'){
    $prenomina_empleados = $nomina->limpiarPrenomina();
    echo json_encode($prenomina_empleados);
}
