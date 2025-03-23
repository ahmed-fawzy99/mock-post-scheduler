<script setup lang="ts">
defineProps<{
  title: string;
  actionName?: string;
  icon?: string;
  to?: string;
}>()
</script>

<template>
  <div class="flex max-sm:flex-col justify-between">
    <div class="flex gap-2 items-center">
      <Icon name="solar:arrow-left-outline" class="cursor-pointer" @click="$router.back()"/>
      <h1>{{ title }}</h1>
    </div>
    <NuxtLink v-if="actionName" :to="to" class="action-div">
      <Icon :name="icon" />
      {{ actionName }}
    </NuxtLink>
  </div>
</template>

<style scoped>

</style>