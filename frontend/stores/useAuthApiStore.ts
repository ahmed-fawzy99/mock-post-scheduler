export const useAuthApiStore = () => {
    const config = useRuntimeConfig()
    const baseURL = (url) =>
        url === '/sanctum/csrf-cookie'
            ? config.public.backendBase
            : config.public.backendBase + '/api'

    return (url, options = {}) => {
        const token = useCookie('XSRF-TOKEN').value

        const headers = {
            'Accept': 'application/json',
        }


        // If the body is not a FormData object, set the Content-Type to multipart/form-data
        if (!(options.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json'
        }

        return $fetch(url, {
            baseURL: baseURL(url),
            credentials: 'include',
            headers: {
                ...headers,
                ...options.headers,
            },
            ...options
        })
    }
}