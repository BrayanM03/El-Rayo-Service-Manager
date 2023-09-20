

function alertaCorte(){
    console.log('Soy yo de nuevo');
    Swal.fire({
        icon: 'info',
        text: `Esta es la hora del corte, las ventas o abonos realizados despues de esta hora
        pasaran al dia siguiente, o al lunes en caso de los sabados.`
    })
}