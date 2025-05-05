<?php

include '../conexion.php';
include 'Empleado.php';
session_start();
include '../helpers/response_helper.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}

$empleado = $_POST['empleado'];
$tipo = $_POST['tipo']; 
$categoria = $_POST['categoria'];

$fecha_inicio = $_POST['fecha-inicio'];
$fecha_final = $_POST['fecha-final'];
$periocidad = $_POST['periocidad'];
$monto = $_POST['monto'];
$descripcion = $_POST['descripcion'];

 $insert = 'INSERT INTO incidencias(concepto, id_categoria, id_empleado, fecha_inicio, fecha_final, monto,
 tipo, estatus, periocidad, id_usuario_registro) VALUES(?,?,?,?,?,?,?,1,?,?)';
 $stmt = $con->prepare($insert);
 $stmt->bind_param('sssssssss',$descripcion, $categoria, $empleado, $fecha_inicio, $fecha_final, $monto, $tipo, $periocidad,
$_SESSION['id_usuario']);
$stmt->execute();
$id_incidencia = $stmt->insert_id;
$stmt->close();

if($categoria==5){
    $monto_prestamo = floatval($_POST['monto-prestamo']);
    $restante = $monto_prestamo;
   /*  $monto_periodo = floatval($_POST['monto-periodo']); */

    $update = 'UPDATE incidencias SET monto_prestamo =?, restante = ?/* , monto_periodo =? */ WHERE id = ?';
    $stmt = $con->prepare($update);
    $stmt->bind_param('ddi',$monto_prestamo, $restante, $id_incidencia);
    $stmt->execute();
    $stmt->close();
}

responder(true, 'Incidencia ingresada con exito', 'success',[]);
