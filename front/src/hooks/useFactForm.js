import { useState } from 'react'

export function useFactForm() {
    const [factText, setFactText] = useState('')
    const [techno, setTechno] = useState('')
    const [saving, setSaving] = useState(false)
    const [saveError, setSaveError] = useState(null)

    const submit = async () => {
        setSaving(true)
        setSaveError(null)
        try {
            const response = await fetch('http://localhost:8000/api/facts?_format=json', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ fact: factText, techno })
            })
            if (!response.ok) {
                const msg = await response.text()
                throw new Error(msg || 'Échec de la création')
            }
            return true
        } catch (err) {
            setSaveError(err)
            return false
        } finally {
            setSaving(false)
        }
    }

    return { factText, setFactText, techno, setTechno, saving, saveError, submit }
}


