<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST['ancho'])) {
     $ancho = $_POST['ancho'];
     $parametro = "%$ancho%";
     $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l INNER JOIN inventario_mat1 pedro
                                                                          ON pedro.id_Llanta = l.id 
                                                                          WHERE l.Ancho LIKE ? 
                                                                          OR l.Proporcion LIKE ? 
                                                                          OR l.Diametro LIKE ?
                                                                          OR l.Modelo LIKE ? 
                                                                          OR l.Marca LIKE ? 
                                                                          OR l.Descripcion LIKE ?");

     //-----------------------------------------------------------------------------------------------------//
     //-----------------------------------------------------------------------------------------------------//
    
     $query_mostrar->bind_param('ssssss', $parametro, $parametro, $parametro, $parametro, $parametro, $parametro);
     $query_mostrar->execute();
     $query_mostrar->bind_result($total);
     $query_mostrar->fetch();
     $query_mostrar->close();


     if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT l.*, pedro.id, pedro.Codigo, pedro.Sucursal, pedro.Stock FROM llantas l INNER JOIN inventario_mat1 pedro
                                                                ON pedro.id_Llanta = l.id 
                                                                WHERE l.Ancho LIKE '%$ancho%'  
                                                                OR l.Proporcion LIKE '%$ancho%'  
                                                                OR l.Diametro LIKE '%$ancho%'
                                                                OR l.Modelo LIKE '%$ancho%'  
                                                                OR l.Marca LIKE '%$ancho%' 
                                                                OR Descripcion LIKE '%$ancho%'";
        $result = mysqli_query($con, $sqlTraerLlanta);
        while ($datas=mysqli_fetch_array($result)){
           
           $arrayAnchos[] = $datas;
          
        }

        echo json_encode($arrayAnchos, JSON_UNESCAPED_UNICODE);
       
    
    }else{ 
        
        echo 'Ninguna llanta coincide con ese ancho';
    }   
        
    }else{
        print_r("Error al conectar");
    }
    ?>