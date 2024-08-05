<?php
    
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {


       $codigo = $_POST["codigo"];

       $count = 'SELECT count(*) FROM llantas WHERE id = ?';
       $stmt = $con->prepare($count);
       $stmt->bind_param('i', $codigo);
       $stmt->execute();
       $stmt->bind_result($total_llantas);
       $stmt->fetch();
       $stmt->close();

       if($total_llantas>0){
        $sqlContarLlantas= $con->prepare("SELECT * FROM llantas WHERE id = ?");
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
 
             $data = array("id" => $codigo, "ancho" => $ancho,"alto" => $alto,"rin" => $rin, "descripcion"=>$descripcion, 
                                     "modelo" => $modelo, "marca"=> $marca, "costo"=> $costo, "precio"=>$precio, 
                                     'precio_promocion'=>$precio_promocion, 'promocion'=>$promocion,
                                     "mayoreo"=>$mayoreo, "fecha"=>$fecha,
                                     'construccion'=>$construccion,
                                     'aplicacion'=>$aplicacion,
                                     'tipo_vehiculo'=>$tipo_vehiculo,
                                     'urls' => $fila_imagenes);
        
                                     $response = array('estatus'=>true, 'mensaje'=>'Datos de la llanta encontrados', 'datos'=> $data);
       }else{
          $response = array('estatus'=>false, 'mensaje'=>'Datos de la llanta no encontrados', 'datos'=> []);
       }
 
    }else{
                 $response = array('estatus'=>false, 'mensaje'=>'No existe un dato POST', 'datos'=> []);
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);   

    
    
    ?>