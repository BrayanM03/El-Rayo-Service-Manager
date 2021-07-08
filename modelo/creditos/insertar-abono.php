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
    $fecha = date("d-m-Y");
    $hora = date("h:i a");
    $metodo = $_POST["metodo"];
    $usuario = $_SESSION["user"];

     //Obtenemos estatus del credito
     $obtenerStatus = "SELECT estatus FROM creditos WHERE id = ?";
     $stmt = $con->prepare($obtenerStatus);
     $stmt->bind_param('i', $id_credito);
     $stmt->execute();
     $stmt->bind_result($estatus);
     $stmt->fetch(); 
     $stmt->close();
  
     if ($estatus !== 5) {
        $traerdata = "SELECT pagado, restante, total FROM creditos WHERE id = ?";
        $result = $con->prepare($traerdata);
        $result->bind_param('i',$id_credito);
        $result->execute();
        $result->bind_result($pagado, $restante, $total);
        $result->fetch();
        $result->close();
    
        $comproba = $abono + $pagado;
    
    
        if($comproba > $total){
            print_r (1);
        }else{
    
            $insertar_abono = "INSERT INTO abonos(id, id_credito, fecha, hora, abono, metodo_pago, usuario)
            VALUES(null,?,?,?,?,?,?)";
            $resultado = $con->prepare($insertar_abono);                     
            $resultado->bind_param('issdss', $id_credito, $fecha, $hora, $abono, $metodo, $usuario);
            $resultado->execute();
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
    
            if($pagado2 == $total2){
    
                $actualizar2 = "UPDATE creditos SET estatus= 3 WHERE id = ?";
                $res2 = $con->prepare($actualizar2);
                $res2->bind_param('i', $id_credito);
                $res2->execute();
                $res2->close();
    
                $data = array("pagado_nuevo"=> $pagado2, "restante_nuevo"=>$restante2);
    
            }else{
    
                $data = array("pagado_nuevo"=> $pagado2, "restante_nuevo"=>$restante2);
            }
        
            
        
            echo json_encode($data, JSON_UNESCAPED_UNICODE); 
    
        }
     }else if($estatus == 5){
         print_r(6);
     }
   
   

    
  

    
 

  
    
}


?>