<script setup lang="ts">
import {useForm} from 'vee-validate'
import {toTypedSchema} from '@vee-validate/zod'
import * as z from 'zod'
import {useToast} from "primevue/usetoast";

definePageMeta({
  middleware: ['auth'],
})

const auth = useAuthStore()
const { updateAccountInfo, updatePasswordInfo } = auth
const { user, isLoading } = storeToRefs(auth)

const toast = useToast();

const formSchema = toTypedSchema(z.object({
  name: z.string().min(1),
  email: z.string().email(),
  password: z.string(),
  new_password: passwordSchema,
  new_password_confirmation: z.string().refine((value) => value === form.values.new_password, {
    message: "Passwords do not match",
  }),
}))

const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    name: user.value.attributes.name,
    email: user.value.attributes.email,
    password: '1Password',
    new_password: '2Password',
    new_password_confirmation: '2Password',
  }
})

const errors = ref([])
const submittedForm = ref(null)
const handleUpdateInfoRequest = form.handleSubmit(async (values) => {
  try {
    await updateAccountInfo({
      name: values.name,
      email: values.email,
    })
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: `Your information has been updated.`,
      group: TOAST_GROUP,
      life: TOAST_TIMEOUT
    });
  } catch (error) {
    submittedForm.value = 0
    errors.value = handleError(error)
    toast.add({
      severity: 'error',
      summary: 'Something went wrong',
      detail: `Please check the errors shown and try again.`,
      group: TOAST_GROUP,
      life: TOAST_TIMEOUT
    });
  }
})

const handleUpdatePasswordRequest = form.handleSubmit(async (values) => {
  try {
    await updatePasswordInfo({
      password: values.password,
      new_password: values.new_password,
      new_password_confirmation: values.new_password_confirmation
    })
    form.resetForm()
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: `Your information has been updated.`,
      group: TOAST_GROUP,
      life: TOAST_TIMEOUT
    });
  } catch (error) {
    submittedForm.value = 1
    errors.value = handleError(error)
    form.resetField('password')
    form.resetField('password_confirmation')
    toast.add({
      severity: 'error',
      summary: 'Something went wrong',
      detail: error.response._data.status === 403 ? 'Your current password is incorrect' : `Please check the errors shown and try again.`,
      group: TOAST_GROUP,
      life: TOAST_TIMEOUT + 2000
    });
  }
})
</script>

<template>
  <div class="h-full flex flex-col  items-center gap-4">
    <!-- Name - Email Card -->
    <div class="account-card">
      <div class="mx-auto w-full md:w-md">
        <!-- Header -->
        <h1 class="font-bold mb-2">Update your account information</h1>
        <!-- Form -->
        <form id="form-1" @submit="handleUpdateInfoRequest" class="flex flex-col gap-4">
          <FormContainer class="!grid-cols-1" :margin="false">
            <FormTextItem id="name" title="Name" placeholder="Luke Skywalker"/>
            <FormTextItem id="email" title="Email" placeholder="John@Doe.com"/>
          </FormContainer>
          <FormErrors v-if="submittedForm === 0" :errors="errors"/>
          <Button class="w-full" type="submit"
                  :disabled="isLoading" severity="contrast" :label="isLoading ? 'Updating...' : 'Update Information'"/>
        </form>
      </div>
    </div>
    <div class="account-card">
      <div class="mx-auto w-full md:w-md">
        <!-- Header -->
        <h1 class="font-bold mb-2">Update your password</h1>

        <!-- Form -->
        <form id="form-2" @submit="handleUpdatePasswordRequest" class="flex flex-col gap-4">
          <FormContainer class="!grid-cols-1" :margin="false">
            <FormPasswordItem id="password" title="Your Current Password" :helper="false"/>
            <FormPasswordItem id="new_password" title="Your New Password" :helper="true"/>
            <FormPasswordItem id="new_password_confirmation" title="Confirm Your New Password" :helper="false"/>
          </FormContainer>
          <FormErrors v-if="submittedForm === 1" :errors="errors"/>
          <Button class="w-full" type="submit"
                  :disabled="isLoading" severity="contrast" :label="isLoading ? 'Updating...' : 'Update Password'"/>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>