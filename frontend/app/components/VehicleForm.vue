<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    default: null, // null ise new record dolu ise edit 
  },
});

const emit = defineEmits(['update:modelValue', 'saved', 'cancel']);

const { $fetchApi } = useApi();

const form = ref({
  id: null,
  plate: '',
  brand: '',
  model: '',
  year: '',
  current_km: '',
  status: 'active',
});

const loading = ref(false);
const error = ref(null);

// modelValue degisince formu doldur
watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      form.value = {
        id: val.id,
        plate: val.plate || '',
        brand: val.brand || '',
        model: val.model || '',
        year: val.year || '',
        current_km: val.current_km || '',
        status: val.status || 'active',
      };
    } else {
      form.value = {
        id: null,
        plate: '',
        brand: '',
        model: '',
        year: '',
        current_km: '',
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
      plate: form.value.plate,
      brand: form.value.brand,
      model: form.value.model,
      year: form.value.year || null,
      current_km: form.value.current_km || 0,
      status: form.value.status,
    };

    if (form.value.id) {
      // update
      await $fetchApi(`/vehicles/${form.value.id}`, {
        method: 'PUT',
        body: payload,
      });
    } else {
      // new record 
      await $fetchApi('/vehicles', {
        method: 'POST',
        body: payload,
      });
    }

    emit('saved'); // save and refresh 
  } catch (err) {
    console.error(err);
    error.value = err?.data?.message || 'Kayıt sırasında bir hata oluştu.';
  } finally {
    loading.value = false;
  }
};

const onCancel = () => {
  emit('cancel');
};
</script>

<template>
  <!-- basic modal -->
  <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
    <div class="w-full max-w-lg bg-slate-900 border border-slate-700 rounded-xl p-6 shadow-xl">
      <h2 class="text-lg font-semibold mb-4">
        {{ form.id ? 'Edit Vehicle' : 'Add New Vehicle' }}
      </h2>

      <form @submit="onSubmit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-300 mb-1">Plaka</label>
            <input
              v-model="form.plate"
              type="text"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Marka</label>
            <input
              v-model="form.brand"
              type="text"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Model</label>
            <input
              v-model="form.model"
              type="text"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Yıl</label>
            <input
              v-model="form.year"
              type="number"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">KM</label>
            <input
              v-model="form.current_km"
              type="number"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Durum</label>
            <select
              v-model="form.status"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="active">Aktif</option>
              <option value="in_service">Serviste</option>
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
