import React, { useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useFact, useFactDelete } from '../hooks'
import { Link } from 'react-router-dom'

const Fact = () => {
    const { id } = useParams()
    const navigate = useNavigate()
    const [deleteError, setDeleteError] = useState(null)
    const { data, loading, error } = useFact(id)
    const { deleting, deleteError: hookDeleteError, deleteFact } = useFactDelete(id, {
        onSuccess: () => navigate('/facts'),
        onError: (err) => setDeleteError(err)
    })

    if (loading) {
        return (
            <div className="container">
                <div className="loading">
                    <div>Chargement du fact...</div>
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

    if (!data) {
        return (
            <div className="container">
                <div className="text-center">
                    <h2>Fact non trouvé</h2>
                    <p>Le fact demandé n'existe pas.</p>
                    <Link to="/facts" className="btn-link">
                        Retour à la liste
                    </Link>
                </div>
            </div>
        )
    }

    const handleDelete = async () => {
        if (!id) return
        const confirmDelete = window.confirm('Voulez-vous vraiment supprimer ce fact ?')
        if (!confirmDelete) return
        setDeleteError(null)
        await deleteFact()
    }

    return (
        <div className="container">
            <div className="card fact-detail sober">
                <header className="mb-3">
                    <h1>Fact #{data.id}</h1>
                    {data.techno && (
                        <div className="tech-badge minimal">
                            <span className="tech-label">Technologie:</span>
                            <span className="tech-value">{data.techno}</span>
                        </div>
                    )}
                </header>
                
                <div className="fact-content">
                    <div className="fact-text-large">
                        {data.fact}
                    </div>
                </div>
                
                <div className="fact-actions mt-4">
                    <Link to={`/facts/${data.id}/edit`} className="btn-link">
                        Éditer ce fact
                    </Link>
                    <button
                        type="button"
                        className="btn-link danger"
                        onClick={handleDelete}
                        disabled={deleting}
                    >
                        {deleting ? 'Suppression…' : 'Supprimer ce fact'}
                    </button>
                    <Link to="/facts" className="btn-link secondary">
                        Retour à la liste
                    </Link>
                </div>
                {(deleteError || hookDeleteError) && (
                    <div className="error mt-3">
                        <p>{(deleteError || hookDeleteError)?.message}</p>
                    </div>
                )}
            </div>
        </div>
    )
}

export default Fact


