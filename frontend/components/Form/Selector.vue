<script setup lang="ts">
import {useField} from "vee-validate";

const props = defineProps<{
    id: string,
    placeholder: string,
    title: string,
    src: Array<object> | undefined,
    labelHelper?: string,
    required?: boolean,
    optionLabel?: string,
    searchable?: boolean,
    maxSelectedLabels?: number;

}>();

const { errorMessage, value } = useField(() => props.id, undefined, {
  validateOnValueUpdate: false,
});

</script>

<template>
    <div class="flex flex-col">
      <label :for="id">{{ title }} <FormRequiredAst v-if="required"/> <FormLabelHelper v-if="labelHelper" :text="labelHelper"/></label>
      <Select v-if="src" :id="id" v-model="value" :filter="searchable"
              :options="src" :optionLabel="optionLabel ?? 'name'" :placeholder="placeholder ?? 'Select an item'" class="w-full" required/>
      <small v-show="errorMessage" class="text-red-500">{{ errorMessage }}</small>
    </div>
</template>

<style scoped>

</style>
