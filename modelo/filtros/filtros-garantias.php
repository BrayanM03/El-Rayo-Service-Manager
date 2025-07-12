<?php

include '../conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "Problemas con la conexion";
}
//Funcion que sanitiza entradas
$sanitizacion = function($valor, $con){
    $valor_ = $con->real_escape_string($valor);
    $valor_str = "'".$valor_."'";
    return $valor_str;
};

$folio = empty($_POST['folio']) ? '' : $_POST['folio'];
$cliente = empty($_POST['cliente']) ? '' : $_POST['cliente'];
$llanta = empty($_POST['descripcion_llanta']) ? '' : $_POST['descripcion_llanta']; //Multiple values
$proveedor = empty($_POST['proveedor']) ? '' : $_POST['proveedor'];
$fecha_inicial = empty($_POST['fecha_inicial']) ? '' : $_POST['fecha_inicial'];
$fecha_final = empty($_POST['fecha_final']) ? '' : $_POST['fecha_final'];
$estatus_fisico = empty($_POST['estatus_fisico']) ? '' : $_POST['estatus_fisico']; //Multiple values
$dictamen = empty($_POST['dictamen']) ? '' : $_POST['dictamen']; //Multiple values
$ray = empty($_POST['ray']) ? '' : $_POST['ray']; //Multiple values
$factura = empty($_POST['factura']) ? '' : $_POST['factura']; //Multiple values
$serie = empty($_POST['serie']) ? '' : $_POST['serie'] ; //Multiple values
$dot_produccion = empty($_POST['dot_produccion']) ? '' : $_POST['dot_produccion'];
$dot_fabricacion = empty($_POST['dot_fabricacion']) ? '' : $_POST['dot_fabricacion'];


// Define la consulta SQL base
$sql = "SELECT DISTINCT g.* FROM vista_garantias g WHERE 1=1";

// Filtra por folio si está definido
if (!empty($folio)) {
    $folio_ = $con->real_escape_string($folio);
    $sql .= " AND g.id = '" .$folio_ . "'";
}

// Filtra por cliente si está definido
if (!empty($cliente)) {
    $clientes_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $cliente));
    $sql .= " AND g.id_cliente IN (" . $clientes_ids .")";
}

// Filtra por cliente si está definido
if (!empty($llanta)) {
    $llantas_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $llanta));
    $sql .= " AND g.id_llanta IN (" . $llantas_ids .")";
}

// Filtra por vendedor si está definido
if (!empty($proveedor)) {
    
    $proveedores_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $proveedor));
    $sql .= " AND g.id_proveedor IN (" . $proveedores_ids. ")";
}

// Filtra por fecha inicial y final si están definidas
if (!empty($fecha_inicial) && !empty($fecha_final)) {
    $fecha_inicial_ = $con->real_escape_string($fecha_inicial);
    $fecha_final_ = $con->real_escape_string($fecha_final);
    $sql .= " AND g.fecha_registro BETWEEN '" . $fecha_inicial_ . "' AND '" . $fecha_final_ . "'";
}else if (!empty($fecha_inicial)) {
    $fecha_inicial_ = $con->real_escape_string($fecha_inicial);
    $sql .= " AND g.fecha_registro = '" . $fecha_inicial_ . "'";
}else if (!empty($fecha_final)) {
    $fecha_final_ = $con->real_escape_string($fecha_final);
    $sql .= " AND g.fecha_registro = '" . $fecha_final_ . "'";
}

if (!empty($estatus_fisico)) {
    $estatus_fisico_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $estatus_fisico));
    $sql .= " AND g.estatus_fisico IN (" . $estatus_fisico_ids. ")";
}

if (!empty($dictamen)) {
    $dictamen_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $dictamen));
    $sql .= " AND g.dictamen IN (" . $dictamen_ids. ")";
}

if (!empty($ray)) {
    $ray_=$con->real_escape_string($ray);
    $sql .= " AND g.id = $ray_";
}

if (!empty($factura)) {
    $factura_=$con->real_escape_string($factura);
    $sql .= " AND g.factura = $factura_";
}

if (!empty($serie)) {
    $serie_=$con->real_escape_string($serie);
    $sql .= " AND g.serie = $serie_";
}

if (!empty($dot_fabricacion)) {
    $dot_fabricacion_=$con->real_escape_string($dot_fabricacion);
    $sql .= " AND g.dot = $dot_fabricacion_";
}

if (!empty($dot_produccion)) {
    $dot_produccion_=$con->real_escape_string($dot_produccion);
    $sql .= " AND g.dot_produccion = $dot_produccion_";
}

// Filtra por sucursal si está definida
/* if (!empty($sucursal)) {
    $sucursal_ = $con->real_escape_string($sucursal);
    $sql .= " AND v.id_sucursal = '" . $sucursal_ . "'";
}
 */


// Filtra por estatus si está definido (múltiples valores)
/* if (!empty($estatus)) {
    $estatus_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $estatus));
    $sql .= " AND v.estatus IN (" . $estatus_ids .")";
}  */
//print_r($sql);
//Ejecutamos la quert
$sql .= " ORDER BY g.id DESC";
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