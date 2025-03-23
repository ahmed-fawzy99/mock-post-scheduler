<script setup lang="ts">

defineProps<{
  errors: Array<string> | Object<string> | string
}>()

const isObject = (value: unknown): value is Record<string, unknown> => {
  return value !== null && typeof value === 'object' && !Array.isArray(value)
}
</script>

<template>
  <Message v-if="Object.keys(errors).length" severity="error">
    <div class="flex flex-col gap-2">
      <h2 class="font-bold">Error</h2>
      <ul v-if="isObject(errors)">
        <li v-for="(val, key, idx) in errors" :key="idx">
          {{ val[0] ?? val }}
        </li>
      </ul>
      <p v-else>
        {{ errors ?? "An error occurred." }}
      </p>
    </div>
  </Message>

</template>

<style scoped>

</style>