<?php
    
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {


       $codigo = $_POST['id_llanta'];

       $count = 'SELECT count(*) FROM llantas WHERE id = ?';
       $stmt = $con->prepare($count);
       $stmt->bind_param('i', $codigo);
       $stmt->execute();
       $stmt->bind_result($total_llantas);
       $stmt->fetch();
       $stmt->close();

       if($total_llantas>0){
        $sqlContarLlantas= $con->prepare("
        SELECT l.*, 
            a.nombre as nombre_aplicacion, a.descripcion as descripcion_aplicacion,
            tv.nombre as nombre_tipo_vehiculo, tv.descripcion as descripcion_tipo_vehiculo,
            ic1.kg_max as kg_max_1, ic2.kg_max as kg_max_2,
            iv.codigo as codigo_velocidad, iv.velocidad_max
        FROM llantas l 
        LEFT JOIN aplicacion a ON a.id = l.id_aplicacion 
        LEFT JOIN tipo_vehiculos tv ON tv.id = l.id_tipo_vehiculo 
        LEFT JOIN indices_carga ic1 ON ic1.id = l.id_indice_carga_1 
        LEFT JOIN indices_carga ic2 ON ic2.id = l.id_indice_carga_2 
        LEFT JOIN indices_velocidad iv ON iv.id = l.id_indice_velocidad
        WHERE l.id = ?
    ");
        $sqlContarLlantas->bind_param('i', $codigo);
        $sqlContarLlantas->execute();
        $resultado = $sqlContarLlantas->get_result();
        $fila = $resultado->fetch_assoc();
        $sqlContarLlantas->close();
        
        //Trayendo urls de imagenes
        $sel = 'SELECT * FROM llantas_imagenes WHERE id_llanta = ?';
        $stmt= $con->prepare($sel);
        $stmt->bind_param('i', $codigo);
        $stmt->execute();
        $resultado_ = $stmt->get_result();
        $fila_imagenes = $resultado_->fetch_assoc();
        $stmt->close();
     
             $ancho = $fila['Ancho'];
             $alto = $fila['Proporcion'];
             $rin = $fila['Diametro'];
             $descripcion = $fila['Descripcion'];
             $modelo = $fila['Modelo'];
             $marca = $fila['Marca'];
             $costo = $fila['precio_Inicial'];
             $precio = $fila['precio_Venta'];
             $mayoreo = $fila['precio_Mayoreo'];
             $precio_promocion = $fila['precio_promocion'];
             $promocion = $fila['promocion'];
             $fecha = $fila['Fecha'];
             $construccion = $fila['construccion'];
             $aplicacion = $fila['id_aplicacion'];
             $tipo_vehiculo = $fila['id_tipo_vehiculo'];
             $indice_carga_1 = $fila['id_indice_carga_1'];
             $indice_carga_2 = $fila['id_indice_carga_2'];
             $indice_velocidad = $fila['id_indice_velocidad'];
             $posicion = $fila['posicion'];
             $psi = $fila['psi'];
 
             $nombre_aplicacion = $fila['nombre_aplicacion'];
             $descripcion_aplicacion = $fila['descripcion_aplicacion'];
             $nombre_tipo_vehiculo = $fila['nombre_tipo_vehiculo'];
             $descripcion_tipo_vehiculo = $fila['descripcion_tipo_vehiculo'];
             $kg_max_1 = $fila['kg_max_1'];
             $kg_max_2 = $fila['kg_max_2'];
             $codigo_velocidad = $fila['codigo_velocidad'];
             $velocidad_max = $fila['velocidad_max'];

             $data = array("id" => $codigo, "ancho" => $ancho,"alto" => $alto,"rin" => $rin, "descripcion"=>$descripcion, 
                                     "modelo" => $modelo, "marca"=> $marca, "costo"=> $costo, "precio"=>$precio, 
                                     'precio_promocion'=>$precio_promocion, 'promocion'=>$promocion,
                                     "mayoreo"=>$mayoreo, "fecha"=>$fecha,
                                     'construccion'=>$construccion,
                                     'aplicacion'=>$aplicacion,
                                     'tipo_vehiculo'=>$tipo_vehiculo,
                                     'urls' => $fila_imagenes,
                                     'indice_carga_1'=> $indice_carga_1,
                                     'indice_carga_2'=> $indice_carga_2,
                                     'indice_velocidad'=> $indice_velocidad,
                                     'posicion'=> $posicion,
                                      'psi'=> $psi,
                                      "nombre_aplicacion" => $nombre_aplicacion,
                                      "descripcion_aplicacion" => $descripcion_aplicacion,
                                      "nombre_tipo_vehiculo" => $nombre_tipo_vehiculo,
                                      "descripcion_tipo_vehiculo" => $descripcion_tipo_vehiculo,
                                      "kg_max_1" => $kg_max_1,
                                      "kg_max_2" => $kg_max_2,
                                      "codigo_velocidad" => $codigo_velocidad,
                                      "velocidad_max" => $velocidad_max
                                    );
        
                                     $response = array('estatus'=>true, 'mensaje'=>'Datos de la llanta encontrados', 'datos'=> $data);
       }else{
          $response = array('estatus'=>false, 'mensaje'=>'Datos de la llanta no encontrados', 'datos'=> []);
       }
 
    }else{
                 $response = array('estatus'=>false, 'mensaje'=>'No existe un dato POST', 'datos'=> []);
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);   

    
    
    ?>