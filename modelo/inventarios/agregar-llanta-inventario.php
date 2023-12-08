<?php
    session_start();
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../../login.php");
    }
    

    if (isset($_POST["code"]) && isset($_POST["stock"]) ) {
        
        date_default_timezone_set("America/Matamoros");
        
        $codigo =  $_POST["code"];
        $stock = $_POST["stock"];
        $id_usuario = $_SESSION["id_usuario"]; 
        $sucursal_id = $_POST["sucursal_id"];

        if ($codigo == "") {
            print_r(3);
        }else if ($stock == 0){
            print_r(4);
        }else{

            
            $sqlcomprobar = "SELECT COUNT(*) total FROM inventario WHERE id_Llanta = ? AND id_sucursal =?";
            $res = $con->prepare($sqlcomprobar);
            $res->bind_param('ii', $codigo, $sucursal_id);
            $res->execute();
            $res->bind_result($total);
            $res->fetch();
            $res->close();

            

            if ($total == 0) {
                $sql = "SELECT COUNT(*) total FROM inventario WHERE id_sucursal ='$sucursal_id'";
                $result = mysqli_query($con, $sql);
                $fila = mysqli_fetch_assoc($result);
                $concatenar = intval($fila["total"])+ 1;
                
                $construct = construirCodigoLlanta($con, $sucursal_id);
            
                $codigo_suc = $construct[0];
                $nombre_sucursal = $construct[1];

                $code = $codigo_suc . $codigo;
                
                //Insertand llanta al inventario de la sucursal
        
                $query = "INSERT INTO inventario (Codigo, id_Llanta, Sucursal, id_sucursal, Stock) VALUES (?,?,?,?,?)";
                $resultado = $con->prepare($query);
                $resultado->bind_param('sssii', $code, $codigo, $nombre_sucursal, $sucursal_id, $stock );
                $resultado->execute();
                $resultado->close();

                 
                $fecha = date("Y-m-d");   
                $hora =date("h:i a");   
                $usuario = $_SESSION["nombre"] . " " . $_SESSION["apellidos"];

                $traerdatadenew = "SELECT Descripcion FROM llantas WHERE id = ?";
                $result = $con->prepare($traerdatadenew);
                $result->bind_param('i',$codigo);
                $result->execute();
                $result->bind_result($descripcion_llanta);
                $result->fetch();
                $result->close(); 

                if($stock==1){

                    $descripcion_movimiento = "Se agregó ". $stock ." nueva llanta al inventario de " . $nombre_sucursal;
                }else{

                    $descripcion_movimiento = "Se agregaron ". $stock ." nuevas llantas al inventario de " . $nombre_sucursal;
                }
    
             //Registramos el movimiento
                $insertar_movimi = "INSERT INTO movimientos(id, descripcion, mercancia, fecha, hora, usuario, id_usuario)
                VALUES(null,?,?,?,?,?,?)";
                $resultado = $con->prepare($insertar_movimi);                     
                $resultado->bind_param('ssssss', $descripcion_movimiento, $descripcion_llanta, $fecha, $hora, $usuario, $id_usuario);
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

    function construirCodigoLlanta($con, $id_suc){
        $total = 9;
        $traer_nombre = "SELECT COUNT(*) FROM sucursal WHERE id=?";
        $resp = $con->prepare($traer_nombre);
        $resp ->bind_param('s', $id_suc);
        $resp->execute();
        $resp->bind_result($total);
        $resp->fetch();
        $resp->close();

        
        if($total>0){
        
        $code = "";
        $nombre_suc ="";
        $traer_nombres = "SELECT code, nombre FROM sucursal WHERE id=?";
        $resp = $con->prepare($traer_nombres);
        $resp ->bind_param('i', $id_suc);
        $result = $resp->execute();
        $resp->bind_result($code, $nombre_suc);
        $resp->fetch();
        $resp->close(); 
        $arr = [$code, $nombre_suc];
        return $arr;
        }else{

            return "NOCODE";
        }
    }
    
    
    ?>