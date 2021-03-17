<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (isset($_POST["codigo"])) {

        $codigo1 = $_POST["codigo"];
        $codigo = intval($codigo1);
        $datoSuc = $_POST["sucursal"];

        if ($datoSuc == "pedro" || $datoSuc == "sendero") {

            if ($datoSuc == "pedro") {
                $sucursal = "inventario_mat1";
            }else if($datoSuc == "sendero"){
                $sucursal = "inventario_mat2";
            }
    
            
            $parametro = "%$codigo%";
            
            $sqlContarLlantas= $con->prepare("SELECT COUNT(*) total FROM $sucursal WHERE id_Llanta = ?");
    
                                                                                     
           
           $sqlContarLlantas->bind_param('i', $codigo); 
           $sqlContarLlantas->execute();
           $sqlContarLlantas->bind_result($total);
           $sqlContarLlantas->fetch();
           $sqlContarLlantas->close();
           
    
           if ($total > 0) { 
            
            $sqlTraerStock="SELECT Stock FROM $sucursal WHERE id_Llanta  =  $codigo";
            
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
    
        }else if($_POST["sucursal"] == "total"){

            $codigo = $_POST["codigo"];
            
            $traerStockPedro = $con->prepare("SELECT Stock FROM inventario_mat1 WHERE id_Llanta = ?");
            $traerStockPedro->bind_param('i', $codigo);
            $traerStockPedro->execute();
            $traerStockPedro->bind_result($stockPedro);
            $traerStockPedro->fetch();
            $traerStockPedro->store_result();
            $row_cnt = $traerStockPedro->num_rows();
           

            $traerStockPedro->close();

            $traerStockSendero = $con->prepare("SELECT Stock FROM inventario_mat2 WHERE id_Llanta = ?");
            $traerStockSendero->bind_param('i', $codigo);
            $traerStockSendero->execute();
            $traerStockSendero->bind_result($stockSendero);
            $traerStockSendero->fetch();
            $traerStockSendero->store_result();
            $row_cnt2 = $traerStockSendero->num_rows();
            
            $traerStockSendero->close();

            $totalStock = $stockPedro + $stockSendero;
            $data = array("stock"=>$totalStock);

            echo json_encode($data, JSON_UNESCAPED_UNICODE);



        }

        
    }
    
    
    ?>