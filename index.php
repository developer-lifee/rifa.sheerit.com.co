<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rifa</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://checkout.bold.co/library/boldPaymentButton.js"></script>
</head>
<body>
    <h1>Selecciona tu número de la rifa</h1>

    <div class="form-container">
        <!-- Barra de búsqueda -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Busca tu número de la rifa">
            <button id="searchButton">Buscar</button>
        </div>

        <!-- Input para el correo del cliente -->
        <div class="customer-info">
            <label for="customerEmail">Correo electrónico:</label>
            <input type="email" id="customerEmail" name="customerEmail" placeholder="Ingresa tu correo" required>
        </div>
    </div>

    <!-- Grilla de números traídos de la base de datos -->
    <div class="raffle-grid">
        <?php include 'raffle_grid.php'; ?>
    </div>

    <!-- Botón de compra -->
    <div class="purchase-container">
        <button type="button" id="custom-button-payment">Comprar por $0</button>
    </div>

    <script>
        $(document).ready(function() {
            var selectedNumbers = [];
            var pricePerNumber = 20000; // Precio fijo por cada número

            // Selección de números
            $('.raffle-number.available').click(function() {
                var numberId = $(this).text().trim();

                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                    selectedNumbers = selectedNumbers.filter(num => num !== numberId);
                } else {
                    $(this).addClass('selected');
                    selectedNumbers.push(numberId);
                }

                $('#selectedNumbers').val(selectedNumbers.join(','));
                var totalPrice = selectedNumbers.length * pricePerNumber;
                $('#custom-button-payment').text('Comprar por $' + totalPrice);
            });

            // Procesar búsqueda
            $('#searchButton').click(function() {
                var searchValue = $('#searchInput').val().trim();
                $('.raffle-number').each(function() {
                    var number = $(this).text().trim();
                    if (number.includes(searchValue)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
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
                    amount: (selectedNumbers.length * 20000).toString(), // Calcular el total dinámico
                    apiKey: '1y0D48xaDriWO_CNz7oXUopfkKx5VjiExsdDW0gj2eA',
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
