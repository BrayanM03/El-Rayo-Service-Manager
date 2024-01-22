<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
 
if (!$con) {
    echo "maaaaal";
}
$fecha = date("Y-m-d");
$hora = date("h:i a");

if(isset($_POST)){


    $id_detalle = $_POST["id_detalle"];
    //$sucursal_id = $_SESSION["id_sucursal"];
    //$id_usuario = $_POST["id_usuario"];
    $tipo = $_POST["tipo"];
    $estatus_movimiento = 'Pendiente';
    //Comprobar que no exista un movimiento ya hecho para este requerimiento
    $traercantidad = "SELECT r.id, r.id_movimiento FROM detalle_requerimientos dr INNER JOIN requerimientos r ON dr.id_requerimiento = r.id WHERE dr.id = ?";
    $result = $con->prepare($traercantidad);
    $result->bind_param('s', $id_detalle);
    $result->execute();
    $result->bind_result($id_requerimiento, $id_movimiento);
    $result->fetch();
    $result->close();
    if($tipo ==4){

        if(isset($id_movimiento)){
            $traercantidad = "SELECT COUNT(*) FROM historial_detalle_cambio WHERE id_movimiento = ?";
            $result = $con->prepare($traercantidad);
            $result->bind_param('s', $id_movimiento);
            $result->execute();
            $result->bind_result($total_detalles);
            $result->fetch();
            $result->close();
            /* print_r($total_detalles);
            die(); */
            if($total_detalles == 1){
                $del = "DELETE FROM movimientos WHERE id = ?";
                $stmt = $con->prepare($del);
                $stmt->bind_param('s', $id_movimiento);
                $stmt->execute();
                $stmt->close();
                $upd = "UPDATE requerimientos SET estatus = 1, id_movimiento = null WHERE id = ?";
                $stmt = $con->prepare($upd);
                $stmt->bind_param('s', $id_requerimiento);
                $stmt->execute();
                $stmt->close();
            }else{
                $upd = "UPDATE requerimientos SET estatus = 5 WHERE id = ?";
                $stmt = $con->prepare($upd);
                $stmt->bind_param('s', $id_requerimiento);
                $stmt->execute();
                $stmt->close();
            }

            $traercantidad = "SELECT id_llanta, id_ubicacion, id_destino, cantidad FROM detalle_requerimientos WHERE id = ?";
            $result = $con->prepare($traercantidad);
            $result->bind_param('s', $id_detalle);
            $result->execute();
            $result->bind_result($id_llanta, $id_ubicacion, $id_destino, $cantidad);
            $result->fetch();
            $result->close();

            $res_mov = moverLlanta($id_llanta, $id_destino, $id_ubicacion, $cantidad, $con);

            //Borrando los detalle
            $del = "DELETE FROM historial_detalle_cambio WHERE id_llanta = ? AND id_movimiento = ?";
            $stmt = $con->prepare($del);
            $stmt->bind_param('ss', $id_llanta, $id_movimiento);
            $stmt->execute();
            $stmt->close();
            $upd = "UPDATE detalle_requerimientos SET estatus = 1 WHERE id = ?";
            $stmt = $con->prepare($upd);
            $stmt->bind_param('s', $id_detalle);
            $stmt->execute();
            $stmt->close();

            echo json_encode($res_mov);
        }
    }else if($tipo == 3){
        $id_detalle = $_POST["id_detalle"];
        $traercantidad = "SELECT COUNT(*) FROM detalle_requerimientos WHERE id = ?";
            $result = $con->prepare($traercantidad);
            $result->bind_param('s', $id_detalle);
            $result->execute();
            $result->bind_result($total_detalles);
            $result->fetch();
            $result->close();
    
        if($total_detalles > 0){
            $upd = "UPDATE detalle_requerimientos SET estatus = 1 WHERE id = ?";
            $stmt = $con->prepare($upd);
            $stmt->bind_param('s', $id_detalle);
            $stmt->execute();
            $stmt->close();
            $res = array('estatus'=>true, 'mensaje'=>'Cambio deshecho, estatus actualizado correctamente');
        }  else{
            $res = array('estatus'=>false, 'mensaje'=>'No existe el id del registro');
        }

        $traercantidad = "SELECT COUNT(*) FROM detalle_requerimientos WHERE id_requerimiento = ?";
        $result = $con->prepare($traercantidad);
        $result->bind_param('s', $id_requerimiento);
        $result->execute();
        $result->bind_result($total_detalles_actuales);
        $result->fetch();
        $result->close();

        if($total_detalles_actuales == 1){
            $upd = "UPDATE requerimientos SET estatus = 1 WHERE id = ?";
            $stmt = $con->prepare($upd);
            $stmt->bind_param('s', $id_requerimiento);
            $stmt->execute();
            $stmt->close();
        }else{
            $update = "UPDATE requerimientos SET estatus = 5 WHERE id = ?";
            $respp = $con->prepare($update);
            $respp->bind_param('s', $id_requerimiento);
            $respp->execute();
            $error = $respp->error;
            $respp->close();
        }

        
        $res['id_requerimiento'] = $id_requerimiento;
        echo json_encode($res);
    }
    

    
}

function moverLlanta($id_llanta, $id_destino, $id_ubicacion, $cantidad, $con){
    $stock_destino_actual =0;
    $stock_ubicacion_actual=0;
    $comprobar = "SELECT Stock FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
    $result = $con->prepare($comprobar);
    $result->bind_param('ss', $id_destino, $id_llanta);
    $result->execute();
    $result->bind_result($stock_destino_actual);
    $result->fetch();
    $result->close();
    $comprobar = "SELECT Stock FROM inventario WHERE id_sucursal = ? AND id_Llanta = ?";
    $result = $con->prepare($comprobar);
    $result->bind_param('ss', $id_ubicacion, $id_llanta);
    $result->execute();
    $result->bind_result($stock_ubicacion_actual);
    $result->fetch();
    $result->close();
    $nuevo_stock_destino = $stock_destino_actual - $cantidad;
    $nuevo_stock_ubicacion = $stock_ubicacion_actual + $cantidad;
    /* print_r($nuevo_stock_destino. '-');
    print_r($nuevo_stock_ubicacion);
    die(); */
   //Actualizando sucursal destino
   if($nuevo_stock_destino < 0 || $nuevo_stock_ubicacion <0){
    $re = array('estatus'=>false, 'mensaje'=> 'El stock resultante resultará menor a 0 en algun inventario, revisalo por favor');
   }else{
    $update = "UPDATE inventario SET Stock = ? WHERE id_sucursal = ? AND id_Llanta = ?";
    $result = $con->prepare($update);
    $result->bind_param('sss', $nuevo_stock_destino, $id_destino, $id_llanta);
    $result->execute();
    $result->close();
 
    //Actualizando sucursal remitente
    $update = "UPDATE inventario SET Stock = ? WHERE id_sucursal = ? AND id_Llanta = ?";
    $result = $con->prepare($update);
    $result->bind_param('sss', $nuevo_stock_ubicacion, $id_ubicacion, $id_llanta);
    $result->execute();
    $result->close();
    $re = array('estatus'=>true, 'mensaje'=> 'Cambio deshecho, inventario actualizado');

    return $re;
   }
   
}

?>