<?php

// Función para extraer las medidas de la descripción de la llanta

include '../conexion.php';
$con= $conectando->conexion();

obtenerLlantas(11,0,22.5,$con);
function obtenerLlantas($ancho, $alto, $rin, $con){

    $query = "SELECT * FROM llantas WHERE Ancho =? AND Proporcion =? AND Diametro =?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('sss', $ancho, $alto, $rin);
    $stmt->execute();
    $ree = $stmt->get_result();
    $stmt->free_result();
    $stmt->close();
    foreach ($ree as $row) {
        $id_llanta = $row['id'];
        $response[] = $row;
        $query = "SELECT c.Nombre_Cliente as cliente, v.Fecha FROM ventas v INNER JOIN detalle_venta de ON de.id_venta = v.id
        INNER JOIN clientes c ON c.id = v.id_Cliente WHERE de.id_Llanta = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $id_llanta);
        $stmt->execute();
        $data = $stmt->get_result();
        $stmt->free_result();
        $stmt->close();
        foreach ($data as $fil) {
            $response['cliente'] = $fil['cliente'];
        }
    }

    echo json_encode($response);
}