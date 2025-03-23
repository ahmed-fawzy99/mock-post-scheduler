export default defineNuxtRouteMiddleware(async () => {
    if (import.meta.client) {
        const { checkAuth } = useAuthStore()
        const response = await checkAuth()
        if (!response)
            return navigateTo('/login')
    }
})
