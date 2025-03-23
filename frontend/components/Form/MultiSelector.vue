<script setup lang="ts">
import {useField} from "vee-validate";

const props = defineProps<{
    id: string,
    placeholder: string,
    title: string,
    src: object | undefined,
    labelHelper: string,
    required?: boolean,
    optionLabel?: string,
    searchable?: boolean,
    maxSelectedLabels?: number;
    preselected?: Array<object>;
}>();

const { errorMessage, value } = useField(() => props.id, undefined, {
  validateOnValueUpdate: false,
});
value.value = props.preselected ?? [];
</script>

<template>
    <div class="flex flex-col">
      <label :for="id">{{ title }} <FormRequiredAst v-if="required"/> <FormLabelHelper v-if="labelHelper" :text="labelHelper"/></label>
      <MultiSelect v-if="src" :id="id" v-model="value" :options="src" display="chip" :optionLabel="optionLabel ?? 'name'" filter :placeholder="placeholder ?? 'Select an item'"
                   :maxSelectedLabels="maxSelectedLabels ?? 99" :required="required" />
        <small v-show="errorMessage" class="text-red-500">{{ errorMessage }}</small>
    </div>
</template>

<style scoped>

</style>
