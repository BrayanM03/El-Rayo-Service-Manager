<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {
        $ancho = $POST["Anchovalor"];
     //   $consulta = "SELECT * FROM llantas";

        print_r("Correcto" + $ancho);
    }
    ?>