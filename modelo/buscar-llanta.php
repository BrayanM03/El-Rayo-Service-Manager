<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST['ancho']) && is_numeric($_POST['ancho'])==true) {
     $ancho = $_POST['ancho'];
     $parametro = "%$ancho%";
     $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas WHERE Ancho LIKE ?");
    
     $query_mostrar->bind_param('s', $parametro);
     $query_mostrar->execute();
     $query_mostrar->bind_result($total);
     $query_mostrar->fetch();
     $query_mostrar->close();

     if ($total > 0) {
        
        $sqlTraerLlanta="SELECT * FROM llantas WHERE Ancho LIKE '%$ancho%'";
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