<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

date_default_timezone_set("America/Matamoros");
$hora = date("h:i a");
$fecha = date("Y-m-d"); 
$mes = date("m");
$año = date("Y");
$id_sucursal = $_POST['id_sucursal'];
$tipo_accion = $_POST['tipo_accion'];
function Arreglo_Get_Result( $Statement ) {
    $RESULT = array();
    $Statement->store_result();
    for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
        $Metadata = $Statement->result_metadata();
        $PARAMS = array();
        while ( $Field = $Metadata->fetch_field() ) {
            $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
        }
        call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
        $Statement->fetch();
    } 
    return $RESULT;
}

if($tipo_accion==1){
    montos_ventas_hoy($con, $id_sucursal, $fecha);
}else if($tipo_accion==2){
    lista_ventas_hoy($con, $id_sucursal, $fecha);
}else if($tipo_accion == 3){
    creditos_abiertos_hoy($con, $id_sucursal, $fecha);
}else if($tipo_accion == 4){
    abonos_realizados_hoy($con, $id_sucursal, $fecha);
}else if($tipo_accion == 5){
    $gastos = obtenerGastos($con, $fecha, $id_sucursal);
    echo json_encode($gastos);
}

function montos_ventas_hoy($con, $id_sucursal, $fecha){
    $venta_metodo_efectivo = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Efectivo");
    $venta_metodo_tarjeta = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Tarjeta");
    $venta_metodo_cheque = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Cheque");
    $venta_metodo_transferencia = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Transferencia");
    $venta_metodo_deposito = 0;//obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Deposito");
    $venta_metodo_sin_definir = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Por definir");

    $total_ingreso = $venta_metodo_efectivo + $venta_metodo_tarjeta + $venta_metodo_transferencia + $venta_metodo_cheque + $venta_metodo_sin_definir + $venta_metodo_deposito;
    
    $gastos = obtenerGastos($con, $fecha, $id_sucursal);
    $gasto_efectivo = 0;
    $gasto_tarjeta = 0;
    $gasto_transferencia = 0;
    $gasto_cheque = 0;
    $gasto_sin_definir = 0;
    foreach ($gastos['datos'] as $key => $value) {
        $forma_pago = $value['forma_pago'];
        $monto = floatval($value['monto']);
        switch ($forma_pago) {
            case 'Efectivo':
                $gasto_efectivo += $monto;
                break;
                case 'Tarjeta':
                    $gasto_tarjeta += $monto;
                    break;
                    case 'Transferencia':
                        $gasto_transferencia += $monto;
                        break;
                        case 'Cheque':
                            $gasto_cheque += $monto;
                            break;
                            case 'Sin definir':
                                $gasto_sin_definir += $monto;
                                break;
            
            default:
            $gasto_sin_definir += $monto;
                break;
        }
    }
    $total_gasto = $gasto_efectivo + $gasto_tarjeta + $gasto_transferencia + $gasto_cheque + $gasto_sin_definir;
    $response = array(
        'estatus'=>true,
        'post'=>$_POST,
        'data'=>array(
        'ingreso_efectivo' => $venta_metodo_efectivo,
        'ingreso_tarjeta' => $venta_metodo_tarjeta,
        'ingreso_transferencia' => $venta_metodo_transferencia,
        'ingreso_cheque' => $venta_metodo_cheque,
        /* 'ingreso_deposito' => $venta_metodo_deposito, */
        'ingreso_sin_definir' => $venta_metodo_sin_definir,
    
        'gasto_efectivo' => $gasto_efectivo,
        'gasto_tarjeta' => $gasto_tarjeta,
        'gasto_transferencia' => $gasto_transferencia,
        'gasto_cheque' => $gasto_cheque,
        /* 'gastos_deposito' => $gasto_deposito, */
        'gasto_sin_definir' => $gasto_sin_definir,
    
        'total_ingreso' => $total_ingreso,
        'total_gasto'=>$total_gasto)
    );
    
    
    echo json_encode($response);
}

//Funciones para obtener ganancias
function obtenerVentaTotal($con, $id_sucursal, $fecha, $tipo, $estatus){
    $total_venta = 0;
    $total_venta_apartados = 0;
    $total_venta_pedidos = 0;
    if($tipo == "Credito"){
        $consulta = "SELECT SUM(abono) FROM abonos WHERE id_sucursal=? AND fecha_corte = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta);
        $res->fetch();
        $res->close();
    }else{
        $consulta = "SELECT SUM(Total) FROM ventas WHERE id_sucursal=? AND fecha_corte = ? AND tipo = ? AND estatus = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
        $res->execute();
        $res->bind_result($total_venta);
        $res->fetch();
        $res->close();

        $consulta = "SELECT SUM(abono) FROM abonos_apartados WHERE id_sucursal=? AND fecha_corte = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta_apartados);
        $res->fetch();
        $res->close();

        $consulta = "SELECT SUM(abono) FROM abonos_pedidos WHERE id_sucursal=? AND fecha_corte = ? AND credito != 1";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta_pedidos);
        $res->fetch();
        $res->close();

        $total_venta = $total_venta + $total_venta_apartados + $total_venta_pedidos;
    }
   

    if($total_venta == "" || $total_venta ==null){
        $total_venta = 0;
    }
    return $total_venta;
}

function obtenerVentaMetodoPago($con, $id_sucursal, $fecha, $tipo, $metodo_pago){
    $total_venta = 0;
    $total_venta_apartados = 0;
    $total_venta_pedidos = 0;
    $total_venta_credito =0;
    switch($metodo_pago){
        case "Efectivo":
            $col = "pago_efectivo";
            break;
        case "Tarjeta":
            $col = "pago_tarjeta";
            break;
        case "Cheque":
            $col = "pago_cheque";
            break;
        case "Transferencia":
            $col = "pago_transferencia";
            break;
        case "Deposito":
                $col = "pago_deposito";
            break;
        case "Por definir":
            $col = "pago_sin_definir";
            break;

    }

        $estatus = 'Pagado';
        $consulta = "SELECT SUM($col) FROM ventas WHERE id_sucursal LIKE ? AND fecha_corte = ? AND tipo = ? AND estatus =?";
        $res = $con->prepare($consulta);
        $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
        $res->execute();
        $res->bind_result($total_venta);
        $res->fetch();
        $res->close();

        $consulta = "SELECT SUM($col) FROM abonos_apartados WHERE id_sucursal LIKE ? AND fecha_corte = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta_apartados);
        $res->fetch();
        $res->close();

        $consulta = "SELECT SUM($col) FROM abonos_pedidos WHERE id_sucursal LIKE ? AND fecha_corte = ? AND credito != 1";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta_pedidos);
        $res->fetch();
        $res->close();

        
        $consulta = "SELECT SUM($col) FROM abonos WHERE id_sucursal LIKE ? AND fecha_corte = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta_credito);
        $res->fetch();
        $res->close();
        
        $total_venta = $total_venta + $total_venta_apartados + $total_venta_pedidos + $total_venta_credito;
    
    
    if($total_venta == "" || $total_venta ==null){
        $total_venta = 0;
    }
    return $total_venta;
}


function abonos_realizados_hoy($con, $id_sucursal, $fecha)
{
    $traer_id = $con->prepare("SELECT * FROM `abonos` WHERE fecha_corte =? AND id_sucursal LIKE ?");
    $traer_id->bind_param('ss', $fecha, $id_sucursal);
    $traer_id->execute();
    $resultado = $traer_id->get_result();
    $traer_id->close();
    $total =0;
    if ($resultado->num_rows < 1) {
        $response = array('estatus' =>true, 'datos' =>[], 'mensaje' =>'No hay datos', 'total'=>0);
   
    } else {
        $id_venta=0;
        
        while ($fila = $resultado->fetch_assoc()) {
            $id_cliente="";
            $id_credito = $fila['id_credito'];
            $abono = $fila['abono'];
            $sucurs = $fila['sucursal'];
            $metodo_pago = $fila['metodo_pago'];
            $pago_efectivo = isset($fila['pago_efectivo']) ? $fila['pago_efectivo']  :0;
            $pago_tarjeta = isset($fila['pago_tarjeta']) ? $fila['pago_tarjeta']: 0;
            $pago_cheque = isset($fila['pago_cheque']) ? $fila['pago_cheque']: 0;
            $pago_transferencia = isset($fila['pago_transferencia']) ? $fila['pago_transferencia']: 0;
            $pago_por_definir = isset($fila['pago_sin_definir']) ? $fila['pago_sin_definir']: 0;
            $usuario = $fila['usuario'];
            $id_credito = $fila['id_credito'];
            $total += $fila['abono'];
            $traer_id = $con->prepare("SELECT id_Cliente, id_Venta FROM `creditos` WHERE id= ?");
            $traer_id->bind_param('s', $id_credito);
            $traer_id->execute();
            $traer_id->bind_result($id_cliente, $id_venta);
            $traer_id->fetch();
            $traer_id->close();

            $cliente="";
            $traer_id = $con->prepare("SELECT Nombre_Cliente FROM `clientes` WHERE id= ?");
            $traer_id->bind_param('s', $id_cliente);
            $traer_id->execute();
            $traer_id->bind_result($cliente);
            $traer_id->fetch();
            $traer_id->close();

            $arreglo[] = array("id_credito"=>$id_credito, 
            'metodo'=>$metodo_pago,
            'pago_efectivo'=>$pago_efectivo,
            'pago_tarjeta'=>$pago_tarjeta,
            'pago_cheque'=>$pago_cheque,
            'pago_transferencia'=>$pago_transferencia,
            'pago_sin_definir'=>$pago_por_definir,
            'cliente'=>$cliente, 
            'id_venta'=>$id_venta,
            'abono'=> $abono, 'fecha'=> $fecha, 'sucursal'=> $sucurs, 'usuario'=>$usuario);
        
        };
        $response = array('estatus' =>true, 'datos' =>$arreglo, 'mensaje' =>'No hay datos', 'total'=>$total, 'post'=>$_POST);
    }
    echo json_encode($response);
}

function obtenerGastos($con, $fecha, $id_sucursal){
    $total_gastos = 0;
    $resultado= [];
    $total =0;
    $query = "SELECT COUNT(*) FROM vista_gastos WHERE fecha = ? AND id_sucursal LIKE ?";
    $res = $con->prepare($query);
    $res->bind_param('ss', $fecha, $id_sucursal);
    $res->execute();
    $res->bind_result($total_gastos);
    $res->fetch();
    $res->close();
    $data =array();
    if($total_gastos>0){
        $select = "SELECT * FROM vista_gastos WHERE fecha = ? AND id_sucursal LIKE ?";
        $res = $con->prepare($select);
        $res->bind_param('ss', $fecha, $id_sucursal);
        $res->execute();
        $resultado = $res->get_result();
        $res->close();
        while ($fila = $resultado->fetch_assoc()) {
            $data[] = $fila;
            $total += $fila['monto'];
        }
        $mensj = 'Se encontrarón gastos';
    }else{
        $mensj = 'No se encontrarón gastos';
    }
    $response = array('estatus'=>true, 'datos'=>$data, 'total'=>$total, 'mensaje'=>$mensj, 'post'=>$_POST);
    return $response;
};


function lista_ventas_hoy($con, $id_sucursal, $fecha){
    $total_ventas =0;
    $tipo = 'Credito';
    $estatus = 'Pagado';
  
    $consulta = "SELECT COUNT(*) FROM ventas WHERE id_sucursal LIKE ? AND fecha_corte = ? AND tipo != ? AND estatus = ?";
    $res = $con->prepare($consulta);
    $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
    $res->execute();
    $res->bind_result($total_ventas);
    $res->fetch();
    $res->close();
    $total = 0;
    if($total_ventas>0){
        $consulta = "SELECT * FROM vista_ventas WHERE id_sucursal LIKE ? AND fecha_corte = ? AND tipo != ? AND estatus = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
        $res->execute();
        $datos_ = $res->get_result();
        $res->free_result();
        $res->close();
        foreach ($datos_ as $key => $value) {
            $datos[]= $value;
            $total += $value['Total'];
        }
        $mensaje = 'Se encontrarón resultados';
    }else{
        $mensaje = 'No se han realizado ventas';

        $datos = array();
    }
    $response = array('estatus' =>true, 'datos' =>$datos, 'mensaje' =>$mensaje, 'total'=>$total, 'post'=>$_POST);
    echo json_encode($response);

}
function creditos_abiertos_hoy($con, $id_sucursal, $fecha){
    $total_ventas =0;
    $tipo = 'Credito';
    $estatus = 'Cancelado';
    $total =0;
    /* print_r($fecha);
    print_r($id_sucursal); */
    $consulta = "SELECT COUNT(*) FROM ventas WHERE id_sucursal LIKE ? AND fecha_corte = ? AND tipo = ? AND estatus !=?";
    $res = $con->prepare($consulta);
    $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
    $res->execute();
    $res->bind_result($total_ventas);
    $res->fetch();
    $res->close();

    if($total_ventas>0){
        $consulta = "SELECT * FROM vista_ventas WHERE id_sucursal LIKE ? AND fecha_corte = ? AND tipo = ? AND estatus != ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
        $res->execute();
        $datos_ = $res->get_result();
        $res->free_result();
        $res->close();
        foreach ($datos_ as $key => $value) {
            $datos[]= $value;
            $total += $value['Total'];
        }
        $mensaje = 'Se encontrarón resultados';
    }else{
        $mensaje = 'No se han realizado ventas';

        $datos = array();
    }
    $response = array('estatus' =>true, 'datos' =>$datos, 'mensaje' =>$mensaje, 'total' =>$total, 'post'=>$_POST);
    echo json_encode($response);
}
