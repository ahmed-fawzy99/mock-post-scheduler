<script setup lang="ts">
import {toTypedSchema} from "@vee-validate/zod";
import * as z from "zod";
import {useForm, useIsSubmitting} from "vee-validate";
import {useToast} from "primevue/usetoast";

const props = defineProps<{
  mode: 'create' | 'edit';
  platform: object | null;
}>();

const isEdit = props.mode === 'edit';
const toast = useToast();


const formSchema = toTypedSchema(z.object({
  name: z.string(),
  type: z.string(),
  character_limit: z.number().optional(),
}))

const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    name: isEdit ? props.platform.data.attributes.name : '',
    type: isEdit ? props.platform.data.attributes.type : '',
    character_limit: isEdit ? props.platform.data.attributes.characterLimit : 280,
  }
})

const isFormSubmitting = useIsSubmitting();
const errors = ref([])
const handleCreatePlatformRequest = form.handleSubmit(async (values) => {
  try {
    errors.value = []
    const {error} = await useApi(`/platforms`, {
      method: 'POST',
      body: {
        name: values.name,
        type: values.type,
        character_limit: values.character_limit,
      },
      onResponse({response}) {
        if (response.ok) {
          toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Platform created successfully!',
            group: TOAST_GROUP,
            life: TOAST_TIMEOUT
          });
          navigateTo('/dashboard/admin?tab=platforms')
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

const handleUpdatePlatformRequest = form.handleSubmit(async (values) => {
  try {
    errors.value = []
    const {error} = await useApi(`/platforms/${props.platform.data.id}`, {
      method: 'PUT',
      body: {
        name: values.name,
        type: values.type,
        character_limit: values.character_limit,
      },
      onResponse({response}) {
        if (response.ok) {
          toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Platform edited successfully!',
            group: TOAST_GROUP,
            life: TOAST_TIMEOUT
          });
          navigateTo('/dashboard/admin?tab=platforms')
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
    ? handleCreatePlatformRequest
    : handleUpdatePlatformRequest;

</script>

<template>
  <PageHeader :title="props.mode === 'create' ? 'Create a New Platform' : 'Edit Platform'" />

  <!-- Form -->
  <form @submit="handleSubmit" class="flex flex-col gap-4">
    <FormContainer class="" :margin="false">
      <FormTextItem id="name" title="Name" placeholder="Facebook" required/>
      <FormTextItem id="type" title="Type" placeholder="facebook" required/>
      <FormNumberItem id="character_limit" title="Character Limit"  placeholder="280" :noFraction="true" required/>
      
    </FormContainer>
    <FormErrors :errors="errors"/>
    <Button class="w-full" type="submit"
            :disabled="isFormSubmitting" severity="contrast" :label="props.mode === 'create' ? 'Create Platform' : 'Edit Platform'"/>
  </form>
</template>

<style scoped>

</style>