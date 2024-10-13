<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rifa</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://checkout.bold.co/library/boldPaymentButton.js"></script>
</head>
<body>
    <h1 class="text-2xl font-bold text-center my-4">Selecciona tu número de la rifa</h1>

    <div class="container mx-auto">
        <!-- Barra de búsqueda -->
        <div class="flex justify-center mb-4">
            <input type="text" id="searchInput" placeholder="Busca tu número de la rifa" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" class="border rounded p-2 mr-2">
            <button id="searchButton" class="bg-blue-500 text-white px-4 py-2 rounded">Buscar</button>
        </div>

        <!-- Input para el correo del cliente -->
        <div class="flex justify-center mb-4">
            <label for="customerEmail" class="mr-2">Correo electrónico:</label>
            <input type="email" id="customerEmail" name="customerEmail" placeholder="Ingresa tu correo" required class="border rounded p-2">
        </div>

        <!-- Input oculto para almacenar los números seleccionados -->
        <input type="hidden" id="selectedNumbers" name="selectedNumbers">

        <!-- Grilla de números traídos de la base de datos -->
        <div id="raffleGrid">
            <?php include 'raffle_grid.php'; ?>
        </div>

        <!-- Botón de compra -->
        <div class="flex justify-center mt-4">
            <button type="button" id="custom-button-payment" class="bg-green-500 text-white px-6 py-2 rounded">Comprar por $0</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var selectedNumbers = JSON.parse(localStorage.getItem('selectedNumbers')) || [];
            var pricePerNumber = 20000; // Precio fijo por cada número

            // Actualizar la interfaz según los números seleccionados
            selectedNumbers.forEach(function(num) {
                var element = $('.raffle-number[data-numero="' + num + '"]');
                element.addClass('border-4 border-blue-500');
            });

            updateTotalPrice();

            // Selección de números
            $('.raffle-number').click(function() {
                if ($(this).hasClass('cursor-not-allowed')) {
                    // No hacer nada si el número está reservado
                    return;
                }

                var numberId = $(this).data('numero').toString();

                if ($(this).hasClass('border-4 border-blue-500')) {
                    $(this).removeClass('border-4 border-blue-500');
                    selectedNumbers = selectedNumbers.filter(num => num !== numberId);
                } else {
                    $(this).addClass('border-4 border-blue-500');
                    selectedNumbers.push(numberId);
                }

                localStorage.setItem('selectedNumbers', JSON.stringify(selectedNumbers));
                $('#selectedNumbers').val(selectedNumbers.join(','));

                updateTotalPrice();
            });

            function updateTotalPrice() {
                var totalPrice = selectedNumbers.length * pricePerNumber;
                $('#custom-button-payment').text('Comprar por $' + totalPrice.toLocaleString());
            }

            // Procesar búsqueda
            $('#searchButton').click(function() {
                var searchValue = $('#searchInput').val().trim();
                window.location.href = '?search=' + encodeURIComponent(searchValue);
            });

            // Procesar pago al hacer clic en el botón de pago
            $('#custom-button-payment').click(function() {
                var selectedNumbersVal = $('#selectedNumbers').val();
                var customerEmail = $('#customerEmail').val();

                if (selectedNumbersVal === "" || customerEmail === "") {
                    alert('Por favor selecciona un número de la rifa y proporciona tu correo electrónico.');
                    return;
                }

                var orderId = "MY-ORDER-" + Date.now();

                // Crear la instancia de BoldCheckout
                const checkout = new BoldCheckout({
                    orderId: orderId,
                    currency: 'COP',
                    amount: (selectedNumbers.length * pricePerNumber).toString(), // Calcular el total dinámico
                    apiKey: '1y0D48xaDriWO_CNz7oXUopfkKx5VjiExsdDW0gj2eA', // Reemplaza con tu clave API real
                    integritySignature: '', // El hash de integridad será generado por el servidor
                    description: 'Compra de boletos de rifa',
                    redirectionUrl: 'https://rifa.sheerit.com.co/confirm_purchase.php?orderId=' + orderId
                });

                // Abrir la pasarela de pago de Bold
                checkout.open();
            });
        });
    </script>
</body>
</html>
