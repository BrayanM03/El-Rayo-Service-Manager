<?php
session_start();
include '../conexion.php';
include '../helpers/response_helper.php';
require_once '../clientes/Cliente.php';

$con= $conectando->conexion(); 

if (!$con) {
    echo "Problemas con la conexion";
} 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$ancho = $_POST['ancho'];
$alto = isset($_POST['alto']) ? $_POST['alto'] : 0;
$diametro = $_POST['diametro'];
$filtros = isset($_POST['filtros']) == true ? $_POST['filtros'] : [];
$id_sucursal_sesion = $_SESSION['id_sucursal'];
$id_cliente = isset($_POST['id_cliente']) ? $_POST['id_cliente'] : 0;

if($id_cliente>0){
    $catalogo = new Clientes($con);
    $cliente_arreglo = $catalogo->obtenerCliente($id_cliente);
    if(!$cliente_arreglo['estatus']){
        responder(false, $cliente_arreglo['mensaje'], 'danger', [], true);
    }
   
$tipo_cliente = $cliente_arreglo['cliente']['tipo_cliente'];
}else{
    $tipo_cliente=$id_cliente;
}

   

$count = "SELECT count(*) FROM llantas l INNER JOIN inventario i ON l.id = i.id_Llanta WHERE i.Stock > 0 
AND l.Ancho = ? AND l.Proporcion = ? AND l.Diametro = ?";


if(count($filtros) > 0) {
    $aplicacion_ids = [];
    $sucursal_ids = [];
    $tipo_carga_ids = [];

    foreach ($filtros as $value) {
        $categoria = explode('_', $value)[0];
        $filtro_id = substr($value, strrpos($value, '_') + 1);

        if($categoria == 'aplicacion') {
            $aplicacion_ids[] = $filtro_id;
        }
        if($categoria == 'sucursal') {
            $sucursal_ids[] = $filtro_id;
        }
        if($categoria == 'tipo_carga') {
            $tipo_carga_ids[] = $filtro_id;
        }
    }

    // Usamos implode para unir los IDs sin comas al final
    $aplicacion_ids_str = implode(', ', $aplicacion_ids);
    $sucursal_ids_str = implode(', ', $sucursal_ids);
    $tipo_carga_ids_str = implode(', ', $tipo_carga_ids);

    //placeholders
    $placeholders_aplicacion = implode(',', array_fill(0, count($aplicacion_ids), '?'));
    $placeholders_sucursal = implode(',', array_fill(0, count($sucursal_ids), '?'));
    $placeholders_tipo_carga = implode(',', array_fill(0, count($tipo_carga_ids), '?'));
    $types_aplicacion = implode('', array_fill(0, count($aplicacion_ids), 'i'));
    $types_sucursal = implode('', array_fill(0, count($sucursal_ids), 'i'));
    $types_tipo_carga = implode('', array_fill(0, count($tipo_carga_ids), 'i'));
    $types_ = $types_aplicacion.$types_tipo_carga.$types_sucursal;
   
    if($placeholders_aplicacion){
        $count .= ' AND l.id_aplicacion IN (' . $placeholders_aplicacion . ')';
    }
    if($placeholders_tipo_carga){
        $count .= ' AND l.id_tipo_carga IN (' . $placeholders_tipo_carga . ')';
    }
    if($placeholders_sucursal){
        $count .= ' AND i.id_sucursal IN (' . $placeholders_sucursal.')';
    }

}
$types = 'ddd';
$stmt  = $con->prepare($count);
if(count($filtros) > 0) {
    $types.=$types_;
    // Inicializamos los arrays vacíos si no están definidos
    $aplicacion_ids = $aplicacion_ids ?? [];
    $sucursal_ids = $sucursal_ids ?? [];
    $tipo_carga_ids = $tipo_carga_ids ?? [];

    // Unimos los arreglos en uno solo
    $combined_ids = array_merge($aplicacion_ids, $tipo_carga_ids, $sucursal_ids);

    // Agregamos las variables adicionales al array
    $params = array_merge([$ancho, $alto, $diametro], $combined_ids);
  
    $stmt->bind_param($types,...$params);

}else{

    $stmt->bind_param($types, $ancho, $alto, $diametro);
}
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();
/* print_r($count);
print_r($types);
print_r($params);
print_r($total);
die(); */

if($total > 0) {
    $sel = "SELECT DISTINCT l.*, i.Stock, i.Codigo, s.nombre, s.id as id_sucursal, li.url_principal, li.url_frontal, 
    li.url_perfil, li.url_piso, l.promocion, l.precio_promocion, i.id_sucursal FROM llantas l 
    INNER JOIN inventario i ON l.id = i.id_Llanta 
    INNER JOIN sucursal s ON s.id = i.id_sucursal 
    LEFT JOIN llantas_imagenes li ON l.id = li.id_llanta WHERE i.Stock > 0 
    AND l.Ancho = ? AND l.Proporcion = ? AND l.Diametro = ?";

    if(count($filtros) > 0) {
        if($placeholders_aplicacion){
            $sel .= ' AND l.id_aplicacion IN (' . $placeholders_aplicacion . ')';
        }
        if($placeholders_tipo_carga){
            $sel.= ' AND l.id_tipo_carga IN (' . $placeholders_tipo_carga . ')';
        }
        if($placeholders_sucursal){
            $sel .= ' AND i.id_sucursal IN (' . $placeholders_sucursal.')';
        }
        $sel .= " ORDER BY FIELD(i.id_sucursal, $id_sucursal_sesion) DESC, i.id_sucursal DESC";
        
        $res = $con->prepare($sel);
        $res->bind_param($types,...$params);
    }else{
        $sel .= " ORDER BY FIELD(i.id_sucursal, $id_sucursal_sesion) DESC, i.id_sucursal DESC";
        
        $res = $con->prepare($sel);
        $res->bind_param('ddd', $ancho, $alto, $diametro);
    }
    
    $res->execute();
    $resultado = $res->get_result();
    $res->free_result();
    $res->close();
    $resultado_f=[];
    while ($value = $resultado->fetch_assoc()) { 
        if($tipo_cliente==1){
            $value['precio_Venta']=$value['precio_Mayoreo'];
            $value['cliente_mayoreo']=true;
        }else{
            $value['cliente_mayoreo']=false;

        }
        $value['tipo_cliente']=$tipo_cliente;
        $resultado_f[]= $value;

    }

    $mensaje = 'Se encontró esa medida';
    $response =  array('estatus'=>true, 'mensaje'=>$mensaje, 'datos' => $resultado_f);
    echo json_encode($response);
}else{
    $mensaje = 'No se encontró esa medida';
    $response =  array('estatus'=>false, 'mensaje'=>$mensaje);
    echo json_encode($response);
}
?>