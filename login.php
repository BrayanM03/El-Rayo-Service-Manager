<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="src/img/rayo.svg" />
    <title>Iniciar Sesión</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<link href="./src/css/login.css" rel="stylesheet">
<!------ Include the above in your HEAD tag ---------->
</head>
<body>
<div class="simple-login-container">
    <div class="formulario">

    <h2><img id="logo-login" src="./src/img/Optimized-logo-horizintal.png" alt=""></h2>
    <div class="row">
        <div class="col-md-12 form-group">
            <label for="">Usuario</label>
            <input type="text" class="form-control" id="user"  placeholder="Username">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 form-group">
            <label for="">Contraseña</label>
            <input type="password" id="password" placeholder="Enter your Password" class="form-control">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 form-group">
            <input value="Iniciar sesión" onclick="iniciarSesion();" type="submit" class="btn btn-block btn-login" placeholder="Enter your Password" >
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
           
        </div>
    </div>

    </div> 
</div>

<script src="src/js/iniciar-sesion.js"></script>
</body>
</html>