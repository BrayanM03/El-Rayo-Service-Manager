<?php
    session_start();
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../login.php");
    }
    

    if (isset($_POST["code"]) && isset($_POST["stock"]) ) {

        
        
        $codigo =  $_POST["code"];
        $stock = $_POST["stock"];

        if ($codigo == "") {
            print_r(3);
        }else if ($stock == 0){
            print_r(4);
        }else{

            
            $sqlcomprobar = "SELECT COUNT(*) total FROM inventario_mat1 WHERE id_Llanta = ?";
            $res = $con->prepare($sqlcomprobar);
            $res->bind_param('i', $codigo);
            $res->execute();
            $res->bind_result($total);
            $res->fetch();
            
            $res->close();

            

            if ($total == 0) {
                $sql = "SELECT COUNT(*) total FROM inventario_mat1";
                $result = mysqli_query($con, $sql);
                $fila = mysqli_fetch_assoc($result);
                $concatenar = intval($fila["total"])+ 1;
                
                $code = "PEDC" . $codigo;
                
                $sucursal = "Pedro Cardenas";
                
        
                //print_r($code);
        
                $query = "INSERT INTO inventario_mat1 (Codigo, id_Llanta, Sucursal, Stock) VALUES (?,?,?,?)";
                $resultado = $con->prepare($query);
                $resultado->bind_param('sisi', $code, $codigo, $sucursal,$stock );
                $resultado->execute();
                $resultado->close();
                
                print_r(1);
            }else if($total == 1){

                
                print_r(5);
            }

         
            
        }
        
    

    }else{
        print_r(2);
    }
    
    
    ?>