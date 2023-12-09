<?php

include '../conexion.php';
$con= $conectando->conexion(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!$con) {
    echo "Problemas con la conexion";
}
//Funcion que sanitiza entradas
$sanitizacion = function($valor, $con){
    $valor_ = $con->real_escape_string($valor);
    $valor_str = "'".$valor_."'";
    return $valor_str;
};

$fecha_inicial = empty($_POST['fecha_inicial']) ? '' : $_POST['fecha_inicial'];
$fecha_final = empty($_POST['fecha_final']) ? '' : $_POST['fecha_final'];
$sucursal_ubicacion = empty($_POST['sucursal_ubicacion']) ? '' : $_POST['sucursal_ubicacion'];
$sucursal_destino = empty($_POST['sucursal_destino']) ? '' : $_POST['sucursal_destino'];
$proveedor = empty($_POST['filtro_proveedor']) ? '' : $_POST['filtro_proveedor'];
$cliente = empty($_POST['cliente']) ? '' : $_POST['cliente'];
$folio = empty($_POST['folio']) ? '' : $_POST['folio'];
$factura = empty($_POST['factura']) ? '' : $_POST['factura'];
$marca_llanta = empty($_POST['marca_llanta']) ? '' : $_POST['marca_llanta']; //Multiple values
$ancho_llanta = empty($_POST['ancho_llanta']) ? '' : $_POST['ancho_llanta']; //Multiple values
$alto_llanta = empty($_POST['alto_llanta']) ? '' : $_POST['alto_llanta']; //Multiple values
$rin_llanta = empty($_POST['rin_llanta']) ? '' : $_POST['rin_llanta']; //Multiple values
$tipo = empty($_POST['filtro_tipo']) ? '' : $_POST['filtro_tipo']; //Multiple values
$estatus = empty($_POST['filtro_estatus']) ? '' : $_POST['filtro_estatus']; //Multiple values
$estado_factura = empty($_POST['filtro_estado']) ? '' : $_POST['filtro_estado']; //Multiple values
// Define la consulta SQL base
$sql = "SELECT DISTINCT m.* FROM vista_movimientos m 
LEFT JOIN usuarios u ON m.id_usuario = u.id LEFT JOIN proveedores p ON m.proveedor_id = p.id";

// Filtra por marcas si está definido
if (!empty($marca_llanta) || !empty($ancho_llanta) || !empty($alto_llanta) || !empty($rin_llanta)) {

    $sql .= " LEFT JOIN historial_detalle_cambio hdc ON m.id = hdc.id_movimiento";
    $sql .= " LEFT JOIN llantas ll ON ll.id = hdc.id_llanta LEFT JOIN marcas ma ON ll.Marca = ma.Imagen";
    $sql .= " WHERE 1=1";
    if(!empty($marca_llanta)){
        $marcas_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
            return $sanitizacion($valor, $con);
        }, $marca_llanta));
        // Agrega la operación INNER JOIN y la condición
        $sql .= " AND ma.id IN (" . $marcas_ids . ")";
    }
    if(!empty($ancho_llanta) && $ancho_llanta != 'null'){
        $sql .= " AND ll.Ancho = '" . $ancho_llanta . "'";
    }
    if(!empty($alto_llanta) && $ancho_llanta != 'null'){
        $sql .= " AND ll.Proporcion = '" . $alto_llanta . "'";
    }
    if(!empty($rin_llanta) && $ancho_llanta != 'null'){
        $sql .= " AND ll.Diametro = '" . $rin_llanta . "'";
    }
    
}

// Filtra por fecha inicial y final si están definidas
if (!empty($fecha_inicial) && !empty($fecha_final)) {
    $fecha_inicial_ = $con->real_escape_string($fecha_inicial);
    $fecha_final_ = $con->real_escape_string($fecha_final);
    $sql .= " AND m.fecha BETWEEN '" . $fecha_inicial_ . "' AND '" . $fecha_final_ . "'";
}else if (!empty($fecha_inicial)) {
    $fecha_inicial_ = $con->real_escape_string($fecha_inicial);
    $sql .= " AND m.fecha = '" . $fecha_inicial_ . "'";
}else if (!empty($fecha_final)) {
    $fecha_final_ = $con->real_escape_string($fecha_final);
    $sql .= " AND m.fecha = '" . $fecha_final_ . "'";
}

// Filtra por sucursal si está definida
if (!empty($sucursal_ubicacion)) {
    $sucursal_ubicacion_ = $con->real_escape_string($sucursal_ubicacion);
    $sql .= " AND hdc.id_ubicacion = '" . $sucursal_ubicacion_ . "'";
}

if (!empty($sucursal_destino)) {
    $sucursal_destino_ = $con->real_escape_string($sucursal_destino);
    $sql .= " AND hdc.id_destino = '" . $sucursal_destino_ . "'";
}

// Filtra por vendedor si está definido
if (!empty($proveedor)) {
    
    $proveedores_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $proveedor));
    $sql .= " AND m.proveedor_id IN (" . $proveedores_ids. ")";
}

// Filtra por folio si está definido
if (!empty($folio)) {
    $folio_ = $con->real_escape_string($folio);
    $sql .= " AND m.id = '" .$folio_ . "'";
}

// Filtra por folio si está definido
if (!empty($factura)) {
    $factura_ = $con->real_escape_string($factura);
    $sql .= " AND m.folio_factura = '" .$factura_ . "'";
}

// Filtra por tipo de venta si está definido
if (!empty($tipo)) {
    $tipo_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $tipo));
    $sql .= " AND m.tipo IN (" . $tipo_ids .")";
}

// Filtra por estatus si está definido (múltiples valores)
if (!empty($estatus)) {
    $estatus_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $estatus));
    $sql .= " AND m.estatus IN (" . $estatus_ids .")";
} 

// Filtra por estatus si está definido (múltiples valores)
if (!empty($estado_factura)) {
    $estado_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $estado_factura));
    $sql .= " AND m.estado_factura IN (" . $estado_ids .")";
} 
//print_r($sql);
//Ejecutamos la quert
$sql .= " ORDER BY m.id DESC";
/* print_r($sql);
die(); */
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total_resultados = count($data);
if($total_resultados >0){
    $estatus = true;
    $mensaje = 'Se encontrarón resultados';
}else{
    $data = [];
    $estatus = true;
    $mensaje = 'No se encontrarón resultados';
}

$res = array('query' => $sql, 'post'=> $_POST, 'data'=>$data, 'estatus'=>$estatus, 'mensaje'=>$mensaje, 'numero_resultados'=>$total_resultados);
echo json_encode($res);
?>