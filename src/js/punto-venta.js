traerAnchos()

const checkboxes = document.querySelectorAll('input[type="checkbox"]');

// Recorre cada checkbox y verifica si está marcado
checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            // Verifica si el checkbox está marcado o no
           buscarNeumaticoPuntoVenta();
        });
    });

//Funciones para el punto de venta

//Funcion que carga el loader
function load(flag) {
    if(flag){
        $("#contenedor-loader").removeClass('d-none') //True desaparece animacion
    }else{
        $("#contenedor-loader").addClass('d-none')
    }
}

//Cargando medidas
function traerAnchos(){
    console.log('Me ejecuto');
    load(true)
    
    $.ajax({
        type: "post",
        url: "./modelo/punto_venta/filtros-medidas.php",
        data: { 'tabla': 'llantas', 'fuente': 'Ancho'},
        dataType: 'json',
        success: function (response) {
            if(response.estatus){
                $("#ancho").empty();
                $("#ancho").append('<option value="">Seleccione un ancho</option>')
                response.medidas.forEach(element => {
                    $("#ancho").append(`<option value="${element}">${element}</option>`);
                });
                $("#ancho").selectpicker('refresh')
                setTimeout(() => {
                    load(false)
                }, 700)
            }
            
        }
    });
}

function cargarAltos(){
    let ancho = $("#ancho").val();
    $.ajax({
        type: "post",
        url: "./modelo/punto_venta/filtros-medidas.php",
        data: { 'tabla': 'llantas', 'fuente': 'Proporcion', 'ancho': ancho},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                $("#alto").prop('disabled',false)
                $("#alto").removeClass('disabled form-field')
                $("#alto").addClass('selectpicker')
                $("#alto").addClass('w-100')
                $("#alto").attr('data-live-search',"true")
                $("#alto").attr('onchange',"cargarRines()")
                $("#alto").empty();
                $("#alto").append('<option value="">Seleccione un alto</option>')
                response.medidas.forEach(element => {
                    $("#alto").append(`<option value="${element}">${element}</option>`);
                });
                $("#alto").selectpicker('refresh')
                setTimeout(() => {
                    load(false)
                }, 700)
            }
        }
    });
}

function cargarRines(){
    let ancho  = $('#ancho').val();
    let alto = $('#alto').val();

    $.ajax({
        type: "post",
        url: "./modelo/punto_venta/filtros-medidas.php",
        data: { 'tabla': 'llantas', 'fuente': 'Diametro', 'ancho': ancho, 'alto': alto},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                $("#rin").prop('disabled',false)
                $("#rin").removeClass('disabled form-field')
                $("#rin").addClass('selectpicker')
                $("#rin").addClass('w-100')
                $("#rin").attr('data-live-search',"true")
                $("#rin").attr('onchange',"buscarNeumaticoPuntoVenta()")
                $("#rin").empty();
                $("#rin").append('<option value="">Seleccione un alto</option>')
                response.medidas.forEach(element => {
                    $("#rin").append(`<option value="${element}">${element}</option>`);
                });
                $("#rin").selectpicker('refresh')
                setTimeout(() => {
                    load(false)
                }, 700)
            }
        }
    });
}

//Buscador automatico basado en filtros
function buscarNeumaticoPuntoVenta(){
    let ancho = $("#ancho").val();
    let alto = $("#alto").val();
    let diametro = $("#rin").val()

    if(ancho == '' || alto == '' || diametro == ''){
        Swal.fire({
            icon: 'error',
            title: 'Completa los filtros de medidas porfavor',
            confirmButtonText: 'Enterado'
        })
        return false;
    }

    //filtros
    // Obtén todos los checkbox en el formulario (puedes especificar un contenedor o clase específica si es necesario)
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');

    // Inicializa un array para almacenar los ids de los checkboxes seleccionados
    let checkedIds = [];

    // Recorre cada checkbox y verifica si está marcado
    checkboxes.forEach(checkbox => {
    if (checkbox.checked) {
        // Si está marcado, añade su id al array
        checkedIds.push(checkbox.id);
    }
    });
    
    load(true)
    $.ajax({
        type: "post",
        url: "./modelo/punto_venta/buscar.php",
        data: {ancho, alto, diametro,  filtros: checkedIds},
        dataType: "json",
        success: function (response) {

            $("#contenedor-resultados-llantas").empty();
            $("#titulo-busqueda").text('Resultados: ')
            if(response.estatus){
                response.datos.forEach(element => {
                    if(element.url_principal ==null){ 
                         src_imagen_llanta = './src/img/neumaticos/NA.jpg';
                    }else{
                        src_imagen_llanta = `./src/img/neumaticos/${element.url_principal}`;
                    }
                
                    let precio = Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(element.precio_Inicial)
                    let precio_mayoreo = Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(element.precio_Mayoreo)
                    if(element.promocion == 1){
                         precio_promocion = Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(element.precio_promocion)
                         display_promo = ''  
                     }else{
                        precio_promocion =0;
                        display_promo = 'd-none'
                    }
                    $("#contenedor-resultados-llantas").append(`
                    <div class="card mb-3 card_busqueda" onclick="previsualizarNeumatico(${element.id})">
                    <img class="${display_promo}" src="./src/img/promo-image.png" style="width:120px; position: absolute; z-index: 999; bottom: -.8rem;"></img>
                    <article class="tire-teaser">
                        <div class="row">
                            <div class="col-12 col-md-2">
                                <a>
                                  <img alt="" src="${src_imagen_llanta}" class="mt-4" style="width:150px; border-radius:10px">
                                </a>
                            </div>
                            <div class="col-12 col-md-10 tire-teaser__right mt-3 mb-3">
                                <div class="row">
                                    <div class="col-12 col-md-8">
                                        <h4><b>${element.Descripcion}</b></h4>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <div style="display:flex">
                                        <div class="btn btn-info" style="border-radius:10px 0px 0px 10px !important;" onclick="aumentarCantidad(0,'cantidad_id_${element.Codigo}',event)"><b>-</b></div>
                                        <input type="number" id="cantidad_id_${element.Codigo}" style="border-radius:0px !important;" class="form-control" placeholder="0" value="1">
                                        <div class="btn btn-info" style="border-radius:0px 10px 10px 0px !important;" onclick="aumentarCantidad(1,'cantidad_id_${element.Codigo}',event)"><b>+</b></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2 text-right">
                                        <div class="btn btn-warning mr-3">Agregar</div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12 col-md-9">
                                        <span><b>Sucursal:</b> ${element.nombre}</span><br>
                                        <span><b>Stock:</b> ${element.Stock}</span>
                                        <span class="ml-3"><b>Codigo:</b> ${element.Codigo}</span>
                                        <div class="row p-2 mt-2">
                                            <div class="col-6 col-md-6 text-center">
                                                <div style="background-color:#FF7F50; width: 80%; color:white; border-radius:8px" class="p-2">
                                                    <span>Precio normal</span><br>
                                                    <h3><b>${precio}</b></h3>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6 text-center ${display_promo}">
                                                <div style="background-color:#4682b4; width: 80%; color:white; border-radius:8px" class="p-2">
                                                    <span>Promoción</span><br>
                                                    <h3><b>${precio_promocion}</b></h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3 text-right mt-3">
                                        <div class="div mr-3">
                                             <span><b>Marca:</b> ${element.Marca}</span>
                                             <img alt="" src="./src/img/logos/${element.Marca}.jpg" style="width:110px; border-radius:10px">
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </article>
                </div>
                `);
                });
            }else{
                $("#contenedor-resultados-llantas").append(`
                <div class="card mb-3 p-5" style="border-radius: 15px !important; box-shadow: rgba(0,0,0,0.1) 0 0 30px 0;">
                    ${response.mensaje}
                </div>
                `)
            }
            
            setTimeout(function () {
                load(false)
            }, 1000)
        }
    });
}

//Funcion que previsualiza información detallada del producto neumatico
function previsualizarNeumatico(id_llanta){
    let body = document.getElementsByTagName('body')[0];
    body.style.overflow = 'hidden';
        $.ajax({
            type: "post",
            url: "./modelo/punto_venta/informacion-neumatico.php",
            data: {id_llanta},
            dataType: "json",
            success: function (response) {
                $("#contenedor-loader").removeClass('d-none')
                $("#contenedor-loader-2").addClass('d-none')
                let kg_max_2 = response.datos.kg_max_2 == null ? '' : '/'+response.datos.kg_max_2 +'kg'
                $("#contenedor-loader").append(`
                    <div class="card animate__animated animate__fadeInDown p-5 prevista_neumatico">
                        <div class="row justify-content-end" style="margin-top:-40px !important; margin-right:-40px">
                            <div class="col-4 col-md-2 text-right">
                                <h2 class="boton-close-preview-neumatico" onclick="cerrarModalPrevista()"><b><i class="fas fa-window-close"></i></b></h2>
                            </div>
                        </div>
                        <div class="row mt-4">
                                <div class="col-12 col-md-8">
                                    <div id="contenedor-imagenes-preview"></div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <h4><b>${response.datos.descripcion} ${response.datos.marca}</b></h4>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6">
                                            Aplicación de la llanta<br>
                                            <i class="fas fa-road"></i> ${response.datos.nombre_aplicacion}
                                        </div>
                                        <div class="col-12 col-md-6">
                                            Tipo de vehiculo<br>
                                            <i class="fas fa-truck"></i> ${response.datos.nombre_tipo_vehiculo}
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6">
                                            Rango de carga<br>
                                            <i class="fas fa-truck-loading"></i> ${response.datos.kg_max_1} kg ${kg_max_2}
                                        </div>
                                        <div class="col-12 col-md-6">
                                            Rango de velocidad<br>
                                            <i class="fas fa-clock"></i></i> ${response.datos.velocidad_max} 
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-6">
                                            Presión maxima<br>
                                            <i class="fas fa-clock"></i> ${response.datos.psi}
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <span><b>Marca:</b> ${response.datos.marca}</span>
                                            <img alt="" src="./src/img/logos/${response.datos.marca}.jpg" style="width:96px; border-radius:10px">
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-12">
                                            <b>Descripción del uso:</b><br>
                                            ${response.datos.descripcion_aplicacion}
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12 col-md-12">
                                            <b>Descripción del tipo de vehiculo:</b><br>
                                            ${response.datos.descripcion_tipo_vehiculo}
                                        </div>
                                    </div>
                                </div>
                                </div>
                               
                        </div>
                        
                    </div>
                `);

                let element = response.datos.urls ?? { url_principal: null, url_frontal: null, url_perfil: null, url_piso: null };

                console.log(element);

                let url_principal = element.url_principal ?? 'NA.JPG';
                let url_frontal = element.url_frontal ?? 'NA.JPG';
                let url_perfil = element.url_perfil ?? 'NA.JPG';
                let url_piso = element.url_piso ?? 'NA.JPG';

                $("#contenedor-imagenes-preview").append(`
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <img class="small" src="./src/img/neumaticos/${url_principal}" style="width: 250px;" />
                        </div>
                        <div class="col-md-6">
                            <img src="./src/img/neumaticos/${url_frontal}" style="width: 250px;" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <img src="./src/img/neumaticos/${url_perfil}" style="width: 250px;" />
                        </div>
                        <div class="col-md-6">
                            <img src="./src/img/neumaticos/${url_piso}" style="width: 250px;" />
                        </div>
                    </div>
                `);
                

            }
        });
}

//Cerrar modal prevista
function cerrarModalPrevista(){
    let body = document.getElementsByTagName('body')[0];
    body.style.overflow = '';
    $("#contenedor-loader").addClass('d-none')
    $("#contenedor-loader").empty();
    $("#contenedor-loader").append(`
    <div id="contenedor-loader-2">
    <div class="option-card text-center loader-principal">
        <lottie-player src="https://lottie.host/9ff4fc94-43ef-467b-aaf1-dafe7abaf53b/GFRYdl5GrJ.json" background="transparent" speed="1" style="width: 250px; height: 250px" loop autoplay></lottie-player>
    </div>  
    <span class="span-text-loader">Cargando
    </span>
    <div class="dots ml-2">
            <div class="dot">&#9679;</div>
            <div class="dot">&#9679;</div>
            <div class="dot">&#9679;</div>
        </div>
</div>
    `)
}

//Aumentar cantidad en los productos
function aumentarCantidad(tipo, id_input,e=null) {
    e.stopPropagation();
    let valorActual = $(`#${id_input}`).val();
    valorActual = parseInt(valorActual);
    console.log(id_input);
    if(tipo ==0 && valorActual ==0) {return false;}
    if(tipo ==0 && valorActual >0){
        var nuevo_valor = valorActual-1;
    };
    if(tipo ==1){
        var nuevo_valor = valorActual+1;
    }

    $.ajax({
        type: "post",
        url: ":/modelo/punto_venta/comprobar-stock.php",
        data: "data",
        dataType: "dataType",
        success: function (response) {
            
        }
    });

    $(`#${id_input}`).val(nuevo_valor);
    
}

//Ni me acuerdo jaja, algo tiene que ver con los estilos en modo telefono
if(screen.width < 1445){
    $("#area-resultados").removeClass('col-lg-10')
    $("#area-resultados").addClass('col-md-12')
}else{

}
