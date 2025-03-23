<script setup lang="ts">
import { useField } from 'vee-validate';

const props = defineProps<{
    id: string;
    title: string;
    required?: boolean;
}>();

const { errorMessage, value } = useField(() => props.id, undefined, {
  validateOnValueUpdate: false,
});
</script>

<template>
  <div>
    <div class="flex items-center">
      <Checkbox v-model="value" :inputId="id" :name="title" :binary="true"/>
      <label :for="id" class="text-md ms-2"><slot /> <FormRequiredAst v-if="required" /></label>
    </div>
    <small v-show="errorMessage" class="text-red-500">{{ errorMessage }}</small>
  </div>

</template>

<style scoped>

</style>
