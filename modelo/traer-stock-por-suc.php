<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if ($_POST["sucursal"] == "total") {


        $codigo = $_POST["codigo"];
        $sqlContarLlantas= $con->prepare("SELECT COUNT(*) total FROM inventario WHERE id_Llanta = ?");
        
                                                                                         
               
               $sqlContarLlantas->bind_param('i', $codigo); 
               $sqlContarLlantas->execute();
               $sqlContarLlantas->bind_result($total);
               $sqlContarLlantas->fetch();
               $sqlContarLlantas->close();


        if ($total > 0) { 
                
            $traerStockPedro = $con->prepare("SELECT SUM(Stock) FROM inventario WHERE id_Llanta = ?");
        $traerStockPedro->bind_param('i', $codigo);
        $traerStockPedro->execute();
        $traerStockPedro->bind_result($totalStock);
        $traerStockPedro->fetch();
        $traerStockPedro->close();
        $data = array("stock"=>$totalStock);
           
        
        }else{ 
            
            $stock = 0;
            $data = array("stock" => $stock);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            
        } 

        echo json_encode($data, JSON_UNESCAPED_UNICODE);

         
    
        }else{


            $codigo1 = $_POST["codigo"];
            $codigo = intval($codigo1);
            $datoSuc = $_POST["sucursal"];
         
        
                
                $parametro = "%$codigo%";
                
                $sqlContarLlantas= $con->prepare("SELECT COUNT(*) total FROM inventario WHERE id_Llanta = ? AND id_sucursal =?");
        
                                                                                         
               
               $sqlContarLlantas->bind_param('ii', $codigo, $datoSuc); 
               $sqlContarLlantas->execute();
               $sqlContarLlantas->bind_result($total);
               $sqlContarLlantas->fetch();
               $sqlContarLlantas->close();
               
        
               if ($total > 0) { 
                
                $sqlTraerStock="SELECT SUM(Stock) as Stock FROM inventario WHERE id_Llanta  =  $codigo AND id_sucursal =$datoSuc";
                
                $resultado = mysqli_query($con, $sqlTraerStock);
               
                while($fila= $resultado->fetch_assoc()){
                    $stock = $fila["Stock"];
                    $data = array("stock" => $stock);
                }
        
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
               
            
            }else{ 
                
                $stock = 0;
                $data = array("stock" => $stock);
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
                
            } 



        }

        
    
    
    
    ?>