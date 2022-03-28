<?php

include 'conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST)) {


        $codigo = $_POST["codigo"];
    
         $editar_llanta= $con->prepare("DELETE FROM llantas WHERE id = ?");
         $editar_llanta->bind_param('i', $codigo);
         $editar_llanta->execute();
         $editar_llanta->close();

         $editar_llanta= $con->prepare("DELETE FROM inventario WHERE id = ?");
         $editar_llanta->bind_param('i', $codigo);
         $editar_llanta->execute();
         $editar_llanta->close();
         
        if($editar_llanta){
            print_r(1);
        }else{
            print_r(2);
        }


}else{
    print_r(2);
}


?>