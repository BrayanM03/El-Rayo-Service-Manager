<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "Problemas con la conexion";
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$tabla = $_POST['tabla'];
$columna = $_POST['columna'];

if($columna == 'Ancho'){
    //Filtro a anchos diferetes de 0 para el ancho del neumatico
    $query= "SELECT COUNT(*) FROM $tabla WHERE $columna !=0";   
}else{
    $query= "SELECT COUNT(*) FROM $tabla";   
}

$res = $con->prepare($query);
$res->execute();
$res->bind_result($total);
$res->fetch();
$res->close();

if($total>0){
    if($columna == 'Ancho'){
        $select = "SELECT DISTINCT $columna FROM $tabla WHERE $columna != 0 ORDER BY $columna ASC";
    }else{
        $select = "SELECT DISTINCT $columna FROM $tabla ORDER BY $columna ASC";
    }
    $res = $con->prepare($select);
    $res->execute();
    $get_result = $res->get_result();
    $resultado = $get_result->fetch_all(MYSQLI_ASSOC);
    $res->close();
    $resultado_f=[];
    foreach ($resultado as $key => $value) {
        $valorFloat = floatval($value[$columna]);
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
        'medidas'=>$resultado_f
    );
}else{
    $response = array(
        'estatus'=> false,
        'mensaje'=>'No encontrarón resultados',
        'medidas'=>[]
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
