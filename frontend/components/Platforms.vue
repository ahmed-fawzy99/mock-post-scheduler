<script setup lang="ts">
import {truncateText, scrollToTop, capitalizeFirstLetter} from "~/composables/useGenericHelpers";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";


const toast = useToast();
const confirm = useConfirm();
const {formatDateTime} = useDayjs()

const {data: platforms, status, error} = await useApi(`/platforms?page=1&includePostsCount=true`,)

const onPageChange = async (ev) => {
  const {data} = await useApi(`/platforms?page=${ev.page+1}&includePostsCount=true`,{
    onResponse({ response }) {
      platforms.value = response._data
      scrollToTop()
    }
  })
};

const confirmDelete = (event, id) => {
  confirm.require({
    target: event.currentTarget,
    message: 'Do you want to delete this platform? all associated posts will be deleted as well',
    icon: 'pi pi-info-circle',
    rejectProps: {
      label: 'Cancel',
      severity: 'secondary',
      outlined: true
    },
    acceptProps: {
      label: 'Delete',
      severity: 'danger'
    },
    accept: () => {
      handleDelete(id);
    },
    reject: () => {
      toast.add({ severity: 'info', summary: 'Cancelled', detail: 'Deletion Cancelled', group: TOAST_GROUP, life: TOAST_TIMEOUT });
    }
  });
};

const handleDelete = async (id) => {
  const {data, status, error} = await useApi(`/platforms/${id}`, {
    method: 'DELETE',
    onResponse({ response }) {
      if (response.ok) {
        platforms.value.data = platforms.value.data.filter(post => post.id !== id)
        toast.add({ severity: 'success', summary: 'Success', detail: `User Deleted Successfully`, group: TOAST_GROUP, life: TOAST_TIMEOUT });
      }
    }});
};
</script>

<template>
  <div class="flex justify-end">
    <NuxtLink to="/dashboard/admin/platforms/create" class="action-div">
      <Icon name="solar:add-square-outline" />
      Create a New Platform
    </NuxtLink>
  </div>
  <div v-if="status === 'success'" class="w-full flex flex-col gap-4">
    <DataTable :value="platforms.data" stripedRows tableStyle="min-width: 50rem">
      <template #empty> No platforms found. </template>
      <Column field="attributes.name" header="Name" />
      <Column field="attributes.type" header="Type" />
      <Column field="attributes.characterLimit" header="Character Limit" />
      <Column header="Posts" >
        <template #body="{data}">
          <NuxtLink :to="`/dashboard/admin/platforms/view?id=${data.id}`">{{ data.includes.postCount }}</NuxtLink>
        </template>
      </Column>
      <Column header="Actions">
        <template #body="{data}">
          <Button v-if="data.attributes.status !== 'PUBLISHED'" label="Edit" icon="pi pi-pencil"  variant="text" class="!-ml-3"
                  @click="navigateTo(`/dashboard/admin/platforms/edit?id=${data.id}`)"/>
          <ConfirmPopup></ConfirmPopup>
          <Button label="Delete" icon="pi pi-trash" severity="danger" variant="text" @click="confirmDelete($event, data.id)"/>
        </template>
      </Column>
    </DataTable>
    <Paginator v-if="platforms.meta?.per_page < platforms.meta?.total" @page="onPageChange($event)" :rows="platforms.meta?.per_page" :totalRecords="platforms.meta?.total"></Paginator>
  </div>
  <CustomSkeleton v-else-if="status !== 'success' && !error" class="h-60" />
  <div v-else-if="status !== 'success' && error" class="flex flex-col justify-center text-center text-neutral-500 dark:text-neutral-300">
    <h1>Something Went Wrong</h1>
    <p>The service might be down. Please try again at a later time</p>
  </div>
</template>

<style scoped>

</style>