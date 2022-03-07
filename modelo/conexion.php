<?php

class Conectar
{
    public function conexion()
    {
       /* $host = "llantera";
        $user = "root";
        $password = "";
        $db = "llante14_servicemanager";*/

        $host = "localhost";
        $user = "root";
        $password = "";
        $db = "el_rayo";  

        $con = mysqli_connect($host, $user, $password, $db);
        mysqli_set_charset($con,"utf8");
        return $con;
    }
}

$conectando = new Conectar;


?>