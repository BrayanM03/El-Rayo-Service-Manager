<?php
    session_start();

    date_default_timezone_set("America/Matamoros");
    include 'modelo/conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:login.php");
    }

    
    ?>
<!DOCTYPE html>
<html lang="es">

<head> 

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="src/img/rayo.svg" />

    <title>Nueva Venta</title>

    <!-- Custom fonts for this template-->
 
    <link rel="stylesheet" href="src/css/nueva-venta.css">
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link href='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css' rel='stylesheet' />

    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- <link href="src/vendor/node_modules/@splidejs/splide/dist/css/splide.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="src/vendor/datatables-responsive/css/responsive.bootstrap4.min.css">
   
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">

    <!---Librerias de estilos-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet" />
    <link href="src/css/slide_notificaciones.css" rel="stylesheet">
     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />
<style>
   /*  .card,.grupo-2, #content, .sticky-footer{
        background-color: rgb(21, 20, 27);
    } */
    .selectpicker{
        background-color: white !important; 
      
    }
    .bootstrap-select{
        border: 1px solid #ccc !important;
        max-width: 350px !important;
   
    }
</style>
</head>

<body id="page-top"> 

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!----sidebar--->

        <?php 
            require_once 'sidebar.php'
        ?>


        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content" sucursal_session_id= "<?php echo $_SESSION['id_sucursal']; ?>" rol_session_id="<?php echo $_SESSION['rol']?>">

                <!-- Topbar -->
                <?php include 'views/navbar.php'; ?>
                <!-- End of Topbar -->


                <!-- Begin Page Content -->
                <div class="container-fluid" style="display: flex; justify-content: center; ">

                     <!-- Contenido Nueva venta -->

                     <div class="card" style="margin-bottom: 7vh; padding-bottom: 5vh; padding-right: 30px;">
                     <div class="row"> 
                         <div class="col-12 col-md-6">
                             <h3 class="titulo-nueva-venta">Nueva venta</h3>
                             <p class="ml-4" id="texto-modo-venta">Modo de venta: <span style="color: green; text-shadow:#00a000 3px 0 10px;">Neumaticos</span></p>
                         </div>
                         <?php 

                            if ($_SESSION["id_usuario"] !== 10) {
                                # code...
                           

                         ?>
                         <!--<div class="col-12 col-md-6 text-center">
                             <span><b>Creditos vencidos</b></span>
                            <div class="splide">
                                <div class="splide__track">
                                            <ul class="splide__list" id="lista_splides">
                                                <!-- <li class="splide__slide"><div class="slide_credito">Hola</div></li>
                                                <li class="splide__slide"><div class="slide_credito">Hola</div></li> -->
                                            <!--</ul>
                                </div>

                                <div class="splide__progress">
                                        <div class="splide__progress__bar">
                                        </div>
                                </div>
                            </div>
                         </div>-->
                         <?php 
                          } 
                         ?>
                    </div> 

                         <div class="card-body">
                            <div class="row">

                            <div class="grupo-1 col">
                            
                                                <div class="logo-marca-grande">

                                                </div>

                                                <!--Fila 1-->
                                                <form id="form-punto-venta">

                                                <div class="fila1">
                                                        <!-- <label class="input-largo">
                                                            <span for="search">Busqueda</span>
                                                            <input type="text" id="search" name="search" class="input-group"> -->
                                                           <!--  
                                                        </label>  -->
                                                        
                                                            <div id="select-search-contain">
                                                                <select id="search" style="margin-bottom: 15px;" name="clientes" class="form-control"> 
                                                                </select>
                                                            </div>
                                                       
                                                        
                                                        
                                                       

                                                        <!-- <div class="contenedor-tabla oculto">
                                                                <table class="table" id="table-llantas-mostradas">
                                                                <thead class="table-info">
                                                                <tr>
                                                                    <th>Codigo</th>
                                                                    <th>Descripción</th>
                                                                    <th>Modelo</th>
                                                                    <th>Costo</th>
                                                                    <th>Costo c/Desc</th>
                                                                    <th>Marca</th>
                                                                    <th>Sucursal</th>
                                                                    <th>Stock</th>
                                                                </tr>
                                                                </thead> 
                                                                <tbody class="tbody">
                                                                </tbody> 
                                                                </table>
                                                        </div> -->

                                                        
                                                            <div id="select-search-contain">
                                                                
                                                                <select id="clientes" name="clientes" class="form-control"> 
                                                                </select>
                                                            </div>
                                                     
                                                            
                                                        

                                                         
                                                        <select id="metodos-pago" class="selectpicker form-control mb-2" data-live-search="true"  multiple name="clientes" title="Metodos de pago"> 
                                                                
                                                                <option value="0">Efectivo</option>
                                                                <option value="1">Tarjeta</option>
                                                                <option value="2">Transferencia</option>
                                                                <option value="3">Cheque</option>
                                                                <option value="4">Sin definir</option>
                                                         </select>
                                                    
                                                  

                                                        <label class="no-editable">
                                                            <span for="description">Descripción</span>
                                                            <input style="cursor: not-allowed;color:#696969;" type="text" id="description" name="description" class="input-group" disabled>
                                                        </label>
                                                        
                                                </div> 
                                                
                                                <!--Fila 1-->

                                                <div class="fila2 row">
                                                    
                                                    <label class="no-editable-corto">
                                                        <span for="modelo">Modelo</span>
                                                        <input modelo="" type="text" style="cursor: not-allowed; color:#696969;" id="modelo" name="modelo" class="input-group" disabled>
                                                    </label> 

                                                    <label class="no-editable-corto">
                                                         <span for="sucursal" >Sucursal</span>
                                                         <select style="color:#696969;" id="sucursal" name="sucursal" class="select-group form-select"> 
                                                                <option disabled selected value></option>
                                                                <!-- <option value="0">Pedro Cardenas</option>
                                                                <option value="1">Sendero</option>  -->

                                                                <?php
            
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
                                                                        echo '<option value='.$suc_identificador .'>'.$nombre.'</option>';
                                                                        }
                                                                }
                                                            
                                                            ?>

                                                         </select>
                                                    </label>

                                                    <label class="input-corto">
                                                        <span for="cantidad">Cantidad</span>
                                                        <input type="number" id="cantidad" name="cantidad" class="input-group" required>
                                                    </label>
                                        
                                                    <label class="input-corto precio-pointer" id="precio-tok" onclick="generarToken();">
                                                            <span for="precio" class="precio-pointer">$ Precio</span>
                                                            <input type="number" id="precio" name="precio" class="input-group precio-pointer disabled" disabled>
                                                    </label>
                                                    
                                                    <label style="border-color: transparent;">
                                                        <div class="btn btn-info" flag="0" onclick="generarToken();" style="width: 45px;margin: 8px 3px;"><i class="fas fa-lock"></i></div>
                                                    <div class="btn btn-info" onclick="busqueda();" id="btn-busqueda-llanta" style="width: 45px;margin: 8px 3px;"><i class="fas fa-search"></i></div>
                                                    <div class="btn btn-info" flag="0" onclick="changeServicios();" id="btn-change-servicios" style="width: 45px;margin: 8px 3px;"><i class="fas fa-car"></i></div>
                                                    <div class="btn btn-info" onclick="agregarcliente();" id="btn-add-client" style="width: 45px;margin: 8px 30px 8px 0px;"><i style=" width: 20px;" class="fas fa-user-plus"></i></div>
                                                              

                                                    <div class="btn btn-info" rol="<?php echo $_SESSION['rol']; ?>" sucursal="<?php echo $_SESSION['sucursal']; ?>"  id_sucursal="<?php echo $_SESSION['id_sucursal']; ?>" id="agregar-producto" onclick="agregarInfo()" >Agregar</div>
                                                    </label>

                                                   <!--  <div id="help-searchtyre-span" class="targeta-ayuda">
                                                       <div class="card text-white border-info">
                                                       <div class="card-header bg-info">Busqueda avanzada</div>
                                                       <div class="card-body bg-white text-info" >
                                                       <p class="card-text">Con este boton puedes revisar la disponibilidad de una llanta en otras sucursales</p>
                                                       </div>
                                                    </div> 
                                                    </div>  -->
                                                    
                                                    <!-- <div id="help-addclient-span" class="targeta-ayuda">
                                                       <div class="card text-white border-info">
                                                       <div class="card-header bg-info">Agregar un cliente</div>
                                                       <div class="card-body bg-white text-info" >
                                                       <p class="card-text">Con este boton pueden agregar un cliente a la base de datos</p>
                                                       </div>
                                                    </div> 
                                                    </div>  -->

                                                   <!--  <div id="help-changeservice-span" class="targeta-ayuda">
                                                       <div class="card text-white border-info">
                                                       <div class="card-header bg-info" id="title-help-card">Modo servicios</div>
                                                       <div class="card-body bg-white text-info" >
                                                       <p class="card-text" id="body-help-card">Pulsa este boton para cambiar a modo venta de servicios</p>
                                                       </div>
                                                    </div> 
                                                    </div>  -->

                                                </div>
                                                 
                                                </form>
                                        
                                </div>


                            <div class="grupo-2 col">

                            
                                    
                                    
                                    <div class="fila4 row">
                                        
                                                <table id="pre-venta" class="table table-striped table-bordered table-hover">
                                           
                                                </table>
                                        
                                        
                                                
                                                <div class="row" style="width: 100%; display: flex; justify-content: center; align-items: center; margin-top:25px">
                                                    <div class="form-group" style="display: flex; align-items: center; justify-content: space-around; width: 25%;" >
                                                        <span for="total" >Total: $</span>
                                                        <input type="number" value="0" class="form-control" id="total" name="total" style="width:120px; margin-left:5px; display:flex; justify-content:center;" disabled> 
                                                
                                                    </div>
                                                    </div>
                                                <div class="botones-de-venta" style="margin-top: 8px;">
                                                    <div class="btn btn-danger text-white" comentario=" " style="color: rgb(31, 28, 28); margin-right: 2vh;" id="hacer-comentario"><i class="fas fa-comment-dots"></i></div>
                                                    <div class="btn btn-warning" onclick="limpiarTabla();" style="color: rgb(31, 28, 28); margin-right: 2vh;" id="limpiar-venta">Limpiar</div>
                                                    <div class="btn btn-success" onclick="procesarVenta();" id="realizar-venta">Realizar venta</div>
                                                    <div class="btn btn-primary" onclick="revisarCredito();" style="margin-left: 2vh;" id="realizar-venta">Realizar venta a credito</div>
                                                    <div class="btn" onclick="procesarApartado();" style="margin-left: 2vh; background-color: #D29BFD; color:white;" id="realizar-venta">Realizar apartado</div>
                                                </div>   
                                                
                                    
                                    </div>
                                </div>

                            </div> <!---Fin row-->

                                
                              
                                
                            
                         </div>
                     </div>
                   

                </div>
            <!-- End of Main Content -->
  <!-- Footer -->
            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; El Rayo Service Manager <?php print_r(date("Y")) ?></span><br><br>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Listo para salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Seleccione "salir" para cerra su sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="modelo/login/cerrar-sesion.php">Salir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="src/vendor/jquery/jquery.min.js"></script>
    <script src="src/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="src/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="src/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="src/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts 
    <script src="src/js/demo/chart-area-demo.js"></script>
    <script src="src/js/demo/chart-pie-demo.js"></script>-->


    <!-- Cargamos nuestras librerias-->
    
    <script src="src/vendor/datatables/jquery.dataTables.min.js"></script> 
    <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
    
    <script src="src/vendor/datatables/defaults.js"></script>
    <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css">

    <!--<script src="src/js/agregar-info-tabla-venta.js"></script>-->
    <!-- <script src="src/vendor/node_modules/@splidejs/splide/dist/js/splide.min.js"></script> -->
    <script src="src/js/bootstrap-select.min.js"></script>
    <script src="src/js/nueva-venta.js?v=<?php echo(rand()); ?>"></script>
    <script src="src/js/agregar-product-temp.js?v=<?php echo(rand()); ?>"></script>
    <script src="src/js/generar-token.js?v=<?php echo(rand()); ?>"></script>
    <script src="src/js/nueva-venta-credito.js?v=<?php echo(rand()); ?>"></script>
    <!-- <script src="src/js/splide_notifications.js"></script> -->
    <script src="src/js/consultar-llanta-nv.js?v=<?php echo(rand()); ?>"></script>
    <script src="src/js/apartados.js?v=<?php echo(rand()); ?>"></script>
  <!--  <script src="src/js/notificaciones.js"></script>   -->
   
   
</body>

</html>