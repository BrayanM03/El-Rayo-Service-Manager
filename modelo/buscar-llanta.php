<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST['ancho'])) {
     $ancho = $_POST['ancho'];
     $parametro = "%$ancho%";
     $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas WHERE Ancho LIKE ? OR Proporcion LIKE ? OR Diametro LIKE ?
     OR Modelo LIKE ? OR Marca LIKE ? OR Descripcion LIKE ?");
    
     $query_mostrar->bind_param('ssssss', $parametro, $parametro, $parametro, $parametro, $parametro, $parametro);
     $query_mostrar->execute();
     $query_mostrar->bind_result($total);
     $query_mostrar->fetch();
     $query_mostrar->close();

    //$querySucursal = $con->prepare("SELECT OR INNER JOIN sucursal ON llantas.id_Sucursal = sucursal.id")

     if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT * FROM llantas WHERE Ancho LIKE '%$ancho%'  OR Proporcion LIKE '%$ancho%'  OR Diametro LIKE '%$ancho%'
         OR modelo LIKE '%$ancho%'  OR Marca LIKE '%$ancho%' OR Descripcion LIKE '%$ancho%'";
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