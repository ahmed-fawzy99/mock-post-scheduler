export default defineNuxtRouteMiddleware(async () => {
    if (import.meta.client) {
        const { isAdmin } = storeToRefs(useAuthStore())
        if (!isAdmin.value)
            return navigateTo('/dashboard')
    }
})
