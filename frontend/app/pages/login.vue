<script setup>
definePageMeta({ //bu sayede sayfanin layout'u layouts/auth.vue oluyor 
                 // ekstra layout belirtilmeyen kisimlarda default layout kullanilmis olacak 
    layout: 'auth',
});

const email = ref('admin@example.com');
const password = ref();

const { login, loading, error, initAuth, isLoggedIn } = useAuth();

// page acilinca eger zaten loginse direkt olarak dashboarda yonlendirir 
onMounted(() => {
  initAuth();

  if (isLoggedIn.value) {
    navigateTo('/');
  }
});

const onSubmit = async (e) => {
  e.preventDefault();
  await login(email.value, password.value);
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-900">
    <div class="w-full max-w-md bg-slate-800 rounded-xl border border-slate-700 p-8 shadow-lg">
      <h1 class="text-2xl font-bold text-white mb-6 text-center">
        Fleet-Master Giriş
      </h1>

      <form @submit="onSubmit" class="space-y-4">
        <div>
          <label class="block text-sm text-slate-300 mb-1">E-posta</label>
          <input
            v-model="email"
            type="email"
            class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-600 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="admin@example.com"
            required
          />
        </div>

        <div>
          <label class="block text-sm text-slate-300 mb-1">Şifre</label>
          <input
            v-model="password"
            type="password"
            class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-600 text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="••••••••"
            required
          />
        </div>

        <div v-if="error" class="text-sm text-red-400">
          {{ error }}
        </div>

        <button
          type="submit"
          class="w-full py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm flex items-center justify-center gap-2 disabled:opacity-60"
          :disabled="loading"
        >
          <span v-if="!loading">Giriş Yap</span>
          <span v-else>Giriş yapılıyor...</span>
        </button>
      </form>
    </div>
  </div>
</template>

<!-- 
form submit edildiginde onSubmit triggerlaniyor login(email, password) cagriliyor
login basarili olursa token + user state'e ve localStorage'a yaziliyor 
router.push('/') dashboarda gidiliyor 

hata olursa error state doluyor -> form altinda kirmizi mesaj gozukecek 
-->