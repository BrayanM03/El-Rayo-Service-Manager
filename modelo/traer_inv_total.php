<?php
session_start();
include 'conexion.php';
$con = $conectando->conexion();

if (!$con) {
    echo "maaaaal";
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (isset($_POST)) {

    $search_value = $_POST['search']['value']; // Valor de búsqueda enviado desde DataTables
    $search_query = "%{$search_value}%";
    
    if($_POST['search']==''){
        $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l");

    }else{
        $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l WHERE l.id LIKE ? OR l.modelo LIKE ? OR l.Descripcion LIKE ? OR l.Marca LIKE ?");
        $query_mostrar->bind_param('ssss', $search_query, $search_query, $search_query, $search_query);
    }
   
    $query_mostrar->execute();
    $query_mostrar->bind_result($total);
    $query_mostrar->fetch();
    $query_mostrar->close();

    $querySuc = "SELECT COUNT(*) FROM sucursal";
    $resp = $con->prepare($querySuc);
    $resp->execute();
    $resp->bind_result($total_suc);
    $resp->fetch();
    $resp->close();

    $sucursales = [];
    if ($total_suc > 0) {
        $querySuc = "SELECT * FROM sucursal";
        $resp = mysqli_query($con, $querySuc);

        while ($row = $resp->fetch_assoc()) {
            $suc_identificador = $row['id'];
            $nombre = $row["nombre"];

            $sucursales[] = array("id" => $suc_identificador, "nombre" => $nombre);
        }
    }

    // Pagination
    //$results_per_page = 10;
    $results_per_page = $_POST['length'];
    $current_page = isset($_POST['page']) ? $_POST['page'] : 1;
    $total_pages = ceil($total / $results_per_page);
    $offset = ($current_page - 1) * $results_per_page;


    $order_column_index = $_POST['order'][0]['column']; // Índice de la columna de ordenamiento
    if($_POST['columns'][$order_column_index]['data'] == 'mayoreo'){
        $columna_db = 'l.precio_Mayoreo';
    }else if($_POST['columns'][$order_column_index]['data'] == 'precio'){
        $columna_db = 'l.precio_Venta';
    }else if($_POST['columns'][$order_column_index]['data'] == 'costo'){
        $columna_db = 'l.precio_Inicial';
    }else if($_POST['columns'][$order_column_index]['data'] == 'sucursal')
    {
        $columna_db = 'l.id';
    }
    else if($_POST['columns'][$order_column_index]['data'] == 'stock'){
        $columna_db = 'total_stock';
    }else{
        $columna_db = 'l.'.$_POST['columns'][$order_column_index]['data'];
    }
    $order_column_name = $columna_db; // Nombre de la columna de ordenamiento
    $order_direction = $_POST['order'][0]['dir']; // Dirección de ordenamiento (ascendente o descendente)

    $order_by = $order_column_name . ' ' . $order_direction;
    
    if ($total > 0) {
        // Obtener registros de la base de datos
            $sqlTraerLlanta = "SELECT l.*, COALESCE((SELECT SUM(i.Stock) FROM inventario i WHERE i.id_Llanta = l.id), 0) AS total_stock
            FROM llantas l WHERE l.id LIKE ? OR l.modelo LIKE ? OR l.Descripcion LIKE ? OR l.Marca LIKE ? ORDER BY $order_by LIMIT ?, ?;
            ";
                                                      
                                                  
        $stmt = $con->prepare($sqlTraerLlanta);
        
        $stmt->bind_param("ssssii", $search_query, $search_query, $search_query, $search_query, $offset, $results_per_page);
        $stmt->execute();
        $resultado = $stmt->get_result();

        while ($fila = $resultado->fetch_assoc()) {
            
            $id = $fila['id'];
            $stock = $fila['total_stock'];
            $ancho = $fila['Ancho'];
            $alto = $fila["Proporcion"];
            $rin = $fila['Diametro'];
            $descripcion = $fila['Descripcion'];
            $modelo = $fila['Modelo'];
            $marca = $fila['Marca'];
            $costo = $fila['precio_Inicial'];
            $precio = $fila['precio_Venta'];
            $mayoreo = $fila['precio_Mayoreo'];
            $fecha = $fila['Fecha'];
            $precio_lista = $fila['precio_lista'];

            $data['data'][] = array(
                'id' => $id, 'ancho' => $ancho, 'alto' => $alto, 'rin' => $rin, 'descripcion' => $descripcion,
                'modelo' => $modelo, 'marca' => $marca, 'costo' => $costo, 'precio_lista' => $precio_lista, 'precio' => $precio,
                'mayoreo' => $mayoreo, 'fecha' => $fecha, 'sucursales' => $sucursales, 'stock' => $stock
            );
        }

        $data['total_pages'] = $total_pages;
        $data['current_page'] = $current_page;
        $data['recordsTotal'] = $total;
        $data['recordsFiltered'] = $total;

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else {
        $data['data'] = [];
        $data['total_pages'] = 0;
        $data['current_page'] = 1;
        $data['recordsTotal'] = 0;
        $data['recordsFiltered'] = 0;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);

    }
} else {
    print_r("Error al conectar");
}
?>
