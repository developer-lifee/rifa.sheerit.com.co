$(document).ready(function() {
    $('#purchaseButton').click(function() {
        var numberId = $('#selectedNumber').val(); // El número seleccionado
        var userId = $('#userId').val(); // ID del usuario si lo tienes

        // Enviar solicitud de creación de pago a PHP
        $.ajax({
            url: 'request_payment.php',
            method: 'POST',
            data: {
                numberId: numberId,
                userId: userId
            },
            success: function(response) {
                var data = JSON.parse(response);
                
                if (data.payment_url) {
                    // Redirigir al usuario a la pasarela de pagos de Bold
                    window.location.href = data.payment_url;
                } else {
                    alert('Error al generar el pago.');
                }
            },
            error: function() {
                alert('Error en la solicitud.');
            }
        });
    });
});