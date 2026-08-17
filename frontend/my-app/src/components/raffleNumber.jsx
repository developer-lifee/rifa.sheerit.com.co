import React from 'react';

function RaffleNumber({ number, isSelected, onClick }) {
  const numberId = number.number_id.toString();

  return (
    <div
      className={`p-2 text-center rounded ${
        number.status === 'reserved'
          ? 'bg-gray-300 text-gray-600 cursor-not-allowed opacity-50'
          : isSelected
          ? 'bg-blue-500 text-white border-4 border-blue-500'
          : 'bg-green-500 text-white hover:bg-green-600 cursor-pointer'
      }`}
      onClick={onClick}
    >
      {numberId}
    </div>
  );
}

export default RaffleNumber;