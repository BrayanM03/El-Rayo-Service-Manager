<?php 
include '../conexion.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}
include '../helpers/response_helper.php';


$qr = "SELECT COUNT(*) FROM proveedores WHERE activo = 1";
$stmt = $con->prepare($qr);
$stmt->execute();
$stmt->bind_result($total_prov);
$stmt->fetch();
$stmt->close();

$data =[]; 
if($total_prov > 0){
    $query = "SELECT * FROM proveedores WHERE activo = 1";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    while($fila = $result->fetch_assoc()){
        $data[] =  $fila;
    }


    $mensaje = 'Se encontraron datos';
    $estatus = true;
}else{
    $mensaje = 'No se encontraron datos';
    $estatus = false;
}

responder($estatus, $mensaje, '', $data, false, true) 


?>