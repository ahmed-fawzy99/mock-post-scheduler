<script setup lang="ts">
import {truncateText, scrollToTop, capitalizeFirstLetter} from "~/composables/useGenericHelpers";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import { debounce } from "lodash";


const toast = useToast();
const confirm = useConfirm();
const curPage = ref(0)
const typedSearch = ref('')

const {data: users, status, error} = await useApi(`/users?page=1`,)

const onPageChange = async (ev) => {
  const {data} = await useApi(`/users?page=${ev.page+1}`,{
    onResponse({ response }) {
      users.value = response._data
      scrollToTop()
    }
  })
};

const confirmDelete = (event, id) => {
  confirm.require({
    target: event.currentTarget,
    message: 'Do you want to delete this user?',
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
  const {data, status, error} = await useApi(`/users/${id}`, {
    method: 'DELETE',
    onResponse({ response }) {
      if (response.ok) {
        users.value.data = users.value.data.filter(post => post.id !== id)
        toast.add({ severity: 'success', summary: 'Success', detail: `User Deleted Successfully`, group: TOAST_GROUP, life: TOAST_TIMEOUT });
      }
    }});
};

// Title Filter API Call
watch(
    typedSearch,
    debounce(async (newVal) => {
      const {data} = await useApi(apiUrlBuilder('users', [`page=1`, `filter[search]=${newVal}`]),{
        onResponse({ response }) {
          users.value = response._data
          curPage.value = 0
        }
      })
    }, 500)
);
</script>

<template>
  <div v-if="status === 'success'" class="w-full flex flex-col gap-4">
    <!-- Filters -->
    <div v-if="users?.data.length > 0" class="flex max-sm:flex-col justify-end gap-4">
      <IconField class="max-h-10">
        <InputIcon class="pi pi-search" />
        <InputText v-model="typedSearch" placeholder="Search for user..." />
      </IconField>
    </div>
    <DataTable :value="users.data" stripedRows tableStyle="min-width: 50rem">
      <template #empty> No users found. </template>
      <Column field="attributes.name" header="Name" />
      <Column field="attributes.email" header="Email">
        <template #body="{ data }">
          <a :href="'mailto:' + data.attributes.email">{{data.attributes.email}}</a>
        </template>
      </Column>
      <Column  header="Verified Email?">
        <template #body="{ data }">
          <Icon v-if="data.attributes.emailVerifiedAt" name="solar:check-circle-linear" class="text-green-500"/>
          <Icon v-else name="solar:close-circle-linear" class="text-red-500"/>
        </template>
      </Column>
      <Column header="Disallowed Platforms">
        <template #body="{ data }">
          <div v-if="data.includes.disallowedPlatforms.length > 0" class="flex gap-1">
            <i v-for="platform in data.includes.disallowedPlatforms" :key="platform.id"
                  :class="`pi pi-${platform.attributes.type}`"/>
          </div>
          <span v-else name="solar:close-circle-linear">-</span>
        </template>
      </Column>
      <Column header="Posts">
        <template #body="{ data }">
          <NuxtLink :to="`/dashboard/admin/users/${data.id}`">{{data.includes.postCount}}</NuxtLink>
        </template>
      </Column>
      <Column header="Actions">
        <template #body="{data}">
          <Button v-if="data.attributes.status !== 'PUBLISHED'" label="Edit" icon="pi pi-pencil"  variant="text" class="!-ml-3"
                  @click="navigateTo(`/dashboard/admin/users/edit?id=${data.id}`)"/>
          <ConfirmPopup></ConfirmPopup>
          <Button label="Delete" icon="pi pi-trash" severity="danger" variant="text" @click="confirmDelete($event, data.id)"/>
        </template>
      </Column>
    </DataTable>
    <Paginator v-model:first="curPage" v-if="users.meta?.per_page < users.meta?.total" @page="onPageChange($event)" :rows="users.meta?.per_page" :totalRecords="users.meta?.total"></Paginator>
  </div>
  <CustomSkeleton v-else-if="status !== 'success' && !error" class="h-60" />
  <div v-else-if="status !== 'success' && error" class="flex flex-col justify-center text-center text-neutral-500 dark:text-neutral-300">
    <h1>Something Went Wrong</h1>
    <p>The service might be down. Please try again at a later time</p>
  </div>
</template>

<style scoped>

</style>