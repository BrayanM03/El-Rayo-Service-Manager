<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (isset($_POST["code"])) {
        
        $codigo =  $_POST["code"];
        
        print_r($codigo);
   
    

    }
    
    
    ?>