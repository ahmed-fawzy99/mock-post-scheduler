<script setup lang="ts">
import { useField } from 'vee-validate';

const props = defineProps<{
    id: string;
    title: string;
    fullWidth?: boolean;
    lightLabel?: boolean;
    required?: boolean;
    labelHelper?: string;
    autocomplete?: string;
    placeholder?: string;
}>();

const { errorMessage, value } = useField(() => props.id, undefined, {
  validateOnValueUpdate: false,
});
</script>

<template>
    <div class="flex flex-col">
        <label :for="id" :class="{'font-light text-md text-zinc-500': lightLabel}">
            {{ title }} <FormRequiredAst /> <FormLabelHelper v-if="labelHelper" :text="labelHelper"/>
        </label>
        <InputText :id="id" v-model="value" :invalid="errorMessage?.length > 0" :placeholder="placeholder"
                   :required="required" :autocomplete="autocomplete" />
        <small v-show="errorMessage" class="text-red-500">{{ errorMessage }}</small>
    </div>
</template>

<style scoped>

</style>
