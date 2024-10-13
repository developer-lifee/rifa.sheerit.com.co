$(document).ready(function() {
    var selectedNumbers = []; // Arreglo para almacenar los números seleccionados
    var pricePerNumber = 20000; // Precio por número
    var totalPrice = 0; // Precio total

    // Marcar/desmarcar números seleccionados
    $('.grid-item.disponible').click(function() {
        var selectedNumber = $(this).text().trim();
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            // Eliminar el número del arreglo y restar el precio
            selectedNumbers = selectedNumbers.filter(number => number !== selectedNumber);
            totalPrice -= pricePerNumber;
        } else {
            $(this).addClass('selected');
            // Agregar el número al arreglo y sumar el precio
            selectedNumbers.push(selectedNumber);
            totalPrice += pricePerNumber;
        }

        // Actualizar el precio total en el botón de compra
        $('#purchaseButton').text('Comprar por $' + totalPrice);
    });

    // Acción del botón de compra
    $('#purchaseButton').click(function() {
        if (selectedNumbers.length > 0) {
            $.ajax({
                url: 'request_payment.php',
                method: 'POST',
                data: { 
                    numberIds: selectedNumbers.join(','), // Enviar todos los números seleccionados
                    userId: $('#userId').val()
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.payment_url) {
                        window.location.href = data.payment_url; // Redirigir a la pasarela de pago
                    } else {
                        alert('Error al generar el pago.');
                    }
                },
                error: function() {
                    alert('Error en la solicitud.');
                }
            });
        } else {
            alert('Por favor selecciona al menos un número.');
        }
    });

    // Búsqueda de números
    $('#searchButton').click(function() {
        var searchValue = $('#searchInput').val().trim();
        $('.grid-item').each(function() {
            var number = $(this).text().trim();
            if (number.includes(searchValue)) {
                $(this).show(); // Mostrar los números que coincidan con la búsqueda
            } else {
                $(this).hide(); // Ocultar los números que no coincidan
            }
        });
    });
});