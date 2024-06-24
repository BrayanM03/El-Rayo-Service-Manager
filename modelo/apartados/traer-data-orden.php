
<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 

$id = $_POST["id"];

$ID = $con->prepare("SELECT a.sucursal, a.id_usuario, c.Nombre_Cliente, c.Telefono, a.primer_abono, a.restante, a.total,  a.tipo, a.estatus, a.metodo_pago, a.hora, a.comentario, a.plazo, a.fecha_inicial, a.fecha_final FROM apartados a INNER JOIN clientes c ON a.id_cliente = c.id WHERE a.id = ?");
$ID->bind_param('i', $id);
$ID->execute();
$ID->bind_result($sucursal, $vendedor_id, $cliente, $telefono_cliente, $primer_abono, $restante, $total, $tipo, $estatus, $metodo_pago, $hora, $comentario, $plazo, $fecha_inicio, $fecha_final);
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
$data_servicios = array();

$detalles = $con->prepare("SELECT da.modelo, da.cantidad,servicios.descripcion, da.precio_unitario, da.importe FROM detalle_apartado da INNER JOIN servicios ON da.id_llanta = servicios.id WHERE da.id_apartado = ?");
$detalles->bind_param('i', $id);
$detalles->execute();
$resultadoServ = $detalles->get_result();
$detalles->close(); 

//Iterando servicios
while($fila = $resultadoServ->fetch_assoc()) {

    $cantidad = $fila["cantidad"];
    $modelo = "N/A";
    $descripcion = $fila["descripcion"];
    $marca = "N/A";
    $precio_unitario = $fila["precio_unitario"];
    $importe = $fila["importe"];
    $caracteres = mb_strlen($descripcion);
    $data_servicios[] = array(
        'cantidad' => $cantidad,
        'modelo' => $modelo,
        'descripcion' => $descripcion,
        'marca' => $marca,
        'precio_unitario' => $precio_unitario,
        'importe' => $importe,
        'caracteres' => $caracteres
    );
}

$query = "SELECT da.modelo, da.cantidad, llantas.descripcion, llantas.Marca, da.precio_unitario, da.importe FROM detalle_apartado da INNER JOIN llantas ON da.id_llanta = llantas.id WHERE da.id_apartado = ? AND da.modelo != 'no aplica'";
$detallep = $con->prepare($query);
$detallep->bind_param('s', $id);
$detallep->execute();
$resultadoProd = $detallep->get_result();

//Iterando productos
$data_productos = array();  // Inicializar el array para evitar problemas si está vacío
while ($filaP = $resultadoProd->fetch_assoc()) {
    $cantidad = $filaP["cantidad"];
    $modelo = $filaP["modelo"];
    $descripcion = $filaP["descripcion"];
    $marca = $filaP["Marca"];
    $precio_unitario = $filaP["precio_unitario"];
    $importe = $filaP["importe"];
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

$detallep->free_result(); // Mover free_result() después de iterar
$detallep->close();
    

$detalle = $con->prepare("SELECT COUNT(*) FROM abonos_apartados WHERE id_apartado = ?");
$detalle->bind_param('i', $id);
$detalle->execute();
$detalle->bind_result($no_abonos);
$detalle->fetch();
$detalle->close();
if($no_abonos > 0){
    $detalle = $con->prepare("SELECT * FROM abonos_apartados WHERE id_apartado = ?");
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



$arreglo_final = array_merge($data_servicios, $data_productos);


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
    'metodo_pago' => $metodo_pago,
    'hora' => $hora,
    'comentario' => $comentario,
    'plazo' => $plazo,
    'fecha_inicio' => $fecha_inicio,
    'fecha_final' => $fecha_final,
    'vendedor_usuario' => $vendedor_usuario,
    'detalles' => $arreglo_final,
    'abonos' => $arreglo_abonos
);

echo json_encode($data);

?>
