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
        return <div>Loading...</div>
    }

    if (error) {
        return <div>Error: {error.message}</div>
    }

    if (!data) {
        return <div>Not found</div>
    }

    return (
        <form onSubmit={onSubmit}>
            <h2>Éditer le fact #{id}</h2>
            <div>
                <label>
                    Fact
                    <textarea value={factText} onChange={(e) => setFactText(e.target.value)} />
                </label>
            </div>
            <div>
                <label>
                    Techno
                    <input value={techno} onChange={(e) => setTechno(e.target.value)} />
                </label>
            </div>
            {saveError && <div style={{ color: 'red' }}>Erreur: {saveError.message}</div>}
            <div>
                <button type="submit" disabled={saving}>{saving ? 'Enregistrement…' : 'Enregistrer'}</button>
                <button type="button" onClick={() => navigate(-1)} disabled={saving}>Annuler</button>
            </div>
        </form>
    )
}

export default FactEdit


