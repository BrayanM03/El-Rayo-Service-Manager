<?php
include '../../conexion.php';
$con = $conectando->conexion();

$consulta ="SELECT COUNT(*) FROM creditos WHERE estatus != 3 AND estatus != 5"; // WHERE estatus=4
$res =$con->prepare($consulta);

$res->execute();
$res->bind_result($total_creditos);
$res->fetch();
$res->close();
//print_r("el total de creditos son: ".$total_creditos);
$margen_dias = 7; // margen de días
$fecha_actual = date('Y-m-d'); // fecha actual
$hora_actual = date('h:i a'); // hora
$fecha_actual_ = new DateTime();
$categoria_notificacion = 'Aviso';
$estatus_notificacion_abierta = 0;
$estatus_notificacion_vista = 0;
$departamento_id = 1;
if($total_creditos>0)
{
    $traer_creditos = "SELECT c.*,s.nombre as nombre_sucursal, v.hora FROM creditos c
    LEFT JOIN ventas v ON v.id = c.id_venta INNER JOIN sucursal s ON v.id_sucursal =s.id WHERE c.estatus != 3 AND c.estatus != 5";
    $resp = $con->prepare($traer_creditos);
    $resp->execute();
    $respuesta = Arreglo_Get_Result($resp);
    $resp->close();
    
    $response = [];
    $contador=1;
    foreach ($respuesta as $key => $value) {
        
        $fecha_vencimiento = $value["fecha_final"]; // fecha de vencimiento
        $fecha_vencimiento_ = new DateTime($fecha_vencimiento);
        $fecha_limite = date('Y-m-d', strtotime($fecha_actual . ' + ' . $margen_dias . ' days')); // fecha límite (7 días después de la fecha actual)
        $diferencia = $fecha_vencimiento_->diff($fecha_actual_);
        $dias_restantes = $diferencia->days+1;
        $id_credito = $value['id'];

        $select_count = 'SELECT COUNT(*) FROM notificaciones WHERE id_credito = ?';
        $resp = $con->prepare($select_count);
        $resp->bind_param('s', $id_credito);
        $resp->execute();
        $resp->bind_result($notificaciones_found);
        $resp->fetch();
        $resp->close();

        $plazo_numerico=$value["plazo"];
        

        if ($fecha_vencimiento <= $fecha_limite && $fecha_vencimiento > $fecha_actual && $notificaciones_found==0 && $plazo_numerico !='6') {
           
        switch ($plazo_numerico) {
                case '1':
                  $plazo="7 dias";
                 break;
                 case '2':
                     $plazo="15 dias";
                 break;
                  case '3':
                      $plazo="1 mes";
                  break;
     
                  case '4':
                  $plazo="1 año";
                  break;
     
                  case '5':
                     $plazo="7 dias";
                     break;
     
                 case '6':
                     $plazos = '1 día';
                 break;
     
                 default:
                 $plazo="Sin definir";
                     # code...
                     break;
             }
        
        $cliente_id=$value["id_cliente"];
        $pagado=$value["pagado"];
        $restante=$value["restante"];
        $sucursal = $value["nombre_sucursal"];
        $total=$value["total"];
        $estatus="Vencido";

        $fecha_inicio=$value["fecha_inicio"] . ' ' . $value['hora'];
        $fecha_final=$value["fecha_final"]. ' ' . $value['hora'];
        
        $id_venta=$value["id_venta"];

        $consulta ="SELECT Nombre_Cliente FROM clientes WHERE id=?";
        $res =$con->prepare($consulta);
        $res->bind_param("i",$cliente_id);
        $res->execute();
        $res->bind_result($nombre_cliente);
        $res->fetch();
        $res->close();
        $mensaje =  "El crédito de $nombre_cliente está por vencerse dentro de los próximos $dias_restantes días.";
        $response[] = [
            'id_credito' => $id_credito,
            'id_venta' => $id_venta,
            'contador' => $contador,
            'nombre_cliente' => $nombre_cliente,
            'pagado' => "$" . $pagado,
            'restante' => "$" . $restante,
            'total' => "$" . $total,
            'estatus' => $estatus,
            'fecha_inicio' => $fecha_inicio,
            'fecha_final' => $fecha_final,
            'sucursal' => $sucursal,
            'mensaje' => $mensaje,
        ];

        $insert = "INSERT INTO notificaciones(mensaje, fecha, hora, id_credito, categoria, id_departamento) VALUES(?,?,?,?,?, 1)";
        $stmt = $con->prepare($insert);
        $stmt->bind_param('sssss', $mensaje, $fecha_actual, $hora_actual, $id_credito, $categoria_notificacion);
        $stmt->execute();
        $stmt->close();
        $id_notificacion = $con->insert_id;
        $count_usuarios = "SELECT count(*) FROM usuarios WHERE id_departamento = ?";
        $res =$con->prepare($count_usuarios);
        $res->bind_param("i",$departamento_id);
        $res->execute();
        $res->bind_result($total_usuarios_admin);
        $res->fetch();
        $res->close();

        if($total_usuarios_admin>0){
            $sel_usuarios = "SELECT * FROM usuarios WHERE id_departamento = ?";
            $res =$con->prepare($sel_usuarios);
            $res->bind_param("i",$departamento_id);
            $res->execute();
            $respuesta = Arreglo_Get_Result($res);
            $res->close();
            foreach ($respuesta as $key => $value) {
              $id_usuario = $value['id'];
              $insert = 'INSERT INTO notificaciones_usuarios (id_usuario, id_notificacion, estatus_vista, estatus_abierta) VALUES (?,?,?,?)';
              $stmt = $con->prepare($insert);
              $stmt->bind_param('ssss', $id_usuario, $id_notificacion, $estatus_notificacion_vista, $estatus_notificacion_abierta);
              $stmt->execute();
              $stmt->close();
            }
        }

        } else {
            $mensaje = 'El crédito no está por vencerse dentro de los próximos 7 días.';
        
        }
    }
    echo json_encode($response);
}
function Arreglo_Get_Result( $Statement ) {
    $RESULT = array();
    $Statement->store_result();
    for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
        $Metadata = $Statement->result_metadata();
        $PARAMS = array();
        while ( $Field = $Metadata->fetch_field() ) {
            $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
        }
        call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
        $Statement->fetch();
    } 
    return $RESULT;
}
?>