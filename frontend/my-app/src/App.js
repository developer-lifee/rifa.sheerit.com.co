import React from 'react';
import RaffleGrid from './components/RaffleGrid';

function App() {
  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold text-center my-4">Selecciona tu número de la rifa</h1>
      <RaffleGrid />
    </div>
  );
}

export default App;
