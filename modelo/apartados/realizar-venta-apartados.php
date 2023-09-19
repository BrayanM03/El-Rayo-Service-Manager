
<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion();

date_default_timezone_set("America/Matamoros");

$id = $_POST["id_apartado"];
$fecha_actual = date("Y-m-d");
$hora = date("h:i a");
$ID = $con->prepare("SELECT a.id_sucursal, a.sucursal, a.id_usuario, a.id_cliente, c.Nombre_Cliente, c.Telefono, a.primer_abono, a.restante, a.total,  a.tipo, a.metodo_pago, a.hora, a.comentario, a.plazo, a.fecha_inicial, a.fecha_final, a.pago_efectivo, a.pago_tarjeta, a.pago_transferencia, a.pago_cheque, a.pago_sin_definir FROM apartados a INNER JOIN clientes c ON a.id_cliente = c.id WHERE a.id = ?");
$ID->bind_param('i', $id);
$ID->execute();
$ID->bind_result($id_sucursal, $sucursal, $vendedor_id, $id_cliente, $cliente, $telefono_cliente, $primer_abono, $restante, $total, $tipo, $metodo_pago, $hora, $comentario, $plazo, $fecha_inicio, $fecha_final, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir);
$ID->fetch();
$ID->close();

$ID = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
$ID->bind_param('i', $vendedor_id);
$ID->execute();
$ID->bind_result($vendedor_name, $vendedor_apellido);
$ID->fetch();
$ID->close();

$vendedor_usuario = $vendedor_name . " " . $vendedor_apellido;
$estatus = 'Pagado';

$desc_metodos = '';
$pago_efectivo = 0;
$pago_transferencia = 0;
$pago_tarjeta = 0;
$pago_cheque = 0;
$pago_sin_definir = 0;
foreach ($_POST["metodos_pago"] as $key => $value) {
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

    case 4:
      $pago_sin_definir = $value['monto'];
      break;

    default:
      break;
  }
  $monto_pago = $value['monto'];
  $metodo_pago = $value['metodo'];
  if ($key != count($_POST["metodos_pago"]) - 1) {
    // Este código se ejecutará para todos menos el último
    $desc_metodos .= $metodo_pago . ", ";
  } else {
    $desc_metodos .= $metodo_pago . ". ";
  }
}

//Haciendo consulta a detalle del apartado

$detalle = $con->prepare("SELECT * FROM detalle_apartado da WHERE da.id_apartado = ?");
$detalle->bind_param('i', $id);
$detalle->execute();
$resultado_da = $detalle->get_result();
$detalle->close();

//Script que verifica la hora de corte actual
include '../helpers/verificar-hora-corte.php';

//Insertando la venta
$insertar = $con->prepare("INSERT INTO ventas (Fecha, sucursal, id_sucursal, id_Usuarios, id_Cliente, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir, Total, tipo, estatus, metodo_pago, hora, comentario, fecha_corte, hora_corte) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
$insertar->bind_param('ssssssssssssssssss', $fecha_actual, $sucursal, $id_sucursal, $vendedor_id, $id_cliente, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $total, $tipo, $estatus, $metodo_pago, $hora, $comentario, $fecha_corte, $hora_corte);
$insertar->execute();
// Obtener el ID insertado
$id_Venta = $con->insert_id;
$insertar->close();

//Actualizar apartado
$total_abonado =  floatval($restante);
$nuevo_restante = 0;
$actualizar = $con->prepare("UPDATE apartados SET fecha_pago_final = ?, hora_pago_final = ?, estatus = ?, id_venta = ?, restante = ? WHERE id = ?");
$actualizar->bind_param('sssiii', $fecha_actual, $hora, $estatus, $id_Venta, $total_abonado, $id);
$actualizar->execute();
$actualizar->close();
//Iterando servicios
$data_productos = array();

//Insertar abono pendiente
$estado = 0;
$queryInsertar = "INSERT INTO abonos_apartados (id, id_apartado, fecha, hora, abono, metodo_pago, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir, usuario, estado, sucursal, id_sucursal, fecha_corte, hora_corte) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$resultado = $con->prepare($queryInsertar);
$resultado->bind_param('issdsdddddssssss', $id, $fecha_actual, $hora, $restante, $metodo_pago, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $vendedor_usuario, $estado, $sucursal, $id_sucursal, $fecha_corte, $hora_corte);
$resultado->execute();
$resultado->close();

//Iterando productos
while ($fila = $resultado_da->fetch_assoc()) {

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

$res = array('estatus' => true, 'mensaje' => 'Venta realizada correctamente. ', 'id_venta' => $id_Venta);
echo json_encode($res);
