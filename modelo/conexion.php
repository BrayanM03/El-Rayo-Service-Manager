<?php

class Conectar
{
    public function conexion()
    {
  /*     $host = "174.136.52.208";
        $user = "powerpsc";
        $password = "9y40bEwzL3:>)<";
        $db = "powerpsc_servicemanager";
*/
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