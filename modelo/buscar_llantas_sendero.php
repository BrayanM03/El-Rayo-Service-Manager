<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST['ancho'])) {
     $ancho = $_POST['ancho'];
     $parametro = "%$ancho%";
     $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l INNER JOIN inventario_mat2 sendero
                                                                          ON sendero.id_Llanta = l.id 
                                                                          WHERE l.Ancho LIKE ? 
                                                                          OR l.Proporcion LIKE ? 
                                                                          OR l.Diametro LIKE ?
                                                                          OR l.Modelo LIKE ? 
                                                                          OR l.Marca LIKE ? 
                                                                          OR l.Descripcion LIKE ?");
    
     $query_mostrar->bind_param('ssssss', $parametro, $parametro, $parametro, $parametro, $parametro, $parametro);
     $query_mostrar->execute();
     $query_mostrar->bind_result($total);
     $query_mostrar->fetch();
     $query_mostrar->close();

    //$querySucursal = $con->prepare("SELECT OR INNER JOIN sucursal ON llantas.id_Sucursal = sucursal.id")

     if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT l.*, sendero.id, sendero.Sucursal, sendero.Stock FROM llantas l INNER JOIN inventario_mat2 sendero
                                                                    ON sendero.id_Llanta = l.id 
                                                                    WHERE l.Ancho LIKE '%$ancho%'  
                                                                    OR l.Proporcion LIKE '%$ancho%'  
                                                                    OR l.Diametro LIKE '%$ancho%'
                                                                    OR l.modelo LIKE '%$ancho%'  
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