<script setup lang="ts">
const props = defineProps<{
  post: object | null;
}>();
defineEmits(['close'])

const {data: poster, status, error} = await useApi(`/users/${props.post.relationship.poster.data.id}`)

const {fromNow, formatDate} = useDayjs()

const MAX_CHARS = 240;
const showFullContent = ref(false);

const isContentTruncated = computed(() => {
  return props.post.attributes.content.length > MAX_CHARS;
});

const displayContent = computed(() => {
  if (!isContentTruncated.value || showFullContent.value) {
    return props.post.attributes.content;
  }
  return props.post.attributes.content.slice(0, MAX_CHARS) + '...';
});


</script>

<template>
  <div class="p-4 bg-neutral-100 dark:bg-neutral-800 rounded-lg  max-w-lg mx-auto my-4">
    <div class="flex items-start mb-3">
      <div
          v-if="status === 'success'"
          class="w-10 h-10 rounded-full object-cover mr-3 flex justify-center items-center bg-neutral-300 dark:bg-neutral-700"
      >
        <span class="align-middle">{{poster.data?.attributes.name?.[0]}}</span>
      </div>
      <CustomSkeleton
          v-else
          class="!w-10 h-10 rounded-full mr-3 overflow-hidden"
      />

      <div class="flex flex-col">
        <h3 v-if="status === 'success'" class="font-medium">{{ poster.data.attributes.name }}</h3>
        <CustomSkeleton v-else class="w-20 h-4 mb-1" />
        <span v-if="post.attributes?.status !== 'DRAFT'" class="text-xs text-gray-500">{{
            post.attributes.status === 'SCHEDULED'
                ? `Scheduled for ${formatDate(post.attributes.scheduledAt)}`
                : `Posted ${fromNow(post.attributes.createdAt)}`
          }}
        </span>
        <span v-else class="text-xs text-neutral-500 dark:text-neutral-300">
          Draft
        </span>
      </div>
    </div>

    <div class="mb-2 ms-1">
      <h2 class="font-medium !text-lg mb-2">
        {{ post.attributes.title }}
      </h2>
      <div class="inline" v-html="displayContent" />
      <button
          v-if="isContentTruncated"
          @click="showFullContent = !showFullContent"
          class="font-medium focus:outline-none hover:underline ms-1 text-primary cursor-pointer"
      >
        {{ showFullContent ? 'See less' : 'See more' }}
      </button>
    </div>
  </div>

  <div>
    <Button label="Close" severity="secondary" class="float-end" @click="$emit('close')"/>
  </div>
</template>

<style scoped>

</style>