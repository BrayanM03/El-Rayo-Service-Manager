<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "Problemas con la conexion";
} 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$tabla = $_POST['tabla'];
$fuente = $_POST['fuente'];

if($fuente == 'Ancho'){
    //Filtro a anchos diferetes de 0 para el ancho del neumatico
    $query= "SELECT COUNT(*) FROM $tabla WHERE $fuente >=0";   
}else{
    $query= "SELECT COUNT(*) FROM $tabla";   
}

$res = $con->prepare($query);
$res->execute();
$res->bind_result($total);
$res->fetch();
$res->close();

if($total>0){
    if($fuente == 'Ancho'){
        $select = "SELECT DISTINCT l.$fuente FROM llantas l INNER JOIN inventario i ON l.id = i.id_Llanta 
         WHERE $fuente >= 0 AND i.Stock > 0 ORDER BY l.$fuente ASC";
    }else{
        $ancho = isset($_POST['ancho']) ? floatval($_POST['ancho']): 0;
        $alto = isset($_POST['alto']) ? floatval($_POST['alto']): 0;

        $select = "SELECT DISTINCT l.$fuente FROM llantas l INNER JOIN inventario i ON l.id = i.id_Llanta 
        WHERE l.Ancho = ? AND i.Stock > 0";

        if(isset($_POST['alto'])){
           $select .=  ' AND l.Proporcion = ?'; 
        } 
        $select.= " ORDER BY l.$fuente ASC";
    }
   
    $res = $con->prepare($select);
    switch ($fuente) {
        case 'Proporcion':
           $res->bind_param('d', $ancho);
            break;
        
        case 'Diametro':
        
            $res->bind_param('dd', $ancho, $alto);
             break;
             
        default:
            # code...
            break;
    }
    
    $res->execute();
    $get_result = $res->get_result();
    $resultado = $get_result->fetch_all(MYSQLI_ASSOC);
    $res->close();
    $resultado_f=[];
    foreach ($resultado as $key => $value) { 
        $valorFloat = floatval($value[$fuente]);
        if ($valorFloat == (int)$valorFloat) {
            // Si es un número entero, convertir a entero
            $numero = (int)$valorFloat;
        } else {
            // Si tiene decimales, mantenerlo como punto flotante
            $numero = $valorFloat;
        }
        $resultado_f[] = $numero;
    }
  
    $resultado_f = eliminarRepetidosYOrdenar($resultado_f);
    $response = array(
        'estatus'=> true,
        'mensaje'=>'Se encontrarón los siguientes resultados',
        'medidas'=>$resultado_f,
        'sql' => $select
    );
}else{
    $response = array(
        'estatus'=> false,
        'mensaje'=>'No encontrarón resultados',
        'medidas'=>[],
        'sql' => $query
    );
}

echo json_encode($response);

function eliminarRepetidosYOrdenar($array) {
    // Usamos array_unique para eliminar elementos duplicados
    $arrayUnico = array_unique($array, SORT_REGULAR);
    // Reindexamos el arreglo para que las claves sean consecutivas
    $arrayUnico = array_values($arrayUnico);
    // Ordenamos el arreglo
    sort($arrayUnico);
    return $arrayUnico;
}
?>
