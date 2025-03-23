export const useApi = (endpoint: string, options: FetchOptions = {}) => {
    const config = useRuntimeConfig()

    const headers: Record<string, string> = {
        'Accept': 'application/json',
    };

    if (!(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json'
    }

    // Get CSRF token directly
    const token = useCookie('XSRF-TOKEN').value

    if (token) {
        headers['X-XSRF-TOKEN'] = token;
    }

    // If options has headers, merge them
    if (options.headers) {
        Object.assign(headers, options.headers);
    }

    const { headers: _, ...restOptions } = options;

    return useFetch(endpoint, {
        credentials: 'include',
        lazy: true,
        server: false,
        baseURL: config.public.backendApi,
        headers,
        ...restOptions
    })
}