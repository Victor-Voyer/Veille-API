import React from 'react'
import { Link } from 'react-router-dom'

const FactCard = ({ fact }) => {
    if (!fact) return null
    
    return (
        <div className="card fact-card">
            <div className="fact-content">
                <p className="fact-text">{fact.fact}</p>
                {fact.techno && (
                    <div className="tech-badge">
                        <span className="tech-label">Tech:</span>
                        <span className="tech-value">{fact.techno}</span>
                    </div>
                )}
            </div>
            {fact.id && (
                <div className="fact-actions">
                    <Link to={`/facts/${fact.id}`} className="btn-link">
                        Voir les détails
                    </Link>
                </div>
            )}
        </div>
    )
}

export default FactCard


