import React from 'react'
import { useFactForm } from '../hooks'
import FactForm from '../components/FactForm'

const FactNew = () => {
    const { factText, setFactText, techno, setTechno, saving, saveError, submit } = useFactForm()
    const onSubmit = async (e) => {
        e.preventDefault()
        await submit()
    }

    return (
        <div className="container">
            <FactForm
                factText={factText}
                setFactText={setFactText}
                techno={techno}
                setTechno={setTechno}
                saving={saving}
                saveError={saveError}
                onSubmit={onSubmit}
            />
        </div>
    )
}

export default FactNew