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

$querySuc = "SELECT COUNT(*) FROM sucursal";
                $resp=$con->prepare($querySuc);
                $resp->execute();
                $resp->bind_result($total_suc);
                $resp->fetch();
                $resp->close();

                if($total_suc>0){
                    $querySuc = "SELECT * FROM sucursal";
                    $resp = mysqli_query($con, $querySuc);

                    

                    while ($row = $resp->fetch_assoc()){
                        $suc_identificador = $row['id'];
                        
                       
                        $nombre = $row['nombre'];
                        echo '<option value="'.$suc_identificador .'">'.$nombre.'</option>';
                        }
                }


?>