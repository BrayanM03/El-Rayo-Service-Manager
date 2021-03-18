<?php
    
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {


       $codigo = $_POST["codigo"];


       $sqlContarLlantas= $con->prepare("SELECT * FROM llantas WHERE id = ?");
       $sqlContarLlantas->bind_param('i', $codigo);
       $sqlContarLlantas->execute();
       $resultado = $sqlContarLlantas->get_result();
       $fila = $resultado->fetch_assoc();
       $sqlContarLlantas->close();
    
            $ancho = $fila["Ancho"];
            $alto = $fila["Proporcion"];
            $rin = $fila["Diametro"];
            $descripcion = $fila["Descripcion"];
            $modelo = $fila["Modelo"];
            $marca = $fila["Marca"];
            $costo = $fila["precio_Inicial"];
            $precio = $fila["precio_Venta"];
            $mayoreo = $fila["precio_Mayoreo"];
            $fecha = $fila["Fecha"];

            $data = array("id" => $codigo, "ancho" => $ancho,"alto" => $alto,"rin" => $rin, "descripcion"=>$descripcion, 
                                    "modelo" => $modelo, "marca"=> $marca, "costo"=> $costo, "precio"=>$precio, 
                                    "mayoreo"=>$mayoreo, "fecha"=>$fecha);
        
            
               
            
          //echo $fila["Descripcion"];  
             //print_r(" la segunda iteracion de la llanta ID " . $id . " tiene de stock ". $TotalStock);
        

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

      
       
    
    }else{
        print_r(2);
    }
        

    
    
    ?>