function generarToken(){
   /* token = random() + random();
    alert(token);*/

    Swal.fire({
        title: "Ingrese el token",
        icon: 'info',
        html: '<form>'+
        '<label >Ingrese el token de acceso para poder cambiar el precio de la llanta</span><br><br>'+
        '<input class="form-control" placeholder="Codigo"></form>',
        showCancelButton: true,
        cancelButtonText: 'Cerrar',
        cancelButtonColor: '#00e059',
        showConfirmButton: true,
        confirmButtonText: 'Validar', 
        cancelButtonColor:'#ff764d',
        focusConfirm: false,
        iconColor : "#36b9cc",
        backdrop: `
                     transparent
                     no-repeat
                     blur(10px)
                `
});
}

function random() {
    return Math.random().toString(36).substr(2); // Eliminar `0.`
};