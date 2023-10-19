function aprovMercancia(id_dc, tipo, accion, id_movimiento){
    /**    
        Acción:
        1.- Acción Aprobar
        2.- Acción Cancelar

        Tipo:
        1.- Tipo por Enviar
        2.- Tipo por Recibir
    */
        let tipo_req = tipo == 1 ? 'por enviar' : 'por recibir';
        //let accion_text = accion == 1 ? 'Aprobar' : 'Cancelar';
        console.log(accion);
        console.log(tipo);
        if(accion ==2){
            Swal.fire({
                icon: 'question',
                html: `<p>¿Porque estas cancelando este requerimiento ${tipo_req} </p>
                <textarea id="comentario-requerimiento" class="form-control" placeholder="Escribe aquí tu motivo... "></textarea>`,
                confirmButtonText: 'Cancelar'
            }).then(function(res){
                let comentario = $("#comentario-requirimiento").val();
                if(res.isConfirmed){
                    procesarMercancia(id_dc, tipo, accion, comentario, id_movimiento);
                }
            })
        }else{
            Swal.fire({
                html: `<p>¿Deseas aprobar este requerimiento ${tipo_req}</p>`,
                confirmButtonText: 'Aprobar',
            }).then(function(re){
                if(re.isConfirmed){
                    let comentario = '';
                    procesarMercancia(id_dc, tipo, accion, comentario, id_movimiento);
                }
            })
        }
}

function procesarMercancia(id_dc, tipo, accion, comentario, id_movimiento){
    console.log(id_movimiento);
    $.ajax({
        type: "post",
        url: "./modelo/requerimientos/procesar-mercancia.php",
        data: {id_dc, tipo, accion, comentario, id_movimiento},
        dataType: "json",
        success: function (response) {
            if(response.estatus){
                Swal.fire({
                    icon: 'success',
                    html: response.mensaje,
                    allowOutsideClick: false
                }).then(function(r){
                    if(r.isConfirmed){
                        window.location.reload();
                    }
                })
            }else{
                Swal.fire({
                    icon: 'error',
                    html: `Ocurrio un error al actualizar mercancia actual: ${response.mensaje}` ,
                    allowOutsideClick: false
                })
            }
        }
    });
}