<?php
session_start();

unset($_SESSION["id_usuario"]);
session_destroy($_SESSION);
header('location:../../login.php');
?>