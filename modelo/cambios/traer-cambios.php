<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();
setlocale(LC_MONETARY, 'en_US');
if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$id_usuario = $_POST['id_usuario'];


$queryChange = "SELECT COUNT(*) FROM detalle_cambio WHERE id_usuario =?";
$resps = $con->prepare($queryChange);
$resps->bind_param("i", $id_usuario);
$resps->execute();
$resps->bind_result($total_cambios);
$resps->fetch();
$resps->close();

if ($total_cambios > 0) {
    $querySuc = "SELECT * FROM detalle_cambio WHERE id_usuario ='$id_usuario'";
    $respon = mysqli_query($con, $querySuc);

    while ($rows = $respon->fetch_assoc()) {
        $id = $rows['id'];
        $id_llanta = $rows['id_llanta'];
        $id_ubicacion = $rows['id_ubicacion'];
        $id_destino = $rows['id_destino'];
        $cantidad = $rows['cantidad'];
        $costo =number_format($rows['costo']);
        $importe = $rows['importe'];
        $importe_ft = number_format($importe);
        

        $queryDesc = "SELECT Descripcion, precio_Inicial, precio_Venta, precio_Mayoreo FROM llantas WHERE id =?";
        $resps = $con->prepare($queryDesc);
        $resps->bind_param("i", $id_llanta);
        $resps->execute();
        $resps->bind_result($descripcion, $costo_, $precio, $mayoreo);
        $resps->fetch();
        $resps->close();


        $queryDesc = "SELECT nombre FROM sucursal WHERE id =?";
        $resps = $con->prepare($queryDesc);
        $resps->bind_param("i", $id_ubicacion);
        $resps->execute();
        $resps->bind_result($sucursal_remitente);
        $resps->fetch();
        $resps->close();


        $queryDesc = "SELECT nombre FROM sucursal WHERE id =?";
        $resps = $con->prepare($queryDesc);
        $resps->bind_param("i", $id_destino);
        $resps->execute();
        $resps->bind_result($sucursal_destino);
        $resps->fetch();
        $resps->close();

        $data[] = array('id'=>$id, 'id_llanta'=> $id_llanta, 
                        'descripcion'=> $descripcion, 
                        'id_ubicacion'=> $id_ubicacion,
                        'sucursal_remitente'=> $sucursal_remitente,
                        'id_destino'=> $id_destino, 
                        'sucursal_destino' => $sucursal_destino,
                        'cantidad'=> $cantidad,
                        'costo'=>$costo,
                        'importe'=> $importe,
                        'importe_ft'=> $importe_ft);
    }

   echo json_encode($data, JSON_UNESCAPED_UNICODE);
}else{
    $data = array("id"=>false, "mensaje"=> "Sin datos");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}


