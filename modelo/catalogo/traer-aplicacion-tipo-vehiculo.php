<?php
    
    include '../conexion.php';
    include '../helpers/response_helper.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "No existe un objecto de conexión";
        die();
    }

    if($_POST){
        $id_tipo_vehiculo = $_POST['id_tipo_vehiculo'];
        $sel = "SELECT count(*) FROM aplicacion WHERE id_tipo_vehiculo=?";
        $stmt = $con->prepare($sel);
        $stmt->bind_param('s', $id_tipo_vehiculo);
        $stmt->execute();
        $stmt->bind_result($total_aplicacion);
        $stmt->fetch();
        $stmt->close();

        if($total_aplicacion>0){
            $sqlContarLlantas= $con->prepare("SELECT * FROM aplicacion WHERE id_tipo_vehiculo = ?");
            $sqlContarLlantas->bind_param('i', $id_tipo_vehiculo);
            $sqlContarLlantas->execute();
            $resultado = $sqlContarLlantas->get_result();
            
            while ($fila = $resultado->fetch_assoc()) {
                $data[] = [
                    'id' => $fila['id'],
                    'nombre' => $fila['nombre']
                ];
            }
            $sqlContarLlantas->close();
            responder(true,  "Se encontrarón aplicaciones", 'success', $data, true,true);  
        }else{
            responder(false,  "No se encontrarón aplicaciones", 'dannger', [], true,true);  
        }
        
    }
    ?>