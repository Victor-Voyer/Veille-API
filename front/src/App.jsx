import React from 'react'
import Facts from './pages/Facts'
import Fact from './pages/Fact'
import FactEdit from './pages/FactEdit'
import Nav from './components/Nav'
import './App.css'
import { Routes, Route } from 'react-router-dom'

const App = () => {
    return (        
        <div className="app">
            <Nav />
            <main className="main-content">
                <Routes>
                    <Route path="/" element={<Facts />} />
                    <Route path="/facts/:id" element={<Fact />} />
                    <Route path="/facts/:id/edit" element={<FactEdit />} />
                </Routes>
            </main>
        </div>
    )
};

export default App;