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

$querySuc = "SELECT COUNT(*) FROM usuarios";
                $resp=$con->prepare($querySuc);
                $resp->execute();
                $resp->bind_result($total_suc);
                $resp->fetch();
                $resp->close();

                if($total_suc>0){
                    $querySuc = "SELECT * FROM usuarios WHERE id !=1";
                    $resp = mysqli_query($con, $querySuc);

                    

                    while ($row = $resp->fetch_assoc()){
                        $id = $row['id'];
                        if($id !==1){
                            $nombre = $row['nombre'] ." ". $row['apellidos'];
                            $data[] = array('nombre' => $nombre, 'id'=> $id);
                        }
                       
                        }

                        echo json_encode($data, JSON_UNESCAPED_UNICODE);
                }


?>