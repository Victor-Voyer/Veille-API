import { useEffect, useState } from 'react'

export function useApi(url) {
    const [data, setData] = useState(null)
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState(null)

    useEffect(() => {
        let aborted = false
        async function fetchJson() {
            try {
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } })
                const json = await response.json()
                if (!aborted) setData(json)
            } catch (err) {
                if (!aborted) setError(err)
            } finally {
                if (!aborted) setLoading(false)
            }
        }
        fetchJson()
        return () => { aborted = true }
    }, [url])

    return { data, loading, error }
}


