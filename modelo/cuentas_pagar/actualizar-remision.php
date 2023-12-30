<?php

include '../conexion.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}

$proveedor_actualizado = $_POST['proveedor_actualizado'];
$factura_actualizado = $_POST['factura_actualizado'];
$usuario_actualizado = $_POST['usuario_actualizado'];
$estado_actualizado = $_POST['estado_actualizado'];
$estatus_actualizado = $_POST['estatus_actualizado'];
$importe_total_actualizado = $_POST['importe_total_actualizado'];
$id_movimiento = $_POST['id_movimiento'];
$aprobacion_importe = $_POST['aprobacion_importe'];
$id_sucursal = $_POST['id_sucursal'];
$aprobacion_actualizar_stock = $_POST['aprobacion_actualizar_stock'];
$necesita_aprobacion_stock = false;
$necesita_aprobacion_importes = false;

$qr = "SELECT COUNT(*) FROM movimientos WHERE id =?";
$stmt = $con->prepare($qr);
$stmt->bind_param('i', $id_movimiento);
$stmt->execute();
$stmt->bind_result($total_movimientos);
$stmt->fetch();
$stmt->close();

if($total_movimientos>0){
    $qr = "SELECT sucursal FROM movimientos WHERE id =?";
    $stmt = $con->prepare($qr);
    $stmt->bind_param('i', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($id_sucursal_actual);
    $stmt->fetch();
    $stmt->close();

    if($id_sucursal != $id_sucursal_actual && $aprobacion_actualizar_stock=='unknowns'){
        //Acabo de desactivar este codigo por cambios en el modulo
        /* $mensaje = 'Seleccionaste una sucursal distinta, puedes actualizar el stock de ambos inventarios
            <br> ¿quieres cuadrar y actualizar el stock de ambas sucursales';
            $estatus = false;
            $icon = 'warning';
            $necesita_aprobacion_stock = true;
            $response = array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'icon'=>$icon, 'necesita_aprobacion_stock'=>$necesita_aprobacion_stock, 'necesita_aprobacion_importes'=>$necesita_aprobacion_importes);
            echo json_encode($response);
            die(); */
        }else if($id_sucursal != $id_sucursal_actual && $aprobacion_actualizar_stock=='true'){

         //Cuadrando inventario   

        $up = "UPDATE historial_detalle_cambio SET id_destino = ? WHERE id_movimiento = ?";
        $stmt = $con->prepare($up);
        $stmt->bind_param('ss',$id_sucursal, $id_movimiento);
        $stmt->execute();
        $stmt->close();
        $necesita_aprobacion_stock = false;
    }

    $query = "SELECT * FROM historial_detalle_cambio WHERE id_movimiento = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i',$id_movimiento);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $suma_importe_actual = 0;
        foreach ($data as $key => $value) {
            $suma_importe_actual += $value['importe'];
            
        }

        if($importe_total_actualizado != $suma_importe_actual && $aprobacion_importe == 'unknown'){
            $mensaje = 'Las cantidades del importe actualizado y la suma de las partidas no son iguales. 
            Importe actualizado : <b>$'. $importe_total_actualizado  .'</b> Suma de las partidas <b>'. $suma_importe_actual .'</b>
            <br> ¿Deseas actualizar el importe de todos modos?';
            $estatus = false;
            $icon = 'warning';
            $necesita_aprobacion_importes = true;
        }else if($importe_total_actualizado != $suma_importe_actual && $aprobacion_importe== 'true'){
            $update = 'UPDATE movimientos SET sucursal = ?, proveedor_id = ?, folio_factura = ?, id_usuario = ?, estado_factura = ?, estatus = ?, total = ? WHERE id = ?';
            $stmt = $con->prepare($update);
            $stmt->bind_param('ssssssss', $id_sucursal, $proveedor_actualizado, $factura_actualizado, $usuario_actualizado, $estado_actualizado, $estatus_actualizado, $importe_total_actualizado, $id_movimiento);
            $stmt->execute();
            $stmt->close();
            $estatus = true;
            $icon = 'success';
            $mensaje = 'Actualizado con exito';
        }else if($importe_total_actualizado == $suma_importe_actual && $aprobacion_importe == 'unknown'){
            $update = 'UPDATE movimientos SET sucursal = ?, proveedor_id = ?, folio_factura = ?, id_usuario = ?, estado_factura = ?, estatus = ?, total = ? WHERE id = ?';
            $stmt = $con->prepare($update);
            $stmt->bind_param('ssssssss', $id_sucursal, $proveedor_actualizado, $factura_actualizado, $usuario_actualizado, $estado_actualizado, $estatus_actualizado, $importe_total_actualizado, $id_movimiento);
            $stmt->execute();
            $stmt->close();
            $estatus = true;
            $icon = 'success';
            $mensaje = 'Actualizado con exito';

        }else{
            $update = 'UPDATE movimientos SET sucursal = ?, proveedor_id = ?, folio_factura = ?, id_usuario = ?, estado_factura = ?, estatus = ? WHERE id = ?';
            $stmt = $con->prepare($update);
            $stmt->bind_param('sssssss', $id_sucursal, $proveedor_actualizado, $factura_actualizado, $usuario_actualizado, $estado_actualizado, $estatus_actualizado, $id_movimiento);
            $stmt->execute();
            $stmt->close();
            $estatus = true;
            $mensaje = 'Actualizado con exito, no se modificarón los montos';
            $icon = 'success';
        }

}else{
    $estatus = false;
    $icon = 'error';
    $mensaje = 'No existe una movimiento con ese ID';
}

$response = array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'post'=>$_POST, 'icon'=>$icon, 'necesita_aprobacion_stock'=>$necesita_aprobacion_stock, 'necesita_aprobacion_importes'=>$necesita_aprobacion_importes);
echo json_encode($response);
?>