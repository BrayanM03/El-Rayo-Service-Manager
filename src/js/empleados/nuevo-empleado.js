toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }
 
  let rol_usuario = $("#emp-title").attr('sesion_rol')
  var formData = new FormData();
  let semana_array = ['', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo']
  let breadcrumbs = document.querySelectorAll('.breadcrumbs__item')

  function siguienteProceso(paso, tipo){
    
    if(paso==1){
      paso1(1,tipo)
    }else if(paso==2){
        if(validacionFormularioEmpleado()){
          paso2(tipo, 2)
        }
    }else if(paso==3){
      console.log(paso);
      console.log(tipo);
        paso3()
      
    }
    
  }
  
  function validacionFormularioEmpleado(){
    let formulario = document.querySelectorAll('#formulario-nuevo-empleado input, #formulario-nuevo-empleado select,  #formulario-nuevo-empleado textarea')
    let arreglo_validar = ['nombre', 'apellidos', 'sucursal', 'puesto', 'salario-base']
    let formulario_valido = true;
    formulario.forEach(element => {
        let id_elemento = element.id;
        if(arreglo_validar.includes(id_elemento)){
            if(element.value ==''){
                if(id_elemento=='sucursal' || id_elemento=='puesto' || id_elemento=='genero'){
                    let select_actual = element.nextSibling
                    
                    select_actual.classList.remove('btn-light')
                    select_actual.style.border = "1px solid red";
                    let adv = document.getElementById(id_elemento+'-adv')
                    if(adv!=null){adv.classList.remove('d-none')}
                }else{
                    element.style.border ='1px solid red'
                    let adv = document.getElementById(id_elemento+'-adv')
                    if(adv!=null){adv.classList.remove('d-none')}
                }
                formulario_valido = false;
            }else{
                if(id_elemento=='sucursal' || id_elemento=='puesto' || id_elemento=='genero'){
                    let select_actual = element.nextSibling
                    
                    if(!select_actual.classList.contains('btn-light')){
                       select_actual.classList.add('btn-light')
                        select_actual.style.border = "";
                        let adv = document.getElementById(id_elemento+'-adv')
                        if(adv!=null){adv.classList.add('d-none')}
                    }
                    
                }else{
                    element.style.border ='1px solid #CDD9ED'
                    let adv = document.getElementById(id_elemento+'-adv')
                    if(adv!=null){adv.classList.add('d-none')}
                }
            }
        }
        
    });

    return formulario_valido;
  }

  function cargarFoto(){
    document.getElementById("foto-perfil").click(); 
  }

  function setearFotoThumb(tipo=1, foto_adjunta=''){
    let file;
    if(tipo==2){
      file = foto_adjunta
    }else{
      let input_comprobante = document.getElementById('foto-perfil');
      file = input_comprobante.files[0];
    }
    
    let area_canvas = $("#area-canvas");
   if(file.type.startsWith('image/')){
    // Si es una imagen (cualquier tipo de imagen)
    var reader = new FileReader();
    area_canvas.empty().append(`
    <span aria-hidden="true" class="btn-x-documento" onclick="deleteThumb()">×</span>
    <img src="" id="foto" style="width: 9rem; border:1px whitesmoke solid; border-radius:7px">`)
    let img_preview = document.getElementById('foto')
  
      reader.onloadend = function () {
        img_preview.src = reader.result;
      }
    
      if (file) {
        reader.readAsDataURL(file);
       // clearCanvas()
      } else {
        img_previewpreview.src = "";
      }

      Swal.resetValidationMessage()
      toastr.success('Documento adjunto agregado con exito' ); 
      $("#input-comprobante-edicion").attr('eliminar', false);
  }else{
    area_canvas.empty()
    toastr.errpr(
      `Tipo de archivo no admitido`, 'Error'
    );
  }
  }

  function clearCanvas() {
    var canvas = document.getElementById('thumbnailCanvas');
    var context = canvas.getContext('2d');
    context.clearRect(0, 0, canvas.width, canvas.height);
  }

  function deleteThumb(flag =0){
      let area_canvas = $("#area-canvas");
      let input_comprobante = document.getElementById('foto-perfil');
    let file = input_comprobante.files[0];
    if(file!==undefined){
      file.value=''
    }
      area_canvas.empty().append(`<img src="./src/img/neumaticos/NA.JPG" id="foto" alt="" style="width: 9rem; border:1px whitesmoke solid; border-radius:7px">
     `)
      flag !== 1 ? toastr.success('Documento adjunto eliminado con exito' ) : false; 
      $("#input-comprobante-edicion").val('').attr('eliminar', true);
    eliminar_comprobante = true;
  }

  function paso1(tipo, avanzar_otra_vez){
    let breadcrumbs = document.querySelectorAll('.breadcrumbs__item')
    breadcrumbs.forEach(element => {
      element.classList.remove('is-active')
    });
    $("#bread_paso_1").addClass('is-active')
    if(tipo==2){
      let horario = $("#horario").val()
      formData.set('horario', horario);
      toastr.success('Horario guardado temporalmente', 'Guardado')

      let nombre_fd = formData.get('nombre')
      let apellidos_fd = formData.get('apellidos')
      let sucusal_fd = formData.get('sucursal')
      let puesto_fd = formData.get('puesto')
      let salario_base_fd = formData.get('salario-base')
      let telefono_fd = formData.get('telefono')
      let correo_fd = formData.get('correo')
      let genero_fd = formData.get('genero')
      let fecha_cumple_fd = formData.get('fecha-cumple')
      let fecha_registro_fd = formData.get('fecha-ingreso')
      let direccion_fd = formData.get('direccion')
      let usuario_fd = formData.get('usuario')
      let foto_adjunta = formData.get('documento_adjunto');

      $("#card-body").empty().append(`
                            <div class="row mt-4 justify-content-center">
                        
                                    <div id="area-canvas" class="col-md-2 col-12">
                                        <img src="./src/img/neumaticos/NA.JPG" alt="" style="width: 9rem; border:1px whitesmoke solid; border-radius:7px">
                                    </div>
                                    <div class="col-md-8 col-12">
                                        <input type="file" id="foto-perfil" class="d-none" onchange="setearFotoThumb()">
                                        <div class="btn btn-sm btn-primary" style="margin-top: 5.5rem; margin-left:.7rem" onclick="cargarFoto()">Subir foto</div>
                                    </div>
                            </div>
                            <div class="row mt-3 justify-content-center">
                                <div class="col-4">
                                    <label for=""><b>Nombre</b></label>
                                    <input type="text" value="${nombre_fd}" class="form-field form-control-sm" id="nombre" placeholder="Nombre">
                                    <small style="color:red" class="d-none" id="nombre-adv">Ingresa nombre del empleado</small>
                                </div>
                                <div class="col-6">
                                    <label for=""><b>Apellidos</b></label>
                                    <input type="text" value="${apellidos_fd}" class="form-field form-control-sm" id="apellidos" placeholder="Apellidos">
                                    <small style="color:red" class="d-none" id="apellidos-adv">Ingresa apellidos del empleado</small>
                                </div>
                                <div class="col-4 mt-3">
                                    <label for=""><b>Sucursal</b></label>
                                    <select class="form-control selectpicker form-control-sm" id="sucursal" style="border: 1px solid red !important;">
                                        <option value="">Selecciona una sucursal</option>
                                        
                                    </select>
                                    <small style="color:red" class="d-none" id="sucursal-adv">Selecciona una sucursal</small>
                                </div>
                                <div class="col-3 mt-3">
                                    <label for=""><b>Puesto</b></label>
                                    <select class="form-control selectpicker form-control-sm" id="puesto" data-live-search="true">
                                        <option value="">Selecciona un puesto</option>
                                  
                                    </select>
                                </div>
                                <div class="col-3 mt-3">
                                    <div>
                                    <label><b>Salario base</b></label>
                                    <input type="number" value="${salario_base_fd}" class="form-field form-control-sm" id="salario-base" placeholder="0.00">
                                    <small style="color:red" class="d-none" id="salario-base-adv">Ingresa salario base</small>

                                    </div>
                                </div> 
                            </div>
                            <div class="row text-start mt-4 justify-content-center">
                                <div class="col-4">
                                    <label><b>Telefono</b></label>
                                   <input type="text" value="${telefono_fd}" class="form-field form-control-sm" id="telefono" placeholder="+52 83 4268 2283">
                                </div> 
                                <div class="col-3">
                                    <label><b>Correo</b></label> 
                                   <input type="email" value="${correo_fd}" class="form-field form-control-sm" id="correo" placeholder="alguien@empresa.com">
                                </div> 
                                <div class="col-3">
                                    <div>
                                    <label><b>Genero</b></label>
                                    <select class="form-control selectpicker form-control-sm" id="genero">
                                        <option value="1">Masculino</option>
                                        <option value="2">Femenino</option>
                                    </select>
                                    </div>
                                </div> 
                            </div> 
                            <div class="row text-start mt-4 justify-content-center">
                                <div class="col-3">
                                    <div>
                                    <label><b>Cumpleaños</b></label>
                                    <input value="${fecha_cumple_fd}" type="date" class="form-field form-control-sm" id="fecha-cumple">
                                    </div>
                                </div> 
                                <div class="col-3">
                                    <div>
                                    <label><b>Fecha ingreso</b></label>
                                    <input type="date" value="${fecha_registro_fd}" class="form-field form-control-sm" id="fecha-ingreso">
                                    </div>
                                </div> 
                                <div class="col-4">
                                    <div>
                                    <label><b>Usuario sistema</b></label>
                                    <select class="form-control selectpicker form-control-sm" id="usuario" data-live-search="true">
                                        <option value="">Sin usuario</option>
                                    </select>
                                    </div>
                                </div> 
                            </div> 
                            <div class="row text-start mt-4 justify-content-center">
                                <div class="col-10">
                                    <div>
                                    <label for="direccion"><b>Dirección</b></label>
                                    <textarea class="form-field form-control-md" id="direccion" placeholder="Escribe la dirección del empleado">${direccion_fd}</textarea>
                                    </div>
                                </div> 
                            </div> 

                            <div class="row text-center mt-4 justify-content-center">
                                <div class="col-4">
                                    <div class="btn btn-info" onclick="siguienteProceso(1,2)">Siguiente</div>
                                </div>
                            </div>
      
      `)

    $("#genero").val(genero_fd)
    $("#sucursal").selectpicker({
        style: "form-field form-control-sm "
    });
    
    $("#puesto").selectpicker({
        style: "form-field form-control-sm"
    });
    $("#genero").selectpicker({
        style: "form-field form-control-sm"
    });
    $("#usuario").selectpicker({
        style: "form-field form-control-sm"
    });

    
    $.ajax({
      type: "post",
      url: "./modelo/empleados/obtener-datos-formulario-agregar-empleado.php",
      data: "data",
      dataType: "JSON",
      success: function (response) {
        if(response.estatus){
          $("#sucursal").empty().append('<option value="">Selecciona un sucursal</option>')
          $("#puesto").empty().append('<option value="">Selecciona un puesto</option>')
          $("#usuario").empty().append('<option value="">Selecciona un usuario</option>')
          response.data.sucursales.forEach(element => {
            $("#sucursal").append(`
              <option value="${element[0]}">${element[2]}</option>
            `)
          });
          response.data.puestos.forEach(element => {
            $("#puesto").append(`
              <option value="${element[0]}">${element[1]}</option>
            `)
          });
          response.data.usuarios.forEach(element => {
            $("#usuario").append(`
              <option value="${element[0]}">${element[1]} ${element[2]}</option>
            `)
          });

          $("#sucursal").val(sucusal_fd)
          $("#puesto").val(puesto_fd)
          $("#usuario").val(usuario_fd)

          $("#sucursal").selectpicker({
        style: "form-field form-control-sm "
    })
          $("#puesto").selectpicker({
            style: "form-field form-control-sm "
        })
          $("#usuario").selectpicker({
            style: "form-field form-control-sm "
        })
        $('#sucursal').selectpicker('refresh')
        $('#puesto').selectpicker('refresh')
        $('#usuario').selectpicker('refresh')
        }
      }
    });

    if (foto_adjunta) {
      setearFotoThumb(2, foto_adjunta);
  }

    }else{
      let validacion_formulario = validacionFormularioEmpleado()
      validacion_formulario=true
      let datos = {};
    if(validacion_formulario){
   
      let formulario = document.querySelectorAll('#formulario-nuevo-empleado input, #formulario-nuevo-empleado select,  #formulario-nuevo-empleado textarea')
      formulario.forEach(element => {
          let id_elemento = element.id;
          let valor = element.value;
          if(avanzar_otra_vez==2){
            formData.set(id_elemento, valor);
          }else{
            formData.append(id_elemento, valor);
          }
      })
      let extension_archivo;
      let documento_adjunto = document.getElementById('foto-perfil');
      var file =  documento_adjunto.files[0];
      if(file != undefined){
          const extension = file.name.split('.').pop();
          extension_archivo=extension;
      };
      if(avanzar_otra_vez==2 && file != undefined){
        formData.set('extension_archivo', extension_archivo);
        formData.set('documento_adjunto', file);
      }else if(avanzar_otra_vez==1 && file != undefined){
        formData.append('extension_archivo', extension_archivo);
        formData.append('documento_adjunto', file);
      }
      
    
      siguienteProceso(2,1)
      }else{
        toastr.error('Complete el formulario para registrar un nuevo empleado', 'Error')
      }
    }
  }

  function paso2(tipo, avanzar_){
    if(avanzar_==3){
    toastr.warning('Los documentos no se guardan temporalmente', 'Advertencia')
    }else{
      toastr.success('Información guardada temporalmente', 'Guardado')
    }
    let contador_paso_3
    let breadcrumbs = document.querySelectorAll('.breadcrumbs__item')
    breadcrumbs.forEach(element => {
      element.classList.remove('is-active')
    });
    $("#bread_paso_2").addClass('is-active')
    origen=2;

      let card_height = document.getElementById('card-body').clientHeight;
      $("#card-body").empty().append(`
      <div style="height: ${card_height}px">
          <div class="row mt-3 justify-content-center">
                <div class="col-4">
                    <label for=""><b>Selecciona un horario para el nuevo empleado</b></label>
                    <select onchange="resetTablaDetalleHorario()" class="form-field form-control selectpicker" id="horario">
                    </select>
                </div>
                </div>
                <div class="row mt-3 justify-content-center">
                  <div class="col-10">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <td>#</td>
                          <td>Día</td>
                          <td>Hora inicio</td>
                          <td>Hora final</td>
                          <td></td>
                          </tr>
                      </thead>
                      <tbody id="detalle_horario">
                          
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="row text-center mt-4 justify-content-center">
                <div class="col-4">
                    <div class="btn btn-info mr-3" onclick="paso1(2, 2)">Atras</div>
                    <div class="btn btn-info" onclick="siguienteProceso(3, ${contador_paso_3})">Siguiente</div>
                </div>
            </div>
         
        </div>
      `)

      $.ajax({
        type: "post",
        url: "./modelo/configuraciones/configuracion_horarios/traer-lista-horarios.php",
        data: "data",
        dataType: "json",
        success: function (response) {
          if(response.estatus){
            response.data.forEach(element => {
              $("#horario").append(`
                <option value="${element[0]}">${element[1]}</option>
              `)
            });
            let index_horario
            if((avanzar_==2 && tipo == 2) || (avanzar_==2 && tipo == 1)){
              let horario;
              
              if(formData.has('horario')){
                horario = formData.get('horario');
                $("#horario").val(horario)
              }else{

                horario = $('#horario').val();
                formData.append('horario', horario);
              }
              index_horario =  response.data.findIndex(item => item[0]==horario);

            }else{
              index_horario = 0
              formData.append('horario', '');
              
            }
          $("#horario").selectpicker()

            let contador=0;
             response.data[index_horario].detalle_horario.forEach(element_ => {
              let hora_inicial = convertirHora(element_[2]);
              let hora_final= convertirHora(element_[3]);
                contador++
                $("#detalle_horario").append(`
                  <tr>
                    <td>${contador}</td>
                    <td>${semana_array[element_[1]]}</td>
                    <td>${hora_inicial}</td>
                    <td>${hora_final}</td>
                    <td><i class="fas fa-clock"></i></td>
                  <tr>

                `)
              });
              
            
              
          }else{
            toastr.error(response.mensaje, 'error')
          }
        }
      });
  }

  function resetTablaDetalleHorario(){
    let id_horario = $("#horario").val()
    $("#detalle_horario").empty()
    $.ajax({
      type: "post",
      url: "./modelo/configuraciones/configuracion_horarios/traer-lista-horarios.php",
      data: "data",
      dataType: "json",
      success: function (response) {
        if(response.estatus){
          response.data.forEach((element) => {
            if(element[0]==id_horario){
              let contador=0;
              element.detalle_horario.forEach(element_ => {
                contador++;
                let hora_inicial = convertirHora(element_[2]);
                let hora_final= convertirHora(element_[3]);
                $("#detalle_horario").append(`
              <tr>
                    <td>${contador}</td>
                    <td>${semana_array[element_[1]]}</td>
                    <td>${hora_inicial}</td>
                    <td>${hora_final}</td>
                    <td><i class="fas fa-clock"></i></td>
                  <tr>
            `)
              });
            }

            
          });
        }else{
          toastr.error(response.mensaje, 'error')
        }
      }
    })
  }

  function convertirHora(hora24) {
    const [hora, minutos, segundos] = hora24.split(":");
    const fecha = new Date();
    fecha.setHours(hora, minutos, segundos);
    
    return fecha.toLocaleTimeString("es-MX", { hour: 'numeric', minute: '2-digit', hour12: true });
  }

  function paso3() {
  let horario = $("#horario").val()
  formData.set('horario', horario);
  toastr.success('Horario guardado temporalmente', 'Guardado');
  let breadcrumbs = document.querySelectorAll('.breadcrumbs__item');
  breadcrumbs.forEach(element => element.classList.remove('is-active'));
  document.getElementById("bread_paso_3").classList.add('is-active');
  
  const documentos = [
    { id: "cv", label: "Curriculum Vitae" },
    { id: "curp", label: "CURP" },
    { id: "domicilio", label: "Comprobante de Domicilio" },
    { id: "nss", label: "Número de Seguridad Social (NSS)" },
    { id: "id", label: "Identificación Oficial" },
    { id: "contrato", label: "Contrato de Trabajo" },
    { id: "bancarios", label: "Datos Bancarios" }
];

let formHtml = '';
documentos.forEach(doc => {
    formHtml += `
    
        <div class="file-upload-section" ondrop="dropHandler(event, '${doc.id}')" ondragover="dragOverHandler(event)">
            <label class="file-label" for="${doc.id}">${doc.label}</label>
            <input type="file" id="${doc.id}" class="file-input d-none" accept=".pdf,.jpg,.jpeg,.png" onchange="mostrarArchivo(event, '${doc.id}-preview')">
            <div class="file-drop-zone" onclick="document.getElementById('${doc.id}').click()">Arrastra y suelta aquí ó<br><button class="browse-btn">Selecciona un archivo</button></div>
            <div id="${doc.id}-preview" class="file-preview"></div>
        </div>
    `;
});

$("#card-body").empty().append(`<div class="file-grid">${formHtml}</div>
<div class="row text-center mt-4 justify-content-center">
                <div class="col-4">
                    <div class="btn btn-info mr-3" onclick="paso2(2, 3)">Atras</div>
                    <div class="btn btn-success" onclick="registrarEmpleado(1)">Registrar empleado</div>
                </div>
            </div>
`);

let documentos_ = ["cv", "curp", "domicilio", "nss", "id", "contrato", "bancarios"];
  
  documentos_.forEach(id => {
      let fileInput = document.getElementById(id);
      if (fileInput.files.length > 0) {
          formData.append(id, '');
      }
  });
  }

  function mostrarArchivo(event, previewId) {
  const file = event.target.files[0];
  if (file) {
      const preview = document.getElementById(previewId);
      preview.innerHTML = '';
      
      if (file.type.includes("image")) {
          const img = document.createElement("img");
          img.src = URL.createObjectURL(file);
          img.style.maxWidth = "100px";
          img.style.borderRadius = "8px";
          preview.appendChild(img);
      } else if (file.type.includes("pdf")) {
          const link = document.createElement("a");
          link.href = URL.createObjectURL(file);
          link.textContent = "Ver PDF";
          link.target = "_blank";
          preview.appendChild(link);
      }
  }
  }

  function dropHandler(event, inputId) {
  event.preventDefault();
  let fileInput = document.getElementById(inputId);
  fileInput.files = event.dataTransfer.files;
  mostrarArchivo({ target: fileInput }, inputId + '-preview');
  }

  function dragOverHandler(event) {
  event.preventDefault();
  }

  function registrarEmpleado(n_interacion)
  {
  const documentos = ["cv", "curp", "domicilio", "nss", "id", "contrato", "bancarios"];
  
  if(n_interacion>0){
    documentos.forEach(id => {
      let fileInput = document.getElementById(id);
      if (fileInput.files.length > 0) {
          formData.set(id, fileInput.files[0]);
      }
  });
  }

  $.ajax({
      type: "post",
      url: "./modelo/empleados/registrar-empleado.php",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (response) {
        let icon;  
        if(response.estatus){
          Swal.fire({
            icon: 'success',
            title: 'Empleado registrado correctamente'
          }).then(()=>{
            window.location.reload();
          })  
        }else{
          Swal.fire({
            icon: 'error',
            title: response.mensaje
          }) 
        }
      }
  });
  }