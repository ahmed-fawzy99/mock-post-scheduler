<script setup lang="ts">
definePageMeta({
  middleware: ['guest']
})
import {useForm} from 'vee-validate'
import {toTypedSchema} from '@vee-validate/zod'
import * as z from 'zod'

const auth = useAuthStore()
const {forgotPassword} = auth
const {isLoading} = storeToRefs(auth)

import { useToast } from "primevue/usetoast";
const toast = useToast();

const formSchema = toTypedSchema(z.object({
  email: z.string().email(),
}))

const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    email: 'super@root.com',
  }
})

const errors = ref([])
const emailSent = ref(null)
const handleForgotRequest = form.handleSubmit(async (values) => {
  try {
    await forgotPassword({
      email: values.email,
    })
    emailSent.value = values.email
    form.resetForm()
    errors.value = []
    toast.add({ severity: 'success', summary: 'Success', detail: `if the email exists in our system, you will receive a password reset link to ${values.email}.`, group: TOAST_GROUP, life: TOAST_TIMEOUT });
  } catch (error) {
    errors.value = handleError(error)
    toast.add({ severity: 'error', summary: 'Something went wrong', detail: `Please check the errors shown and try again.`, group: TOAST_GROUP, life: TOAST_TIMEOUT });
  }
})
</script>

<template>
  <div class="h-full flex justify-center items-center">
    <!-- Main Card -->
    <div v-if="!emailSent" class="flex flex-col div-card w-full sm:w-1/2 lg:w-1/3 gap-4">
      <!-- Header -->
      <div class="flex flex-col gap-2">
        <h1 class="font-bold">Reset your password</h1>
        <NuxtLink to="/login" class="text-primary font-semibold">Back to login</NuxtLink>
      </div>

      <!-- Form -->
      <form @submit="handleForgotRequest" class="flex flex-col gap-4">
        <FormContainer class="!grid-cols-1" :margin="false">
          <FormTextItem id="email" title="Your Email Address" placeholder="John@Doe.com" required/>
        </FormContainer>
        <FormErrors :errors="errors" />
        <Button class="w-full" type="submit"
                :disabled="isLoading" severity="contrast" :label="isLoading ? 'Please wait...' : 'Request Password Reset'" />
      </form>
    </div>

    <div v-else class="flex flex-col div-card w-full sm:w-1/2 lg:w-1/3 gap-4">
      <div class="flex flex-col gap-2">
        <h1 class="font-bold">Thank You</h1>
        <p>
          If the email exists in our system, you will receive a password reset link to <span class="font-semibold">{{ emailSent }}</span>.
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>