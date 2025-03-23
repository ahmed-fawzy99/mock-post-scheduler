<script setup lang="ts">
defineProps<{
  margin?: boolean
}>();
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-4" :class="{'mb-6' : margin}">
        <slot />
    </div>
</template>

<style scoped>

</style>
