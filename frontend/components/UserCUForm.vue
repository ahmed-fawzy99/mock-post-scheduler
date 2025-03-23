<script setup lang="ts">
import {toTypedSchema} from "@vee-validate/zod";
import * as z from "zod";
import {useForm, useIsSubmitting} from "vee-validate";
import {useToast} from "primevue/usetoast";

const props = defineProps<{
  mode: 'create' | 'edit';
  user: object | null;
}>();

const isEdit = props.mode === 'edit';
let disallowedPlatforms = null;
const toast = useToast();

const {data: platforms, status, error} = await useApi(`/platforms?all=true`,{
  onResponse({response}) {
    if (response.ok && isEdit) {
      disallowedPlatforms = response._data.data.filter(platform => props.user.data.includes.disallowedPlatforms.some(disallowed => disallowed.id === platform.id));
    }
  }
})

const formSchema = toTypedSchema(z.object({
  name: z.string(),
  email: z.string().email(),
  disallowedPlatforms: z.array(z.object({
    id: z.number(),
  })),
}))

const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    name: isEdit ? props.user.data.attributes.name : '',
    email: isEdit ? props.user.data.attributes.email : '',
  }
})

const isFormSubmitting = useIsSubmitting();
const errors = ref([])
const handleCreateUserRequest = form.handleSubmit(async (values) => {
  // to be extended when we want to allow admins to create users
})


const handleUpdateUserRequest = form.handleSubmit(async (values) => {
  try {
    errors.value = []
    const {error} = await useApi(`/users/${props.user.data.id}`, {
      method: 'PATCH',
      body: {
        name: values.name,
        email: values.email,
        disallowedPlatforms: values.disallowedPlatforms.map(platform => platform.id),
      },
      onResponse({response}) {
        if (response.ok) {
          toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'User edited successfully!',
            group: TOAST_GROUP,
            life: TOAST_TIMEOUT
          });
          // navigateTo('/dashboard')
        }
      },
    })
    if (error.value) {
      throw error.value;
    }
  } catch (error) {
    errors.value = handleError(error.data, true)
    toast.add({
      severity: 'error',
      summary: 'Something went wrong',
      detail: `Please check the errors shown and try again.`,
      group: TOAST_GROUP,
      life: TOAST_TIMEOUT
    });
  }
})

const handleSubmit =  props.mode === "create"
    ? handleCreateUserRequest
    : handleUpdateUserRequest;

</script>

<template>
  <PageHeader :title="props.mode === 'create' ? 'Create a New User' : 'Edit User'" />

  <!-- Form -->
  <form @submit="handleSubmit" class="flex flex-col gap-4">
    <FormContainer class="" :margin="false">
      <FormTextItem id="name" title="Name" placeholder="Luke Skywalker" required/>
      <FormTextItem id="email" title="Email" placeholder="grogu@man.do" required/>

      <FormMultiSelector v-if="platforms" id="disallowedPlatforms" title="Disallowed Platforms" :src="platforms?.data" :preselected="isEdit ? disallowedPlatforms : null"
                         optionLabel="attributes.name" :searchable="true" labelHelper="Unchecked platforms will be disallowed for this user"
                         placeholder="Select platforms..." required/>
    </FormContainer>
    <FormErrors :errors="errors"/>
    <Button class="w-full" type="submit"
            :disabled="isFormSubmitting" severity="contrast" :label="props.mode === 'create' ? 'Create User' : 'Edit User'"/>
  </form>
</template>

<style scoped>

</style>