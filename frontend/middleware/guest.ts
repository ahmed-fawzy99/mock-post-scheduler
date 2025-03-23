export default defineNuxtRouteMiddleware(async (to, from) => {
    if (import.meta.client) {
        const authStore = useAuthStore()
        const { isAuthenticated } = storeToRefs(authStore)

        if (isAuthenticated.value) {
            return navigateTo('/dashboard')
        }
        return
    }
})