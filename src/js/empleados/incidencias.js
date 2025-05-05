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
  
function mostrarIncidencias() {  
    //$.fn.dataTable.ext.errMode = 'none';

table = $('#incidencias').DataTable({
      
    "bDestroy": true,
    processing: true,
    serverSide: true,
    ajax: './modelo/empleados/lista-incidencias.php',
    rowCallback: function(row, data, index) {
        var info = this.api().page.info();
        var page = info.page;
        var length = info.length;
        var columnIndex = 0; // Índice de la primera columna a enumerar
  
        $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
        $(row).css('cursor','pointer')
       if(data[7]==0){
           $(row).css('background-color','#eeeeee')
       }
      },
     
    columns: [   
    { title: "#",  data: null},
    { title: "ID", data: 1, visible: false},
    { title: "Empleado",          data: 9},
    { title: 'Foto',  data: 12, render: (data)=>{
        return `<img onerror="this.src='./src/img/neumaticos/NA.JPG';" class="foto-incidencia" src="./src/img/fotos_empleados/E${data}.jpg"></img>`;
    }},
    { title: "Fechas",          data: null, render: (row, data, display)=>{

       // Función para formatear la fecha
       const formatFecha = (fecha) => {
        if (!fecha) return ''; // Manejo de valores nulos o vacíos
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: '2-digit' });
    };

    const fechaInicio = formatFecha(row[3]);
    const fechaFin = formatFecha(row[4]);

    return fechaInicio === fechaFin ? fechaInicio : `${fechaInicio} al ${fechaFin}`;
    }},
    { title: "Concepto",       data: 2       }, 
    { title: "Monto",       data: 5,
    render: (data) => {
        return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(data);
    }},
    { title: "Categoria",       data: 10       }, 
    { title: "Tipo",           data: null, render: (row, data, display)=>{
        let etiquet = row[6]==1 ? 'Deduccion' : 'Percepción'
        let class_tipo = row[6]==1 ? 'bg-danger' : 'bg-success'
        if(row[7]==0){
            class_tipo='bg-secondary'
        }
        return `<span class="badge p-2 text-white ${class_tipo}">${etiquet}</span>`
    }},
    { title: "Periocidad", data: null, render: (row, data, display)=>{
        let tag_periocidad
        switch (row[8]) {
            case 1:
                tag_periocidad='Solo esta semana'
                break;
                case 2:
                    tag_periocidad='Cada semana'
                    break;
                    case 3:
                        tag_periocidad='Cada 15 dias'
                        break;
                        case 4:
                            tag_periocidad='Cada mes'
                            break;
        
            default:
                tag_periocidad = 'El id no corresponde a ninguna periodo'
                break;
        }
       return  `<span>${tag_periocidad}</span>`
       
    }},
    { title: "Estatus", data: null, render: (row, data, display)=>{
        let etiquet = row[7]==1 ? 'Activa' : 'Inactiva'
        let class_tipo = row[7]==1 ? 'bg-success' : 'bg-secondary'
        return `<span class="badge p-2 text-white ${class_tipo}">${etiquet}</span>`
    }},
   {title: 'Accion', data: null, render: (row, data)=>{

    return `
       <a href="editar-incidencia.php?id_incidencia=${row[1]}"> <div class="btn btn-warning mr-2"><i class="fas fa-edit"></i></div>
        <i class="fas fa-ellipsis-v"></i>
    `;

   }}
  ],
  paging: true,
  searching: true,
  scrollY: "50vh",
  info: false,
  responsive: false,
  ordering: "enable",
  multiColumnSort: true,
  order: [[1, "desc"]],
  /* 'columnDefs': [
    { 'orderData':[2], 'targets': [1] },
    {
        'targets': [2],
        'visible': false,
        'searchable': false
    },
], */
  //order: [1, "desc"],
 
  
});
//table.columns( [6] ).visible( true );
$("table.dataTable thead").addClass("table-dark")
$("table.dataTable thead").addClass("text-white")
$("table.dataTable").css("background-color", 'white')


// Agregar evento de clic derecho (contextmenu) en las filas de la tabla
$('#incidencias tbody').on('contextmenu', 'tr', function(event) {
        event.preventDefault(); // Evita que se abra el menú contextual del navegador

        // Obtener el ID del registro o cualquier dato necesario
        var data = table.row(this).data();  
        var id = data[1]; // Ajusta el índice según la estructura de tu tabla
        var estatus = data[7]; // Columna 7 contiene el estatus (1 = activo, 0 = inactivo)

        // Mostrar u ocultar opciones según el estatus
        if (estatus == 1) { // Activo
            $('#activar').hide();
            $('#desactivar').show();
        } else { // Inactivo
            $('#activar').show();
            $('#desactivar').hide();
        }


        // Mostrar el menú en la posición del cursor
        $('#context-menu')
            .css({
                top: event.pageY + 'px',
                left: event.pageX + 'px'
            })
            .show()
            .attr('data-id', id); // Guardar el ID para su uso en las acciones
    });

    // Ocultar el menú contextual si se hace clic fuera de él
    $(document).click(function() {
        $('#context-menu').hide();
    });

       // Manejar la opción "Activar"
       $('#activar').click(function() {
        var id = $('#context-menu').attr('data-id');
        $('#context-menu').hide();
        cambiarEstadoIncidencia(3, id);
        // Aquí puedes hacer una petición AJAX para activar el registro
    });

    // Manejar la opción "Desactivar"
    $('#desactivar').click(function() {
        var id = $('#context-menu').attr('data-id');
        $('#context-menu').hide();
        cambiarEstadoIncidencia(1, id);
       
    });

    // Manejar la opción "Eliminar"
    $('#eliminar').click(function() {
        var id = $('#context-menu').attr('data-id');
        $('#context-menu').hide();
        // Aquí puedes hacer una petición AJAX para eliminar el registro
        Swal.fire({
            icon: 'question',
            title: '¿Desea eliminar esta incidencia? ',
            subtitle: 'No quedará registro de ella',
            confirmButtonText: 'Si',
            cancelButtonText: 'Mejor no',
            showCancelButton: true,
            showCloseButton: true
        }).then((r)=>{
            if(r.isConfirmed){
                cambiarEstadoIncidencia(2, id);
              
            }
        })
       
    });


} 

function cambiarEstadoIncidencia(tipo, id){
    $.ajax({
        type: "post",
        url: "./modelo/empleados/cambiar-estado-incidencia.php",
        data: {tipo, id},
        dataType: "json",
        success: function (response) {
            let icon_cambiar = response.estatus ? 'success': 'error'
            
            Swal.fire({
                icon: icon_cambiar,
                title: response.mensaje
            })

            table.ajax.reload(null, false)
        }
    });
}

function setearDatosIncidencia(id){
   
    $.ajax({
        type: "post",
        url: "./modelo/empleados/actualizar-incidencia.php",
        data: {id, 'tipo_peticion': 'traer'},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
               let data_incidencia = response.data.data_incidencia.data[0]
               $("#empleado").val(data_incidencia.id_empleado)
               $("#tipo").val(data_incidencia.tipo)
               $("#categoria").val(data_incidencia.id_categoria)
               $("#monto").val(data_incidencia.monto)
               $("#periocidad").val(data_incidencia.periocidad)
               $("#descripcion").val(data_incidencia.concepto)
               $("#empleado").selectpicker('refresh')
               $("#fecha-inicio").val(data_incidencia.fecha_inicio)
               $("#fecha-final").val(data_incidencia.fecha_final)
               $("#tipo").selectpicker('refresh')
               $("#categoria").selectpicker('refresh')
               $("#periocidad").selectpicker('refresh')
               let area = $("#area-cambios-extras")
               console.log(data_incidencia.categoria);
               if(data_incidencia.id_categoria == 5){
                area.append(`
                <div class="row m-3">
                    <div class="col-6 col-md-3">
                        <label for="monto-prestamo">Monto del prestamo</label>
                        <input class="form-field" placeholder="0.00" id="monto-prestamo" type="number">
                    </div>
                    <div class="col-6 col-md-3" id="area-monto-descontar-periodo">
                         
                    </div>            
                </div>            
                `)
                $("#monto-prestamo").val(data_incidencia.monto_prestamo)
               }
               let etiqueta_fecha
               if(data_incidencia.fecha_inicio == data_incidencia.fecha_final){
                 fecha_obj = new Date(data_incidencia.fecha_inicio);
                let formato = fecha_obj.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
                etiqueta_fecha = formato.replace('.', '')
               }else{
                fecha_obj_1 = new Date(data_incidencia.fecha_inicio);
                fecha_obj_2 = new Date(data_incidencia.fecha_final);
                let formato_1 = fecha_obj_1.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
                let formato_2 = fecha_obj_2.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
                let etiqueta_fecha_1 = formato_1.replace('.', '')
                let etiqueta_fecha_2 = formato_2.replace('.', '')
                etiqueta_fecha= etiqueta_fecha_1 + ' al ' + etiqueta_fecha_2
               }
              
               $("#fechas-incidencia").text(etiqueta_fecha)
               console.log(data_incidencia);
            }
        }
    });
}

function actualizarIncidencia(id_incidencia){
  
    let formDatas = new FormData()
    formDatas.append('id_incidencia', id_incidencia)
    formDatas.append('tipo_peticion', 'actualizar')
    let formulario = document.querySelectorAll('#formulario-nueva-incidencia input, #formulario-nueva-incidencia select, #formulario-nueva-incidencia textarea')
    let valido = true;
    formulario.forEach(element => {
        id_elemento = element.id
        value_elemento = element.value
      
        if(validarFormulario(id_elemento, value_elemento, element)){
            formDatas.append(id_elemento, value_elemento)
        }else{
            valido = false;
        }
    });
    if(valido){
        $.ajax({
            type: "post",
            url: "./modelo/empleados/actualizar-incidencia.php",
            contentType: false,
            processData: false,
            data: formDatas,
            dataType: "json",
            success: function (response) {
                if(response.estatus){
                  icon_registro='success' 
                }else{icon_registro='error'}
                Swal.fire({
                    icon: icon_registro,
                    title: response.mensaje
                })
            }
        });
    }
}