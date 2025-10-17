import React from 'react'
import { Link } from 'react-router-dom'

const FactCard = ({ fact }) => {
    if (!fact) return null
    return (
        <div>
            <div>{fact.fact}</div>
            {fact.techno && <small>Tech: {fact.techno}</small>}
            {fact.id && <Link to={`/facts/${fact.id}`}>Voir</Link>}
        </div>
    )
}

export default FactCard


