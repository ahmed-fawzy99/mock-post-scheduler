<script setup lang="ts">
import {toTypedSchema} from "@vee-validate/zod";
import * as z from "zod";
import {useForm, useIsSubmitting} from "vee-validate";
import {useToast} from "primevue/usetoast";
import PageHeader from "~/components/PageHeader.vue";

const props = defineProps<{
  mode: 'create' | 'edit';
  post: object | null;
}>();

const toast = useToast();
const router = useRouter();

const isEdit = props.mode === 'edit';
const maxCharCount = ref(null);
const postStatus = [
  { type: 'published' },
  { type: 'scheduled' },
  { type: 'draft' },
]
const {data: platforms, status, error} = await useApi(`/platforms`)


const formSchema = toTypedSchema(z.object({
  title: z.string(),
  content: z.string().refine(
      (val) => maxCharCount.value === null || val.length <= maxCharCount.value,
  ),
  image: z.any(),
  status: z.object({
    type: z.string(),
  }),
  scheduled_at: z.date().optional(),
  platforms: z.array(z.object({
    id: z.number(),
  })),
}))

const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    title: isEdit ? props.post.data.attributes.title : '',
    content: isEdit ? props.post.data.attributes.content : '',
    status: isEdit ? { type: props.post.data.attributes.status.toLowerCase() } : { type: 'scheduled' },
    ...(isEdit && props.post.data.attributes.scheduledAt ? { scheduled_at: new Date(props.post.data.attributes.scheduledAt) } : {})
  }
})
const isFormSubmitting = useIsSubmitting();


watch(() => form.values.platforms, (p) => {
  if (p?.length) {
    maxCharCount.value = Math.min(...p.map(platform => platform.attributes.characterLimit))
  } else {
    maxCharCount.value = null
  }
}, { immediate: true })

const processFormValues = (values) => {
  const formData = new FormData();

  formData.append('title', values.title);
  formData.append('content', values.content);
  formData.append('status', values.status.type);

  values.platforms?.forEach(platform => {
    formData.append('platforms[]', platform.id);
  });

  if (values.scheduled_at)
    formData.append('scheduled_at', values.scheduled_at.toISOString());

  if (values.image) {
    values.image instanceof File
        ? formData.append('image', values.image)
        : formData.append('image_url', values.image.url);
  }

  if (isEdit) {
    // Spoof PUT for Laravel
    formData.append('_method', 'PUT');
  }
  return formData;
}

const errors = ref([])
const handleCreatePostRequest = form.handleSubmit(async (values) => {
  try {
    errors.value = []
    const {error} = await useApi('/posts', {
      method: 'POST',
      body: processFormValues(values),
      onResponse({response}) {
        if (response.ok) {
          toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Post created successfully!',
            group: TOAST_GROUP,
            life: TOAST_TIMEOUT
          });
          navigateTo('/dashboard')
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

const handleUpdatePostRequest = form.handleSubmit(async (values) => {
  try {
    errors.value = []
    const {error} = await useApi(`/posts/${props.post.data.id}`, {
      method: 'POST',
      body: processFormValues(values),
      onResponse({response}) {
        if (response.ok) {
          toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Post edited successfully!',
            group: TOAST_GROUP,
            life: TOAST_TIMEOUT
          });
          navigateTo('/dashboard')
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
    ? handleCreatePostRequest
    : handleUpdatePostRequest;

</script>

<template>
  <PageHeader :title="props.mode === 'create' ? 'Create a New Post' : 'Edit Post'" />

  <!-- Form -->
  <form @submit="handleSubmit" class="flex flex-col gap-4">
    <FormContainer  :margin="false">
      <FormTextItem id="title" title="Title" placeholder="How to schedule a post" required/>
      <FormMultiSelector id="platforms" title="Platforms" :src="platforms?.data" :preselected="isEdit ? props.post.data.includes.platforms: null"
                         optionLabel="attributes.name" :searchable="true" labelHelper="Minimum 1 platform is required"
                         placeholder="Select platforms..." required/>

      <FormEditorItem id="content" title="Content" :required="true" :maxCharLen="maxCharCount"
                      placeholder="You post goes here..."/>

      <FormFileUploader id="image" title="Image" :accepted-types="['jpeg','png','jpg','gif']"
                        :imageUrl="isEdit ? props.post.data.attributes.imageUrl : null" max-file-size="4" :noRecommendedType="true"/>

      <FormSelector id="status" title="Status" :src="postStatus" :required="true" optionLabel="type"
                     placeholder="Select Type..." />

      <FormDateItem v-if="form.values?.status?.type === 'scheduled'" id="scheduled_at" title="Schedule At" :useTime="true"
                    :required="true" :minDate="new Date()" />

    </FormContainer>
    <FormErrors :errors="errors"/>
    <Button class="w-full" type="submit"
            :disabled="isFormSubmitting" severity="contrast" :label="props.mode === 'create' ? 'Create Post' : 'Edit Post'"/>
  </form>
</template>

<style scoped>

</style>