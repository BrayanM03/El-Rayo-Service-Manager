<?php
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }


    if (isset($_POST["searchTerm"])) {
        $term = $_POST["searchTerm"];
        $parametro = "%$term%";
        $id_sucursal = $_POST["id_sucursal"];
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
    
     $query_mostrar->bind_param('isssssss',$id_sucursal, $parametro, $parametro, $parametro, $parametro, $parametro, $parametro, $parametro);
     $query_mostrar->execute();
     $query_mostrar->bind_result($total);
     $query_mostrar->fetch();
     $query_mostrar->close();


     if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT l.*, inv.id, inv.id_Llanta, inv.Codigo, inv.Sucursal, inv.id_sucursal, inv.Stock FROM llantas l INNER JOIN inventario inv
                                                                ON inv.id_Llanta = l.id 
                                                                WHERE inv.id_sucursal = '$id_sucursal' AND 
                                                                (l.Ancho LIKE '%$term%'  
                                                                OR inv.Codigo LIKE '%$term%'
                                                                OR l.Proporcion LIKE '%$term%'  
                                                                OR l.Diametro LIKE '%$term%'
                                                                OR l.Modelo LIKE '%$term%'  
                                                                OR l.Marca LIKE '%$term%' 
                                                                OR l.Descripcion LIKE '%$term%')";
        $result = mysqli_query($con, $sqlTraerLlanta);
        while ($datas=mysqli_fetch_array($result)){
           
            $llantasEncontradas[]  = $datas;
          
        }//Aqui termina de buscar en una sucursal y valida si hay en otra

        echo json_encode($llantasEncontradas, JSON_UNESCAPED_UNICODE);
        
                            
                            
                        
    
    }
        
    }else{
        print_r("Error al conectar");
    }
    ?>