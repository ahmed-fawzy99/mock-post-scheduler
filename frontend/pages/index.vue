<script setup lang="ts">
import LandingImg from "~/components/LandingImg.vue";

const appConfig = useAppConfig()
function toggleDarkMode() {
  document.documentElement.classList.toggle('dark-mode');
}

</script>

<template>
  <div class="wrapper !grid grid-cols-1 md:grid-cols-2 max-md:text-center items-center gap-8 !md:p-12">
    <div class="flex justify-center">
      <div class="flex flex-col gap-4 max-w-lg text-neutral-800 dark:text-neutral-200 max-md:items-center">
        <h1 class="!text-3xl font-medium">
          Plan Once, Post Everywhere — Effortlessly!
        </h1>
        <LandingImg class="flex md:hidden w-full " />
        <p>
          Create, schedule, and manage content across multiple platforms — all from one streamlined dashboard.
        </p>
        <NuxtLink to="/register"
                  class="flex gap-2 items-center px-4 py-2 bg-primary hover:bg-primary-600
                text-white rounded-md font-semibold w-fit transition-colors ease-in-out duration-100">
          <span>Get Started</span>
          <Icon name="solar:arrow-right-linear" class="-mb-0.5"/>
        </NuxtLink>
      </div>
    </div>
    <LandingImg class="hidden md:flex" />
  </div>


</template>

<style scoped>

</style>