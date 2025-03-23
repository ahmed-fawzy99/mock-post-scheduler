<script setup lang="ts">
defineProps<{
    text: string;
    noBraces?: boolean;
}>();
</script>
<template>
  <span v-if="noBraces" class="text-xs text-neutral-600 dark:text-neutral-300">{{text}}</span>
  <span v-else class="text-xs text-neutral-600 dark:text-neutral-300">({{text}})</span>
</template>

