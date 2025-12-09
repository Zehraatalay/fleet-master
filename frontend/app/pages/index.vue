<script setup lang="ts">
const { $fetchApi } = useApi();

/**
 * Backend'in /api/dashboard/summary endpoint'inin döndürdüğü tip
 */
interface DashboardSummary {
  totalVehicles: number;
  activeTrips: number;
  vehiclesInService: number;
  tripsToday: number;
}

/**
 * Trip list icin basic typler 
 */
interface TripVehicle {
  plate: string;
  brand: string;
}

interface TripDriver {
  name: string;
}

interface Trip {
  id: number;
  start_time: string;
  status: string;
  vehicle?: TripVehicle;
  driver?: TripDriver;
}

interface PaginatedTrips {
  data: Trip[];
}

const {
  data: summary,
  pending: summaryPending,
  error: summaryError,
} = await useAsyncData<DashboardSummary>('dashboard-summary', () =>
  $fetchApi<DashboardSummary>('/dashboard/summary')
);

const {
  data: recentTrips,
  pending: tripsPending,
  error: tripsError,
} = await useAsyncData<PaginatedTrips>('recent-trips', () =>
  $fetchApi<PaginatedTrips>('/trips', { query: { page: 1 } })
);
</script>

<template>
  <div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <!-- Kartlar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <!-- Toplam araç -->
      <div class="rounded-lg border p-4">
        <p class="text-sm text-gray-500">Toplam Araç</p>
        <p class="text-2xl font-semibold">
          {{ summary?.totalVehicles ?? '-' }}
        </p>
      </div>

      <!-- Aktif sefer -->
      <div class="rounded-lg border p-4">
        <p class="text-sm text-gray-500">Aktif Sefer</p>
        <p class="text-2xl font-semibold">
          {{ summary?.activeTrips ?? '-' }}
        </p>
      </div>

      <!-- Servisteki araçlar -->
      <div class="rounded-lg border p-4">
        <p class="text-sm text-gray-500">Servisteki Araçlar</p>
        <p class="text-2xl font-semibold">
          {{ summary?.vehiclesInService ?? '-' }}
        </p>
      </div>

      <!-- Bugün başlayan seferler -->
      <div class="rounded-lg border p-4">
        <p class="text-sm text-gray-500">Bugün Başlayan Seferler</p>
        <p class="text-2xl font-semibold">
          {{ summary?.tripsToday ?? '-' }}
        </p>
      </div>
    </div>

    <!-- Son seferler -->
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

    <!-- Yükleniyor / hata -->
    <div v-if="summaryPending || tripsPending" class="text-sm text-gray-500">
      Yükleniyor...
    </div>

    <div v-if="summaryError || tripsError" class="text-sm text-red-500">
      Dashboard verileri alınırken hata oluştu.
    </div>
  </div>
</template>
