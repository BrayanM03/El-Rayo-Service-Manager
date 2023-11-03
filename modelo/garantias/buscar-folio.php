<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)) {
    date_default_timezone_set("America/Matamoros");
    $folio = $_POST['folio'];
    $select = "SELECT COUNT(*) FROM ventas WHERE id = ?";
    $stmt = $con->prepare($select);
    $stmt->bind_param('s', $folio);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();
    if($total>0){
        $select = "SELECT c.Nombre_Cliente as cliente, v.sucursal, v.id_sucursal, v.id_Cliente FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id WHERE v.id = ?";
        $stmt = $con->prepare($select);
        $stmt->bind_param('s',$folio);
        $stmt->execute();
        $stmt->bind_result($nombre_cliente, $sucursal, $id_sucursal, $id_cliente);
        $stmt->fetch();
        $stmt->close();

        $select_c = "SELECT id_Llanta, Cantidad, Importe FROM detalle_venta WHERE id_venta = ?";
        $stmt = $con->prepare($select_c);
        $stmt->bind_param('s', $folio);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $ids_llantas_arr= $get_result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if(count($ids_llantas_arr)>0){
            $id_llantas = array(); // Crear un array para almacenar los IDs de llantas
            $cantidades = array();
            $precios = array();
          
            foreach ($ids_llantas_arr as $row) {
                $cantidades[$row['id_Llanta']] = $row['Cantidad'];
                $precios[$row['id_Llanta']] = $row['Importe'];
                $id_llantas[] = $row['id_Llanta'];
            }
            $data = array();
            // Convertir el array en una cadena separada por comas
            $ids_llantas = implode(",", $id_llantas);
            $select_c = "SELECT * FROM llantas WHERE id IN ($ids_llantas)";
            $stmt = $con->prepare($select_c);
            $stmt->execute();
            $get_resultz = $stmt->get_result();
            $datos_llantas= $get_resultz->fetch_all(MYSQLI_ASSOC);
            foreach ($datos_llantas as $key => $value) {
                $id_llanta_actual = $value['id'];
                $cantidad_= $cantidades[$id_llanta_actual];
                $precio_= $precios[$id_llanta_actual];
                $value['Cantidad'] =$cantidad_;
                $value['Precio'] =$precio_;
                $data[] = $value;
            }
            $stmt->close();
        }

        $response = array(
            'estatus'=> true,
            'nombre_cliente'=>$nombre_cliente,
            'id_cliente'=>$id_cliente,
            'id_sucursal'=>$id_sucursal,
            'sucursal'=>$sucursal,
            'mensaje'=>'Se encontrarón los siguientes resultados',
            'data'=>$data
        );
    }else{
        $response = array(
            'estatus'=> false,
            'mensaje'=>'No se encontró resultado',
            'data'=>[]
        );
    }

    echo json_encode($response);
}