import { useApi } from './useApi'
import { useState } from 'react'

export function useFact(id) {
    const url = id ? `http://localhost:8000/api/facts/${id}?_format=json` : null
    // Si id est null, on renvoie un état de chargement faux avec data null
    if (!id) return { data: null, loading: false, error: null }
    return useApi(url)
}

export function useFactDelete(id, options = {}) {
    const [deleting, setDeleting] = useState(false)
    const [deleteError, setDeleteError] = useState(null)

    const deleteFact = async () => {
        if (!id) return false
        setDeleting(true)
        setDeleteError(null)
        try {
            const response = await fetch(`http://localhost:8000/api/facts/${id}?_format=json`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            })
            if (!response.ok) {
                const msg = await response.text()
                throw new Error(msg || 'Échec de la suppression')
            }
            if (typeof options.onSuccess === 'function') {
                options.onSuccess()
            }
            return true
        } catch (err) {
            setDeleteError(err)
            if (typeof options.onError === 'function') {
                options.onError(err)
            }
            return false
        } finally {
            setDeleting(false)
        }
    }

    return { deleting, deleteError, deleteFact }
}


