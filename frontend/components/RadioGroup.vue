<script setup lang="ts">

/* options
[
  { value: 'cheese', label: 'Cheese' },
  { value: 'mushroom', label: 'Mushroom' },
  { value: 'pepper', label: 'Pepper' },
  { value: 'onion', label: 'Onion' },
]
 */
const props = defineProps<{
  options: Array<object>,
  name: string,
  disabled?: boolean,
}>();

const value = defineModel()
</script>

<template>
  <div class="flex flex-wrap gap-4 px-3 py-2 rounded-md bg-neutral-100 dark:bg-neutral-900">
    <div v-for="(options, index) in options" :key="options.value" class="flex items-center gap-2">
      <RadioButton v-model="value" :inputId="`${name}${index}`" :name="name" :value="options.value" :disabled="disabled"/>
      <label :for="`${name}${index}`">{{ options.label }}</label>
    </div>
  </div>
</template>

<style scoped>

</style>