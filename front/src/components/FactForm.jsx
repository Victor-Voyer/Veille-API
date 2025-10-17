import React from 'react'

const FactForm = ({ factText, setFactText, techno, setTechno, saving, saveError, onSubmit }) => {
    return (
        <div className="card">
            <header className="mb-4">
                <h1>Créer un fact</h1>
            </header>
            <form onSubmit={onSubmit} className="form-container">
                <div className="form-group">
                    <label htmlFor="fact-text">Contenu du fact</label>
                    <textarea id="fact-text" value={factText} onChange={(e) => setFactText(e.target.value)} required />
                </div>
                <div className="form-group">
                    <label htmlFor="techno">Technologie</label>
                    <input id="techno" type="text" value={techno} onChange={(e) => setTechno(e.target.value)} />
                </div>
                {saveError && (
                    <div className="error mb-3">
                        <strong>Erreur lors de l'enregistrement:</strong> {saveError.message}
                    </div>
                )}
                <div className="form-actions">
                    <button type="submit" disabled={saving} className={saving ? 'secondary' : ''}>
                        {saving ? 'Création en cours...' : 'Créer le fact'}
                    </button>
                </div>
            </form>
        </div>
    )
}

export default FactForm


