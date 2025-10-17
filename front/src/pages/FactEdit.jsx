import React from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useFactEditor } from '../hooks'

const FactEdit = () => {
    const { id } = useParams()
    const navigate = useNavigate()
    const { data, loading, error, factText, setFactText, techno, setTechno, saving, saveError, submit } = useFactEditor(id)

    const onSubmit = async (e) => {
        e.preventDefault()
        const ok = await submit()
        if (ok) navigate(`/facts/${id}`)
    }

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
                    <p>Le fact à éditer n'existe pas.</p>
                    <button onClick={() => navigate('/facts')} className="btn-link">
                        Retour à la liste
                    </button>
                </div>
            </div>
        )
    }

    return (
        <div className="container">
            <div className="card">
                <header className="mb-4">
                    <h1>Éditer le fact #{id}</h1>
                    <p className="text-secondary">Modifiez le contenu et la technologie associée</p>
                </header>

                <form onSubmit={onSubmit} className="form-container">
                    <div className="form-group">
                        <label htmlFor="fact-text">
                            Contenu du fact
                        </label>
                        <textarea 
                            id="fact-text"
                            value={factText} 
                            onChange={(e) => setFactText(e.target.value)}
                            placeholder="Saisissez le contenu du fact..."
                            required
                        />
                    </div>

                    <div className="form-group">
                        <label htmlFor="techno">
                            Technologie
                        </label>
                        <input 
                            id="techno"
                            type="text"
                            value={techno} 
                            onChange={(e) => setTechno(e.target.value)}
                            placeholder="Ex: React, Node.js, Python..."
                        />
                    </div>

                    {saveError && (
                        <div className="error mb-3">
                            <strong>Erreur lors de l'enregistrement:</strong> {saveError.message}
                        </div>
                    )}

                    <div className="form-actions">
                        <button 
                            type="submit" 
                            disabled={saving}
                            className={saving ? 'secondary' : ''}
                        >
                            {saving ? 'Enregistrement en cours...' : 'Enregistrer les modifications'}
                        </button>
                        <button 
                            type="button" 
                            onClick={() => navigate(-1)} 
                            disabled={saving}
                            className="secondary"
                        >
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    )
}

export default FactEdit


