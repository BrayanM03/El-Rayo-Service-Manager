<?php
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    
    if (isset($_POST["searchTerm"])) {
        $term = $_POST["searchTerm"];
        $page = $_POST['page'] ?? 1; // Número de página actual (1 si no se especifica)
        $parametro = "%$term%";
        $id_sucursal = $_POST["id_sucursal"];

        // Parámetros de paginación
       $resultadosPorPagina = 10;
       $offset = ($page - 1) * $resultadosPorPagina;

       $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l INNER JOIN inventario 
       ON inventario.id_Llanta = l.id 
       WHERE inventario.id_sucursal = ? AND (l.Ancho LIKE ?
       OR inventario.Codigo LIKE ? 
       OR l.Proporcion LIKE ? 
       OR l.Diametro LIKE ?
       OR l.Modelo LIKE ? 
       OR l.Marca LIKE ? 
       OR l.Descripcion LIKE ?)");

     //-----------------------------------------------------------------------------------------------------//
     //-----------------------------------------------------------------------------------------------------//
    
     $query_mostrar->bind_param('ssssssss',  $id_sucursal, $parametro, $parametro, $parametro, $parametro, $parametro, $parametro, $parametro);
     $query_mostrar->execute();
     $query_mostrar->bind_result($total);
     $query_mostrar->fetch();
     $query_mostrar->close();
     if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT l.*, inv.id, inv.id_Llanta, inv.Codigo, inv.Sucursal, inv.id_sucursal, inv.Stock FROM llantas l INNER JOIN inventario inv
        ON inv.id_Llanta = l.id 
        WHERE  
        (l.Ancho LIKE '%$term%'  
        OR inv.Codigo LIKE '%$term%'
        OR l.Proporcion LIKE '%$term%'  
        OR l.Diametro LIKE '%$term%'
        OR l.Modelo LIKE '%$term%'  
        OR l.Marca LIKE '%$term%' 
        OR l.Descripcion LIKE '%$term%') AND inv.id_sucursal = $id_sucursal LIMIT $resultadosPorPagina OFFSET $offset";
        $result = mysqli_query($con, $sqlTraerLlanta);
   
        while ($datas=mysqli_fetch_array($result)){
           
            $llantasEncontradas[]  = $datas;
          
        }//Aqui termina de buscar en una sucursal y valida si hay en otra
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
    }else{
        $llantasEncontradas = []; 
        $response = array(
            'results' => $llantasEncontradas, // Array de resultados obtenidos de la consulta SQL
            'post'=> $_POST,
            'pagination' => array(
              'page'=> $page,
              'offset'=> $offset,
              'paginas_per_page'=> $resultadosPorPagina,
              'more' => count($llantasEncontradas) == $resultadosPorPagina // Verificar si hay más resultados disponibles
            ));
        echo json_encode($response, JSON_UNESCAPED_UNICODE);                    

    }
        
    }else{
        print_r("Error al conectar");
    }
    ?>