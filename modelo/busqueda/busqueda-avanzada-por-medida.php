<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}

$ancho = $_POST["ancho"];
$proporcion = $_POST["proporcion"];
$diametro = $_POST["diametro"];


$querySuc = "SELECT COUNT(*) FROM llantas WHERE Ancho = ? AND Proporcion =? AND Diametro=?";
                $resp=$con->prepare($querySuc);
                $resp->bind_param("sss", $ancho, $proporcion, $diametro);
                $resp->execute();
                $resp->bind_result($total_llantas);
                $resp->fetch();
                $resp->close();
   
                if($total_llantas > 0){
                    $queryTyres= "SELECT * FROM llantas WHERE Ancho = '$ancho' AND Proporcion ='$proporcion' AND Diametro= '$diametro'"; //LIMIT $comienzo, $record_by_pag";
                    $respons = mysqli_query($con, $queryTyres);
                    
                    $acumulado=0;
                    while ($row = $respons->fetch_assoc()) {
                        $id_llanta = $row["id"];

                        $queryDetalle = "SELECT DISTINCT COUNT(*) FROM detalle_venta WHERE id_Llanta = ?";
                        $respz=$con->prepare($queryDetalle);
                        $respz->bind_param("s", $id_llanta);
                        $respz->execute();
                        $respz->bind_result($total_detalle);
                        $respz->fetch();
                        $respz->close();

                        $amount =0;
                        if ($total_detalle > 0){
                            $total_reg = $total_detalle + $amount;
                            
                            $querySucu = "SELECT DISTINCT id_Venta FROM detalle_venta WHERE id_Llanta = '$id_llanta'";
                            $respu = mysqli_query($con, $querySucu);

                            while ($fila = $respu->fetch_assoc()) {
                                $id_venta = $fila["id_Venta"];                  

                                $queryVent = "SELECT * FROM ventas WHERE id = '$id_venta'";
                                $respuesta = mysqli_query($con, $queryVent);
                                
                                while ($fila2 = $respuesta->fetch_assoc()) {
                                    $id_usuario = $fila2['id_Usuarios'];
                                    $id_cliente = $fila2['id_Cliente'];
            
                                    $queryuser = "SELECT nombre, apellidos FROM usuarios WHERE id = ?";
                                    $respx=$con->prepare($queryuser);
                                    $respx->bind_param("s", $id_usuario);
                                    $respx->execute();
                                    $respx->bind_result($nombre_usuario, $apellido_usuario);
                                    $respx->fetch();
                                    $respx->close(); 
            
                                    $nombre_completo = $nombre_usuario . " " . $apellido_usuario;
                                    
            
                                    $queryCustomer= "SELECT Nombre_Cliente FROM clientes WHERE id = ?";
                                    $respuc=$con->prepare($queryCustomer);
                                    $respuc->bind_param("i", $id_cliente);
                                    $respuc->execute();
                                    if($respuc){
                                        $respuc->bind_result($nombre_cliente);
                                        $respuc->fetch();
                                        $respuc->close();
            
                                    }else{
                                        $nombre_cliente = "Cliente vacio";
                                    }
            
                                    
                                    $fila2["cliente"] = $nombre_cliente;
                                    $fila2["vendedor"] = $nombre_completo;
                                    
                                    $data["registros"][] = $fila2;
                                    
                                }
                               
                            }
                            $amount = $total_detalle;
                            $acumulado= $acumulado + $amount;
                        }

                        
                    }
                    $data["total_registros"] = $acumulado;
                    echo json_encode($data, JSON_UNESCAPED_UNICODE);

                    

                }else{
                    $data["total_registros"] =1;
                    $data["status"] =false;

                    echo json_encode($data, JSON_UNESCAPED_UNICODE);
                }


