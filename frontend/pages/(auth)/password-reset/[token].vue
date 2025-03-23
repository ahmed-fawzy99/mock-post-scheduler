<script setup lang="ts">
definePageMeta({
  middleware: ['guest']
})
import {useForm} from 'vee-validate'
import {toTypedSchema} from '@vee-validate/zod'
import {useToast} from "primevue/usetoast";
import * as z from 'zod'

const toast = useToast();
const route = useRoute()

const token = route.params.token
const email = route.query.email

const formSchema = toTypedSchema(z.object({
  email: z.string().email(),
  token: z.string().min(1),
  password: passwordSchema,
  password_confirmation: z.string().refine((value) => value === form.values.password, {
    message: "Passwords do not match",
  }),
}))

const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    email: email || '',
    token: token || '',
    password: '1Password',
    password_confirmation: '1Password',
  }
})

const auth = useAuthStore()
const {resetPassword} = auth
const {isLoading} = storeToRefs(auth)

const errors = ref([])
const resetDone = ref(false)

const handleResetRequest = form.handleSubmit(async (values) => {
  try {
    await resetPassword({
      email: values.email,
      token: values.token,
      password: values.password,
      password_confirmation: values.password_confirmation,
    })
    resetDone.value = true
    form.resetForm()
    errors.value = []
    setTimeout(() => {
      navigateTo('/login')
    }, 10000)
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: `Your password has been reset. You can now login with your new password.`,
      group: TOAST_GROUP,
      life: TOAST_TIMEOUT
    });
  } catch (error) {
    errors.value = handleError(error)
    form.resetField('password')
    form.resetField('password_confirmation')
    toast.add({
      severity: 'error',
      summary: 'Something went wrong',
      detail: `Please check the errors shown and try again.`,
      group: TOAST_GROUP,
      life: TOAST_TIMEOUT
    });
  }
})
</script>

<template>
  <div class="h-full flex justify-center items-center">
    <!-- Main Card -->
    <div v-if="!resetDone" class="flex flex-col div-card w-full sm:w-1/2 lg:w-1/3 gap-4">
      <!-- Header -->
      <div class="flex flex-col gap-2">
        <h1 class="font-bold">Reset your password</h1>
        <NuxtLink to="/login" class="text-primary font-semibold">Back to login</NuxtLink>
      </div>

      <!-- Form -->
      <form @submit="handleResetRequest" class="flex flex-col gap-4">
        <FormContainer class="!grid-cols-1" :margin="false">
          <FormPasswordItem id="password" title="Your Password" :helper="true" required/>
          <FormPasswordItem id="password_confirmation" title="Confirm Your Password" :helper="false" required/>
        </FormContainer>
        <FormErrors :errors="errors"/>
        <Button class="w-full" type="submit"
                :disabled="isLoading" severity="contrast" :label="isLoading ? 'Please wait...' : 'Reset Password'"/>
      </form>
    </div>

    <div v-else class="flex flex-col div-card w-full sm:w-1/2 lg:w-1/3 gap-4">
      <div class="flex flex-col gap-2">
        <h1 class="font-bold">Password Reset Complete</h1>
        <p>
          Your password has been reset. You can now login with your new password.<br>
          You'll be redirected to the login page in a few seconds.
        </p>
      </div>
    </div>
  </div>

</template>

<style scoped>

</style>