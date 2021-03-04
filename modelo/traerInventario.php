<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    
    $sqlTraerInventario="SELECT * FROM llantas l INNER JOIN inventario_mat1 pedro
    ON pedro.id_Llanta = l.id ";

    $result = mysqli_query($con, $sqlTraerInventario);
    while ($datas=mysqli_fetch_assoc($result)){

    $arrayInven['data'][] = $datas;
    }

    echo json_encode($arrayInven, JSON_UNESCAPED_UNICODE);
    
    
    ?>