<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    default: null, // null ise yeni kayit obje ise edit
  },
});

const emit = defineEmits(['update:modelValue', 'saved', 'cancel']);

const { $fetchApi } = useApi();

const form = ref({
  id: null,
  name: '',
  phone: '',
  license_type: '',
  status: 'active',
});

const loading = ref(false);
const error = ref(null);

// modelValue degistikce formu doldur
watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      form.value = {
        id: val.id,
        name: val.name || '',
        phone: val.phone || '',
        license_type: val.license_type || '',
        status: val.status || 'active',
      };
    } else {
      form.value = {
        id: null,
        name: '',
        phone: '',
        license_type: '',
        status: 'active',
      };
    }
  },
  { immediate: true }
);

const onSubmit = async (e) => {
  e.preventDefault();
  loading.value = true;
  error.value = null;

  try {
    const payload = {
      name: form.value.name,
      phone: form.value.phone || null,
      license_type: form.value.license_type || null,
      status: form.value.status || 'active',
    };

    if (form.value.id) {
      // guncelleme
      await $fetchApi(`/drivers/${form.value.id}`, {
        method: 'PUT',
        body: payload,
      });
    } else {
      // yeni kayit
      await $fetchApi('/drivers', {
        method: 'POST',
        body: payload,
      });
    }

    emit('saved'); // listeyi yenilemek icin parent'a haber ver
  } catch (err) {
    console.error(err);
    error.value = err?.data?.message || 'An error occured during registration';
  } finally {
    loading.value = false;
  }
};

const onCancel = () => {
  emit('cancel');
};
</script>

<template>
  <!-- Basit modal -->
  <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-700 rounded-xl p-6 shadow-xl">
      <h2 class="text-lg font-semibold mb-4">
        {{ form.id ? 'Edit Driver' : 'Add New Driver' }}
      </h2>

      <form @submit="onSubmit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-sm text-slate-300 mb-1">İsim</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Telefon</label>
            <input
              v-model="form.phone"
              type="text"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Lisans Tipi</label>
            <input
              v-model="form.license_type"
              type="text"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              placeholder="B, C, CE..."
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Durum</label>
            <select
              v-model="form.status"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="active">Aktif</option>
              <option value="inactive">Pasif</option>
            </select>
          </div>
        </div>

        <div v-if="error" class="text-sm text-red-400">
          {{ error }}
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button
            type="button"
            class="px-4 py-2 rounded bg-slate-700 text-sm hover:bg-slate-600"
            @click="onCancel"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 rounded bg-indigo-600 text-sm font-medium hover:bg-indigo-500 disabled:opacity-60"
            :disabled="loading"
          >
            {{ loading ? 'Kaydediliyor...' : 'Kaydet' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
