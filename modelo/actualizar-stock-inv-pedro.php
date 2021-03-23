<?php

include 'conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST)) {


        $codigo = $_POST["codigo"];

        $stock  = $_POST["stock"];
       
    
         $editar_llanta= $con->prepare("UPDATE inventario_mat1 SET Stock = ? WHERE id_Llanta = ?");
         $editar_llanta->bind_param('ii', $stock, $codigo);
         $editar_llanta->execute();
         $editar_llanta->close();
         
        print_r(1);



}else{
    print_r(2);
}


?>