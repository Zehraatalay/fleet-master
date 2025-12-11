<script setup>
import DriverTable from '~/components/DriverTable.vue';
import DriverForm from '~/components/DriverForm.vue';
import ConfirmDialog from '~/components/ConfirmDialog.vue';

const { $fetchApi } = useApi();

definePageMeta({
  title: 'Drivers',
});

// status filter icin 
const statusFilter = ref('all');

// form modal icin state
const showForm = ref(false);          // form acik mi kapali mi
const editingDriver = ref(null);      // null ise yeni kayit obje ise edit

// silme onayi icin state
const showDeleteConfirm = ref(false); // confirm popup acik mi
const driverToDelete = ref(null);     // hangi surucuyu silecegiz

// confirm dialog icin mesaj (template literal burada, template icinde degil)
const deleteMessage = computed(() =>
  driverToDelete.value
    ? `Do you want to delete the driver "${driverToDelete.value.name}"?`
    : ''
);

const fetchDrivers = () => {
  const query = {};

  if (statusFilter.value !== 'all') {
    // backend'de DriverController@index icinde status array destekledigimiz icin
    // burada status[]=active gibi gonderiyoruz
    query['status[]'] = statusFilter.value;
  }

  return $fetchApi('/drivers', { query });
};

const {
  data: driversResponse,
  pending,
  error,
  refresh,
} = await useAsyncData('drivers-list', fetchDrivers);

// filter degisince listeyi yeniden cek
watch(statusFilter, () => {
  refresh();
});

// "+ New Driver" butonuna basinca
const openCreate = () => {
  editingDriver.value = null; // yeni kayit
  showForm.value = true;
};

// tablo satirindaki "Edit" butonuna basinca
const openEdit = (driver) => {
  editingDriver.value = driver;
  showForm.value = true;
};

// form kaydedildiginde
const handleSaved = () => {
  showForm.value = false;
  refresh();
};

// form iptal edildiginde
const handleCancel = () => {
  showForm.value = false;
};

// tablo satirindaki "Delete" butonuna basinca
const handleDelete = (driver) => {
  driverToDelete.value = driver;
  showDeleteConfirm.value = true;
};

// confirm popup'inda "Cancel" basilinca
const cancelDelete = () => {
  showDeleteConfirm.value = false;
  driverToDelete.value = null;
};

// confirm popup'inda "Delete" basilinca
const confirmDelete = async () => {
  if (!driverToDelete.value) return;

  try {
    await $fetchApi(`/drivers/${driverToDelete.value.id}`, {
      method: 'DELETE',
    });
    refresh();
  } catch (err) {
    console.error(err);
    alert('An error occured during deletion');
  } finally {
    showDeleteConfirm.value = false;
    driverToDelete.value = null;
  }
};
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Drivers</h1>

      <button
        type="button"
        class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium text-white"
        @click="openCreate"
      >
        + New Driver 
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2">
      <button
        class="px-3 py-1 rounded-full border text-xs"
        :class="statusFilter === 'all' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
        @click="statusFilter = 'all'"
      >
        All
      </button>
      <button
        class="px-3 py-1 rounded-full border text-xs"
        :class="statusFilter === 'active' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
        @click="statusFilter = 'active'"
      >
        Active
      </button>
      <button
        class="px-3 py-1 rounded-full border text-xs"
        :class="statusFilter === 'inactive' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
        @click="statusFilter = 'inactive'"
      >
        Inactive
      </button>
    </div>

    <!-- Drivers Table -->
    <DriverTable
      :drivers="driversResponse?.data || []"
      @edit="openEdit"
      @delete="handleDelete"
    />

    <!-- State Messages -->
    <div v-if="pending" class="text-sm text-slate-400">
      Loading drivers...
    </div>

    <div v-if="error" class="text-sm text-red-400">
      An error occurred while retrieving the driver list
    </div>

    <!-- Driver Form Modal -->
    <DriverForm
      v-if="showForm"
      :model-value="editingDriver"
      @saved="handleSaved"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
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
</template>
