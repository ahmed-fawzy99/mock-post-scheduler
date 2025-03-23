<script setup lang="ts">
import {useField} from "vee-validate";
import {dateFormat} from "~/composables/useConfig";

const props = defineProps<{
    id: string;
    title: string;
    required?: boolean;
    useTime?: boolean;
    labelHelper?: string;
    placeholder?: string;
    minDate?: object;
    maxDate?: object;
}>();

const { errorMessage, value } = useField(() => props.id, undefined, {
  validateOnValueUpdate: false,
});


onMounted(() => {
    if (value.value)
      value.value = new Date(value.value);

});

</script>

<template>
    <div class="flex flex-col">
        <p class="cursor-default">{{ title }}<FormRequiredAst v-if="required" /> <FormLabelHelper v-if="labelHelper" :text="labelHelper"/> <FormLabelHelper v-if="useTime" noBraces :text="'In your timezone ('+ Intl.DateTimeFormat().resolvedOptions().timeZone + ')'"/></p>
        <DatePicker :id="id" v-model="value" :placeholder="placeholder ?? 'Select a Date...'" :dateFormat="dateFormat" showIcon
                    :showTime="useTime" :hourFormat="useTime ? '12' : null" :minDate="minDate" :maxDate="maxDate"
                    iconDisplay="input" class="!w-full" :manualInput="false" :invalid="errorMessage" />
        <small v-show="errorMessage" class="text-red-500">{{ errorMessage }}</small>
    </div>
</template>

<style scoped>

</style>
