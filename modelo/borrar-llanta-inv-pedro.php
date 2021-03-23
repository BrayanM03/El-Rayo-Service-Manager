<?php

include 'conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST)) {


        $codigo = $_POST["codigo"];
    
         $editar_llanta= $con->prepare("DELETE FROM inventario_mat1 WHERE id_Llanta = ?");
         $editar_llanta->bind_param('i', $codigo);
         $editar_llanta->execute();
         $editar_llanta->close();
         
        print_r(1);



}else{
    print_r(2);
}


?>