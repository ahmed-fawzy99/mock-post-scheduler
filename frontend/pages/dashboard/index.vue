<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

</script>

<template>
  <div class="wrapper-main flex flex-col gap-6">
    <PageHeader title="My Posts" action-name="Create a new Post" icon="solar:add-square-outline" to="/dashboard/posts/create" />
    <Posts />
  </div>
</template>

<style scoped>

</style>