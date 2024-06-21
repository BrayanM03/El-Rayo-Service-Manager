<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();
setlocale(LC_MONETARY, 'en_US');
if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$id_req = $_POST['id_requerimiento'];

$queryChange = "SELECT COUNT(*) FROM requerimientos WHERE id =?";
$resps = $con->prepare($queryChange);
$resps->bind_param("i", $id_req);
$resps->execute();
$resps->bind_result($total_req);
$resps->fetch();
$resps->close();

if($total_req > 0){
    $queryChange = "SELECT CONCAT(u.nombre, ' ', u.apellidos) as usuario, s.nombre as sucursal, r.fecha_inicio, r.hora_inicio, r.id_movimiento FROM requerimientos r 
     INNER JOIN usuarios u ON r.id_usuario = u.id INNER JOIN sucursal s ON r.id_sucursal = s.id
     WHERE r.id =?";
    $resps = $con->prepare($queryChange);
    $resps->bind_param("i", $id_req);
    $resps->execute();
    $resps->bind_result($nombre_usuario, $sucursal_usuario, $fecha_inicio, $hora_inicio, $id_movimiento);
    $resps->fetch();
    $resps->close();

    $queryChange = "SELECT COUNT(*) FROM detalle_requerimientos WHERE id_requerimiento =?";
    $resps = $con->prepare($queryChange);
    $resps->bind_param("i", $id_req);
    $resps->execute();
    $resps->bind_result($total_cambios);
    $resps->fetch();
    $resps->close();
    
    if ($total_cambios > 0) {
        $querySuc = "SELECT * FROM detalle_requerimientos WHERE id_requerimiento ='$id_req'";
        $respon = mysqli_query($con, $querySuc);
        $estatus_realizar_movimiento = false;
        $ids_movimientos=array();
        while ($rows = $respon->fetch_assoc()) {
            $id = $rows['id'];
            $id_llanta = $rows['id_llanta'];
            $id_ubicacion = $rows['id_ubicacion'];
            $id_destino = $rows['id_destino'];
            $cantidad = $rows['cantidad'];    
            $estatus = $rows['estatus'];
            if(isset($rows['id_movimiento'])){
                $ids_movimientos[]= $rows['id_movimiento'];
            }
            if($estatus != 9 && $estatus != 2 && $estatus != 4 && $estatus != 5 && $estatus != 6 && $estatus != 7){
                $estatus_realizar_movimiento = true;
            }
            $id_detalle_movimiento = $rows['id_movimiento'];
    
            $queryDesc = "SELECT Descripcion, Marca, precio_Inicial, precio_Venta, precio_Mayoreo FROM llantas WHERE id =?";
            $resps = $con->prepare($queryDesc);
            $resps->bind_param("i", $id_llanta);
            $resps->execute();
            $resps->bind_result($descripcion, $marca, $costo_, $precio, $mayoreo);
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
            $data['nombre_usuario'] = $nombre_usuario;
            $data['ids_movimientos'] = $ids_movimientos;
            $data['data'][] = array('id'=>$id, 'id_llanta'=> $id_llanta, 
                            'descripcion'=> $descripcion, 
                            'id_ubicacion'=> $id_ubicacion,
                            'sucursal_remitente'=> $sucursal_remitente,
                            'id_destino'=> $id_destino, 
                            'marca' => $marca,
                            'sucursal_destino' => $sucursal_destino,
                            'cantidad'=> $cantidad,
                            'estatus'=> $estatus,
                            'id_movimiento'=> $id_detalle_movimiento);
            $data['estatus_realizar_movimiento'] = $estatus_realizar_movimiento;            
        }
        $data['estatus']=true;
        $data['sucursal']=$sucursal_usuario;
        $data['hora'] = $hora_inicio;
        $data['fecha']= $fecha_inicio;
        $data['id_movimiento']= $id_movimiento;
       echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }else{
        $data = array('data'=>[], 'estatus'=>false, 'mensaje'=> 'No hay un detalle_requerimiento con ese id');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}else{
    $data = array('data'=>[], 'estatus'=>false, 'mensaje'=> 'No hay un requerimiento con ese id');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
}




