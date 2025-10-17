import React from 'react'
import { useApi } from '../hooks'
import FactCard from '../components/FactCard'

const API_URL = 'http://localhost:8000/api/facts'

const Facts = () => {
    const { data, loading, error } = useApi(API_URL)
    const facts = Array.isArray(data) ? data : []

    if (loading) {
        return (
            <div className="container">
                <div className="loading">
                    <div>Chargement des facts...</div>
                </div>
            </div>
        )
    }

    if (error) {
        return (
            <div className="container">
                <div className="error">
                    <h3>Erreur de chargement</h3>
                    <p>{error.message}</p>
                </div>
            </div>
        )
    }

    if (facts.length === 0) {
        return (
            <div className="container">
                <div className="text-center">
                    <h2>Aucun fact trouvé</h2>
                    <p>Il n'y a pas encore de facts dans la base de données.</p>
                </div>
            </div>
        )
    }

    return (
        <div className="container">
            <header className="mb-4">
                <h1>Facts de Veille Technologique</h1>
                <p className="text-secondary">
                    Découvrez <strong>{facts.length}</strong> fact{facts.length > 1 ? 's' : ''} intéressant{facts.length > 1 ? 's' : ''} sur les technologies
                </p>
            </header>
            
            <div className="grid grid-3">
                {facts.map((fact, index) => (
                    <FactCard key={fact.id ?? index} fact={fact} />
                ))}
            </div>
        </div>
    )
};

export default Facts;
