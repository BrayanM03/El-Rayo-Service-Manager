<?php
    session_start();
    include '../conexion.php';
    $con= $conectando->conexion(); 
    
date_default_timezone_set("America/Matamoros");

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../login.php");
    }

    $id_usuario = $_SESSION["id_usuario"];
    $rol = $_SESSION["rol"];

    if ($rol !== "1") {
        print_r("Nada");
    }else{


        if (isset($_POST)) {

            $fecha_actual = date("d-m-Y");

    
            $validar_lapso = $con->prepare("SELECT COUNT(*) total FROM creditos WHERE fecha_final = ?");
            $validar_lapso->bind_param('s', $fecha_actual);
            $validar_lapso->execute();
            $validar_lapso->bind_result($total);
            $validar_lapso->fetch();
            $validar_lapso->close();
    
            if ($total == 0) { 
               
    
            }else{
    
            
                $query="SELECT creditos.id, creditos.id_cliente, creditos.pagado, creditos.restante, creditos.total, creditos.estatus, creditos.fecha_inicio, creditos.fecha_final, creditos.plazo, clientes.Nombre_Cliente FROM creditos INNER JOIN clientes ON creditos.id_cliente = clientes.id WHERE fecha_final LIKE '$fecha_actual'";
    
                $resultado = mysqli_query($con, $query);
        
            
                    while($fila = $resultado->fetch_assoc()){
                        $id = $fila["id"];
                        $cliente = $fila["Nombre_Cliente"];
                        $pagado = $fila["pagado"];
                        $restante = $fila["restante"];
                        $total = $fila["total"];
                        $estatus = $fila["estatus"];
                        $fecha_inicio = $fila["fecha_inicio"];
                        $fecha_final = $fila["fecha_final"];
                        $plazo = $fila["plazo"];
                    
                       
                        
                        if($estatus !== "3"){

                            $validar = $con->prepare("SELECT COUNT(*) FROM registro_notificaciones WHERE refe LIKE ? AND id_usuario LIKE ?");
                            $validar->bind_param('ii', $id, $id_usuario);
                            $validar->execute();
                            $validar->bind_result($count);
                            $validar->fetch();
                            $validar->close();

                            if ($count > 0) {
                                print_r("Este registro ya esta notificado");
                            }else{

                            $estatus_not = "Se a vencido el credito para el cliente " . $cliente;
                            $hora = date("h:i a");
                            $stat = 1;

                            $insertar_notifi = "INSERT INTO registro_notificaciones(id, id_usuario, descripcion, estatus, fecha, hora, refe) VALUES(null,?,?,?,?,?,?)";
                            $result = $con->prepare($insertar_notifi);                     
                            $result->bind_param('isissi', $id_usuario,$estatus_not,$stat, $fecha_actual, $hora, $id);
                            $result->execute();
                            $result->close();
                            

                            $data[] = array("id" => $id,"state"=>1, "fecha_inicial"=>$fecha_inicio,"fecha_final"=>$fecha_final, "restante" => $restante,
                            "pagado" => $pagado, "cliente"=>$cliente, "total"=>$total, "plazo"=>$plazo, "estatus"=>$estatus);

                            $validar_lapso = $con->prepare("UPDATE creditos SET estatus='4' WHERE id = ?");
                            $validar_lapso->bind_param('i', $id);
                            $validar_lapso->execute();
                            $validar_lapso->close();
                            }
    
                        }else{
                           // print_r("El registro " . $id . " del cliente ". $cliente ." esta finalizado");
                        }               
                    
                       
                }

                if(isset($data)){
                    echo json_encode($data, JSON_UNESCAPED_UNICODE); 
                }else{
                    
                }
                
            //
            }
    
    
        }

    }



    

    ?>