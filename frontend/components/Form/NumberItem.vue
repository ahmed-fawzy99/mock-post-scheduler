<script setup lang="ts">

import {useField} from "vee-validate";

const props = defineProps<{
    id: string;
    title: string;
    lightLabel?: boolean;
    error?: boolean;
    required?: boolean;
    labelHelper?: string;
    placeholder?: string;
    min?: number;
    max?: number;
    noFraction?: boolean;
}>();

const { errorMessage, value } = useField(() => props.id, undefined, {
  validateOnValueUpdate: false,
});
</script>

<template>
    <div class="flex flex-col w-full" :class="{ 'gap-2' : !lightLabel, 'gap-1' : lightLabel}">
        <label :for="id" :class="{'font-light text-md text-zinc-500': lightLabel}">
            {{ title }}<sup v-if="required" class="text-red-500">*</sup> <FormLabelHelper v-if="labelHelper" :text="labelHelper"/>
        </label>

        <InputNumber v-model="value"
                     :inputId="id"
                     :min="min ?? 0" :max="max ?? 999999999999"
                     :placeholder="placeholder"
                     :minFractionDigits="0" :maxFractionDigits="noFraction ? 0 : 2" fluid :required="required" />

        <small v-show="error" class="text-red-500">{{ error }}</small>
    </div>
</template>

<style scoped>

</style>
