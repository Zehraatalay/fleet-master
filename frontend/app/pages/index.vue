<script setup>
const { $fetchApi } = useApi();

// Dashboard summary 
const { //composable cagriliyor 
  data: summary, // API'den gelen data (total vehicles, activeTrips ...)
  pending: summaryPending, // yukleniyor mu 
  error: summaryError, // error var mi 
} = await useAsyncData('dashboard-summary', () =>
  $fetchApi('/dashboard/summary')
); //Nuxt server-side client-side data fetching helper 
  // first parameter : cache key -> dashboard-summary 
  // second parameter : datayi nasil cekerim 

// Recent trips 
const {
  data: recentTrips,
  pending: tripsPending,
  error: tripsError,
} = await useAsyncData('recent-trips', () =>
  $fetchApi('/trips', { query: { page: 1 } })
);
</script>



<template>
  <!-- full-screen arka plan -->
  <div class="min-h-screen bg-slate-900 text-slate-100">
    <!-- ortalanmis container -->
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-10 space-y-6">
      <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

      <!-- Cards --> 
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total vehicles-->
        <div class="rounded-lg border p-4"> <!-- koseleri yuvarlak cerceveli paddingli -->
          <p class="text-sm text-gray-500"> Toplam Araç</p> <!-- kucuk gri label -->
          <p class="text-2xl font-semibold"> <!-- buyuk sayi -->
            {{ summary?.totalVehicles ?? '-' }}
          </p>
        </div>

        <!-- Active Trips -->
        <div class="rounded-lg border p-4">
          <p class="text-sm text-gray-500">Aktif Sefer</p>
          <p class="text-2xl font-semibold">
            {{ summary?.activeTrips ?? '-' }}
          </p>
        </div>

        <!-- Vehicles in Services -->
        <div class="rounded-lg border p-4">
          <p class="text-sm text-gray-500">Servisteki Araçlar</p>
          <p class="text-2xl font-semibold">
            {{ summary?.vehiclesInService ?? '-' }}
          </p>
        </div>

        <!-- Trips Today -->
        <div class="rounded-lg border p-4">
          <p class="text-sm text-gray-500">Bugün Başlayan Seferler</p>
          <p class="text-2xl font-semibold">
            {{ summary?.tripsToday ?? '-' }}
          </p>
        </div>
      </div>

      <!-- Recent Trips -->
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

      <!-- Loading... / Error -->
      <div v-if="summaryPending || tripsPending" class="text-sm text-gray-500">
        Yükleniyor...
      </div>

      <div v-if="summaryError || tripsError" class="text-sm text-red-500">
        Dashboard verileri alınırken hata oluştu.
      </div>
    </div>
  </div>
</template>
