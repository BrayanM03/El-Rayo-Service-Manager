<?php


include '../conexion.php';
include '../helpers/response_helper.php';

$con = $conectando->conexion();

if (!$con) {
    responder(false, 'Error en la conexión a la base de datos', 'danger', [], true, true);
}

// Verificar si se recibió un término de búsqueda
if (isset($_GET['query'])) {
    $termino = $_GET['query'];
    $termino = "%$termino%";  // Prepara el término para la búsqueda con LIKE

    // Parámetros de paginación
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 30; // Número de resultados por página
    $offset = ($page - 1) * $limit;

    // Consulta con paginación usando mysqli
    $query = "SELECT id, Nombre_Cliente as nombre_cliente, tipo_cliente
              FROM clientes 
              WHERE Nombre_Cliente LIKE ? 
              LIMIT ?, ?";
    $stmt = $con->prepare($query);

    if (!$stmt) {
        responder(false, 'Error en la preparación de la consulta', 'danger', [], true, true);
    }

    // Vincula los parámetros y ejecuta la consulta
    $stmt->bind_param("sii", $termino, $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si hay resultados
    if ($result->num_rows > 0) {
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = [
                'id' => $row['id'],
                'nombre_cliente' => $row['nombre_cliente'],
                'tipo_cliente' => $row['tipo_cliente']
            ];
        }
        responder(true, 'Clientes encontrados', 'success', $clientes, true, true);
    } else {
        responder(false, 'No se encontraron clientes', 'warning', [], true, true);
    }

    // Cierra la consulta
    $stmt->close();
} else {
    responder(false, 'No se recibió ningún término de búsqueda', 'danger', [], true, true);
}

// Cierra la conexión
$con->close();



/* include '../conexion.php';
include '../helpers/response_helper.php';

$con = $conectando->conexion();

if (!$con) {
    responder(false, 'Error en la conexión a la base de datos', 'danger', [], true, true);
}

if (isset($_GET['query'])) {
    $termino = $_GET['query'];
    $termino = "%$termino%";  // Prepara el término para la búsqueda con LIKE

    // Consulta usando mysqli
    $query = "SELECT id, Nombre_Cliente as nombre_cliente FROM clientes WHERE Nombre_Cliente LIKE ? LIMIT 10";
    $stmt = $con->prepare($query);

    if (!$stmt) {
        responder(false, 'Error en la preparación de la consulta', 'danger', [], true, true);
    }

    // Vincula el parámetro y ejecuta la consulta
    $stmt->bind_param("s", $termino);
    $stmt->execute();
    $result = $stmt->get_result();
    

    // Verifica si hay resultados
    if ($result->num_rows > 0) {
        $clientes = [];
        while ($row = $result->fetch_assoc()) {
            $clientes[] = [
                'id' => $row['id'],
                'nombre_cliente' => $row['nombre_cliente']
            ];
        }
        responder(true, 'Clientes encontrados', 'success', $clientes, true, true);
    } else {
        responder(false, 'No se encontraron clientes', 'warning', [], true, true);
    }

    // Cierra la consulta
    $stmt->close();
} else {
    responder(false, 'No se recibió ningún término de búsqueda', 'danger', [], true, true);
}

// Cierra la conexión
$con->close(); */
?>
