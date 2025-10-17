import React from 'react'
import { useApi } from '../hooks'
import FactCard from '../components/FactCard'

const API_URL = 'http://localhost:8000/api/facts'

const Facts = () => {
    const { data, loading, error } = useApi(API_URL)
    const facts = Array.isArray(data) ? data : []

    if (loading) {
        return <div>Loading...</div>
    }

    if (error) {
        return <div>Error: {error.message}</div>
    }

    if (facts.length === 0) {
        return <div>No facts found</div>
    }

    return (
        <div>
            <p>Nombre de facts: {facts.length}</p>
            {facts.map((fact, index) => (
                <FactCard key={fact.id ?? index} fact={fact} />
            ))}
        </div>
    )
};

export default Facts;
