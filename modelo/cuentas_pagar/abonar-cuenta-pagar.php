<?php

include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}
$id_movimiento = $_POST['id_movimiento'];
$abono = floatval($_POST['abono']);
$forma_pago = $_POST['forma_pago'];
$importe_total = $_POST['importe_total'];
$fecha_hora_pago = $_POST['hora_fecha_pago'];
$folio_forma_pago = $_POST['folio_forma_pago'] == 'false' ? 'No aplica' : $_POST['folio_forma_pago'];
$resultado_fechas = convertirFechaHora($fecha_hora_pago);
$suma_abonos =0;

$fecha = $resultado_fechas['fecha'];
$hora =  $resultado_fechas['hora'];
$tipo =0;

$qr = "SELECT COUNT(*) FROM movimientos WHERE id =? AND tipo = 2";
$stmt = $con->prepare($qr);
$stmt->bind_param('i', $id_movimiento);
$stmt->execute();
$stmt->bind_result($total_movimientos);
$stmt->fetch();
$stmt->close();

if($total_movimientos>0){
    $qr = "SELECT SUM(monto) FROM abonos_cuentas WHERE id_movimiento = ?";
    $stmt = $con->prepare($qr);
    $stmt->bind_param('i', $id_movimiento);
    $stmt->execute();
    $stmt->bind_result($suma_abonos);
    $stmt->fetch();
    $stmt->close();
    
   if(($suma_abonos + $abono) <= $importe_total){
        $qr = "INSERT INTO abonos_cuentas(id, monto, fecha, hora, forma_pago, id_movimiento, folio_forma_pago) VALUES(null, ?,?,?,?,?,?)";
        $stmt = $con->prepare($qr);
       /*  print_r($abono . ' ' . $fecha .' '. $hora .'  '.$forma_pago);
        die(); */
        $stmt->bind_param('ssssss', $abono, $fecha, $hora, $forma_pago, $id_movimiento, $folio_forma_pago);
        $stmt->execute();
        $stmt->close();
        $suma_total = $suma_abonos + $abono;
        $estatus = true;
        
        $restante = $importe_total - $suma_total;
        $update = "UPDATE movimientos SET pagado = ?, restante = ? WHERE id = ?";
        $respp = $con->prepare($update);
        $respp->bind_param('sss', $suma_total, $restante, $id_movimiento);
        $respp->execute();
        $respp->close();

        if($suma_total == $importe_total){
            $update = "UPDATE movimientos SET estado_factura = 4 WHERE id = ?";
            $respp = $con->prepare($update);
            $respp->bind_param('s', $id_movimiento);
            $respp->execute();
            $respp->close();
            $mensaje = 'Abono registrado correctamente, remisión de ingreso pagada, estatus actualizado';
            $tipo = 2;
        }else{
            $mensaje = 'Abono registrado correctamente'; 
            $tipo = 1;
        }
   }else{
        $estatus = false;
        $mensaje = 'La sumatoria del monto sobrepasa el total'; 
   }
}else{
    $estatus = false;
    $mensaje = 'No hay un movimiento con ese ID'; 
}
$response = array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'tipo'=>$tipo);
echo json_encode($response);

function convertirFechaHora($fechaHoraStr) {
    // Validar formato de la cadena
    if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $fechaHoraStr)) {
      return "Formato de fecha y hora incorrecto";
    }
  
    // Separar la fecha de la hora
    $fecha = substr($fechaHoraStr, 0, 10);
    $hora = substr($fechaHoraStr, 11);
  
    // Convertir la hora a formato 12 horas con AM/PM
    $horaFormato12 = date('h:i A', strtotime($hora));
  
    // Devolver las variables
    return [
      'fecha' => $fecha,
      'hora' => $horaFormato12,
    ];
  }
?>