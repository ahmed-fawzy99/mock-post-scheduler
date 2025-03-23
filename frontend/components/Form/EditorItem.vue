<script setup lang="ts">
import {useField} from "vee-validate";

const props = defineProps<{
    id: string;
    title: string;
    required?: boolean;
    labelHelper?: string;
    placeholder?: string;
    height?: string;
    maxCharLen?: number;
}>();

const { errorMessage, value } = useField(() => props.id, undefined, {
  validateOnValueUpdate: false,
});
</script>

<template>
    <div class="flex flex-col md:col-span-2">
        <p class="cursor-default mb-1">{{ title }} <FormRequiredAst v-if="required" /> <FormLabelHelper v-if="labelHelper" :text="labelHelper"/></p>
      <Editor :id="id" v-model="value" editorStyle="height: 200px" :placeholder="placeholder"/>
      <small v-if="maxCharLen" class="text-xs " :class="value?.length > maxCharLen ? 'text-red-500' : 'text-neutral-500'">
        {{value?.length ?? 0}} / {{ maxCharLen }} Character
      </small>
      <small v-show="errorMessage" class="text-red-500">{{ errorMessage }}</small>
    </div>
</template>

<style scoped>

</style>
