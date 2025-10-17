import React from 'react'
import { useApi } from '../hooks/useApi'
import { Link } from 'react-router-dom'

const API_URL = 'http://localhost:8000/api/facts?_format=json'

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
            {facts.map((fact, index) => (
                <div key={fact.id ?? index}>
                    <div>{fact.fact}</div>
                    {fact.techno && <small>Tech: {fact.techno}</small>}
                    <Link to={`/facts/${fact.id}`}>Voir</Link>
                </div>
            ))}
        </div>
    )
};

export default Facts;
