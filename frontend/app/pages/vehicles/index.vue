<script setup>
const { $fetchApi } = useApi();

definePageMeta({
  title: 'Vehicles',
});

// state filter icin 
const statusFilter = ref('all');

// form modal icin state
const showForm = ref(false);          // form acik mi kapali mi
const editingVehicle = ref(null);     // null ise yeni kayit, obje ise edit

const fetchVehicles = () => { // /api/vehicles endpointinden list cekiyoruz
  const query = {};

  if (statusFilter.value !== 'all') {
    // backend'de status filter whereIn ile calisiyor
    // burada tek bir value gonderiyoruz 
    query['status[]'] = statusFilter.value;
  }

  return $fetchApi('/vehicles', { query });
};

const {
  data: vehiclesResponse,
  pending,
  error,
  refresh,
} = await useAsyncData('vehicles-list', fetchVehicles);

// filter degisince listeyi yeniden cek 
watch(statusFilter, () => {
  refresh();
});

// "+ New Record" butonuna basinca calisir
const openCreate = () => {
  editingVehicle.value = null; // new record 
  showForm.value = true;
};

// tablo satirindaki "Edit" butonu basildiginda
const openEdit = (vehicle) => {
  editingVehicle.value = vehicle; // edit edilecek kayit
  showForm.value = true;
};

// form kaydedildiginde
const handleSaved = () => {
  showForm.value = false;
  refresh(); // listeyi yeniden cek
};

// form iptal edildiginde
const handleCancel = () => {
  showForm.value = false;
};

// tablo satirindaki "Delete" butonu basildiginda
const handleDelete = async (vehicle) => {
  if (!confirm(`Do you want to delete the vehicle with the license plate "${vehicle.plate}"?`)) {
    return;
  }

  try {
    await $fetchApi(`/vehicles/${vehicle.id}`, {
      method: 'DELETE',
    });
    refresh();
  } catch (err) {
    console.error(err);
    alert('An error occurred while deleting');
  }
};
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-slate-100">
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-10 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Vehicles</h1>
        <button
          type="button"
          class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium text-white"
          @click="openCreate"
        >
          + New Vehicle
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
        </button> <!-- backendde VehicleController@index icinde status array destekledigimiz icin queryde status[]=active gibi gonderiyoruz -->
        <button
          class="px-3 py-1 rounded-full border text-xs"
          :class="statusFilter === 'active' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
          @click="statusFilter = 'active'"
        >
          Active
        </button>
        <button
          class="px-3 py-1 rounded-full border text-xs"
          :class="statusFilter === 'in_service' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
          @click="statusFilter = 'in_service'"
        >
          In Service
        </button>
        <button
          class="px-3 py-1 rounded-full border text-xs"
          :class="statusFilter === 'inactive' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
          @click="statusFilter = 'inactive'"
        >
          Inactive
        </button>
      </div>

      <!-- Vehicles Table -->
      <VehicleTable
        :vehicles="vehiclesResponse?.data || []"
        @edit="openEdit"
        @delete="handleDelete"
      />

      <!-- State Messages -->
      <div v-if="pending" class="text-sm text-slate-400">
        Loading...
      </div>

      <div v-if="error" class="text-sm text-red-400">
        An error occurred while retrieving the vehicle list
      </div>

      <!-- Vehicle Form Modal (create + edit) -->
      <VehicleForm
        v-if="showForm"
        :model-value="editingVehicle"
        @saved="handleSaved"
        @cancel="handleCancel"
      />
    </div>
  </div>
</template>
