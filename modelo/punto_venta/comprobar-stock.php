<?php
    
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {
    
    
    }else{
        $respuesta = array('estatus' =>false);
    }

    echo json_encode($respuesta);