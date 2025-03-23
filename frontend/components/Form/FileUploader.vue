<script setup lang="ts">

import {useField} from "vee-validate";

const props = defineProps<{
  id: string;
  title: string;
  error?: boolean;
  required?: boolean;
  labelHelper?: string;
  acceptedTypes: Array<string>;
  maxFileSize: string; // in MB
  recommendedSize?: string;
  mediaCollectionName?: string;
  noRecommendedType?: boolean;
  imageUrl?: string;
}>();

const { errorMessage, value } = useField(() => props.id, undefined, {
  validateOnValueUpdate: false,
});

const media_image = defineModel('media_image');
const filePreview = ref(null);
const types = props.acceptedTypes.map(type =>
    type === 'svg' ? `image/svg+xml`
        : type === 'avif' ? ' .avif ,image/avif' : `image/${type}`
).join(',');

const displayPreview = (file, targetPreview) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        targetPreview.value = reader.result;
    };
};

const fileSizeOk = (file) =>  file.size <= props.maxFileSize * 1024 * 1024;


onMounted( () => {
    // Dropzone for Featured Image
    const dropzone = document.getElementById(props.id+'-dropzone');
    const input = document.getElementById(props.id);

  if (props.imageUrl) {
    filePreview.value = props.imageUrl;

    value.value = {
      isExistingImage: true,
      url: props.imageUrl,
      // Add a toString method to make it display nicely in debugging
      toString: () => `[Existing Image: ${props.imageUrl}]`
    };
  }

    // File preview for featured image
    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file && fileSizeOk(file)) {
            displayPreview(file, filePreview);
            value.value = file;
        } else {
            input.value = null;
          alert('File size too large. Max size is ' + props.maxFileSize + 'MB');
        }
    });

    // Drag-and-drop functionality for featured image
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-primary');
    });

    dropzone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-primary');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-primary');
        const file = e.dataTransfer.files[0];
        if (file && fileSizeOk(file)) {
            if (types.includes(file.type)) {
                displayPreview(file, filePreview);
                value.value = file;
            } else {
                alert('Invalid file format. Only ' + props.acceptedTypes.join(', ') + ' are allowed');
            }
        } else {
          alert('File size too large. Max size is ' + props.maxFileSize + 'MB');
        }
    });

});

function removeFile() {
    filePreview.value = null;
    value.value = null;
    document.getElementById(props.id).value = '';
}

</script>

<template>
  <div class="flex flex-col md:col-span-2">
    <p class="mt-2 mb-4 text-xl font-medium">{{ title }} <FormRequiredAst v-if="required" /> <FormLabelHelper v-if="labelHelper" :text="labelHelper" /></p>
    <div class="w-full relative border-2 border-dashed rounded-lg p-6 mb-4"
         :class="{'border-zinc-300': !error, 'border-red-400': error}" :id="props.id + '-dropzone'">
      <input type="file" :accept="types"
             class="absolute inset-0 w-full h-full opacity-0 z-40" :id="id"/>
      <div class="text-center ">
        <img class="mx-auto h-12 w-12 dark:invert" src="~/assets/images/image-upload.svg" alt="Upload Image Icon">
        <h3 class="mt-2 text-sm font-medium text-neutral-900 dark:text-white">
          <label :for="id" class="relative cursor-pointer">
            <span>Drag and drop</span>
            <span class="text-primary"> or browse </span>
            <span>to upload</span>
          </label>
        </h3>
        <p class="mt-1 text-xs text-zinc-700 dark:text-neutral-200">
          Rules: {{acceptedTypes.map(type => type.toUpperCase()).join(', ')}} up to {{maxFileSize}}MB.
          <span v-if="!noRecommendedType">(Recommended: {{acceptedTypes[0].toUpperCase()}})</span>
        </p>
        <p v-if="recommendedSize" class="mt-1 text-xs text-zinc-700 dark:text-neutral-200"> {{
            recommendedSize
          }}.
        </p>
      </div>
      <div class="flex flex-col items-center justify-between gap-4">
        <img v-if="filePreview" :src="filePreview" class="mt-4 max-h-40" alt="Uploaded Feature Image"
             id="preview">
        <Button v-if="filePreview" @click="removeFile" severity="danger" label="Remove Image" class="z-50"
                rounded/>
      </div>
    </div>
    <small v-show="errorMessage" class="text-red-500">{{ errorMessage }}</small>
  </div>
</template>

<style scoped>

</style>
