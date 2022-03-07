function MostrarClientes() {  
   // $.fn.dataTable.ext.errMode = 'none';

table = $('#historial-cortes').DataTable({
    
    "processing": true,
    "serverSide": true,
     "ajax": './modelo/cortes/server_processing.php',   
});
}

MostrarClientes();