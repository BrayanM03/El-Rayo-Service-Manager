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

    if ($rol == "3") {
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
    
            //Esta consulta trae los creditos que ya estan vencidos
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
                    
                        /*creditos:
                          1 = sin abono
                          2 = pagando
                          3 = Finalizado
                          4 = Vencido  */
                        //Esta parte del codigo cuenta la notificaciones que tengan el id del credito vencido y el id de la sesion del usuario
                        //para comprobar que la notificacion no haya sido emitida siempre y cuando el estatus del credito sea distinto a  3 osea finalizado
                        if($estatus !== "3"){
                            $alertada = "SI";
                            $validar = $con->prepare("SELECT COUNT(*) FROM registro_notificaciones WHERE refe LIKE ? AND id_usuario LIKE ? AND alertada LIKE ?");
                            $validar->bind_param('iis', $id, $id_usuario, $alertada);
                            $validar->execute();
                            $validar->bind_result($count);
                            $validar->fetch();
                            $validar->close();

                            //Al ser 0 significa que esta notificaciones
                            if ($count > 0) {
                                print_r("Este registro ya esta notificado a este usuario");
                            }else{

                           

                            /*Insertar notificaciones
                                //$consulta = mysqli_query($con, "SELECT * FROM usuarios WHERE rol LIKE 1 OR rol LIKE 2");


                               // if ($consulta) {
                                //    while($row = mysqli_fetch_assoc($consulta))
                                    {
                                    $id_usuario_admi = $row['id'] . " " . $row["nombre"] ; // Sumar variable $total + resultado de la consulta 
                                   
                                    $estatus_not = "Se a vencido el credito para el cliente " . $cliente;
                                    $estatus = 1; 
                                    $fecha = date("d-m-Y"); 
                                    $hora = date("h:i a");
                                    $refe = 0;  
                                    $estado_alerta = "SI";
                                    $queryInsertarNoti = "INSERT INTO registro_notificaciones (id, id_usuario, descripcion, estatus, fecha, hora, refe, alertada) VALUES (null,?,?,?,?,?,?,?)";
                                            $resultados = $con->prepare($queryInsertarNoti);
                                            $resultados->bind_param('isissis',$id_usuario_admi, $estatus_not, $estatus, $fecha, $hora, $id, $estado_alerta);
                                            $resultados->execute();
                                            $resultados->close();
                                    }
                                    
                                    
                                }*/

                                $estatus_not = "Se a vencido el credito para el cliente " . $cliente;
                                $estatus = 1; 
                                $fecha = date("Y-m-d"); 
                                $hora = date("h:i a");
                                $refe = 0;  
                                $estado_alerta = "SI";
                                $tipo = "vencimiento";
                                $queryInsertarNoti = "INSERT INTO registro_notificaciones (id, id_usuario, descripcion, estatus, fecha, hora, refe, alertada, tipo) VALUES (null,?,?,?,?,?,?,?,?)";
                                        $resultados = $con->prepare($queryInsertarNoti);
                                        $resultados->bind_param('isississ',$id_usuario, $estatus_not, $estatus, $fecha, $hora, $id, $estado_alerta, $tipo);
                                        $resultados->execute();
                                        $resultados->close();
                            

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