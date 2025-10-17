import { useState } from 'react'
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom'
import './App.css'
import Facts from './pages/Facts'

function App() {


  return (
    <>
        <Routes>
          <Route path="/" element={<Facts />} />
        </Routes>

    </>
  )
}

export default App
