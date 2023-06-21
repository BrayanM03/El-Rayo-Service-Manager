<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="src/img/rayo.svg" />
    <title>Iniciar Sesión</title>

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">  
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
   


<link href="./src/css/login.css" rel="stylesheet">
<!------ Include the above in your HEAD tag ---------->

</head>
<body>
    
<!-- <video autoplay muted loop id="myVideo">
  <source src="./src/video/bg.mp4" type="video/mp4">
</video> -->

<div class="simple-login-container" style="border:1px solid #c8c8c8;">
    <div class="formulario">

<form action="" id="login-form">
<h2><img id="logo-login" src="./src/img/Optimized-logo-horizintal.png" alt=""></h2>
    <div class="row">
        <div class="col-md-12 form-group">
            <label for="">Usuario</label>
            <input type="text" class="form-control" name="usuario" id="user"  placeholder="Username">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 form-group">
            <label for="">Contraseña</label>
            <input type="password" id="password" name="contraseña" placeholder="Enter your Password" class="form-control">
        </div>
    </div>
   
    <div class="row">
    <div class="mensaje-oculto" id="alerta">
    
            <div id="label-alert" class="alert alert-danger" role="alert">
                      </div>   
                      </div>
        <div class="col-md-12 form-group">
         
            <div type="submit" onclick="iniciarSesion();" id="iniciar-sesion" class="btn btn-block btn-login">
            Iniciar sesión
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
           
        </div>
    </div>

</form>

    </div> 
</div>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://kit.fontawesome.com/31a28ea63e.js" crossorigin="anonymous"></script>
<script src="src/js/iniciar-sesion.js"></script>
</body>
</html>