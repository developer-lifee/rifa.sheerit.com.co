import React, { useEffect, useState } from 'react';
import axios from 'axios';

function RaffleGrid() {
  const [numbers, setNumbers] = useState([]);
  const [selectedNumbers, setSelectedNumbers] = useState([]);
  const [customerEmail, setCustomerEmail] = useState('');
  const pricePerNumber = 20000;

  useEffect(() => {
    // Reemplaza la URL con la de tu servidor
    axios.get('https://rifa.sheerit.com.co/backend/api/get_numbers.php')
      .then(response => {
        setNumbers(response.data);
      })
      .catch(error => {
        console.error('Error al obtener los números:', error);
      });
  }, []);

  const handleNumberClick = (number) => {
    if (number.status === 'reserved') {
      return;
    }
    const numberId = number.number_id;
    if (selectedNumbers.includes(numberId)) {
      setSelectedNumbers(selectedNumbers.filter(n => n !== numberId));
    } else {
      setSelectedNumbers([...selectedNumbers, numberId]);
    }
  };

  const handlePurchase = () => {
    if (selectedNumbers.length === 0 || customerEmail === '') {
      alert('Por favor selecciona al menos un número y proporciona tu correo electrónico.');
      return;
    }

    const data = new FormData();
    data.append('selectedNumbers', selectedNumbers.join(','));
    data.append('customerEmail', customerEmail);

    axios.post('https://rifa.sheerit.com.co/backend/api/request_payment.php', data)
      .then(response => {
        if (response.data.payment_url) {
          window.location.href = response.data.payment_url;
        } else {
          alert('Error al generar el pago.');
        }
      })
      .catch(error => {
        console.error('Error en la solicitud:', error);
        alert('Error en la solicitud.');
      });
  };

  const totalPrice = selectedNumbers.length * pricePerNumber;

  return (
    <div>
      {/* Campo de correo electrónico */}
      <div className="flex justify-center mb-4">
        <label htmlFor="customerEmail" className="mr-2">Correo electrónico:</label>
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
      <div className="grid grid-cols-5 sm:grid-cols-10 md:grid-cols-20 gap-2">
        {numbers.map(number => (
          <div
            key={number.number_id}
            className={`p-2 text-center rounded ${
              number.status === 'reserved'
                ? 'bg-gray-300 text-gray-600 cursor-not-allowed opacity-50'
                : selectedNumbers.includes(number.number_id)
                ? 'bg-blue-500 text-white'
                : 'bg-green-500 text-white hover:bg-green-600 cursor-pointer'
            }`}
            onClick={() => handleNumberClick(number)}
          >
            {number.number_id}
          </div>
        ))}
      </div>

      {/* Botón de compra */}
      <div className="flex justify-center mt-4">
        <button
          type="button"
          className="bg-green-500 text-white px-6 py-2 rounded"
          onClick={handlePurchase}
        >
          Comprar por ${totalPrice.toLocaleString()}
        </button>
      </div>
    </div>
  );
}

export default RaffleGrid;
