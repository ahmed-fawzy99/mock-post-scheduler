<script setup lang="ts">
definePageMeta({
  middleware: ['auth', 'admin'],
})

const router = useRouter()

const onTabChange = (tab) => {
  router.push({
    query: {
      tab: tab
    }
  })}
</script>

<template>
  <div class="wrapper-main flex flex-col gap-6 overflow-x-scroll">
    <PageHeader title="Admin Board" />
    <Tabs :value="$route.query.tab || 'users'" @update:value="onTabChange">
      <TabList>
        <Tab value="users">All Users</Tab>
        <Tab value="posts">All Posts</Tab>
        <Tab value="platforms">Platforms</Tab>
      </TabList>
      <TabPanels>
        <TabPanel value="users">
          <Users />
        </TabPanel>
        <TabPanel value="posts">
          <Posts endpoint="all-posts"/>
        </TabPanel>
        <TabPanel value="platforms">
          <Platforms />
        </TabPanel>
      </TabPanels>
    </Tabs>
  </div>
</template>

<style scoped>

</style>