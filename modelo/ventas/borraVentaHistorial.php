<?php

include '../conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST)) {
       
    $codigo = $_POST["folio"];
    $tipo_de_venta = $_POST["tipo"];
         $borrar_llanta= $con->prepare("DELETE FROM detalle_venta WHERE id_Venta = ?");
         $borrar_llanta->bind_param('i', $codigo);
         $borrar_llanta->execute();
         $borrar_llanta->close();

         $borrar_Venta = $con->prepare("DELETE FROM ventas WHERE id = ?");
         $borrar_Venta->bind_param('i', $codigo);
         $borrar_Venta->execute();
         $borrar_Venta->close();

         //Revisar el tipo de venta, si es igual a 2 (Venta de credito) se borra tambien del historial de creditos junto con los abonos.
         if ($tipo_de_venta == 2) {
            
            $traer_id_credito = $con->prepare("SELECT id FROM creditos WHERE id_venta = ?");
            $traer_id_credito->bind_param('i', $codigo);
            $traer_id_credito->execute();
            $traer_id_credito->bind_result($id_credito);
            $traer_id_credito->fetch();
            $traer_id_credito->close();

            $borrar_Credito = $con->prepare("DELETE FROM abonos WHERE id_credito = ?");
            $borrar_Credito->bind_param('i', $id_credito);
            $borrar_Credito->execute();
            $borrar_Credito->close();

            $borrar_Credito = $con->prepare("DELETE FROM creditos WHERE id_venta = ?");
            $borrar_Credito->bind_param('i', $codigo);
            $borrar_Credito->execute();
            $borrar_Credito->close();
         }


         
        print_r(1); 
   
                  
}else{
    print_r("No se pudo establecer una conexión");
}


?>