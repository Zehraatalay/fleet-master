<script setup>
const { $fetchApi } = useApi();


// state filter icin 
const statusFilter = ref('all');

const {
  data: vehiclesResponse,
  pending,
  error,
  refresh,
} = await useAsyncData('vehicles-list', () => { // /api/vehicles endpointinden list cekiyoruz
  const query = {};

  if (statusFilter.value !== 'all') {
    // backend'de status filter whereIn ile calisiyor
    // burada tek bir value gonderiyoruz 
    query['status[]'] = statusFilter.value;
  }

  return $fetchApi('/vehicles', { query });
});

// filter degisince listeyi yeniden cek 
watch(statusFilter, () => {
  refresh();
});
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-slate-100">
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-10 space-y-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Araçlar</h1>

        <!-- simdilik button bos sonra VehicleForm eklenecek -->
        <button
          type="button"
          class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium text-white"
        >
          + Yeni Araç
        </button>
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-2">
        <button
          class="px-3 py-1 rounded-full border text-xs"
          :class="statusFilter === 'all' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
          @click="statusFilter = 'all'"
        >
          Tümü
        </button> <!-- backendde VehicleController@index icinde status array destekledigimiz icin queryde status[]=active gibi gonderiyoruz -->
        <button
          class="px-3 py-1 rounded-full border text-xs"
          :class="statusFilter === 'active' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
          @click="statusFilter = 'active'"
        >
          Aktif
        </button>
        <button
          class="px-3 py-1 rounded-full border text-xs"
          :class="statusFilter === 'in_service' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
          @click="statusFilter = 'in_service'"
        >
          Serviste
        </button>
        <button
          class="px-3 py-1 rounded-full border text-xs"
          :class="statusFilter === 'inactive' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
          @click="statusFilter = 'inactive'"
        >
          Pasif
        </button>
      </div>

      <!-- Vehicles Table -->
      <VehicleTable :vehicles="vehiclesResponse?.data || []" />

      <!-- State Messages -->
      <div v-if="pending" class="text-sm text-slate-400">
        Araçlar yükleniyor...
      </div>

      <div v-if="error" class="text-sm text-red-400">
        Araç listesi alınırken bir hata oluştu.
      </div>
    </div>
  </div>
</template>
