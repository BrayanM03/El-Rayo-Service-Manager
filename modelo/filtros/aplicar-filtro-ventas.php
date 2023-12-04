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

$fecha_inicial = empty($_POST['fecha_inicial']) ? '' : $_POST['fecha_inicial'];
$fecha_final = empty($_POST['fecha_final']) ? '' : $_POST['fecha_final'];
$sucursal = empty($_POST['sucursal']) ? '' : $_POST['sucursal'];
$vendedor = empty($_POST['vendedor']) ? '' : $_POST['vendedor'];
$asesor = empty($_POST['filtro_asesor']) ? '' : $_POST['filtro_asesor'];
$cliente = empty($_POST['cliente']) ? '' : $_POST['cliente'];
$folio = empty($_POST['folio']) ? '' : $_POST['folio'];
$marca_llanta = empty($_POST['marca_llanta']) ? '' : $_POST['marca_llanta']; //Multiple values
$ancho_llanta = empty($_POST['ancho_llanta']) ? '' : $_POST['ancho_llanta']; //Multiple values
$alto_llanta = empty($_POST['alto_llanta']) ? '' : $_POST['alto_llanta']; //Multiple values
$rin_llanta = empty($_POST['rin_llanta']) ? '' : $_POST['rin_llanta']; //Multiple values
$tipo = empty($_POST['filtro_tipo']) ? '' : $_POST['filtro_tipo'] ; //Multiple values
$estatus = empty($_POST['filtro_estatus']) ? '' : $_POST['filtro_estatus']; //Multiple values

// Define la consulta SQL base
$sql = "SELECT DISTINCT v.*, concat(u.nombre, ' ', u.apellidos) vendedor, c.Nombre_Cliente as cliente FROM ventas v LEFT JOIN usuarios u ON v.id_Usuarios = u.id LEFT JOIN clientes c ON v.id_Cliente = c.id";

// Filtra por marcas si está definido
if (!empty($marca_llanta) || !empty($ancho_llanta) || !empty($alto_llanta) || !empty($rin_llanta)) {

    $sql .= " LEFT JOIN detalle_venta dv ON v.id = dv.id_Venta";
    $sql .= " LEFT JOIN llantas ll ON ll.id = dv.id_Llanta LEFT JOIN marcas m ON ll.Marca = m.Imagen";
    $sql .= " WHERE 1=1";
    if(!empty($marca_llanta)){
        $marcas_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
            return $sanitizacion($valor, $con);
        }, $marca_llanta));
        // Agrega la operación INNER JOIN y la condición
        $sql .= " AND m.id IN (" . $marcas_ids . ")";
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
    $sql .= " AND v.Fecha BETWEEN '" . $fecha_inicial_ . "' AND '" . $fecha_final_ . "'";
}else if (!empty($fecha_inicial)) {
    $fecha_inicial_ = $con->real_escape_string($fecha_inicial);
    $sql .= " AND v.Fecha = '" . $fecha_inicial_ . "'";
}else if (!empty($fecha_final)) {
    $fecha_final_ = $con->real_escape_string($fecha_final);
    $sql .= " AND v.Fecha = '" . $fecha_final_ . "'";
}

// Filtra por sucursal si está definida
if (!empty($sucursal)) {
    $sucursal_ = $con->real_escape_string($sucursal);
    $sql .= " AND v.id_sucursal = '" . $sucursal_ . "'";
}

// Filtra por vendedor si está definido
if (!empty($vendedor)) {
    
    $vendedores_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $vendedor));
    $sql .= " AND v.id_Usuarios IN (" . $vendedores_ids. ")";
}
// Filtra por vendedor si está definido
if (!empty($asesor)) {
    //print_r($asesor);
    //$array_ases = explode(",", $asesor);
        $asesores_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
            return $sanitizacion($valor, $con);
        }, $asesor));
    if($_SESSION['id_usuario']==6 || in_array(6, $asesor)){ //Usuario de aminta se bloquearan las ventas fuera de su sucursal en filtro de asesor
        $sql .= "AND v.id_sucursal = 1 AND c.id_asesor IN (" . $asesores_ids. ")";

    }else{
        $sql .= " AND c.id_asesor IN (" . $asesores_ids. ")";
    }
}

// Filtra por cliente si está definido
if (!empty($cliente)) {
    $clientes_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $cliente));
    $sql .= " AND v.Id_Cliente IN (" . $clientes_ids .")";
}

// Filtra por folio si está definido
if (!empty($folio)) {
    $folio_ = $con->real_escape_string($folio);
    $sql .= " AND v.id = '" .$folio_ . "'";
}


// Filtra por tipo de venta si está definido
if (!empty($tipo)) {
    $tipo_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $tipo));
    $sql .= " AND v.tipo IN (" . $tipo_ids .")";
}

// Filtra por estatus si está definido (múltiples valores)
if (!empty($estatus)) {
    $estatus_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
        return $sanitizacion($valor, $con);
    }, $estatus));
    $sql .= " AND v.estatus IN (" . $estatus_ids .")";
} 
//print_r($sql);
//Ejecutamos la quert
$sql .= " ORDER BY v.id DESC";
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