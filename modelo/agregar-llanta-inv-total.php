<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST["modelo"])) {
      $respuesta =  $_POST["modelo.rin"];
      

      print_r("Hay conexion " . $respuesta);
        
    }else{
        print_r("Error al conectar");
    }
    ?>