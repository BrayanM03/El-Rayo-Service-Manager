<?php

// Función para extraer las medidas de la descripción de la llanta

include '../../conexion.php';
$con= $conectando->conexion(); 
$ancho = empty($_POST['ancho']) ? 0 : $_POST['ancho']; 
$perfil = empty($_POST['perfil']) ? 0 : $_POST['perfil']; ; 
$rin=empty($_POST['rin']) ? 0 : $_POST['rin']; ; /* 
$id_marca = $_POST['id_marca'];  */
$medida = $_POST['medida']; 
$construccion = $_POST['construccion']; 
$estatus_medida = $_POST['estatus']; 
$stock_minimo = intval($_POST['stock_minimo']); 
$stock_maximo = empty($_POST['stock_maximo']) ? 0 : intval($_POST['stock_maximo']); 
$arreglo_ids_sucursales = $_POST['id_sucursal'];
$fecha_actual = date("Y-m-d H:i:s");


if(count($arreglo_ids_sucursales)>0){
    foreach ($arreglo_ids_sucursales as $id_sucursal_) {
        $id_sucursal = intval($id_sucursal_);
        $validar = "SELECT COUNT(*) FROM medidas_stock WHERE ancho =? AND perfil =? AND rin = ? AND id_sucursal = ? AND construccion = ?";
        $stmt = $con->prepare($validar);
        $stmt->bind_param('sssss', $ancho, $perfil, $rin, $id_sucursal, $construccion);
        $stmt->execute();
        $stmt->bind_result($total_medidas);
        $stmt->fetch();
        $stmt->close();

        if($total_medidas > 0){
            $mensaje = 'La medida ingresada ya existe';
            $estatus = false;
            break;
        }else{
            rellenarStockInventario($ancho, $perfil, $rin, $con, $id_sucursal, $stock_minimo, $stock_maximo);
           
            $insert = "INSERT INTO medidas_stock (id, descripcion, ancho, perfil, rin, construccion, id_sucursal, stock_minimo, stock_maximo, estatus, created_at) VALUES (null, ?,?,?,?,?,?,?,?,?,?)";
            $stmt = $con->prepare($insert);
            $stmt->bind_param('ssssssssss', $medida, $ancho, $perfil, $rin, $construccion, $id_sucursal, $stock_minimo, $stock_maximo, $estatus_medida, $fecha_actual);
            $stmt->execute();
            $stmt->close();
            $mensaje = 'Medida registrada correctamente';
            $estatus = true;
        }
    }
}else{
    $mensaje = 'No hay sucursales';
            $estatus = false;
}

$resp = array('estatus' => $estatus, 'mensaje' => $mensaje);
echo json_encode($resp);

function rellenarStockInventario($ancho, $perfil, $rin, $con, $id_sucursal, $stock_minimo, $stock_maximo){
    
    $stmt = $con->prepare("SELECT id FROM llantas WHERE Ancho =? AND Proporcion = ? AND Diametro = ?");
    $stmt->bind_param('sss', $ancho, $perfil, $rin);
    $stmt->execute();
    $ids_llantas =$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    //$stmt->free_result();
    $stmt->close();
    $ids = array_column($ids_llantas, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    // Prepare the UPDATE query with IN clause 
    $updateQuery = "UPDATE inventario SET stock_minimo = ?, stock_maximo = ? WHERE id_Llanta IN ($placeholders) AND id_sucursal = ?";
    $updateStmt = $con->prepare($updateQuery);
    
    // Bind parameters
    $types = str_repeat('i', count($ids)); // Assuming IDs are integers
    $params = array_merge([$stock_minimo, $stock_maximo], $ids, [$id_sucursal]);
    $updateStmt->bind_param($types.'iii', ...$params);
    
    // Execute the statement
    $updateStmt->execute();

    // Check for successful update
    /* if ($updateStmt->affected_rows > 0) {
        echo "Records updated successfully.";
    } else {
        echo "Error updating records.";
    } */

// Close the prepared statement
$updateStmt->close();
    
}
?>