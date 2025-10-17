import React from 'react'
import { useParams } from 'react-router-dom'
import { useFact } from '../hooks'
import { Link } from 'react-router-dom'

const Fact = () => {
    const { id } = useParams()
    const { data, loading, error } = useFact(id)

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
        <div>
            <h2>Fact #{data.id}</h2>
            <p>{data.fact}</p>
            {data.techno && <small>Tech: {data.techno}</small>}
            <div>
                <Link to={`/facts/${data.id}/edit`}>Éditer</Link>
            </div>
        </div>
    )
}

export default Fact


