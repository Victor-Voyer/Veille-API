import { useEffect, useState } from 'react'
import { useFact } from './useFact'

export function useFactEditor(id) {
    const { data, loading, error } = useFact(id)
    const [factText, setFactText] = useState('')
    const [techno, setTechno] = useState('')
    const [saving, setSaving] = useState(false)
    const [saveError, setSaveError] = useState(null)

    useEffect(() => {
        if (data) {
            setFactText(data.fact || '')
            setTechno(data.techno || '')
        }
    }, [data])

    const submit = async () => {
        setSaving(true)
        setSaveError(null)
        try {
            const response = await fetch(`http://localhost:8000/api/facts/${id}?_format=json`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ fact: factText, techno })
            })
            if (!response.ok) {
                const msg = await response.text()
                throw new Error(msg || 'Échec de la mise à jour')
            }
            return true
        } catch (err) {
            setSaveError(err)
            return false
        } finally {
            setSaving(false)
        }
    }

    return {
        data,
        loading,
        error,
        factText,
        setFactText,
        techno,
        setTechno,
        saving,
        saveError,
        submit,
    }
}


