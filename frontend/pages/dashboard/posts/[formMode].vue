<script setup lang="ts">

definePageMeta({
  middleware: ['auth'],
})

const route = useRoute()
const formMode = route.params.formMode
const editId = route.query.id
const post = ref(null)

if(formMode === 'edit') {
  const {data} = await useApi(`/posts/${editId}`,{
    onResponse({ response }) {
      post.value = response._data
    }
  })
}


</script>

<template>
  <div class="wrapper-main flex flex-col gap-6">
    <div class="w-full flex flex-col gap-4">
      <PostCUForm
          v-if="$route.params.formMode === 'create' || ($route.params.formMode === 'edit' && post)"
          :mode="formMode" :post="post"/>
    </div>
  </div>
</template>

<style scoped>

</style>