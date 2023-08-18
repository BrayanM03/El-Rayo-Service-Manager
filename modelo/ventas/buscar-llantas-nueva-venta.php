<?php

include '../conexion.php';
$con = $conectando->conexion();

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST["searchTerm"]) && $_POST["searchTerm"] != '') {
    $term = $_POST["searchTerm"];
    $parametro = "%$term%";
    $id_user_sucursal = $_POST["id_sucursal"];
    $user_rol = $_POST["rol"];
    $page = $_POST['page'] ?? 1; // Número de página actual (1 si no se especifica)

    // Parámetros de paginación
    $resultadosPorPagina = 10;

    // Calcular el offset
$offset = ($page - 1) * $resultadosPorPagina;

// Consulta preparada para obtener los resultados paginados
$sqlTraerLlanta = "SELECT l.*, inv.id, inv.Codigo, inv.Sucursal, inv.id_sucursal, inv.Stock
                  FROM llantas l
                  INNER JOIN inventario inv ON inv.id_Llanta = l.id
                  WHERE (l.Ancho LIKE ? 
                  OR inv.Codigo LIKE ? 
                  OR l.Proporcion LIKE ? 
                  OR l.Diametro LIKE ? 
                  OR l.Modelo LIKE ? 
                  OR l.Marca LIKE ? 
                  OR l.Descripcion LIKE ?) AND inv.Stock != 0
                  LIMIT ? OFFSET ?";

// Preparar la consulta
$stmt = $con->prepare($sqlTraerLlanta);
$stmt->bind_param("ssssssssi", $parametro, $parametro, $parametro, $parametro, $parametro, $parametro, $parametro, $resultadosPorPagina, $offset);
$stmt->execute();

// Obtener los resultados
$result = $stmt->get_result();
$llantasEncontradas = $result->fetch_all(MYSQLI_ASSOC);

// Cerrar la consulta preparada
$stmt->close();
/*         $sqlTraerLlanta = "SELECT l.*, inv.id, inv.Codigo, inv.Sucursal, inv.id_sucursal, inv.Stock FROM llantas l INNER JOIN inventario inv
ON inv.id_Llanta = l.id WHERE l.Ancho LIKE '%$term%'
OR inv.Codigo LIKE '%$term%'
OR l.Proporcion LIKE '%$term%'  
OR l.Diametro LIKE '%$term%'
OR l.Modelo LIKE '%$term%'  
OR l.Marca LIKE '%$term%' 
OR l.Descripcion LIKE '%$term%' LIMIT $resultadosPorPagina 
OFFSET $offset";
        $result = mysqli_query($con, $sqlTraerLlanta);
        while ($datas = mysqli_fetch_array($result)) {

            $llantasEncontradas[]  = $datas;
        } //Aqui termina de buscar en una sucursal y valida si hay en otra */

        $response = array(
            'results' => $llantasEncontradas, // Array de resultados obtenidos de la consulta SQL
            'post'=> $_POST,
            'pagination' => array(
              'page'=> $page,
              'offset'=> $offset,
              'paginas_per_page'=> $resultadosPorPagina,
              'more' => count($llantasEncontradas) == $resultadosPorPagina // Verificar si hay más resultados disponibles
            ));
        //echo json_encode($_POST);
       echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
   // }
} else {
    print_r("Error al conectar");
}
