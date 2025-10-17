import { useApi } from './useApi'

export function useFact(id) {
    const url = id ? `http://localhost:8000/api/facts/${id}?_format=json` : null
    // Si id est null, on renvoie un état de chargement faux avec data null
    if (!id) return { data: null, loading: false, error: null }
    return useApi(url)
}


