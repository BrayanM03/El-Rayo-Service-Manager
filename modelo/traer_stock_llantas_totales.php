<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (isset($_POST["searchTerm"])) {
      
        $term = $_POST["searchTerm"];
        $parametro = "%$term%";
        $total =0;
        $sqlContarLlantas= $con->prepare("SELECT COUNT(*) total FROM llantas WHERE Descripcion LIKE ? 
                                                                             OR Marca LIKE ?
                                                                             OR Modelo LIKE ?");

                                                                                 
       
       $sqlContarLlantas->bind_param('sss', $parametro, $parametro, $parametro);
       $sqlContarLlantas->execute();
       $sqlContarLlantas->bind_result($total);
       $sqlContarLlantas->fetch();
       $sqlContarLlantas->close();
       

       if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT * FROM llantas WHERE Ancho       LIKE '%$term%'  
                                                  OR Proporcion  LIKE '%$term%'  
                                                  OR Diametro    LIKE '%$term%'
                                                  OR Modelo      LIKE '%$term%'  
                                                  OR Marca       LIKE '%$term%' 
                                                  OR Descripcion LIKE '%$term%'";
        
        $resultado = mysqli_query($con, $sqlTraerLlanta);
       
        while($fila= $resultado->fetch_assoc()){
            $id = $fila["id"];
            $descripcion = $fila["Descripcion"];
            $modelo = $fila["Modelo"];
            $marca = $fila["Marca"];
            $costo = $fila["precio_Inicial"];
            $precio = $fila["precio_Venta"];
            $data[] = array("id" => $id, "descripcion"=>$descripcion, "modelo" => $modelo, "marca"=> $marca,
                             "costo"=> $costo, "precio"=>$precio);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
       
    
    }else{ 
        
        $data[] = array("id" => 0, "Descripcion"=>"Sin resultados");
    }   



       /* $sqlTraerLlantas->bind_param('iisss', $parametro, $parametro, $parametro, $parametro, $parametro);
        $data = array();
        if ($sqlTraerLlantas->execute()) {
            $resultado = $sqlTraerLlantas->get_result();
            if ($resultado->num_rows > 0) {
                while($fila= $resultado->fetch_assoc()){
                    $id = $fila["id"];
                    $descripcion = $fila["Descripcion"];
                    $data = array("id" => $id, "text"=>$descripcion);
                }
                $sqlTraerLlantas->close();
            }else{
                $data = array("id" => 0, "text"=>"Sin resultados");
            }

            echo json_encode($arrayInven, JSON_UNESCAPED_UNICODE);

        }*/

    

   
    

    }
    
    
    ?>