<script setup lang="ts">

definePageMeta({
  middleware: ['auth', 'admin'],
})

const route = useRoute()
const formMode = route.params.formMode
const editId = route.query.id
const platform = ref(null)

if(['edit', 'view'].includes(route.params.formMode)) {
  const {data} = await useApi(`/platforms/${editId}`,{
    onResponse({ response }) {
      platform.value = response._data
    }
  })
}

</script>

<template>
  <div class="flex flex-col gap-4">
    <div v-if="$route.params.formMode !== 'view'" class="wrapper-main flex flex-col gap-6">
      <div class="w-full flex flex-col gap-4">
        <PlatformCUForm
            v-if="$route.params.formMode === 'create' || ($route.params.formMode === 'edit' && platform)"
            :mode="formMode" :platform="platform"/>
      </div>
    </div>
    <div v-if="['edit', 'view'].includes($route.params.formMode)" class="wrapper-main flex flex-col gap-6">
      <div class="w-full flex flex-col gap-4">
        <PageHeader title="System Platforms" />
        <Posts v-if="platform" endpoint="all-posts" :params="[`filter[platform_id]=${platform.data.id}`]"/>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>