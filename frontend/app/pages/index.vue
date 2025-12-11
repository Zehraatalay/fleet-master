<script setup>
const { $fetchApi } = useApi();

// dashboard summary data 
const {
  data: summary,
  pending: summaryPending,
  error: summaryError,
} = await useAsyncData('dashboard-summary', () =>
  $fetchApi('/dashboard/summary')
);

// recent trips 
const {
  data: recentTrips,
  pending: tripsPending,
  error: tripsError,
} = await useAsyncData('recent-trips', () =>
  $fetchApi('/trips', { query: { page: 1 } })
);

// dashboard page icin meta title
definePageMeta({
  title: 'Dashboard',
});
</script>

<template>
  <div class="space-y-6">

    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <!-- summary cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      
      <!-- total vehicles -->
      <div class="rounded-lg border p-4">
        <p class="text-sm text-gray-500">Toplam Araç</p>
        <p class="text-2xl font-semibold">
          {{ summary?.totalVehicles ?? '-' }}
        </p>
      </div>

      <!-- active trips -->
      <div class="rounded-lg border p-4">
        <p class="text-sm text-gray-500">Aktif Sefer</p>
        <p class="text-2xl font-semibold">
          {{ summary?.activeTrips ?? '-' }}
        </p>
      </div>

      <!-- vehicles in service -->
      <div class="rounded-lg border p-4">
        <p class="text-sm text-gray-500">Servisteki Araçlar</p>
        <p class="text-2xl font-semibold">
          {{ summary?.vehiclesInService ?? '-' }}
        </p>
      </div>

      <!-- trips today -->
      <div class="rounded-lg border p-4">
        <p class="text-sm text-gray-500">Bugün Başlayan Seferler</p>
        <p class="text-2xl font-semibold">
          {{ summary?.tripsToday ?? '-' }}
        </p>
      </div>
    </div>

    <!-- recent trips -->
    <div>
      <h2 class="text-xl font-semibold mb-2">Son Seferler</h2>

      <div class="overflow-x-auto border rounded-lg">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left">Araç</th>
              <th class="px-4 py-2 text-left">Sürücü</th>
              <th class="px-4 py-2 text-left">Başlangıç</th>
              <th class="px-4 py-2 text-left">Durum</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="trip in recentTrips?.data || []"
              :key="trip.id"
              class="border-t"
            >
              <td class="px-4 py-2">
                {{ trip.vehicle?.plate }} ({{ trip.vehicle?.brand }})
              </td>
              <td class="px-4 py-2">
                {{ trip.driver?.name }}
              </td>
              <td class="px-4 py-2">
                {{ new Date(trip.start_time).toLocaleString() }}
              </td>
              <td class="px-4 py-2">
                {{ trip.status }}
              </td>
            </tr>

            <tr v-if="!recentTrips || (recentTrips.data || []).length === 0">
              <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                Henüz sefer yok.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- loading / error -->
    <div v-if="summaryPending || tripsPending">
      Loading...
    </div>

    <div v-if="summaryError || tripsError" class="text-red-500">
      Dashboard verileri alınırken hata oluştu.
    </div>

  </div>
</template>
