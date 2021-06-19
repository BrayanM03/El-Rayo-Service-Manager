<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}



if ($_SESSION["rol"]  !== "1" ) {

    header("Location:../../notfound_404.php");
 
}

if(isset($_POST)){
    $id_venta = $_POST["id_venta"];

    //Conseguir susucusal

    $obtenerSuc = "SELECT id_Sucursal FROM ventas WHERE id LIKE ?";
    $stmt = $con->prepare($obtenerSuc);
    $stmt->bind_param('i', $id_venta);
    $stmt->execute();
    $stmt->bind_result($sucursal);
    $stmt->fetch(); 
    $stmt->close();

    if($sucursal == "Pedro"){
        $tabla_suc = "1";
    }else if($sucursal == "Sendero"){
        $tabla_suc = "2";
    }

    //Continuamos con la validad de llantas con esa venta
    $obtenerCant = "SELECT COUNT(*) total FROM detalle_venta WHERE id_Venta = ?";
                $stmt = $con->prepare($obtenerCant);
                $stmt->bind_param('i', $id_venta);
                $stmt->execute();
                $stmt->bind_result($total);
                $stmt->fetch(); 
                $stmt->close();

                if ($total == 0) {
                    print_r(0);
                }else{
                    
                    $llantasaDevolver = "SELECT id_Llanta, Cantidad FROM detalle_venta WHERE id_Venta = ?";
                    $stmt = $con->prepare($llantasaDevolver);
                    $stmt->bind_param('s', $id_venta);
                    $stmt->execute();
                    $resultado = $stmt->get_result(); 
                    $stmt->close();
                           
                    while ($row = $resultado->fetch_array()) {
                      $id_llanta = $row["id_Llanta"];  
                      $cantidad = $row["Cantidad"];
                     

                    //Cotejamos las cantidad para luego sumarlas  
                    $obtenerStock = "SELECT Stock FROM inventario_mat$tabla_suc WHERE id_Llanta LIKE ?";
                    $stmt = $con->prepare($obtenerStock);
                    $stmt->bind_param('i', $id_llanta);
                    $stmt->execute();
                    $stmt->bind_result($stock_actual);
                    $stmt->fetch(); 
                    $stmt->close();
                    
                    $cantidad_total = $cantidad + $stock_actual;

                      $editar_llanta= $con->prepare("UPDATE inventario_mat$tabla_suc SET Stock = ? WHERE id_Llanta = ?");
                      $editar_llanta->bind_param('ii', $cantidad_total, $id_llanta);
                      $editar_llanta->execute();
                      $editar_llanta->close();
                      print_r(1);

                    }
                   
                   
                }

              

}


?>