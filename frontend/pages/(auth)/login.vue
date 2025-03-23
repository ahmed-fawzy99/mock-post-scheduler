<script setup lang="ts">
definePageMeta({
  middleware: ['guest']
})
import {useForm} from 'vee-validate'
import {toTypedSchema} from '@vee-validate/zod'
import * as z from 'zod'


const auth = useAuthStore()
const {login} = auth
const {isLoading, user} = storeToRefs(auth)

import { useToast } from "primevue/usetoast";
const toast = useToast();

const formSchema = toTypedSchema(z.object({
  email: z.string().email(),
  password: z.string(),
}))

const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    email: 'super@root.com',
    password: 'password',
}})

const errors = ref([])
const handleLoginRequest = form.handleSubmit(async (values) => {
  try {
    await login({
      email: values.email,
      password: values.password
    })
    form.resetForm()
    toast.add({ severity: 'success', summary: 'Success', detail: `✅ Welcome back, ${user.value.attributes.name}!`, group: TOAST_GROUP, life: TOAST_TIMEOUT });
  } catch (error) {
    errors.value = handleError(error)
    form.setValues({
      password: ''
    })
    toast.add({ severity: 'error', summary: 'Something went wrong', detail: `Please check the errors shown and try again.`, group: TOAST_GROUP, life: TOAST_TIMEOUT });
  }
})
</script>

<template>
  <div class="h-full flex justify-center items-center">
    <!-- Main Card -->
    <div class="flex flex-col div-card w-full sm:w-1/2 lg:w-1/3 gap-4">
      <!-- Header -->
      <div class="flex flex-col gap-2">
        <h1 class="font-bold">Login</h1>
        <p>
          Enter your email below to login to your account. <br>
          Don't have an account?
          <NuxtLink to="/register" class="text-primary font-semibold">Register</NuxtLink>
        </p>
      </div>

      <!-- Form -->
      <form @submit="handleLoginRequest" class="flex flex-col gap-4">
        <FormContainer class="!grid-cols-1" :margin="false">
          <FormTextItem id="email" title="Email" placeholder="John@Doe.com" required/>
          <FormPasswordItem id="password" title="Password" required :helper="false"/>
        </FormContainer>
        <FormErrors :errors="errors" />
        <div>
          <NuxtLink to="/forgot-password" class="text-primary font-semibold">Forgot your password?</NuxtLink>
        </div>
        <Button class="w-full" type="submit"
            :disabled="isLoading" severity="contrast" :label="isLoading ? 'Authenticating...' : 'Login'" />
      </form>
    </div>
  </div>

</template>

<style scoped>

</style>