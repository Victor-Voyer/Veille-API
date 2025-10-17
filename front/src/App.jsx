import React from 'react'
import Facts from './pages/Facts'
import Fact from './pages/Fact'
import FactEdit from './pages/FactEdit'
import './App.css'
import { Routes, Route } from 'react-router-dom'

const App = () => {
    return (        
    <>
      <Routes>
        <Route path="/" element={<Facts />} />
        <Route path="/facts" element={<Facts />} />
        <Route path="/facts/:id" element={<Fact />} />
        <Route path="/facts/:id/edit" element={<FactEdit />} />
      </Routes>
    </>
    )
};

export default App;