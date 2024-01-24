<?php

include '../conexion.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}
$id_movimiento = $_POST['id_movimiento'];
$qr = "SELECT COUNT(*) FROM movimientos WHERE id =?";
$stmt = $con->prepare($qr);
$stmt->bind_param('i', $id_movimiento);
$stmt->execute();
$stmt->bind_result($total_movimientos);
$stmt->fetch();
$stmt->close();

if($total_movimientos>0){
    $query = "SELECT * FROM movimientos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i',$id_movimiento);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $query = "SELECT * FROM historial_detalle_cambio WHERE id_movimiento = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i',$id_movimiento);
    $stmt->execute();
    $result_hdc = $stmt->get_result();
    $data_hdc =[]; 
    $data_partidas=[];
    while($fila = $result_hdc->fetch_assoc()){
        $id_llanta = $fila['id_llanta'];
        $data_llanta = traer_llanta($id_llanta, $con);
        $marca = $data_llanta[1];
        $descripcion = $data_llanta[0];
        $fila['marca'] = $marca;
        $fila['descripcion'] = $descripcion;
        $data_partidas[]= $fila;
    };

    $stmt->close();

    $query = "SELECT * FROM proveedores";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $result_pr = $stmt->get_result();
    $data_proveedores = $result_pr->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $query = "SELECT * FROM sucursal";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $result_sucu = $stmt->get_result();
    $data_sucursales = $result_sucu->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $query = "SELECT * FROM usuarios";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $result_u = $stmt->get_result();
    $data_usuarios = $result_u->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $resultado = array('estatus'=> true, 'mensaje'=>'Datos encontrados', 'datos_movimiento'=>$data, 'llantas_movimiento'=>$data_partidas, 'proveedores'=>$data_proveedores, 'usuarios'=>$data_usuarios, 'sucursales'=>$data_sucursales);
}else{
    $resultado = array('estatus'=>false, 'mensaje'=>'No existe un movimiento con ese id', 'datos_movimiento'=>[], 'llantas_movimiento'=>[], 'proveedores'=>[], 'usuarios'=>[], 'sucursales'=>[]);
}

echo json_encode($resultado);

function traer_llanta($id_llanta, $con){ 
    $descripcion = '';
    $marca ='';
    $qr = "SELECT descripcion, marca FROM llantas WHERE id =?";
    $stmt = $con->prepare($qr);
    $stmt->bind_param('i', $id_llanta);
    $stmt->execute();
    $stmt->bind_result($descripcion, $marca);
    $stmt->fetch();
    $stmt->close();

    return array($descripcion, $marca);
}