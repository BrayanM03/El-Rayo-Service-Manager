<?php

/* file_put_contents('debug.txt', print_r($_POST, true));
die(json_encode(["debug" => $_POST])); */
include '../conexion.php';
session_start();
$con = $conectando->conexion();

if (!$con) {
    echo "Problemas con la conexion";
}
//Funcion que sanitiza entradas
$sanitizacion = function ($valor, $con) {
    $valor_ = $con->real_escape_string($valor);
    $valor_str = "'" . $valor_ . "'";
    return $valor_str;
};

// Parámetros de DataTables
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$search = $_POST['search']['value'] ?? '';



$fecha_inicial = empty($_POST['fecha_inicial']) ? '' : $_POST['fecha_inicial'];
$fecha_final = empty($_POST['fecha_final']) ? '' : $_POST['fecha_final'];
$fecha_vencimiento_inicial = empty($_POST['fecha_vencimiento_inicial']) ? '' : $_POST['fecha_vencimiento_inicial'];
$fecha_vencimiento_final = empty($_POST['fecha_vencimiento_final']) ? '' : $_POST['fecha_vencimiento_final'];
$sucursal = empty($_POST['sucursal']) ? '' : $_POST['sucursal'];

$vendedor = empty($_POST['vendedor']) ? '' : $_POST['vendedor'];
$asesor = empty($_POST['filtro_asesor']) ? '' : $_POST['filtro_asesor'];
$cliente = empty($_POST['cliente']) ? '' : $_POST['cliente'];
$folio = empty($_POST['folio']) ? '' : $_POST['folio'];
$filtro_ray = empty($_POST['filtro_ray']) ? '' : $_POST['filtro_ray'];
$marca_llanta = empty($_POST['marca_llanta']) ? '' : $_POST['marca_llanta']; //Multiple values
$ancho_llanta = empty($_POST['ancho_llanta']) ? '' : $_POST['ancho_llanta']; //Multiple values
$alto_llanta = empty($_POST['alto_llanta']) ? '' : $_POST['alto_llanta']; //Multiple values
$rin_llanta = empty($_POST['rin_llanta']) ? '' : $_POST['rin_llanta']; //Multiple values
$estatus = isset($_POST['estatus']) ? array_filter($_POST['estatus'], fn($v) => $v !== '' && $v !== null) : [];
$plazo = isset($_POST['plazo']) ? array_filter($_POST['plazo'], fn($v) => $v !== '' && $v !== null) : [];



// Cuenta total sin filtros
$totalQuery = "SELECT COUNT(*) AS total FROM creditos";
$totalResult = $con->query($totalQuery);
$totalData = $totalResult->fetch_assoc()['total'];

$sql = armarQuery(
    0,
    $start,
    $length,
    $search,
    $fecha_inicial,
    $fecha_final,
    $fecha_vencimiento_inicial,
    $fecha_vencimiento_final,
    $sucursal,
    $vendedor,
    $asesor,
    $cliente,
    $folio,
    $marca_llanta,
    $ancho_llanta,
    $alto_llanta,
    $rin_llanta,
    $estatus,
    $plazo,
    $filtro_ray,
    $con,
    $sanitizacion
);

$sql_count = armarQuery(
    1,
    $start,
    $length,
    $search,
    $fecha_inicial,
    $fecha_final,
    $fecha_vencimiento_inicial,
    $fecha_vencimiento_final,
    $sucursal,
    $vendedor,
    $asesor,
    $cliente,
    $folio,
    $marca_llanta,
    $ancho_llanta,
    $alto_llanta,
    $rin_llanta,
    $estatus,
    $plazo,
    $filtro_ray,
    $con,
    $sanitizacion
);
// Cuenta total con filtros
/* print_r($sql_count);
die(); */

/* print_r($sql);
die(); */
//Ejecutamos la quert

$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Cuenta total con filtros
$filteredResult = $con->query($sql_count);
/* print_r($sql_count);
die(); */
$totalFiltered = $filteredResult->fetch_assoc()['total'];

$total_resultados = count($data);
if ($total_resultados > 0) {
    $estatus = true;
    $mensaje = 'Se encontrarón resultados';
} else {
    $data = [];
    $estatus = true;
    $mensaje = 'No se encontrarón resultados';
}

// Respuesta en formato DataTables
$response = [
    "draw" => $draw,
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data,
    'slq' => json_encode($sql),
    'post' => $_POST
];

echo json_encode($response);
/* $res = array('query' => $sql, 'post'=> $_POST, 'data'=>$data, 'estatus'=>$estatus, 'mensaje'=>$mensaje, 'numero_resultados'=>$total_resultados);
echo json_encode($res); */



//FUNCION QUE ARMA LA QUERY
function armarQuery(
    $es_count,
    $start,
    $length,
    $search,
    $fecha_inicial,
    $fecha_final,
    $fecha_vencimiento_inicial,
    $fecha_vencimiento_final,
    $sucursal,
    $vendedor,
    $asesor,
    $cliente,
    $folio,
    $marca_llanta,
    $ancho_llanta,
    $alto_llanta,
    $rin_llanta,
    $estatus,
    $plazo,
    $filtro_ray,
    $con,
    $sanitizacion
) {

    // Define la consulta SQL base
    if ($es_count == 0) {
        $select_ = "DISTINCT cr.id as folio, c.Nombre_Cliente AS cliente, cr.fecha_inicio, cr.fecha_final, 
    cr.total, cr.pagado, cr.restante, cr.estatus, cr.plazo, 
    s.nombre AS sucursal, cr.id_venta AS id_venta, 
    CONCAT(u.nombre, ' ', u.apellidos) AS vendedor ";
    } else {
        $select_ = "COUNT(DISTINCT cr.id) as total ";
    }
    $sql = "SELECT 
$select_
FROM creditos cr
INNER JOIN ventas v ON cr.id_venta = v.id
INNER JOIN usuarios u ON v.id_Usuarios = u.id
INNER JOIN clientes c ON v.id_Cliente = c.id
INNER JOIN sucursal s ON v.id_Sucursal = s.id";

    // Filtra por marcas si está definido
   
    if (!empty($marca_llanta) || !empty($ancho_llanta) || !empty($alto_llanta) || !empty($rin_llanta)) {

        $sql .= " INNER JOIN detalle_venta dv ON v.id = dv.id_Venta";
        $sql .= " LEFT JOIN llantas ll ON ll.id = dv.id_Llanta LEFT JOIN marcas m ON ll.Marca = m.Imagen";
        $sql .= " WHERE 1=1";
        
        if (!empty($marca_llanta)) {
            $marcas_ids =  implode(",", array_map(function ($valor) use ($con, $sanitizacion) {
                return $sanitizacion($valor, $con);
            }, $marca_llanta));
            // Agrega la operación INNER JOIN y la condición
            $sql .= " AND m.id IN (" . $marcas_ids . ")";
           
        }
        if (!empty($ancho_llanta) && $ancho_llanta != 'null') {
            $sql .= " AND ll.Ancho = '" . $ancho_llanta . "'";
        }
        if (!empty($alto_llanta) && $ancho_llanta != 'null') {
            $sql .= " AND ll.Proporcion = '" . $alto_llanta . "'";
        }
        if (!empty($rin_llanta) && $ancho_llanta != 'null') {
            $sql .= " AND ll.Diametro = '" . $rin_llanta . "'";
        }
    }

    // Filtra por fecha inicial y final si están definidas
    if (!empty($fecha_inicial) && !empty($fecha_final)) {
        $fecha_inicial_ = $con->real_escape_string($fecha_inicial);
        $fecha_final_ = $con->real_escape_string($fecha_final);
        $sql .= " AND cr.fecha_inicio BETWEEN '" . $fecha_inicial_ . "' AND '" . $fecha_final_ . "'";
    } elseif (!empty($fecha_inicial)) {
        $fecha_inicial_ = $con->real_escape_string($fecha_inicial);
        $sql .= " AND cr.fecha_inicio = '" . $fecha_inicial_ . "'";
    } elseif (!empty($fecha_final)) {
        $fecha_final_ = $con->real_escape_string($fecha_final);
        $sql .= " AND cr.fecha_inicio = '" . $fecha_final_ . "'";
    }

    // Filtra por fecha de vencimiento inicial y final si están definidas
    if (!empty($fecha_vencimiento_inicial) && !empty($fecha_vencimiento_final)) {
        $fecha_vencimiento_inicial_ = $con->real_escape_string($fecha_vencimiento_inicial);
        $fecha_vencimiento_final_ = $con->real_escape_string($fecha_vencimiento_final);
        $sql .= " AND cr.fecha_final BETWEEN '" . $fecha_vencimiento_inicial_ . "' AND '" . $fecha_vencimiento_final_ . "'";
    } elseif (!empty($fecha_vencimiento_inicial)) {
        $fecha_vencimiento_inicial_ = $con->real_escape_string($fecha_vencimiento_inicial);
        $sql .= " AND cr.fecha_final = '" . $fecha_vencimiento_inicial_ . "'";
    } elseif (!empty($fecha_vencimiento_final)) {
        $fecha_vencimiento_final_ = $con->real_escape_string($fecha_vencimiento_final);
        $sql .= " AND cr.fecha_final = '" . $fecha_vencimiento_final_ . "'";
    }

    // Filtra por sucursal si está definida
    if (!empty($sucursal)) {
        $sucursal_ = $con->real_escape_string($sucursal);
        $sql .= " AND v.id_sucursal = '" . $sucursal_ . "'";
    }

    // Filtra por vendedor si está definido
    if (!empty($vendedor)) {

        $vendedores_ids =  implode(",", array_map(function ($valor) use ($con, $sanitizacion) {
            return $sanitizacion($valor, $con);
        }, $vendedor));
        $sql .= " AND v.id_Usuarios IN (" . $vendedores_ids . ")";
    }
    // Filtra por vendedor si está definido
    if (!empty($asesor)) {
        //print_r($asesor);
        //$array_ases = explode(",", $asesor);
        $asesores_ids =  implode(",", array_map(function ($valor) use ($con, $sanitizacion) {
            return $sanitizacion($valor, $con);
        }, $asesor));
        if ($_SESSION['id_usuario'] == 6 || in_array(6, $asesor)) { //Usuario de aminta se bloquearan las ventas fuera de su sucursal en filtro de asesor
            $sql .= " AND v.id_sucursal = 1 AND c.id_asesor IN (" . $asesores_ids . ")";
        } else {
            $sql .= " AND c.id_asesor IN (" . $asesores_ids . ")";
        }
    }

    // Filtra por cliente si está definido
    if (!empty($cliente)) {
        $clientes_ids = implode(",", array_map(function ($valor) use ($con, $sanitizacion) {
            return $sanitizacion($valor, $con);
        }, $cliente));
        $sql .= " AND v.Id_Cliente IN (" . $clientes_ids . ")";
    }

    // Filtra por folio si está definido
    if (!empty($folio)) {
        $folio_ = $con->real_escape_string($folio);
        $sql .= " AND cr.id = '" . $folio_ . "'";
    }

    // Filtra por folio si está definido
    if (!empty($filtro_ray)) {
        $filtro_ray_ = $con->real_escape_string($filtro_ray);
        $sql .= " AND v.id = '" . $filtro_ray_ . "'";
    }

    // Filtra por estatus si está definido (múltiples valores)
    if (!empty($estatus)) {
        $estatus_ids = implode(",", array_map(function ($valor) use ($con, $sanitizacion) {
            return $sanitizacion($valor, $con);
        }, $estatus));
        $sql .= " AND cr.estatus IN (" . $estatus_ids . ")";
    }

    // Filtra por plazo si está definido (múltiples valores)
    if (!empty($plazo)) {
        $plazo_ids = implode(",", array_map(function ($valor) use ($con, $sanitizacion) {
            return $sanitizacion($valor, $con);
        }, $plazo));
        $sql .= " AND cr.plazo IN (" . $plazo_ids . ")";
    }

    // Si hay búsqueda global
    if ($search != '') {
        $sql .= " AND (cr.id LIKE '%$search%' OR c.Nombre_Cliente LIKE '%$search%' OR cr.total LIKE '%$search%')";
    }

    if ($es_count == 0) {
        $sql .= " ORDER BY cr.id DESC LIMIT $start, $length";
    }
  
    return $sql;
   
};
