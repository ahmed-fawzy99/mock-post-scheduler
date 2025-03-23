import {defineStore} from 'pinia'
import {useAuthApiStore} from "~/stores/useAuthApiStore";

interface User {
    id: number
    name: string
    email: string
}

export const useAuthStore = defineStore('auth', () => {
        const isAuthenticated: Ref<boolean> = ref(false)
        const isAdmin: Ref<boolean> = ref(false)
        const user: Ref<User | null> = ref(null)
        const isLoading: Ref<boolean> = ref(false)
        const error: Ref<string | null> = ref(null)
        const authApi: ReturnType<typeof useAuthApiStore> = useAuthApiStore()
        const lastAuthCheck = ref(0)

        async function checkAuth() {
            try {
                const token = useCookie('XSRF-TOKEN').value
                const response = await authApi('/user',{
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(token as string),
                    }
                })
                if (response.status === 200) {
                    isAuthenticated.value = true
                    isAdmin.value = response.data.attributes.roles.includes('admin')
                    user.value = response.data
                    lastAuthCheck.value = Date.now()
                    return true
                } else {
                    isAuthenticated.value = false
                    isAdmin.value = false
                    user.value = null
                    return false
                }
            } catch (error) {
                isAuthenticated.value = false
                isAdmin.value = false
                user.value = null
                return false
            }
        }

        const shouldRevalidate = computed(() => {
            const now = Date.now()
            // Revalidate every 15 minutes
            return now - lastAuthCheck.value > 15 * 60000
        })

        async function csrf() { // returns XSRF-TOKEN
            try {
                await authApi('/sanctum/csrf-cookie')
                return useCookie('XSRF-TOKEN').value
            } catch (error) {
                const err = new Error('Network/Server Error.');
                err.status = 503;
                throw err;
            }
        }

        async function login(data: {
            email: string,
            password: string,
            remember?: boolean
        }) {
            isLoading.value = true
            error.value = null
            try {
                const token = await csrf()
                const response = await authApi('/login', {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(token as string),
                    }
                })
                if (response.status === 200) {
                    user.value = response.data
                    isAuthenticated.value = true
                    isAdmin.value = response.data.attributes.roles.includes('admin')
                    lastAuthCheck.value = Date.now()
                    navigateTo('/dashboard')
                }
                else {
                    throw new Error('Invalid Login Request')
                }
            } catch (err: any) {
                error.value = err.message || 'Login failed'
                throw err
            } finally {
                isLoading.value = false
            }
        }

        async function register(data: {
            name: string,
            email: string,
            password: string,
            password_confirmation: string,
            accepts_terms: boolean,
        }) {
            isLoading.value = true
            error.value = null

            try {
                const token = await csrf()

                const response = await authApi('/register', {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(token as string),
                    }
                })
                if (response.status === 200) {
                    isAuthenticated.value = true
                    isAdmin.value = response.data.attributes.roles.includes('admin')
                    user.value = response.data
                    lastAuthCheck.value = Date.now()
                    navigateTo('/dashboard')
                }
                else {
                    throw new Error('Invalid Register Request')
                }
            } catch (err: any) {
                error.value = err.message || 'Registration failed'
                throw err
            } finally {
                isLoading.value = false
            }
        }

        // Forgot Password
        async function forgotPassword(data: {
            email: string,
        }) {
            isLoading.value = true
            error.value = null
            try {
                const token = await csrf()

                const response = await authApi('/forgot-password', {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(token as string),
                    }
                })
                if (response.status === 200) {
                    return true
                } else {
                    throw new Error('Invalid Forgot Password Request')
                }
            } catch (err: any) {
                error.value = err.message || 'Failed to send reset link'
                throw err
            } finally {
                isLoading.value = false
            }
        }

        // Reset Password
        async function resetPassword(data: {
            token: string,
            email: string,
            password: string,
            password_confirmation: string
        }) {
            isLoading.value = true
            error.value = null

            try {
                const token = await csrf()
                const response = await authApi('/reset-password', {
                    method: 'POST',
                    body: data,
                    credentials: 'include',
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(token as string),
                    }
                })

                if (response.status === 200) {
                    return true
                } else {
                    throw new Error('Invalid Reset Password Request')
                }
            } catch (err: any) {
                error.value = err.message || 'Password reset failed'
                throw err
            } finally {
                isLoading.value = false
            }
        }

        // Verify Email
        async function verifyEmail(url: string) {
            isLoading.value = true
            error.value = null

            try {
                await authApi(url, {
                    method: 'GET',
                    credentials: 'include'
                })

                return true
            } catch (err: any) {
                error.value = err.message || 'Email verification failed'
                throw err
            } finally {
                isLoading.value = false
            }
        }

        // Resend Verification Email
        async function resendVerification() {
            isLoading.value = true
            error.value = null

            try {
                const token = useCookie('XSRF-TOKEN').value


                await authApi('/resend-verification', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(token as string),
                    }
                })
                return true
            } catch (err: any) {
                error.value = err.message || 'Failed to resend verification email'
                throw err
            } finally {
                isLoading.value = false
            }
        }

        async function logout() {
            isLoading.value = true
            error.value = null
            try {

                const response = await authApi('/logout', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(useCookie('XSRF-TOKEN').value as string),
                    }
                })

                if (response.status === 200) {
                    // Clear all local storage
                    localStorage.clear()
                    // Clear all cookies
                    const csrfCookie = useCookie('XSRF-TOKEN')
                    csrfCookie.value = null

                    isAuthenticated.value = false
                    isAdmin.value = false;
                    user.value = null

                    navigateTo('/')
                }
            } catch (err: any) {
                isAuthenticated.value = false
                isAdmin.value = false;
                user.value = null
                error.value = err.message || 'Logout failed'
                throw err
            } finally {
                isLoading.value = false
            }
        }

        async function updateAccountInfo(data: {
            name: string,
            email: string,
        }) {
            isLoading.value = true
            error.value = null
            try {
                const token = await csrf()
                const response = await authApi('/update-info', {
                    method: 'PATCH',
                    body: data,
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(token as string),
                    }
                })
                if (response.status === 200) {
                    user.value = response.data
                }
                else {
                    throw new Error('Invalid Update Request')
                }
            } catch (err: any) {
                error.value = err.message || 'Update failed'
                throw err
            } finally {
                isLoading.value = false
            }
        }

        async function updatePasswordInfo(data: {
            password: string,
            new_password: string,
            new_password_confirmation: string,
        }) {
            isLoading.value = true
            error.value = null
            try {
                const token = await csrf()
                const response = await authApi('/update-password', {
                    method: 'PATCH',
                    body: data,
                    headers: {
                        'X-XSRF-TOKEN': decodeURIComponent(token as string),
                    }
                })
                if (response.status === 200) {
                    user.value = response.data
                }
                else {
                    throw new Error('Invalid Update Request')
                }
            } catch (err: any) {
                error.value = err.message || 'Update failed'
                throw err
            } finally {
                isLoading.value = false
            }
        }

        return {
            isAuthenticated,
            isAdmin,
            user,
            login,
            register,
            forgotPassword,
            resetPassword,
            updateAccountInfo,
            updatePasswordInfo,
            verifyEmail,
            resendVerification,
            logout,
            isLoading,
            error,
            checkAuth,
            shouldRevalidate,
            lastAuthCheck,
        }
    },
    {
        persist: true,
    },
)