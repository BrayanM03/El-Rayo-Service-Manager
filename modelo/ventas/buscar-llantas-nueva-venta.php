<?php
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST["searchTerm"])) {
        $term = $_POST["searchTerm"];
        $parametro = "%$term%";

       $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l INNER JOIN inventario 
                                                                          ON inventario.id_Llanta = l.id 
                                                                          WHERE l.Ancho LIKE ?
                                                                          OR inventario.Codigo LIKE ? 
                                                                          OR l.Proporcion LIKE ? 
                                                                          OR l.Diametro LIKE ?
                                                                          OR l.Modelo LIKE ? 
                                                                          OR l.Marca LIKE ? 
                                                                          OR l.Descripcion LIKE ?");

     //-----------------------------------------------------------------------------------------------------//
     //-----------------------------------------------------------------------------------------------------//
    
     $query_mostrar->bind_param('sssssss', $parametro, $parametro, $parametro, $parametro, $parametro, $parametro, $parametro);
     $query_mostrar->execute();
     $query_mostrar->bind_result($total);
     $query_mostrar->fetch();
     $query_mostrar->close();


     if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT l.*, inv.id, inv.Codigo, inv.Sucursal, inv.id_sucursal, inv.Stock FROM llantas l INNER JOIN inventario inv
                                                                ON inv.id_Llanta = l.id 
                                                                WHERE l.Ancho LIKE '%$term%'  
                                                                OR inv.Codigo LIKE '%$term%'
                                                                OR l.Proporcion LIKE '%$term%'  
                                                                OR l.Diametro LIKE '%$term%'
                                                                OR l.Modelo LIKE '%$term%'  
                                                                OR l.Marca LIKE '%$term%' 
                                                                OR l.Descripcion LIKE '%$term%'";
        $result = mysqli_query($con, $sqlTraerLlanta);
        while ($datas=mysqli_fetch_array($result)){
           
            $llantasEncontradas[]  = $datas;
          
        }//Aqui termina de buscar en una sucursal y valida si hay en otra

        echo json_encode($llantasEncontradas, JSON_UNESCAPED_UNICODE);
        
        /* $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l INNER JOIN inventario_mat2 sendero
        ON sendero.id_Llanta = l.id 
        WHERE l.Ancho LIKE ?
        OR sendero.Codigo LIKE ? 
        OR l.Proporcion LIKE ? 
        OR l.Diametro LIKE ?
        OR l.Modelo LIKE ? 
        OR l.Marca LIKE ? 
        OR l.Descripcion LIKE ?");
        
                    $query_mostrar->bind_param('sssssss', $parametro, $parametro, $parametro, $parametro, $parametro, $parametro, $parametro);
                    $query_mostrar->execute();
                    $query_mostrar->bind_result($total2);
                    $query_mostrar->fetch();
                    $query_mostrar->close();

                    if ($total2 > 0) {  //Encontro una llanta de sendero

                    $sqlTraerLlantaS="SELECT l.*, sendero.id, sendero.Codigo, sendero.Sucursal, sendero.Stock FROM llantas l INNER JOIN inventario_mat2 sendero
                    ON sendero.id_Llanta = l.id 
                    WHERE l.Ancho LIKE '%$term%'  
                    OR sendero.Codigo LIKE '%$term%'
                    OR l.Proporcion LIKE '%$term%'  
                    OR l.Diametro LIKE '%$term%'
                    OR l.Modelo LIKE '%$term%'  
                    OR l.Marca LIKE '%$term%' 
                    OR Descripcion LIKE '%$term%'";
                    $result = mysqli_query($con, $sqlTraerLlantaS);
                    while ($datas=mysqli_fetch_array($result)){

                    $llantasEncontradasSendero[] = $datas;

                    }
                    $llantastotales = array();
                    $llantastotales =array_merge($llantasEncontradasSendero, $llantasEncontradasPedro);

                    echo json_encode($llantastotales, JSON_UNESCAPED_UNICODE);

                    }else{ //No encontro llantas entoces imprime solo las de la Pedro

                       echo json_encode($llantasEncontradasPedro, JSON_UNESCAPED_UNICODE);
                    

                    } */
                            
                            
                        
    
    }
        
    }else{
        print_r("Error al conectar");
    }
    ?>