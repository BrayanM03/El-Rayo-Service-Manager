<?php
    
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {


       $ancho = $_POST["ancho"];
       $proporcion = $_POST["proporcion"];
       $diametro = $_POST["diametro"];


       $sqlContarLlantas= $con->prepare("SELECT COUNT(*) FROM llantas WHERE Ancho = ? AND Proporcion =? AND Diametro = ?");
       $sqlContarLlantas->bind_param('sss', $ancho, $proporcion, $diametro);
       $sqlContarLlantas->execute();
       $sqlContarLlantas->bind_result($total_encontradas);
       $sqlContarLlantas->fetch();
       $sqlContarLlantas->close();

       if ($total_encontradas > 0) {//Enocntramos llantas que coinciden con las medidas en el catalogo
      
        
          $query = "SELECT * FROM llantas WHERE Ancho = '$ancho' AND Proporcion ='$proporcion' AND Diametro = '$diametro'";
          $resp = mysqli_query($con,$query);

          while($fila = $resp->fetch_assoc()) {
            $id_llanta = $fila["id"];
            $descripcion = $fila["Descripcion"];
            $precio = $fila["precio_Inicial"];
            $precio_mayoreo = $fila["precio_Mayoreo"];
            $marca = $fila["Marca"];

            $sqlContarLlantas= $con->prepare("SELECT COUNT(*) FROM inventario WHERE id_Llanta =?");
            $sqlContarLlantas->bind_param('s', $id_llanta);
            $sqlContarLlantas->execute();
            $sqlContarLlantas->bind_result($total);
            $sqlContarLlantas->fetch();
            $sqlContarLlantas->close();

            if ($total > 0) {
                $query2 = "SELECT * FROM inventario WHERE id_Llanta = '$id_llanta'";
                $resp2 = mysqli_query($con,$query2);

                while($fila2 = $resp2->fetch_assoc()) {
                    $id = $fila2["id"];
                    $codigo = $fila2["Codigo"];
                    $sucursal = $fila2["Sucursal"];
                    $stock = $fila2["Stock"];
                    $data[] = array("id" => $id,
                                    "codigo"=> $codigo,
                                    "sucursal"=> $sucursal,
                                    "stock"=> $stock,
                                    "descripcion"=>$descripcion,
                                    "precio"=>$precio,
                                    "precio_mayoreo"=> $precio_mayoreo,
                                    "marca"=> $marca,
                                    "id_llanta"=> $id_llanta);
                }

            }else{

            $data = array("mensj"=>"Si hay una llanta con esa medida en el catalogo pero por no el inventario.", "estatus"=>false);
            
            }


          }

         
         echo json_encode($data, JSON_UNESCAPED_UNICODE); 
          
       }else{
           $datos = array("mensj"=>"No hay llantas con esa medida en el catalogo.", "estatus"=>false);
        echo json_encode($datos);
       }
        


      
       
    
    }else{
        print_r(2);
    }
        

    
    
    ?>