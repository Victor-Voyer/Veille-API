import React from 'react'
import Facts from './pages/Facts'
import Fact from './pages/Fact'
import './App.css'
import { Routes, Route } from 'react-router-dom'

const App = () => {
    return (        
    <>
      <Routes>
        <Route path="/facts" element={<Facts />} />
        <Route path="/facts/:id" element={<Fact />} />
      </Routes>
    </>
    )
};

export default App;