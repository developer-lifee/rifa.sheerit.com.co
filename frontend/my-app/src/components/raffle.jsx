// src/components/Raffle.js
import React, { useState, useEffect } from 'react';
import axios from 'axios';
import RaffleGrid from './RaffleGrid';

function Raffle() {
  // Estados para manejar el término de búsqueda, el correo del cliente, números seleccionados, números de la rifa, y el precio por número
  const [searchTerm, setSearchTerm] = useState('');
  const [customerEmail, setCustomerEmail] = useState('');
  const [selectedNumbers, setSelectedNumbers] = useState([]);
  const [numbers, setNumbers] = useState([]);
  const pricePerNumber = 20000;

  // useEffect para obtener los números de la rifa al cargar el componente
  useEffect(() => {
    axios
      .get('https://rifa.sheerit.com.co/backend/api/get_numbers.php')
      .then((response) => {
        setNumbers(response.data);
      })
      .catch((error) => {
        console.error('Error al obtener los números:', error);
      });
  }, []);

  // Función para manejar el clic en un número de la rifa
  const handleNumberClick = (number) => {
    if (number.status === 'reserved') {
      return;
    }
    const numberId = number.number_id.toString();

    if (selectedNumbers.includes(numberId)) {
      setSelectedNumbers(selectedNumbers.filter((num) => num !== numberId));
    } else {
      setSelectedNumbers([...selectedNumbers, numberId]);
    }
  };

  // Función para manejar el cambio en el campo de búsqueda
  const handleSearchChange = (e) => {
    setSearchTerm(e.target.value);
  };

  // Función para manejar la compra de los números seleccionados
  const handlePurchase = () => {
    if (selectedNumbers.length === 0 || customerEmail === '') {
      alert('Por favor selecciona al menos un número y proporciona tu correo electrónico.');
      return;
    }

    const orderId = 'MY-ORDER-' + Date.now();
    const totalPrice = selectedNumbers.length * pricePerNumber;

    // Solicitar el integritySignature al backend
    axios
      .post('https://rifa.sheerit.com.co/backend/api/generate_signature.php', {
        orderId: orderId,
        amount: totalPrice.toString(),
        currency: 'COP',
      })
      .then((response) => {
        const integritySignature = response.data.integritySignature;

        // Crear la instancia de BoldCheckout
        const checkout = new window.BoldCheckout({
          orderId: orderId,
          currency: 'COP',
          amount: totalPrice.toString(),
          apiKey: '1y0D48xaDriWO_CNz7oXUopfkKx5VjiExsdDW0gj2eA',
          integritySignature: integritySignature,
          description: 'Compra de boletos de rifa',
          redirectionUrl: `https://rifa.sheerit.com.co/confirm_purchase.php?orderId=${orderId}`,
        });

        // Abrir la pasarela de pago de Bold
        checkout.open();
      })
      .catch((error) => {
        console.error('Error al obtener el integritySignature:', error);
        alert('Error al procesar el pago.');
      });
  };

  const totalPrice = selectedNumbers.length * pricePerNumber;

  return (
    <div>
      <h1 className="text-2xl font-bold text-center my-4">Selecciona tu número de la rifa</h1>

      {/* Barra de búsqueda */}
      <div className="flex justify-center mb-4">
        <input
          type="text"
          placeholder="Busca tu número de la rifa"
          value={searchTerm}
          onChange={handleSearchChange}
          className="border rounded p-2 mr-2"
        />
      </div>

      {/* Input para el correo del cliente */}
      <div className="flex justify-center mb-4">
        <label htmlFor="customerEmail" className="mr-2">
          Correo electrónico:
        </label>
        <input
          type="email"
          id="customerEmail"
          name="customerEmail"
          placeholder="Ingresa tu correo"
          required
          className="border rounded p-2"
          value={customerEmail}
          onChange={(e) => setCustomerEmail(e.target.value)}
        />
      </div>

      {/* Grilla de números */}
      <RaffleGrid
        numbers={numbers}
        searchTerm={searchTerm}
        selectedNumbers={selectedNumbers}
        handleNumberClick={handleNumberClick}
      />

      {/* Botón de compra */}
      <div className="flex justify-center mt-4">
        <button
          type="button"
          onClick={handlePurchase}
          className="bg-green-500 text-white px-6 py-2 rounded"
        >
          Comprar por ${totalPrice.toLocaleString()}
        </button>
      </div>
    </div>
  );
}

export default Raffle;