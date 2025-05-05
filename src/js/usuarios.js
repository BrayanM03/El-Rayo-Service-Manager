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


 
  function mostrarUsuarios() {  
    //$.fn.dataTable.ext.errMode = 'none';
   // ocultarSidebar();
  table = $('#usuarios').DataTable({
      
    processing: true,
    serverSide: true,
    ajax: './modelo/configuraciones/configuracion_usuarios/historial-usuarios.php',
    rowCallback: function(row, data, index) {
        var info = this.api().page.info();
        var page = info.page;
        var length = info.length;
        var columnIndex = 0; // Índice de la primera columna a enumerar
        
        $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
        $(row).css('background-color','white')
        
      },
     
    columns: [   
    { title: "#",              data: null     },
    { title: "Nombre",          data: 1 , render: function(data, type, row){
        return row[2] + ' ' + row[3]
    }},
    { title: "Usuario",   data: 4 },
    { title: "Contraseña encrypt",   data: 5 },
    { title: "Sucursal",   data: 6 },
    { title: "Estatus",    data: 8 , render: function(data, type, row){
        data = parseInt(data)
        switch(data){
          case 1:
          var estatus = 'Activo';
          var class_btn = 'success'
          break;
          case 0:
          var estatus = 'Inactivo';
          var class_btn = 'secondary'
          break;  
          default:
            var estatus ='Sin información'
            var class_btn = 'secondary'
        }
        
          return `<div class="btn btn-${class_btn}" style="font-size: 12px !important;">${estatus}</div>`;
        
      }},
   

    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        /* rol = $("#emp-title").attr("sesion_rol");
        if(rol == "1" || rol == '2'){ */
        return `<div class="btn btn-primary" onclick="verRequerimiento(${row[1]})">
        <i class="fa fa-eye"></i></div>`
      /*  }else{
        return '';
       } */
         },
    },
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: false,
  responsive: false,
  ordering: "enable",
  multiColumnSort: true,
  order: [[2, "asc"]],
  
  //order: [1, "desc"],
  
  
  });
  //table.columns( [6] ).visible( true );
  $("table.dataTable thead").addClass("table-dark")
  $("table.dataTable").css("background-color", 'white')
  
  }

  function agregarEmpleado(){
    let validacion_formulario = validacionFormularioEmpleado()
    console.log(validacion_formulario);
    let datos = {};
    if(validacion_formulario){
    var formData = new FormData();
    let formulario = document.querySelectorAll('#formulario-nuevo-empleado input, #formulario-nuevo-empleado select,  #formulario-nuevo-empleado textarea')
    formulario.forEach(element => {
        let id_elemento = element.id;
        let valor = element.value;
        formData.append(id_elemento, valor);
    })
    let extension_archivo;
    let documento_adjunto = document.getElementById('foto-perfil');
    var file =  documento_adjunto.files[0];
    if(file != undefined){
        const extension = file.name.split('.').pop();
        extension_archivo=extension;
    };
    formData.append('extension_archivo', extension_archivo);
    formData.append('documento_adjunto', file);

    $.ajax({
        type: "post",
        url: "./modelo/empleados/registrar-empleado.php",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
            formulario.forEach(element => {
                let id_elemento = element.id;
                element.value='';
                formData.append(id_elemento, valor);
            })
        }
    });

    }else{
        toastr.error('Complete el formulario para registrar un nuevo empleado', 'Error')
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

  function setearFotoThumb(){
    console.log('Entréee');
    let input_comprobante = document.getElementById('foto-perfil');
    let file = input_comprobante.files[0];
    console.log(file);
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
    console.log(file);
    file.value=''
      area_canvas.empty().append(`<img src="./src/img/neumaticos/NA.JPG" id="foto" alt="" style="width: 9rem; border:1px whitesmoke solid; border-radius:7px">
     `)
      flag !== 1 ? toastr.success('Documento adjunto eliminado con exito' ) : false; 
      $("#input-comprobante-edicion").val('').attr('eliminar', true);
    eliminar_comprobante = true;
  }
