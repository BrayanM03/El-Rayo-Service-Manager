<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php"); 
}


//insertar utilidad
include '../ventas/insertar_utilidad.php';
include '../creditos/obtener-utilidad-abono.php';

if(isset($_POST)) { 

    date_default_timezone_set("America/Matamoros");
    $hora = date("h:i a");

    //Variables para el historial venta
    $fecha = date("Y-m-d");
    $id_apartado = $_POST['id_apartado'];
    $pago_efectivo = 0;
    $pago_transferencia = 0;
    $pago_tarjeta = 0;
    $pago_cheque = 0;
    $pago_deposito = 0;
    $pago_sin_definir = 0;
    $monto_total_abono = 0;
 
    foreach ($_POST['metodos_pago'] as $key => $value) {
        $metodo_id = isset($value['id_metodo']) ? $value['id_metodo'] : $key;
        switch ($metodo_id) {
            case 0:
                $pago_efectivo = $value['monto'];
                break;

            case 1:
                $pago_tarjeta = $value['monto'];
                break;

            case 2:
                $pago_transferencia = $value['monto'];

                break;

            case 3:
                $pago_cheque = $value['monto'];
                break;

            case 5:
                $pago_deposito = $value['monto'];
                break;
            case 4:
                $pago_sin_definir = $value['monto'];
                break;

            default:
                break;
        }
        $monto_pago = $value['monto'];
        $monto_total_abono += $value['monto'];
        $metodo_pago = $value['metodo'];
        $desc_metodos = '';
        if($key != count($_POST["metodos_pago"]) - 1) {
            // Este código se ejecutará para todos menos el último
            $desc_metodos .= $metodo_pago . ", ";
        } else {
            $desc_metodos .= $metodo_pago . ". ";
        }
    }

    $vendedor_id = $_SESSION['id_usuario'];

    //Insertando la venta
    $fecha_actual = date('Y-m-d');
   
 

    $ID = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
    $ID->bind_param('i', $vendedor_id);
    $ID->execute();
    $ID->bind_result($vendedor_name, $vendedor_apellido);
    $ID->fetch();
    $ID->close();

    $vendedor_usuario = $vendedor_name . " " . $vendedor_apellido;
   
 
    $select = "SELECT id_sucursal, id_usuario, id_cliente, sucursal, pago_efectivo, 
    pago_tarjeta, 
    pago_transferencia, 
    pago_cheque, 
    pago_deposito, 
    pago_sin_definir, total, metodo_pago, comentario FROM apartados WHERE id = ?";
    $re = $con->prepare($select);
    $re->bind_param('i', $id_apartado);
    $re->execute();
    $re->bind_result($id_sucursal, $id_usuario, $id_cliente, $sucursal, $actual_pago_efectivo, $actual_pago_tarjeta, $actual_pago_transferencia, $actual_pago_cheque, $actual_pago_deposito,
    $actual_pago_sin_definir, $importe_total, $metodo_pago, $comentario);
    $re->fetch();
    $re->close();

    //Revisar si no hay errores en los montos
    $select = "SELECT SUM(abono) FROM abonos_apartados WHERE id_apartado = ?";
    $re = $con->prepare($select);
    $re->bind_param('i', $id_apartado);
    $re->execute();
    $re->bind_result($suma_abonos);
    $re->fetch();
    $re->close();

    $nueva_suma_abonos = $suma_abonos + $monto_total_abono;
    $nuevo_restante = $importe_total - $nueva_suma_abonos;
    $nuevo_pago_efectivo = $pago_efectivo + $actual_pago_efectivo;
    $nuevo_pago_tarjeta = $pago_tarjeta + $actual_pago_tarjeta;
    $nuevo_pago_transferencia = $pago_transferencia + $actual_pago_transferencia;
    $nuevo_pago_cheque = $pago_cheque + $actual_pago_cheque;
    $nuevo_pago_deposito = $pago_deposito + $actual_pago_deposito;
    $nuevo_pago_sin_definir = $pago_sin_definir + $actual_pago_sin_definir;

    include '../helpers/verificar-hora-corte.php';

    if(($suma_abonos + $monto_total_abono) > $importe_total){
        $res = array('estatus'=>false, 'mensaje'=>'El apartado ya esta liquidado', 'liquidacion'=>true);
        echo json_encode($res);
    }else{

        if(($suma_abonos + $monto_total_abono) == $importe_total){
            $tipo = 0;
        }else{
            $tipo = 1;
        }
    
        $queryInsertar = "INSERT INTO abonos_apartados (id, id_apartado, 
                                                            fecha, 
                                                            hora, 
                                                            abono, 
                                                            metodo_pago,
                                                            pago_efectivo, 
                                                            pago_tarjeta, 
                                                            pago_transferencia, 
                                                            pago_cheque, 
                                                            pago_deposito, 
                                                            pago_sin_definir, 
                                                            usuario, 
                                                            estado,
                                                            sucursal,
                                                            id_sucursal, fecha_corte, hora_corte) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $resultado = $con->prepare($queryInsertar);
        $resultado->bind_param('sssssssssssssssss', $id_apartado, $fecha, $hora, $monto_total_abono,
                                                  $desc_metodos, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_deposito,
                                                  $pago_sin_definir, $vendedor_usuario, $tipo, $sucursal, $id_sucursal, $fecha_corte, $hora_corte);
        $resultado->execute();
        $error = $resultado->error;
        $resultado->close();
        $id_abono = $con->insert_id;
        insertarUtilidadAbonoApartados($id_abono, $con);
        if($nuevo_restante == 0){
            $estatus = 'Pagado';
            $tipo = 'Apartado';
            $liquidacion = true;

            $insertar = $con->prepare("INSERT INTO ventas (Fecha, sucursal, id_sucursal, id_Usuarios, id_Cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_deposito, pago_sin_definir, Total, tipo, estatus, metodo_pago, hora, comentario, fecha_corte, hora_corte) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $insertar->bind_param('sssssssssssssssssss', $fecha_actual, $sucursal, $id_sucursal, $id_usuario, $id_cliente, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_deposito, $pago_sin_definir, $importe_total, $tipo, $estatus, $desc_metodos, $hora, $comentario, $fecha_corte, $hora_corte);
            $insertar->execute();
            // Obtener el ID insertado
            $id_Venta = $con->insert_id;
            $insertar->close();
    
            //Haciendo consulta a detalle del apartado
    
            $detalle = $con->prepare("SELECT * FROM detalle_apartado da WHERE da.id_apartado = ?");
            $detalle->bind_param('i', $id_apartado);
            $detalle->execute();
            $resultado_da = $detalle->get_result(); 
            $detalle->close(); 
    
            while($fila = $resultado_da->fetch_assoc()) {
    
                $cantidad = $fila["cantidad"];
                $modelo = $fila["modelo"];
                $unidad = $fila["unidad"];
                $precio_unitario = $fila["precio_unitario"];
                $importe = $fila["importe"];
                $id_Llanta = $fila["id_llanta"];
            
                $dt_insert = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Cantidad, Modelo, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
                $resultado = $con->prepare($dt_insert);
                $resultado->bind_param('iiisssd', $id_Venta, $id_Llanta, $cantidad, $modelo, $unidad, $precio_unitario, $importe);
                $resultado->execute();
                $resultado->close();
            }

        $utilidad_res = insertarUtilidad($con, $id_Venta);
    
        }else{
            $estatus = 'Activo';
            $id_Venta = null;
            $liquidacion = false;
            $utilidad_res=[];
        }
        
        $upd = "UPDATE apartados SET pago_efectivo = ?, 
        pago_tarjeta = ?, 
        pago_transferencia = ?, 
        pago_cheque = ?, 
        pago_deposito = ?, 
        pago_sin_definir = ?,
        primer_abono = ?,
        restante = ?,
        estatus = ?,
        id_venta =? WHERE id = ?";
        $ress = $con->prepare($upd);
        $ress->bind_param('ddddddddssi', $nuevo_pago_efectivo, $nuevo_pago_tarjeta, $nuevo_pago_transferencia, $nuevo_pago_cheque, $nuevo_pago_deposito, $nuevo_pago_sin_definir, $nueva_suma_abonos, $nuevo_restante, $estatus, $id_Venta, $id_apartado);
        $ress->execute();
        $ress->close();
     
        $res = array('estatus'=>true, 'mensaje'=>'Abono realizado correctamente', 'liquidacion'=>$liquidacion, 'utilidad_res'=>$utilidad_res);
        echo json_encode($res);
    }
    
    
}