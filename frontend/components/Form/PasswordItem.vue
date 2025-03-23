<script setup lang="ts">
import {useField} from 'vee-validate';

const props = defineProps<{
  id: string,
  title: string,
  helper?: boolean;
}>();

const {value, errorMessage} = useField(() => props.id, undefined, {
  validateOnValueUpdate: false,
});

</script>

<template>
  <div>
    <label :for="id" class=" text-md  pb-1">{{ title }} <sup class="text-red-500">*</sup></label>
    <Password :inputId="id" placeholder="*********" v-model="value" :invalid="errorMessage" toggleMask :feedback="helper" fluid required>
      <template #header>
        <div class="font-semibold text-xm mb-4">Pick a password</div>
      </template>
      <template #footer>
        <Divider/>
        <ul class="pl-2 ml-2 my-0 leading-normal">
          <li>At least one lowercase</li>
          <li>At least one uppercase</li>
          <li>At least one numeric</li>
          <li>Minimum 8 characters</li>
        </ul>
      </template>
    </Password>
    <small v-show="errorMessage" class="text-red-500">{{ errorMessage }}</small>
  </div>

</template>

<style scoped>

</style>
