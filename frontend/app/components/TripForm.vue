<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['update:modelValue', 'saved', 'cancel']);

const { $fetchApi } = useApi();

const form = ref({
  id: null,
  vehicle_id: '',
  driver_id: '',
  start_time: '',
  end_time: '',
  origin: '',
  destination: '',
  status: 'planned',
  notes: '',
});

const loading = ref(false);
const error = ref(null);

const vehicles = ref([]);
const drivers = ref([]);

// backend'den aktif arac ve suruculer icin liste cek
const loadOptions = async () => {
  try {
    const [vehRes, drvRes] = await Promise.all([
      $fetchApi('/vehicles', { query: { 'status[]': 'active' } }),
      $fetchApi('/drivers', { query: { 'status[]': 'active' } }),
    ]);

    vehicles.value = vehRes.data || [];
    drivers.value = drvRes.data || [];
  } catch (err) {
    console.error('Options load error', err);
  }
};

// datetime string'i input icin 'YYYY-MM-DDThh:mm' formatina cevir
const toInputDateTime = (value) => {
  if (!value) return '';
  try {
    return new Date(value).toISOString().slice(0, 16);
  } catch {
    return '';
  }
};

watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      form.value = {
        id: val.id,
        vehicle_id: val.vehicle_id || '',
        driver_id: val.driver_id || '',
        start_time: toInputDateTime(val.start_time),
        end_time: toInputDateTime(val.end_time),
        origin: val.origin || '',
        destination: val.destination || '',
        status: val.status || 'planned',
        notes: val.notes || '',
      };
    } else {
      form.value = {
        id: null,
        vehicle_id: '',
        driver_id: '',
        start_time: '',
        end_time: '',
        origin: '',
        destination: '',
        status: 'planned',
        notes: '',
      };
    }

    // her acilis'ta select opsiyonlarini tazele
    loadOptions();
  },
  { immediate: true }
);

const onSubmit = async (e) => {
  e.preventDefault();
  loading.value = true;
  error.value = null;

  try {
    const payload = {
      vehicle_id: form.value.vehicle_id,
      driver_id: form.value.driver_id,
      start_time: form.value.start_time || null,
      end_time: form.value.end_time || null,
      origin: form.value.origin || '',
      destination: form.value.destination || '',
      status: form.value.status || 'planned',
      notes: form.value.notes || null,
    };

    if (form.value.id) {
      await $fetchApi(`/trips/${form.value.id}`, {
        method: 'PUT',
        body: payload,
      });
    } else {
      await $fetchApi('/trips', {
        method: 'POST',
        body: payload,
      });
    }

    emit('saved');
  } catch (err) {
    console.error(err);
    error.value = err?.data?.message || 'An error occured while saving.';
  } finally {
    loading.value = false;
  }
};

const onCancel = () => {
  emit('cancel');
};
</script>

<template>
  <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
    <div class="w-full max-w-3xl bg-slate-900 border border-slate-700 rounded-xl p-6 shadow-xl">
      <h2 class="text-lg font-semibold mb-4">
        {{ form.id ? 'Edit Trip' : 'Create New Trip' }}
      </h2>

      <form @submit="onSubmit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-slate-300 mb-1">Vehicle</label>
            <select
              v-model="form.vehicle_id"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              required
            >
              <option value="" disabled>Select</option>
              <option
                v-for="v in vehicles"
                :key="v.id"
                :value="v.id"
              >
                {{ v.plate }} ({{ v.brand }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Driver</label>
            <select
              v-model="form.driver_id"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              required
            >
              <option value="" disabled>Select</option>
              <option
                v-for="d in drivers"
                :key="d.id"
                :value="d.id"
              >
                {{ d.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Start Time</label>
            <input
              v-model="form.start_time"
              type="datetime-local"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">End Time</label>
            <input
              v-model="form.end_time"
              type="datetime-local"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Origin</label>
            <input
              v-model="form.origin"
              type="text"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Destination</label>
            <input
              v-model="form.destination"
              type="text"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm text-slate-300 mb-1">Status</label>
            <select
              v-model="form.status"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option value="planned">Planned</option>
              <option value="ongoing">Ongoing</option>
              <option value="completed">Completed</option>
              <option value="canceled">Canceled</option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm text-slate-300 mb-1">Notes</label>
            <textarea
              v-model="form.notes"
              rows="3"
              class="w-full px-3 py-2 rounded bg-slate-800 border border-slate-600 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
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
            {{ loading ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
