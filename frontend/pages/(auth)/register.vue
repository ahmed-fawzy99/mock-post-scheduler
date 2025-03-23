<script setup lang="ts">
definePageMeta({
  middleware: ['guest']
})
import {useForm} from 'vee-validate'
import {toTypedSchema} from '@vee-validate/zod'
import * as z from 'zod'

const auth = useAuthStore()
const {register} = auth
const {isLoading, user} = storeToRefs(auth)

import {useToast} from "primevue/usetoast";
const toast = useToast();

const formSchema = toTypedSchema(z.object({
  name: z.string().min(1),
  email: z.string().email(),
  password: passwordSchema,
  password_confirmation: z.string().refine((value) => value === form.values.password, {
    message: "Passwords do not match",
  }),
  accepts_terms: z.boolean().refine((value) => value, {
    message: "You must accept the terms and conditions",
  }),
}))

const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    accepts_terms: false,
  }
})

const errors = ref([])
const handleRegisterRequest = form.handleSubmit(async (values) => {
  try {
    await register({
      name: values.name,
      email: values.email,
      password: values.password,
      password_confirmation: values.password_confirmation,
      accepts_terms: values.accepts_terms
    })
    form.resetForm()
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: `✅ Welcome aboard, ${user.value.attributes.name}!`,
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
    <div class="flex flex-col div-card w-full sm:w-1/2 lg:w-1/3 gap-4">
      <!-- Header -->
      <div class="flex flex-col gap-2">
        <h1 class="font-bold">Register</h1>
        <p>
          Enter your information to create an account. <br>
          Already have an account?
          <NuxtLink to="/login" class="text-primary font-semibold">Login</NuxtLink>
        </p>
      </div>

      <!-- Form -->
      <form @submit="handleRegisterRequest" class="flex flex-col gap-4">
        <FormContainer class="!grid-cols-1" :margin="false">
          <FormTextItem id="name" title="Name" placeholder="Luke Skywalker" required/>
          <FormTextItem id="email" title="Email" placeholder="John@Doe.com" required/>
          <FormPasswordItem id="password" title="Your Password" :helper="true" required/>
          <FormPasswordItem id="password_confirmation" title="Confirm Your Password" :helper="false" required/>
          <FormCheckboxItem id="accepts_terms" title="Accept Terms & Conditions" required>
            I accept the <span class="underline">Terms & Conditions</span>
            and <span class=" underline">Privacy Policy</span>
          </FormCheckboxItem>
        </FormContainer>
        <FormErrors :errors="errors"/>
        <Button class="w-full" type="submit"
                :disabled="isLoading" severity="contrast" :label="isLoading ? 'Registering...' : 'Register'"/>
      </form>
    </div>
  </div>

</template>

<style scoped>

</style>