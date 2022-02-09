<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    $id_sucursal = $_POST["id_sucursal"];

    
    $sqlTraerInventario="SELECT * FROM llantas l INNER JOIN inventario inv
    ON inv.id_Llanta = l.id WHERE id_sucursal = $id_sucursal";

    $result = mysqli_query($con, $sqlTraerInventario);
    while ($datas=mysqli_fetch_assoc($result)){

    $arrayInven['data'][] = $datas;
    }

    echo json_encode($arrayInven, JSON_UNESCAPED_UNICODE);
    
    
    ?>