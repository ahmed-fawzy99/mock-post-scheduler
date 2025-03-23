<script setup lang="ts">

definePageMeta({
  middleware: ['auth', 'admin'],
})

const route = useRoute()
const formMode = route.params.formMode
const editId = route.query.id
const user = ref(null)

if(formMode === 'edit') {
  const {data} = await useApi(`/users/${editId}`,{
    onResponse({ response }) {
      user.value = response._data
    }
  })
}


</script>

<template>
  <div class="flex flex-col gap-4">
    <div class="wrapper-main flex flex-col gap-6">
      <div class="w-full flex flex-col gap-4">
        <UserCUForm
            v-if="$route.params.formMode === 'create' || ($route.params.formMode === 'edit' && user)"
            :mode="formMode" :user="user"/>
      </div>
    </div>
    <div class="wrapper-main flex flex-col gap-6">
      <div class="w-full flex flex-col gap-4">
        <PageHeader title="User Posts" />
        <Posts v-if="user" :endpoint="'all-posts'" :params="[`filter[user_id]=${user.data.id}`]"/>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>