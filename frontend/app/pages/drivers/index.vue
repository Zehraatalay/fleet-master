<script setup>
import DriverTable from '~/components/DriverTable.vue';
import DriverForm from '~/components/DriverForm.vue';
import ConfirmDialog from '~/components/ConfirmDialog.vue';

const { $fetchApi } = useApi();

definePageMeta({
  title: 'Drivers',
});

// Filters
const filters = [
  { label: 'All Drivers', value: 'all' },
  { label: 'Active', value: 'active' },
  { label: 'Inactive', value: 'inactive' },
];

const statusFilter = ref('all');

// Form state
const showForm = ref(false);
const editingDriver = ref(null);

// Delete confirmation state
const showDeleteConfirm = ref(false);
const driverToDelete = ref(null);

const deleteMessage = computed(() =>
  driverToDelete.value
    ? `Do you want to delete the driver "${driverToDelete.value.name}"?`
    : ''
);

// API
const fetchDrivers = () => {
  const query = {};

  if (statusFilter.value !== 'all') {
    query['status[]'] = statusFilter.value;
  }

  return $fetchApi('/drivers', { query });
};

const {
  data: driversResponse,
  pending,
  error,
  refresh,
} = await useAsyncData('drivers-list', fetchDrivers, {
  server: false,
  default: () => ({ data: [], total: 0 }),
});

// Refresh when filter changes
watch(statusFilter, () => {
  refresh();
});

// Derived data
const drivers = computed(() => driversResponse.value?.data ?? []);

const totalDrivers = computed(() => driversResponse.value?.total ?? 0);

// Form actions
const openCreate = () => {
  editingDriver.value = null;
  showForm.value = true;
};

const openEdit = (driver) => {
  editingDriver.value = driver;
  showForm.value = true;
};

const handleSaved = async () => {
  showForm.value = false;
  await refresh();
};

const handleCancel = () => {
  showForm.value = false;
};

// Delete actions
const handleDelete = (driver) => {
  driverToDelete.value = driver;
  showDeleteConfirm.value = true;
};

const cancelDelete = () => {
  showDeleteConfirm.value = false;
  driverToDelete.value = null;
};

const confirmDelete = async () => {
  if (!driverToDelete.value) return;

  try {
    await $fetchApi(`/drivers/${driverToDelete.value.id}`, {
      method: 'DELETE',
    });

    await refresh();
  } catch (err) {
    console.error(err);
    alert('An error occurred while deleting the driver.');
  } finally {
    showDeleteConfirm.value = false;
    driverToDelete.value = null;
  }
};
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-slate-100">
    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">

      <!-- Page Header -->
      <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-white">
            Drivers
          </h1>

          <p class="mt-2 text-sm text-slate-400">
            Manage your drivers and monitor their availability.
          </p>
        </div>

        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-950/30 transition-all hover:bg-indigo-500"
          @click="openCreate"
        >
          <span class="text-lg leading-none">+</span>
          Add Driver
        </button>
      </div>

      <!-- Content -->
      <div class="space-y-5">

        <!-- List Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-lg font-semibold text-slate-100">
              Drivers Overview
            </h2>

            <p class="mt-1 text-sm text-slate-400">
              {{ totalDrivers }} drivers found
            </p>
          </div>

          <!-- Filters -->
          <div class="flex flex-wrap items-center gap-2">
            <button
              v-for="filter in filters"
              :key="filter.value"
              type="button"
              class="rounded-lg border px-3.5 py-2 text-xs font-medium transition-colors"
              :class="
                statusFilter === filter.value
                  ? 'border-indigo-500/40 bg-indigo-500/15 text-indigo-300'
                  : 'border-slate-700 bg-slate-800/40 text-slate-400 hover:border-slate-600 hover:text-slate-200'
              "
              @click="statusFilter = filter.value"
            >
              {{ filter.label }}
            </button>
          </div>
        </div>

        <!-- Loading -->
        <div
          v-if="pending"
          class="rounded-xl border border-slate-700/70 bg-slate-800/40 px-6 py-16 text-center"
        >
          <p class="text-sm text-slate-400">
            Loading drivers...
          </p>
        </div>

        <!-- Error -->
        <div
          v-else-if="error"
          class="rounded-xl border border-red-500/20 bg-red-500/10 px-6 py-6"
        >
          <p class="text-sm font-medium text-red-300">
            Unable to load drivers.
          </p>

          <button
            type="button"
            class="mt-3 text-xs font-semibold text-red-200 underline"
            @click="refresh()"
          >
            Try again
          </button>
        </div>

        <!-- Drivers Table -->
        <DriverTable
          v-else
          :drivers="drivers"
          @edit="openEdit"
          @delete="handleDelete"
        />
      </div>

      <!-- Driver Form Modal -->
      <DriverForm
        v-if="showForm"
        :model-value="editingDriver"
        @saved="handleSaved"
        @cancel="handleCancel"
      />

      <!-- Delete Confirmation -->
      <ConfirmDialog
        :show="showDeleteConfirm"
        title="Delete Driver"
        :message="deleteMessage"
        confirm-label="Delete"
        cancel-label="Cancel"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
      />

    </div>
  </div>
</template>