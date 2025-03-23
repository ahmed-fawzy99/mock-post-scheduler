<script setup lang="ts">
defineProps<{
  meta: object
}>()

//
</script>

<template>
    <div class="card">
      <Paginator :rows="meta.per_page" :totalRecords="meta.total">
        <template #container="{ first, last, page, pageCount, prevPageCallback, nextPageCallback, totalRecords }">
          <div class="flex items-center gap-4 border border-primary bg-transparent rounded-full w-full py-1 px-2 justify-between">
            <Button icon="pi pi-chevron-left" rounded text @click="prevPageCallback" :disabled="page === 0" />
            <div class="text-color font-medium">
              <span class="hidden sm:block">Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }}</span>
              <span class="block sm:hidden">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
            </div>
            <Button icon="pi pi-chevron-right" rounded text @click="nextPageCallback" :disabled="meta.current_page === meta.last_page - 1" />
          </div>
        </template>
      </Paginator>
    </div>
</template>

<style scoped>

</style>