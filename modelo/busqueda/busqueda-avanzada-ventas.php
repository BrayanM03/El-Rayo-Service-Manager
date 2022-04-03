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

$id_selector = $_POST["filtro"];
$comienzo = $_POST["comienzo"];
$columna = $_POST["columna"];
$record_by_pag = 5;

$querySuc = "SELECT COUNT(*) FROM ventas WHERE $columna = ?";
                $resp=$con->prepare($querySuc);
                $resp->bind_param("s", $id_selector);
                $resp->execute();
                $resp->bind_result($total_suc);
                $resp->fetch();
                $resp->close();

                
                if($total_suc>0){
                    $querySuc = "SELECT * FROM ventas WHERE $columna = '$id_selector' ORDER BY id DESC"; //LIMIT $comienzo, $record_by_pag";
                    $resp = mysqli_query($con, $querySuc);

                    while ($row = $resp->fetch_assoc()){

                        $id_usuario = $row['id_Usuarios'];
                        $id_cliente = $row['id_Cliente'];

                         $queryuser = "SELECT nombre, apellidos FROM usuarios WHERE id = ?";
                        $respx=$con->prepare($queryuser);
                        $respx->bind_param("s", $id_usuario);
                        $respx->execute();
                        $respx->bind_result($nombre_usuario, $apellido_usuario);
                        $respx->fetch();
                        $respx->close(); 

                        $nombre_completo = $nombre_usuario . " " . $apellido_usuario;
                        

                        $queryCustomer= "SELECT Nombre_Cliente FROM clientes WHERE id = ?";
                        $respu=$con->prepare($queryCustomer);
                        $respu->bind_param("i", $id_cliente);
                        $respu->execute();
                        if($respu){
                            $respu->bind_result($nombre_cliente);
                            $respu->fetch();
                            $respu->close();

                        }else{
                            $nombre_cliente = "Cliente vacio";
                        }

                        
                        $row["cliente"] = $nombre_cliente;
                        $row["vendedor"] = $nombre_completo;
                        $data["registros"] []= $row;
                        }
                        $data["total_registros"] = $total_suc;
                        echo json_encode($data, JSON_UNESCAPED_UNICODE);
                }else{
                    $data["total_registros"] =1;
                    $data["status"] =false;

                    echo json_encode($data, JSON_UNESCAPED_UNICODE);
                }


?>