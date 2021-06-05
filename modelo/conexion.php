<?php

class Conectar
{
    public function conexion()
    {
     /* $host = "198.59.144.9";
        $user = "llante14_brayanm03";
        $password = "siemprefiel0";
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