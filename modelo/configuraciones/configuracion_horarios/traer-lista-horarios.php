
<?php

session_start();
include '../../conexion.php';
$con= $conectando->conexion(); 
include '../../helpers/response_helper.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../../login.php");
}

if (isset($_POST)) {
    $datos = $_POST;

    // Decodificar si es JSON
    if (is_string($datos)) {
        $datos = json_decode($datos, true);
    }

    // Iniciar transacción
    $con->begin_transaction();

    try {

        $sql = "SELECT count(*) FROM horarios WHERE estatus =1";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($total_horarios);
        $stmt->fetch();
        $stmt->close();

        if($total_horarios>0){
            $sql = "SELECT * FROM horarios WHERE estatus =1";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $fila = $resultado->fetch_all();
            $stmt->close();

            foreach ($resultado as $key => $value) {
                $sql = "SELECT * FROM detalle_horario WHERE id_horario =?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param('s', $value['id']);
                $stmt->execute();
                $resultado_detalle = $stmt->get_result();
                $fila_detalle = $resultado_detalle->fetch_all();
                $stmt->close();
                $fila[$key]['detalle_horario'] = $fila_detalle; 
            }

            $mensaje = "Se encontraron horarios";
            responder(true, $mensaje, 'success', $fila, true, true);
        }else{
            $mensaje = "No se encontrarón horarios";
            responder(false, $mensaje, 'error', [], true, true);
        }
        // Confirmar la transacción
        $con->commit();

       
    } catch (Exception $e) {
        $con->rollback(); // Revertir en caso de error
        $mensaje = "Error: " . $e->getMessage();
        responder(false, $mensaje, 'error', [], true, true);
    }
} else {
    responder(false, "No se recibieron datos", 'error', [], true, true);
}

?>