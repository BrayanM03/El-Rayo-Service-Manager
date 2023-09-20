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

$term = $_POST['term'];
$termino = '%' . $term . '%';
$tabla = $_POST['tabla'];
$columnas = $_POST['columnas'];
$columna_id = $_POST['columna_id'];
$no_columns = count($columnas);
$page = $_POST['page'];  // Página actual
$perPage = 10;  // Cantidad de resultados por página
$offset = ($page - 1) * $perPage;
$placeholders = '';
foreach ($columnas as $key => $columna) {
    if($key === count($columnas) -1){
        $placeholders .= ("$columna LIKE ? ");
    }else{
        $placeholders .= ("$columna LIKE ? OR ");
    }
}



// Preparar dinámicamente los tipos y valores de los parámetros
$data_types = str_repeat('s', count($columnas));
$bind_params = array_merge([$data_types], array_fill(0, $no_columns, $termino));
// Obtener la cadena de tipos de datos (primer valor)
$bind_types = array_shift($bind_params);
$array_params = array_fill(0, $no_columns, $termino);

// Construye la consulta SQL
$conteo = "SELECT COUNT(*) FROM $tabla WHERE $placeholders";
$res = $con->prepare($conteo);
$res->bind_param($bind_types, ...$array_params);
$res->execute();
$res->bind_result($match);
$res->fetch();
$res->close();

if($match>0){
    $query = "SELECT * FROM $tabla WHERE $placeholders LIMIT $offset, $perPage";
    $stmt = $con->prepare($query);
    $stmt->bind_param($bind_types, ...$array_params);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $response = array(
        'estatus' => true,
        'datos' => $data,
        'mensaje'=> 'Información encontrada'
    );
}else{
    $data = [];
    $response = array(
        'estatus' => true,
        'datos' => $data,
        'mensaje'=> 'Información no encontrada'
    );
}

echo json_encode($response);
?>