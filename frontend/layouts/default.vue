<script setup lang="ts">
const appConfig = useAppConfig()
const colorMode = useColorMode()

useHead({
  title: appConfig.name,
  meta: [
    { name: 'description', content: 'Post scheduling for social media.' }
  ],
})

const auth = useAuthStore()
const { logout } = auth
const { isAuthenticated, isAdmin } = storeToRefs(auth)

const menu = ref();
const items = ref([
  {
    label: 'Account Settings',
    items: [
      {
        label: 'My Account',
        command: () => {
          navigateTo('/account')
        }
      },
      {
        label: 'Log Out',
        command: () => {
          logout()
        }
      }
    ]
  }
]);
const toggle = (event) =>
  menu.value.toggle(event);

const toggleDarkMode = () =>
    colorMode.preference = colorMode.preference === 'dark' ? 'light' : 'dark'
</script>

<template>
  <div class="h-screen flex flex-col ">
    <header class="mb-8">
      <nav class="block w-full container mx-auto mt-10 div-card !py-2 lg:!py-3">
        <div class="flex items-center justify-between">
          <NuxtLink to="/" class="mr-4 block cursor-pointer py-1.5 text-base  font-semibold">
            {{ appConfig.name }}
          </NuxtLink>
          <div>
            <ul class="flex gap-2 my-2 lg:mb-0 lg:mt-0 lg:flex-row lg:items-center lg:gap-6">
              <li class="flex items-center text-sm gap-x-2">
                <button @click="toggleDarkMode"
                        class="flex items-center p-1 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 transition-colors duration-50 ease-in-out cursor-pointer">
                  <Icon v-if="colorMode.preference === 'light'" name="solar:moon-outline"/>
                  <Icon v-if="colorMode.preference === 'dark'" name="solar:sun-2-linear"/>
                </button>
              </li>
              <li v-if="!isAuthenticated" class="flex items-center p-1 text-sm gap-x-2 ">
                <Icon name="solar:login-3-broken"/>
                <NuxtLink to="/login" class="flex items-center">
                  Login / Register
                </NuxtLink>
              </li>
              <li v-if="isAuthenticated && isAdmin" class="flex items-center p-1 text-sm gap-x-2" :class="{'font-bold' : $route.path.includes('/dashboard/admin')}">
                <NuxtLink to="/dashboard/admin" class="flex items-center gap-2">
                <Icon name="solar:users-group-rounded-outline"/>
                  <span class="hidden md:block">Admin Board</span>
                </NuxtLink>
              </li>
              <li v-if="isAuthenticated" class="flex items-center p-1 text-sm gap-x-2" :class="{'font-bold' : $route.path === '/dashboard'}">
                <NuxtLink to="/dashboard" class="flex items-center gap-2">
                <Icon name="solar:box-minimalistic-outline"/>
                  <span class="hidden md:block">Dashboard</span>
                </NuxtLink>
              </li>
              <li v-if="isAuthenticated" class="flex items-center p-1 text-sm gap-x-2" :class="{'font-bold' : $route.path === '/account'}">
                <div class="card flex justify-center items-center">
                  <button @click="toggle" class="flex items-center gap-2 cursor-pointer " aria-haspopup="true" aria-controls="overlay_menu">
                    <Icon name="solar:settings-linear"/>
                    <span class="hidden md:block">Options</span>
                    <Icon name="solar:alt-arrow-down-linear" size="1.25em"/>
                  </button>
                  <Menu ref="menu" id="overlay_menu" :model="items" :popup="true" />
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <main class="flex-1">
      <div class="mx-auto flex flex-col h-full">
        <Toast position="bottom-right" group="br" />
        <slot/>
      </div>
    </main>


    <footer class="p-4">
      <div class="flex flex-col sm:flex-row justify-center items-center text-neutral-500 text-sm sm:text-base gap-2 sm:gap-4 text-center">
        <span>Ahmed Deghady</span>
        <span class="hidden sm:inline">|</span>
        <a href="mailto:ahmaddeghady99@gmail.com" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors">
          ahmaddeghady99@gmail.com
        </a>
        <span class="hidden sm:inline">|</span>
        <a href="https://github.com/ahmed-fawzy99" target="_blank" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors">
          GitHub
        </a>
        <span class="hidden sm:inline">|</span>
        <a href="http://linkedin.com/in/ahmeddeghady" target="_blank" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors">
          LinkedIn
        </a>
      </div>
    </footer>
  </div>
</template>

<style scoped>

</style>