<?php
    
    session_start();
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../../login.php");
    }

    if (isset($_POST)) { 

       

     $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l");

     //-----------------------------------------------------------------------------------------------------//
     //-----------------------------------------------------------------------------------------------------/
     $query_mostrar->execute();
     $query_mostrar->bind_result($total);
     $query_mostrar->fetch();
     $query_mostrar->close();


     if ($total > 0) { 
        
        $sqlTraerLlanta="SELECT l.* FROM llantas l";
        $resultado = mysqli_query($con, $sqlTraerLlanta);
       
        while($fila= $resultado->fetch_assoc()){

            $id = $fila["id"];

            //print_r("El ID de la llanta es ".$id ." - ");
           
           
             if ($result = mysqli_query($con,"SELECT Stock FROM inventario_mat1 WHERE id_Llanta = $id")) {

                $row_cnt = mysqli_num_rows($result);
                //echo "Para la llanta " . $id . " se encontraron " .$row_cnt . "filas<br>";
                // Free result set
                mysqli_free_result($result);

                if($row_cnt == 0){
                    $pedro = 0;
                }else{
                    $sqlContarLlantas= $con->prepare("SELECT Stock FROM inventario_mat1 WHERE id_Llanta = $id");
                    $sqlContarLlantas->execute();
                    $sqlContarLlantas->bind_result($pedro);
                    $sqlContarLlantas->fetch();
                    $sqlContarLlantas->close();
                }
              }

              if ($result2 = mysqli_query($con,"SELECT Stock FROM inventario_mat2 WHERE id_Llanta = $id")) {

                $row_cnt2 = mysqli_num_rows($result2);
                //echo "Para la llanta " . $id . " se encontraron " .$row_cnt . "filas<br>";
                // Free result set
                mysqli_free_result($result2);

                if($row_cnt2 == 0){
                    $sendero = 0;
                }else{
                    $sqlContarLlantas2= $con->prepare("SELECT Stock FROM inventario_mat2 WHERE id_Llanta = $id");
                    $sqlContarLlantas2->execute();
                    $sqlContarLlantas2->bind_result($sendero);
                    $sqlContarLlantas2->fetch();
                    $sqlContarLlantas2->close();
                }
              }


         
            //print_r(" la llanta ID " . $id . " tiene de stock ". $TotalStock);

            $TotalStock = $pedro + $sendero;

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

            $data['data'][] = array("id" => $id, "ancho" => $ancho,"alto" => $alto,"rin" => $rin, "descripcion"=>$descripcion, 
                                    "modelo" => $modelo, "marca"=> $marca, "costo"=> $costo, "precio"=>$precio, 
                                    "mayoreo"=>$mayoreo, "fecha"=>$fecha, "stock"=>$TotalStock);
               
            
            
             //print_r(" la segunda iteracion de la llanta ID " . $id . " tiene de stock ". $TotalStock);
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
       
    
    }else{ 
        
        echo 'Ninguna llanta coincide con ese ancho';
    }   
        
    }else{
        print_r("Error al conectar");
    }
    
    
    ?>