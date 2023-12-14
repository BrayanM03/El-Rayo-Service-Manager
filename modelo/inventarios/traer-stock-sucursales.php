<?php
    
    
    include '../conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    if (isset($_POST)) {
        $id_llanta = $_POST['id_llanta'];

        $count = "SELECT COUNT(*) FROM inventario WHERE id_Llanta = ?";
        $res = $con->prepare($count);
        $res->bind_param('i',$id_llanta);
        $res->execute();
        $res->bind_result($no_llantas);
        $res->fetch();
        $res->close();

        if($no_llantas >0){
            $sqlContarLlantas= $con->prepare("SELECT * FROM inventario WHERE id_Llanta = ?");
            $sqlContarLlantas->bind_param('i', $id_llanta);
            $sqlContarLlantas->execute();
            $data_inventario = $sqlContarLlantas->get_result();
            $sqlContarLlantas->close();
        }
        $sucu =  "SELECT COUNT(*) FROM sucursal";
        $res = $con->prepare($count);
        $res->bind_param('i',$id_llanta);
        $res->execute();
        $res->bind_result($no_sucursales);
        $res->fetch();
        $res->close();

        $sucursales =[];
        if($no_sucursales>0){
                $stmt= $con->prepare("SELECT * FROM sucursal");
                $stmt->execute();
                $data_suc = $stmt->get_result();
                $stmt->close();
                while($filas_suc = $data_suc->fetch_assoc()){
                    $sucursales[] = $filas_suc;
                   
                }
                if($no_llantas >0){
                    //Irerando sobre las sucursales encontradas
                    while($filas = $data_inventario->fetch_assoc()){
                        $id_sucursal = $filas['id_sucursal'];
                        $nombre_sucursal = $filas['Sucursal'];
                        $stock = $filas['Stock'];
                        //El stock de la llanta es 0? en la iteracion actual
                        if($stock > 0){
                            $datos_[] = array('id_sucursal' => $id_sucursal, 'nombre'=>$nombre_sucursal, 'stock'=> $stock);
                        }else{
                            $datos_[] = array('id_sucursal' => $id_sucursal, 'nombre'=>$nombre_sucursal, 'stock'=> 0);
                        }
                    }
                }
                $resultado = array('estatus' =>true, 'mensaje' =>'Se encontraron llantas', 'data' =>$datos_, 'sucursales'=>$sucursales);

           
        }else{
            $resultado = array('estatus' =>false, 'mensaje' =>'No existen sucursales agregadas', 'data'=>[]);
        }

        echo json_encode($resultado);
        
    }

    ?>