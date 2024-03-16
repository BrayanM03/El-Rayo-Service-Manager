
<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 

$id = $_POST["id"];

$ID = $con->prepare("SELECT p.sucursal, p.id_usuario, c.Nombre_Cliente, c.Telefono, p.abonado, p.restante, p.total,  p.tipo, p.estatus, p.hora_inicio, p.hora_final, p.comentario, p.fecha_inicio, p.fecha_final FROM pedidos p INNER JOIN clientes c ON p.id_cliente = c.id WHERE p.id = ?");
$ID->bind_param('i', $id);
$ID->execute();
$ID->bind_result($sucursal, $vendedor_id, $cliente, $telefono_cliente, $primer_abono, $restante, $total, $tipo, $estatus, $hora_inicio, $hora_final, $comentario, $fecha_inicio, $fecha_final);
$ID->fetch();
$ID->close();

$ID = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
$ID->bind_param('i', $vendedor_id);
$ID->execute();
$ID->bind_result($vendedor_name, $vendedor_apellido);
$ID->fetch();
$ID->close();

$vendedor_usuario = $vendedor_name . " " . $vendedor_apellido;

//Haciendo consulta a detalle del apartado

$detalle = $con->prepare("SELECT llantas.Modelo as modelo, da.cantidad,llantas.Descripcion as descripcion, llantas.Marca, da.precio_unitario, da.importe FROM detalle_pedido da INNER JOIN llantas ON da.id_Llanta = llantas.id WHERE da.id_pedido = ?");
$detalle->bind_param('i', $id);
$detalle->execute();
$resultado = $detalle->get_result(); 
$detalle->close();

$detalle = $con->prepare("SELECT COUNT(*) FROM abonos_pedidos WHERE id_pedido = ?");
$detalle->bind_param('i', $id);
$detalle->execute();
$detalle->bind_result($no_abonos);
$detalle->fetch();
$detalle->close();
//print_r($no_abonos);
if($no_abonos > 0){
    $detalle = $con->prepare("SELECT * FROM abonos_pedidos WHERE id_pedido = ?");
    $detalle->bind_param('i', $id);
    $detalle->execute();
    $resultado_abonos = $detalle->get_result(); 
    $detalle->close();
    
    while($fila_ab = $resultado_abonos->fetch_assoc()){
        $id = $fila_ab['id']; 
        $fecha_abono = $fila_ab['fecha'];
        $hora = $fila_ab['hora'];
        $abono = $fila_ab['abono'];
        $metodo_pago = $fila_ab['metodo_pago'];
        $pago_efectivo = $fila_ab['pago_efectivo'];
        $pago_tarjeta = $fila_ab['pago_tarjeta'];
        $pago_transferencia = $fila_ab['pago_transferencia'];
        $pago_cheque = $fila_ab['pago_cheque'];
        $pago_deposito = $fila_ab['pago_deposito'];
        $pago_sin_definir = $fila_ab['pago_sin_definir'];
        $usuario =  $fila_ab['usuario'];
        $sucursal = $fila_ab['sucursal'];
        $id_sucursal = $fila_ab['id_sucursal'];

        $arreglo_abonos[] = array(
            'id' => $id,
            'fecha' => $fecha_abono,
            'horas' => $hora,
            'abono' => $abono,
            'metodo_pago' => $metodo_pago,
            'pago_efectivo' => $pago_efectivo,
            'pago_tarjeta' => $pago_tarjeta,
            'pago_transferencia' => $pago_transferencia,
            'pago_cheque' => $pago_cheque,
            'pago_deposito' => $pago_deposito,
            'pago_sin_definir' => $pago_sin_definir,
            'sucursal' => $sucursal,
        );

    }
}else{
    $arreglo_abonos = array();
}


//Iterando servicios
$data_productos = array();


//Iterando productos
while($fila = $resultado->fetch_assoc()) {

    $cantidad = $fila["cantidad"];
    $modelo = $fila["modelo"];
    $descripcion = $fila["descripcion"];
    $marca = $fila["Marca"];
    $precio_unitario = $fila["precio_unitario"];
    $importe = $fila["importe"];
    $caracteres = mb_strlen($descripcion);

    $data_productos[] = array(
        'cantidad' => $cantidad,
        'modelo' => $modelo,
        'descripcion' => $descripcion,
        'marca' => $marca,
        'precio_unitario' => $precio_unitario,
        'importe' => $importe,
        'caracteres' => $caracteres
    );
}

$arreglo_final = ($data_productos);


$data = array(
    'id' => $id,
    'sucursal' => $sucursal,
    'vendedor_id' => $vendedor_id,  
    'cliente' => $cliente,
    'telefono_cliente' => $telefono_cliente,
    'primer_abono' => $primer_abono,
    'restante' => $restante,
    'total' => $total,
    'tipo' => $tipo,
    'estatus' => $estatus,
    'hora_inicio' => $hora_inicio,
    'hora_final' => $hora_final,
    'comentario' => $comentario,
    'fecha_inicio' => $fecha_inicio,
    'fecha_final' => $fecha_final,
    'vendedor_usuario' => $vendedor_usuario,
    'detalles' => $arreglo_final,
    'abonos' => $arreglo_abonos
);

echo json_encode($data);

?>
