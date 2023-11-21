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


$fecha = date("Y-m-d");
$hora = date("h:i a");
$metodo ="Sin definir";
$usuario = $_SESSION["nombre"];
$sucursal_user = $_SESSION["sucursal"];
$id_sucursal = $_SESSION['id_sucursal'];

if (isset($_POST)) {

    if (isset($_POST["id_cred"])) {

        $id_credito = $_POST["id_cred"];
       /*
        $comprobar = "SELECT COUNT(*) FROM abonos WHERE id_credito= ?";
        $r = $con->prepare($comprobar);
        $r->bind_param("i", $id_credito);
        $r->execute();
        $r->bind_result($abonos_enc);
        $r->fetch();
        $r->close(); */
        $data = [];

        $query="SELECT creditos.id, 
        creditos.id_cliente, 
        creditos.pagado, 
        creditos.restante, 
        creditos.total, 
        creditos.estatus, 
        creditos.fecha_inicio, 
        creditos.fecha_final, 
        creditos.plazo, 
        abonos.id , 
        abonos.fecha, 
        abonos.hora, 
        abonos.abono, 
        abonos.pago_efectivo, 
        abonos.pago_tarjeta, 
        abonos.pago_transferencia, 
        abonos.pago_cheque, 
        abonos.pago_sin_definir,
        abonos.usuario FROM creditos INNER JOIN abonos ON creditos.id = abonos.id_credito WHERE abonos.id_credito = $id_credito";
        $resultado = mysqli_query($con, $query);
    
        while($fila = $resultado->fetch_assoc()){
        $id = $fila["id"];
        $clienteid = $fila["id_cliente"];
        $pagado = $fila["pagado"];
        $restante = $fila["restante"];
        $total = $fila["total"];
        $estatus = $fila["estatus"];
        $fecha_inicio = $fila["fecha_inicio"];
        $fecha_final = $fila["fecha_final"];
        $plazo = $fila["plazo"];
        $abono_id = $fila["id"];
        $abono = $fila["abono"];
        $fecha_abono = $fila["fecha"];
        $hora_abono = $fila["hora"];
        $pago_efectivo = $fila["pago_efectivo"];
        $pago_tarjeta = $fila["pago_tarjeta"];
        $pago_transferencia = $fila["pago_transferencia"];
        $pago_cheque = $fila["pago_cheque"];
        $pago_sin_definir = $fila["pago_sin_definir"];
        $usuario = $fila["usuario"];
    
        $sqlcliente = "SELECT Nombre_Cliente FROM clientes WHERE id = ?";
        $stmt = $con->prepare($sqlcliente);
        $stmt->bind_param('i', $clienteid);
        $stmt->execute();
        $stmt->bind_result($cliente_name);
        $stmt->fetch();
        $stmt->close();
    
    
        $data["data"][] = array("id" => $id,"id_cliente"=>$clienteid, "fecha_inicial"=>$fecha_inicio,"fecha_final"=>$fecha_final, "restante" => $restante,
                        "pagado" => $pagado, "cliente"=>$cliente_name, "total"=>$total, "plazo"=>$plazo, "estatus"=>$estatus,"abono_id"=>$abono_id, "abono"=>$abono, "fecha_abono"=>$fecha_abono,
                    "hora_abono"=> $hora_abono, 
                    "pago_efectivo"=> $pago_efectivo,
                    "pago_tarjeta"=> $pago_tarjeta,
                    "pago_transferencia"=> $pago_transferencia,
                    "pago_cheque"=> $pago_cheque,
                    "pago_sin_definir"=> $pago_sin_definir,
                    "usuario"=>$usuario);
    
                      
    }
    print_r($fila);
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);  
    }else{

        $id_credito = $_POST["id_credito"];
        
        $comprobar = "SELECT COUNT(*) FROM abonos WHERE id_credito= ?";
        $r = $con->prepare($comprobar);
        $r->bind_param("i", $id_credito);
        $r->execute();
        $r->bind_result($abonos_enc);
        $r->fetch();
        $r->close();

        //print_r($abonos_enc);
        if($abonos_enc == 0){
            $data = array();
            $query="SELECT creditos.id, creditos.id_cliente, creditos.pagado, creditos.restante, creditos.total, creditos.estatus, creditos.fecha_inicio, creditos.fecha_final, creditos.plazo FROM creditos WHERE id = $id_credito";
        
            $resultado = mysqli_query($con, $query);
        
            
            while($fila = $resultado->fetch_assoc()){
            $id = $fila["id"];
            $clienteid = $fila["id_cliente"];
            $pagado = $fila["pagado"];
            $restante = $fila["restante"];
            $total = $fila["total"];
            $estatus = $fila["estatus"];
            $fecha_inicio = $fila["fecha_inicio"];
            $fecha_final = $fila["fecha_final"];
            $plazo = $fila["plazo"];
            $abono_id = $fila["id"];
            $abono = $fila["abono"];
            $fecha_abono = $fila["fecha"];
            $hora_abono = $fila["hora"];
            $metodo_pago = $fila["metodo_pago"];
            $usuario = $fila["usuario"];
        
            $sqlcliente = "SELECT Nombre_Cliente FROM clientes WHERE id = ?";
            $stmt = $con->prepare($sqlcliente);
            $stmt->bind_param('i', $clienteid);
            $stmt->execute();
            $stmt->bind_result($cliente_name);
            $stmt->fetch();
            $stmt->close();
        
        
            $data = array("id" => $id,"id_cliente"=>$clienteid, "fecha_inicial"=>$fecha_inicio,"fecha_final"=>$fecha_final, "restante" => $restante,
                            "pagado" => $pagado, "cliente"=>$cliente_name, "total"=>$total, "plazo"=>$plazo, "estatus"=>$estatus,"abono_id"=>$abono_id, "abono"=>$abono, "fecha_abono"=>$fecha_abono, "hora_abono"=> $hora_abono, "metodo_pago"=> $metodo_pago, "usuario"=>$usuario);
        
                          
        }
           /*  $estado = 0;
            $nuevo_pagado = 0;
            $metodo = "Sin definir";

              //Script que verifica la hora de corte actual
              include '../helpers/verificar-hora-corte.php';
            $sql = "INSERT INTO abonos(id, id_credito, fecha, hora, abono, metodo_pago, usuario, estado, sucursal, id_sucursal, fecha_corte, hora_corte) VALUES(null,?,?,?,?,?,?,?,?,?,?,?)";
            $res = $con->prepare($sql);
            $res->bind_param('issssssssss', $id_credito, $fecha, $hora, $nuevo_pagado, $metodo, $usuario, $estado, $sucursal_user, $id_sucursal, $fecha_corte, $hora_corte);
            $res->execute();
            $res->close(); */
        }else{
            $query="SELECT creditos.id, creditos.id_cliente, creditos.pagado, creditos.restante, creditos.total, creditos.estatus, creditos.fecha_inicio, creditos.fecha_final, creditos.plazo, abonos.id , abonos.fecha, abonos.hora,  abonos.abono, abonos.metodo_pago, abonos.usuario, abonos.sucursal FROM creditos INNER JOIN abonos ON creditos.id = abonos.id_credito WHERE abonos.id_credito = $id_credito";
        
            $resultado = mysqli_query($con, $query);
        
            
            while($fila = $resultado->fetch_assoc()){
            $id = $fila["id"];
            $clienteid = $fila["id_cliente"];
            $pagado = $fila["pagado"];
            $restante = $fila["restante"];
            $total = $fila["total"];
            $estatus = $fila["estatus"];
            $fecha_inicio = $fila["fecha_inicio"];
            $fecha_final = $fila["fecha_final"];
            $plazo = $fila["plazo"];
            $abono_id = $fila["id"];
            $abono = $fila["abono"];
            $fecha_abono = $fila["fecha"];
            $hora_abono = $fila["hora"];
            $metodo_pago = $fila["metodo_pago"];
            $usuario = $fila["usuario"];
        
            $sqlcliente = "SELECT Nombre_Cliente FROM clientes WHERE id = ?";
            $stmt = $con->prepare($sqlcliente);
            $stmt->bind_param('i', $clienteid);
            $stmt->execute();
            $stmt->bind_result($cliente_name);
            $stmt->fetch();
            $stmt->close();
        
        
            $data = array("id" => $id,"id_cliente"=>$clienteid, "fecha_inicial"=>$fecha_inicio,"fecha_final"=>$fecha_final, "restante" => $restante,
                            "pagado" => $pagado, "cliente"=>$cliente_name, "total"=>$total, "plazo"=>$plazo, "estatus"=>$estatus,"abono_id"=>$abono_id, "abono"=>$abono, "fecha_abono"=>$fecha_abono, "hora_abono"=> $hora_abono, "metodo_pago"=> $metodo_pago, "usuario"=>$usuario);
        
                          
        }
        //print_r($fila); 
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);   
       
    }


       
  

}else{
    print_r("No se pudo establecer una conexión");
}


?>