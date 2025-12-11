<script setup>
import { useRouter, useRoute } from '#imports';


const route = useRoute();

// sol menu open/close state 
const sidebarOpen = ref(false);


// logout token ve session temizliyor /login'e yonlendiriyor 
const { isLoggedIn, logout, initAuth, user } = useAuth();

onMounted(() => {
  // sayfa acilinca auth state'i sessionStorage'dan yukle
  // user ve token aliyor sessionStorage'dan
  initAuth();
});

const menuItems = [
  { label: 'Dashboard', to: '/' },
  { label: 'Vehicles', to: '/vehicles' },
  { label: 'Drivers', to: '/drivers' },
  { label: 'Trips', to: '/trips' },
];

const isActive = (path) => {
  return route.path === path;
};

const handleLogout = async () => {
  await logout();
  sidebarOpen.value = false;
};
</script>


<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 flex">
    <!-- mobilde sidebar acilinca arkayı karartan overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black/40 z-20 md:hidden"
      @click="sidebarOpen = false"
    />

    <!-- sol sidebar (off-canvas) -->
    <aside
      class="fixed inset-y-0 left-0 w-64 border-r border-slate-800 bg-slate-950/90 transform transition-transform duration-200 z-30"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <!-- logo / project name -->
      <div class="h-16 flex items-center px-5 border-b border-slate-800">
        <span class="text-lg font-semibold tracking-wide">
          Fleet-Master
        </span>
      </div>

      <!-- menu -->
      <nav class="flex-1 py-4 px-3 space-y-1">
        <NuxtLink
          v-for="item in menuItems"
          :key="item.to"
          :to="item.to"
          class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors"
          :class="
            isActive(item.to)
              ? 'bg-slate-800 text-white'
              : 'text-slate-300 hover:bg-slate-800/60 hover:text-white'
          "
          @click="sidebarOpen = false"
        >
          {{ item.label }}
        </NuxtLink>
      </nav>

      <!-- alt kisim (user name / logout) -->
      <div class="border-t border-slate-800 px-4 py-3 text-xs text-slate-400">
        <div class="mb-2">
          <span class="font-medium text-slate-200">
            {{ user?.name || 'Kullanıcı' }}
          </span>
        </div>
        <button
          v-if="isLoggedIn"
          type="button"
          class="text-xs text-red-400 hover:text-red-300"
          @click="handleLogout"
        >
          Log out 
        </button>
      </div>
    </aside>

    <!-- sag ana alan -->
    <div
      class="flex-1 flex flex-col w-full transition-[padding-left] duration-200 md:pl-0"
      :class="sidebarOpen ? 'md:pl-64' : ''"
    >
      <!-- ust bar -->
      <header
        class="h-16 border-b border-slate-800 flex items-center px-4 md:px-8 justify-between bg-slate-900/90 backdrop-blur"
      >
        <div class="flex items-center gap-3">
          <!-- hamburger buton (her ekranda olmali) -->
          <button
            type="button"
            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-700 text-slate-200 hover:bg-slate-800"
            @click="sidebarOpen = !sidebarOpen"
          >
            <!-- 3 cizgi -->
            <span class="sr-only">Menüyü aç/kapat</span>
            <div class="space-y-1">
              <span class="block w-4 h-[2px] bg-slate-200"></span>
              <span class="block w-4 h-[2px] bg-slate-200"></span>
              <span class="block w-4 h-[2px] bg-slate-200"></span>
            </div>
          </button>

          <h1 class="text-lg font-semibold">
            {{ route.meta.title || 'Panel' }}
          </h1>
        </div>

        <div class="hidden md:flex items-center gap-3 text-sm text-slate-300">
          <span v-if="user">
            Merhaba,
            <span class="font-medium">{{ user.name }}</span>
          </span>
        </div>
      </header>

      <!-- icerik -->
      <main class="flex-1 px-4 md:px-8 py-6">
        <slot />
      </main>
    </div>
  </div>
</template>


<!-- 
loginden sonra direkt olarak dashboard geliyor (sidebar kapali, sadece ust bar ve icerik var)
ustteki 3 cizgili butona basinca mobilde soldan kayan menu ve arkasi kararan overlay 
desktopta icerik saga kayarak sol sutun aciliyor 
menuden bir sayfaya tiklayinca sidebar yine kapaniyor 
-->




