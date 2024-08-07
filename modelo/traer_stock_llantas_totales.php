<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (isset($_POST["searchTerm"])) {
      
        $term = $_POST["searchTerm"];
        $parametro = "%$term%";
        $total =0;
        $page = $_POST['page'] ?? 1; // Número de página actual (1 si no se especifica)
        // Parámetros de paginación
        $resultadosPorPagina = 10;
        // Calcular el offset
        $offset = ($page - 1) * $resultadosPorPagina;

        $sqlContarLlantas= $con->prepare("SELECT COUNT(*) total FROM llantas WHERE Descripcion LIKE ? 
                                                                             OR Ancho LIKE ?
                                                                             OR Proporcion LIKE ?
                                                                             OR Diametro LIKE ?
                                                                             OR Descripcion LIKE ?
                                                                             OR Marca LIKE ?
                                                                             OR Modelo LIKE ?");

                                                                                  
       
       $sqlContarLlantas->bind_param('sssssss', $parametro, $parametro, $parametro,  $parametro, $parametro, $parametro, $parametro); 
       $sqlContarLlantas->execute();
       $sqlContarLlantas->bind_result($total);
       $sqlContarLlantas->fetch();
       $sqlContarLlantas->close();
       

       if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT * FROM llantas WHERE Ancho       LIKE '%$term%'  
                                                  OR Proporcion  LIKE '%$term%'  
                                                  OR Diametro    LIKE '%$term%'
                                                  OR Modelo      LIKE '%$term%'  
                                                  OR Marca       LIKE '%$term%' 
                                                  OR Descripcion LIKE '%$term%' LIMIT $resultadosPorPagina OFFSET $offset";
        
        $resultado = mysqli_query($con, $sqlTraerLlanta);
       
        while($fila= $resultado->fetch_assoc()){
            $id = $fila['id'];
            $ancho = $fila['Ancho'];
            $alto = $fila['Proporcion'];
            $rin = $fila['Diametro'];
            $descripcion = $fila['Descripcion']; 
            $modelo = $fila['Modelo'];
            $marca = $fila['Marca'];
            $costo = $fila['precio_Inicial'];
            $precio = $fila['precio_Venta'];
            $mayoreo = $fila['precio_Mayoreo'];
            $promocion = intval($fila['promocion']);
            $data[] = array('id' => $id, 'ancho' => $ancho, 'alto' => $alto, 'rin' => $rin, 'descripcion' =>$descripcion, 'modelo' => $modelo, 'marca'=> $marca,
                             'costo'=> $costo, 'precio'=>$precio, 'mayoreo'=>$mayoreo, 'promocion'=>$promocion);
        }

        $response = array(
            'results' => $data, // Array de resultados obtenidos de la consulta SQL
            'post'=> $_POST,
            'pagination' => array(
              'page'=> $page,
              'offset'=> $offset,
              'paginas_per_page'=> $resultadosPorPagina,
              'more' => count($data) == $resultadosPorPagina // Verificar si hay más resultados disponibles
            ));
        //echo json_encode($_POST);
       echo json_encode($response, JSON_UNESCAPED_UNICODE);
       
    
    }else{ 
        
        $data = array("id" => 0, "Descripcion"=>"Sin resultados");
    }   
    

   
    

    }
    
    
    ?>