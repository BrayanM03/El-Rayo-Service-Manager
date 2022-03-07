<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}


if(isset($_POST)){

    $id_credito = $_POST["id-credito"];
    $abono = $_POST["abono"];
    $fecha = date("Y-m-d");
    $hora = date("h:i a");
    $metodo = $_POST["metodo"];
    $usuario = $_SESSION["nombre"];

     //Obtenemos estatus del credito
     $obtenerStatus = "SELECT estatus FROM creditos WHERE id = ?";
     $stmt = $con->prepare($obtenerStatus);
     $stmt->bind_param('i', $id_credito);
     $stmt->execute();
     $stmt->bind_result($estatus);
     $stmt->fetch(); 
     $stmt->close();
  
     if ($estatus !== 5) {
        $traerdata = "SELECT pagado, restante, total, id_venta FROM creditos WHERE id = ?";
        $result = $con->prepare($traerdata);
        $result->bind_param('i',$id_credito);
        $result->execute();
        $result->bind_result($pagado, $restante, $total, $id_venta_otro);
        $result->fetch();
        $result->close();

        $traerdata = "SELECT id_sucursal FROM ventas WHERE id = ?";
        $result = $con->prepare($traerdata);
        $result->bind_param('i',$id_venta_otro);
        $result->execute();
        $result->bind_result($id_sucursal);
        $result->fetch();
        $result->close();

        $querySuc = "SELECT nombre FROM sucursal WHERE id =?";
        $resp=$con->prepare($querySuc);
        $resp->bind_param('i', $id_sucursal);
        $resp->execute();
        $resp->bind_result($sucursal);
        $resp->fetch();
        $resp->close();
    
        $comproba = $abono + $pagado;
    
    
        if($comproba > $total){
            print_r(1);
        }else{
    
            if ($comproba == $total) {
                $estado = 1; //Creditos pagado
            }else{
                $estado = 0; //Aun sin pagar
            }
            $insertar_abono = "INSERT INTO abonos(id, id_credito, fecha, hora, abono, metodo_pago, usuario, estado, sucursal, id_sucursal) VALUES(null,?,?,?,?,?,?,?,?,?)";
            $resultado = $con->prepare($insertar_abono);  
            $resultado->bind_param('issssssss', $id_credito, $fecha, $hora, $abono, $metodo, $usuario, $estado, $sucursal, $id_sucursal);
            $resultado->execute();
           
            if ($resultado == true) {
              
            $resultado->close();
            
            $pagado_update = $pagado + $abono;
            $restante_update = $restante - $abono;
    
    
            $actualizar = "UPDATE creditos SET pagado = ?, restante = ?, estatus= 2 WHERE id = ?";
            $res = $con->prepare($actualizar);
            $res->bind_param('ddi', $pagado_update, $restante_update, $id_credito);
            $res->execute();
            $res->close();
            
        
            $traerdatadenew = "SELECT pagado, restante, total FROM creditos WHERE id = ?";
            $result = $con->prepare($traerdatadenew);
            $result->bind_param('i',$id_credito);
            $result->execute();
            $result->bind_result($pagado2, $restante2, $total2);
            $result->fetch();
            $result->close();
    
            //En este if actualizamos el estatus del credito en caso de que se haya pagado completamente
            if($pagado2 == $total2){
    
                $actualizar2 = "UPDATE creditos SET estatus= 3 WHERE id = ?";
                $res2 = $con->prepare($actualizar2);
                $res2->bind_param('i', $id_credito);
                $res2->execute();
                $res2->close();

                $actualizar2 = "SELECT id_venta FROM creditos WHERE id = ?";
                $res2 = $con->prepare($actualizar2);
                $res2->bind_param('i', $id_credito);
                $res2->execute();
                $res2->bind_result($id_venta);
                $res2->fetch();
                $res2->close();

                $new_status = "Pagado";
                $actualizar2 = "UPDATE ventas SET estatus = ? WHERE id = ?";
                $res2 = $con->prepare($actualizar2);
                $res2->bind_param('si', $new_status, $id_venta);
                $res2->execute();
                $res2->close();

    
                $data = array("pagado_nuevo"=> $pagado2, "restante_nuevo"=>$restante2);
    
            }else{
    
                $data = array("pagado_nuevo"=> $pagado2, "restante_nuevo"=>$restante2);
            }
        
            
        
            echo json_encode($data, JSON_UNESCAPED_UNICODE); 

            }else{
                echo "Error";
                $resultado->close();
            }
            
    
        }
     }else if($estatus == 5){
         print_r(6);
     }
   
   

    
  

    
 

  
    
}


?>