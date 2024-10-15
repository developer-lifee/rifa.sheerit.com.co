// src/components/RaffleGrid.js
import React from 'react';
import RaffleNumber from './raffleNumber.jsx';

function RaffleGrid({ numbers, searchTerm, selectedNumbers, handleNumberClick }) {
  return (
    <div className="grid grid-cols-5 sm:grid-cols-10 md:grid-cols-20 gap-2">
      {numbers
        .filter((number) => number.number_id.toString().includes(searchTerm))
        .map((number) => (
          <RaffleNumber
            key={number.number_id}
            number={number}
            isSelected={selectedNumbers.includes(number.number_id.toString())}
            onClick={() => handleNumberClick(number)}
          />
        ))}
    </div>
  );
}

export default RaffleGrid;
