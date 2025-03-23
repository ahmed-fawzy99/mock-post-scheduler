<script setup lang="ts">
import {truncateText, scrollToTop, capitalizeFirstLetter, apiUrlBuilder} from "~/composables/useGenericHelpers";
import { debounce } from "lodash";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";

const props = defineProps<{
  endpoint?: string,
  params?: Array<string>,
}>()

const toast = useToast();
const confirm = useConfirm();
const apiEndpoint = props.endpoint || 'posts'
const {formatDateTime} = useDayjs()
let shallowParams = props.params || []

const statusFilter = [
  { value: 'all', label: 'All' },
  { value: 'draft', label: 'Draft' },
  { value: 'scheduled', label: 'Scheduled' },
  { value: 'published', label: 'Published' },
]
const curPage = ref(0)
const typedSearch = ref('')
const selectedStatus = ref('all')


const {data: posts, status, error} = await useApi(apiUrlBuilder(apiEndpoint, shallowParams.concat([`page=1`])),{
})

const onPageChange = async (ev) => {
  const {data} = await useApi(apiUrlBuilder(apiEndpoint, shallowParams.concat([`page=${ev.page+1}`])),{
    onResponse({ response }) {
      posts.value = response._data
      scrollToTop()
    }
  })
};

const confirmDelete = (event, id) => {
  confirm.require({
    target: event.currentTarget,
    message: 'Do you want to delete this post?',
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
  const {data, status, error} = await useApi(`posts/${id}`, {
    method: 'DELETE',
    onResponse({ response }) {
      if (response.ok) {
        posts.value.data = posts.value.data.filter(post => post.id !== id)
        toast.add({ severity: 'success', summary: 'Success', detail: `Post Deleted Successfully`, group: TOAST_GROUP, life: TOAST_TIMEOUT });
      }
    }});
};

const modalVisible = ref(false);
const selectedPost = ref(null);
const openModal = (id) => {
  selectedPost.value = posts.value.data.find(post => post.id === id);
  modalVisible.value = true;
}
const closeModal = () => {
  selectedPost.value = null;
  modalVisible.value = false;
}

// Status Filter API Call
watch(selectedStatus, async (newStatus) => {
    const {data} = await useApi(apiUrlBuilder(apiEndpoint, shallowParams.concat([`page=1`], [`filter[search]=${typedSearch.value}`], [`filter[status]=${newStatus.toUpperCase()}`])),{
      onResponse({ response }) {
        posts.value = response._data
        curPage.value = 0
      }
    })
  }
);
// Title Filter API Call
watch(
  typedSearch,
  debounce(async (newVal) => {
    const {data} = await useApi(apiUrlBuilder(apiEndpoint, shallowParams.concat([`page=1`], [`filter[search]=${newVal}`], [`filter[status]=${selectedStatus.value.toUpperCase()}`])),{
      onResponse({ response }) {
        posts.value = response._data
        curPage.value = 0
      }
    })
  }, 500)
);

</script>

<template>
  <Dialog v-model:visible="modalVisible" modal header="Post Details" :style="{ width: '25rem' }">
    <PostView @close="closeModal" :post="selectedPost" />
  </Dialog>
  <div v-if="posts?.data" class="w-full flex flex-col gap-4">

    <!-- Filters -->
    <div class="flex max-sm:flex-col justify-end gap-4">
      <IconField class="max-h-10">
        <InputIcon class="pi pi-search" />
        <InputText v-model="typedSearch" placeholder="Search for a post..." />
      </IconField>
      <RadioGroup v-model="selectedStatus" class="w-fit" name="selectedStatus" :options="statusFilter" />
    </div>

    <DataTable :value="posts.data" stripedRows tableStyle="min-width: 50rem">
      <template #empty> No posts found. </template>
      <Column  field="attributes.title" header="Title">
        <template #body="{ data }">
          <p @click="openModal(data.id)" class="cursor-pointer">{{ data.attributes.title }}</p>
        </template>
      </Column>
      <Column field="attributes.content" header="Content">
        <template #body="{ data }">
          <div @click="openModal(data.id)" class="cursor-pointer" v-html="truncateText(data.attributes.content)" />
        </template>
      </Column>
      <Column field="attributes.imageUrl" header="Image">
        <template #body="{ data }">
          <a v-if="data.attributes.imageUrl" :href="data.attributes.imageUrl" target="_blank">
            <Icon name="solar:camera-outline" />
          </a>
          <span v-else>No Image</span>
        </template>
      </Column>
      <Column field="attributes.status" header="Status">
        <template #body="{ data }">
          {{capitalizeFirstLetter(data.attributes.status.toLowerCase())}}
        </template>
      </Column>
      <Column field="includes.platforms" header="Platforms">
        <template #body="{ data }">
          {{data.includes.platforms ? data.includes.platforms.map(platform => platform.attributes.name).join(', ') : '-'}}
        </template>
      </Column>
      <Column field="attributes.scheduledAt" header="Scheduled">
        <template #body="{ data }">
          {{data.attributes.scheduledAt ? formatDateTime(data.attributes.scheduledAt) : '-'}}
        </template>
      </Column>
      <Column field="attributes.createdAt" header="Created">
        <template #body="{ data }">
          {{formatDateTime(data.attributes.createdAt)}}
        </template>
      </Column>
      <Column header="Actions">
        <template #body="{data}">
          <Button v-if="data.attributes.status !== 'PUBLISHED'" label="Edit" icon="pi pi-pencil"  variant="text" class="!-ml-3"
                  @click="navigateTo(`/dashboard/posts/edit?id=${data.id}`)"/>
          <ConfirmPopup></ConfirmPopup>
          <Button label="Delete" icon="pi pi-trash" severity="danger" variant="text" class="!-ml-3" @click="confirmDelete($event, data.id)"/>
        </template>
      </Column>
    </DataTable>
    <Paginator v-model:first="curPage" v-if="posts.meta?.per_page < posts.meta?.total" @page="onPageChange($event)" :rows="posts.meta?.per_page" :totalRecords="posts.meta?.total"></Paginator>
  </div>
  <CustomSkeleton v-else-if="!posts?.data && !error" class="h-60" />
  <div v-else-if="!posts?.data && error" class="flex flex-col justify-center text-center text-neutral-500 dark:text-neutral-300">
    <h1>Something Went Wrong</h1>
    <p>The service might be down. Please try again at a later time</p>
  </div>
</template>

<style scoped>

</style>