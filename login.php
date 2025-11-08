<!DOCTYPE html>
<html lang="en">
<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    // Genera un token CSRF aleatorio y único
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}?>
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
    <style>
        
i {
    position: absolute;
    
    background: #fff;
    border-radius: 50%;
    box-shadow: inset 0 -1px 1px rgb(114, 114, 114);
    top: -10px;
    animation: nieve linear infinite;
}

@keyframes nieve {
    0% {
        top: -10px;
    }
    100% {
        top: calc(100vh + 50px);
    }
}
</style>
</head>

<body>
    <div class="container-fluid">
        
    <div class="row align-items-center justify-content-center">
    <div class="col-12 col-md-3 col-sm-12">
            <!-- <div class="col-12 col-md-12 mt-3">
                <div class="option-card text-center" id="card-check">
                    <dotlottie-player src="https://lottie.host/b456286d-19b4-4303-be60-5eaea6bdacca/0V7LI9PuA7.json" background="transparent" speed="1" style="width: 250px; height: 250px;" loop autoplay></dotlottie-player>
                </div>
            </div> -->
        </div>
        <div class="col-12 col-md-3 col-sm-12 justify-self-center">
            <div class="simple-login-container" style="border:1px solid #c8c8c8;">
                <div class="formulario">
                    <form action="" id="login-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <h2><img id="logo-login" src="./src/img/Optimized-logo-horizintal.png" alt=""></h2>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="">Usuario</label>
                                <input type="text" class="form-control" name="usuario" id="user" placeholder="Username">
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
        </div>
        <div class="col-12 col-md-3 col-sm-12">
            <!-- <div class="col-12 col-md-12 mt-3">
                <div class="option-card text-center" id="card-check">
                    <dotlottie-player src="https://lottie.host/b456286d-19b4-4303-be60-5eaea6bdacca/0V7LI9PuA7.json" background="transparent" speed="1" style="width: 250px; height: 250px;" loop autoplay></dotlottie-player>
                </div>
            </div> -->
        </div>
    </div>
    </div>

</body>
<!-- <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script> -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://kit.fontawesome.com/31a28ea63e.js" crossorigin="anonymous"></script>
<script src="src/js/iniciar-sesion.js"></script> 
<script>
    const body = document.querySelector('body')

const crearNeive = () => {
    let copo = document.createElement('i')
    let x = innerWidth * Math.random()
    let size = (Math.random() * 8) + 2
    let z = Math.round(Math.random()) * 100
    let delay = Math.random() * 5
    let anima = (Math.random() * 10) + 5

    copo.style.left = x + 'px'
    copo.style.width = size + 'px'
    copo.style.height = size + 'px'
    copo.style.zIndex = z
    copo.style.animationDelay = delay + 's'
    copo.style.animationDuration = anima + 's'

    body.appendChild(copo)

    setTimeout(() => {
        copo.remove()
    }, anima * 1000)
}

setInterval(crearNeive, 50)
</script>    
</html>