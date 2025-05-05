
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

    // Validar que los datos sean un array
    if (!is_array($datos)) {
        echo json_encode(["status" => "error", "message" => "Error en los datos enviados"]);
        exit;
    }

    // Capturar el nombre del horario
    $nombre_horario = isset($datos['nombre-horario']) ? $datos['nombre-horario'] : '';

    if ($nombre_horario == '') {
        echo json_encode(["status" => "error", "message" => "El nombre del horario es obligatorio"]);
        exit;
    }

    // Iniciar transacción
    $con->begin_transaction();

    try {
        // Insertar en la tabla `horarios`
        $sql = "INSERT INTO horarios (nombre, fecha, estatus) VALUES (?, NOW(), 1)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $nombre_horario);
        $stmt->execute();

        // Obtener el ID del horario insertado
        $id_horario = $stmt->insert_id;
        $stmt->close();

        // Insertar los detalles del horario (hora-inicio y hora-fin por día)
        $sql_detalle = "INSERT INTO detalle_horario (dia, hora_inicio, hora_fin, id_horario) VALUES (?, ?, ?, ?)";
        $stmt_detalle = $con->prepare($sql_detalle);

        for ($dia = 1; $dia <= 7; $dia++) {
            if (isset($datos["hora-inicio-$dia"]) && isset($datos["hora-fin-$dia"])) {
                $hora_inicio = $datos["hora-inicio-$dia"];
                $hora_fin = $datos["hora-fin-$dia"];

                $stmt_detalle->bind_param("issi", $dia, $hora_inicio, $hora_fin, $id_horario);
                $stmt_detalle->execute();
            }
        }

        $stmt_detalle->close();

        // Confirmar la transacción
        $con->commit();

        $mensaje = "Horario registrado correctamente";
        responder(true, $mensaje, 'success', [], true, true);
    } catch (Exception $e) {
        $con->rollback(); // Revertir en caso de error
        $mensaje = "Error: " . $e->getMessage();
        responder(false, $mensaje, 'error', [], true, true);
    }
} else {
    responder(false, "No se recibieron datos", 'error', [], true, true);
}

?>