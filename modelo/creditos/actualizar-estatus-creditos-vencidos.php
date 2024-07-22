<?php
    session_start();
    include '../conexion.php';
    $con= $conectando->conexion(); 
    
    date_default_timezone_set("America/Matamoros");

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../login.php");
    }

    $id_usuario = $_SESSION["id_usuario"];
    $rol = $_SESSION["rol"];

    $fecha_hoy = date("Y-m-d");    
    $hora_actual = date('h:i a');
    $fecha_hoy_hora_actual = date('Y-m-d h:i a'); //Esto srive por si queremos usar la hora para defirni vencidos
  
    $estatusvencido = 4;
    $res =0.00;
    $abierta = "Abierta"; 
     
    $update = "UPDATE creditos SET estatus = ? WHERE estatus <> 5 AND pagado <> total AND restante <> ? AND fecha_final <= ?";
    /* $update = "
    UPDATE creditos c
    JOIN ventas v ON c.id_venta = v.id
    SET c.estatus = ?
    WHERE c.estatus <> 5 
        AND c.pagado <> c.total 
        AND c.restante <> ? 
        AND (
            c.fecha_final < ? OR 
            (c.fecha_final = ? AND STR_TO_DATE(CONCAT(c.fecha_final, ' ', v.hora), '%Y-%m-%d %h:%i %p') <= STR_TO_DATE(?, '%Y-%m-%d %h:%i %p'))
        )"; */
    $result = $con->prepare($update);
    $result->bind_param('sss', $estatusvencido, $res, $fecha_hoy);
    //$result->bind_param('sssss', $estatusvencido, $res, $fecha_hoy, $fecha_hoy, $fecha_hoy_hora_actual); // Este bind param toma en cuenta la hora
    $result->execute();
    $result->close(); 
  

    $consulta = "SELECT COUNT(*) FROM creditos INNER JOIN ventas ON creditos.id_venta = ventas.id WHERE ventas.estatus <> 'Abierta' AND creditos.estatus = 4";
    $totr = $con->prepare($consulta);
    //$totr->bind_param('ii', $abierta, $estatusvencido);
    $totr->execute();
    $totr->bind_result($tot);
    $totr->fetch();
    $totr->close();

    $consulta2 = "SELECT COUNT(*) FROM creditos INNER JOIN ventas ON creditos.id_venta = ventas.id WHERE ventas.estatus <> 'Pagado' AND creditos.estatus = 3";
    $totrs = $con->prepare($consulta2);
    $totrs->execute();
    $totrs->bind_result($total2);
    $totrs->fetch();
    $totrs->close();

  

    if($tot == 0) {
        $data = array( "mensaje"=> "No se encontraron reportes cancelados en ventas pero vencidos en creditos", "OK"=>false);
    }else{
        $select = "SELECT creditos.id, creditos.id_venta, ventas.estatus AS 'venta_estatus', creditos.estatus AS 'estatus_vencido' FROM creditos INNER JOIN ventas ON creditos.id_venta = ventas.id WHERE ventas.estatus <> 'Abierta' AND creditos.estatus = 4";
        //$select = "SELECT ventas.id AS venta_id, ventas.estatus AS venta_estatus, creditos.estatus AS credito_estatus FROM creditos INNER JOIN ventas ON creditos.id_venta = ventas.id WHERE ventas.estatus <> 'Pagado' AND creditos.estatus = 3";
        $result = mysqli_query($con, $select);
        while ($row = $result->fetch_assoc()) {
            $id_credito = $row['id'];
            $estatus_reporte = $row['venta_estatus'];
    
            $data[] = $row;
    
            if($estatus_reporte == "Pagado"){
                $nuevo_estatus = 3;
            }else if($estatus_reporte == "Cancelada"){
                $nuevo_estatus = 5;
            }
            $update = "UPDATE creditos SET estatus = ? WHERE id= ?";
                $resultx = $con->prepare($update);
                $resultx->bind_param('ss', $nuevo_estatus, $id_credito);
                $resultx->execute();
                $resultx->close();
            
        }

    }

    if($total2 == 0) {
        $data2 = array( "mensaje"=> "No se encontraron reportes a creditos finalizados con venta abierta", "OK"=>false);
    }else{
       // $select = "SELECT creditos.id, creditos.id_venta, ventas.estatus AS venta_estatus, creditos.estatus AS 'estatus_vencido' FROM creditos INNER JOIN ventas ON creditos.id_venta = ventas.id WHERE ventas.estatus <> 'Abierta' AND creditos.estatus = 4";
        $select = "SELECT ventas.id AS venta_id, ventas.estatus AS venta_estatus, creditos.estatus AS credito_estatus FROM creditos INNER JOIN ventas ON creditos.id_venta = ventas.id WHERE ventas.estatus <> 'Pagado' AND creditos.estatus = 3";
        $result = mysqli_query($con, $select);
        while ($row = $result->fetch_assoc()) {
            $id_venta = $row['venta_id'];
            $estatus_reporte = $row['venta_estatus'];
            $estatus_credito = $row['credito_estatus'];
    
            $data2[] = $row;
    
             if($estatus_credito == "3"){
                $nuevo_estatus_vent = "Pagado";
            }
            $update = "UPDATE ventas SET estatus = ? WHERE id= ?";
                $resultx = $con->prepare($update);
                $resultx->bind_param('ss', $nuevo_estatus_vent, $id_venta);
                $resultx->execute();
                $resultx->close();
            
        }

    }

    $total_creditos_vencidos =0;
    $consulta2 = "SELECT COUNT(*) FROM creditos c WHERE c.estatus = 4";
    $totrs = $con->prepare($consulta2);
    $totrs->execute();
    $totrs->bind_result($total_creditos_vencidos);
    $totrs->fetch();
    $totrs->close();
    if($total_creditos_vencidos>0){
        $select = "SELECT id_cliente FROM creditos c WHERE c.estatus = 4";
        $result = mysqli_query($con, $select);
        $id_clientes = array();
        while ($row = $result->fetch_assoc()) {
            $id_clientes[] = $row['id_cliente'];
        }
        $id_clientes = implode(',', $id_clientes);
        $updt = "UPDATE clientes SET credito_vencido = 1 WHERE id IN ($id_clientes)";
        $totrs = $con->prepare($updt);
        $totrs->execute();
        $totrs->close();
    };
     $RESPONSE = array(
        "validacion1"=>$data,
        "validacion2"=>$data2,
    );

      echo json_encode($RESPONSE, JSON_UNESCAPED_UNICODE);

    
   

    ?>