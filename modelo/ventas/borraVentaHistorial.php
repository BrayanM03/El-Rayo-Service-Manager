<?php

include '../conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST)) {
       
    $codigo = $_POST["folio"];
    
         $borrar_llanta= $con->prepare("DELETE FROM detalle_venta WHERE id_Venta = ?");
         $borrar_llanta->bind_param('i', $codigo);
         $borrar_llanta->execute();
         $borrar_llanta->close();

         $borrar_Venta = $con->prepare("DELETE FROM ventas WHERE id = ?");
         $borrar_Venta->bind_param('i', $codigo);
         $borrar_Venta->execute();
         $borrar_Venta->close();


         
        print_r(1); 
   
                  
}else{
    print_r("No se pudo establecer una conexión");
}


?>