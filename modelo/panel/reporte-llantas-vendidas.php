
<?php
include '../conexion.php';
date_default_timezone_set("America/Matamoros");

$con= $conectando->conexion(); 
include '../helpers/response_helper.php';

if(empty($_POST['fecha_inicio']) && $_POST['fecha_final']){
    responder(false, 'Selecciona una o mas sucursales', []);
}
$fecha_inicio = $_POST['fecha_inicio']== '' ?  $fecha_final :$_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_final']=='' ? $fecha_inicio : $_POST['fecha_final'];
$marcas = empty($_POST['id_marca']) ?  '' :$_POST['id_marca'];
$ancho = $_POST['ancho']== '' ?  '' :$_POST['ancho'];
$alto = $_POST['alto']== '' ?  '' :$_POST['alto'];
$rin = $_POST['rin']== '' ?  '' :$_POST['rin'];

$ids_sucursales= $_POST['sucursal'];
$placeholders = implode(',', array_fill(0, count($ids_sucursales), '?'));
$types = 'ss' . str_repeat('i', count($ids_sucursales));
$params = array_merge([$fecha_inicio, $fecha_fin], $ids_sucursales);
$where ='';
// Filtros opcionales
if (!empty($marcas)) {
    $placeholdersMarca = implode(',', array_fill(0, count($marcas), '?'));
    $where .= " AND l.Marca IN ($placeholdersMarca)";
    foreach ($marcas as $m) {
        $params[] = $m;
        $types .= "s";
    }
}

if (!empty($ancho)) {
    $where .= " AND l.Ancho = ?";
    $params[] = $ancho;
    $types .= "i";
}

if (!empty($alto)) {
    $where .= " AND l.Proporcion = ?";
    $params[] = $alto;
    $types .= "d";
}

if (!empty($rin)) {
    $where .= " AND l.Diametro = ?";
    $params[] = $rin;
    $types .= "d";
}

$query= "SELECT COUNT(*) FROM detalle_venta dv INNER JOIN ventas v ON dv.id_venta = v.id 
INNER JOIN llantas l ON l.id = dv.id_llanta
INNER JOIN sucursal s ON s.id = v.id_sucursal
INNER JOIN clientes c ON c.id = v.id_cliente INNER JOIN usuarios u ON u.id = v.id_Usuarios
WHERE (v.Fecha BETWEEN ? AND ?) AND v.estatus != 'Cancelada' AND v.id_sucursal IN ($placeholders) $where";
$stmt = $con->prepare($query);
$stmt->bind_param( $types,
...array_map(function($v) { return $v; }, $params));
$stmt->execute();
$stmt->bind_result($ventas_realizadas);
$stmt->fetch();
$stmt->close();

/* echo $query;
die(); */
if($ventas_realizadas>0){
    $query = "SELECT l.Descripcion, l.Marca, dv.Cantidad, v.id as ray, s.nombre as sucursal, concat(u.nombre, ' ', u.apellidos) as vendedor,
    c.Nombre_cliente, v.Fecha, v.hora FROM detalle_venta dv INNER JOIN ventas v ON dv.id_venta = v.id 
    INNER JOIN llantas l ON l.id = dv.id_llanta
    INNER JOIN sucursal s ON s.id = v.id_sucursal
    INNER JOIN clientes c ON c.id = v.id_cliente INNER JOIN usuarios u ON u.id = v.id_Usuarios WHERE 
    dv.Unidad = 'Pieza' AND v.estatus != 'Cancelada' AND (v.Fecha BETWEEN ? AND ?) AND v.id_sucursal IN ($placeholders) $where";
       $stmt = $con->prepare($query);
       $stmt->bind_param( $types,
       ...array_map(function($v) { return $v; }, $params));
       $stmt->execute();
       $resultado = $stmt->get_result();
    
       $Data = [];
       $arr_pz_x_marca = [];

       while ($fila = $resultado->fetch_assoc()) {
            $Data[]= $fila;
             // Agrupar por marca
            $marca = $fila['Marca'];
            $cantidad = (int)$fila['Cantidad'];

            if (!isset($arr_pz_x_marca[$marca])) {
                $arr_pz_x_marca[$marca] = 0;
            }

            $arr_pz_x_marca[$marca] += $cantidad;
       }

    responder(true, 'Se encontrarón ventas', $arr_pz_x_marca, $Data);

}else{
    responder(false, 'No se encontrarón ventas', []);
}

?>
