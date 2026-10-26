<script setup>
const { $fetchApi } = useApi();

definePageMeta({
  title: 'Dashboard',
});

// Dashboard summary
const {
  data: summary,
  pending: summaryPending,
  error: summaryError,
  refresh: refreshSummary,
} = await useAsyncData(
  'dashboard-summary',
  () => $fetchApi('/dashboard/summary'),
  {
    server: false,
  }
);

// Recent trips
const {
  data: recentTripsResponse,
  pending: tripsPending,
  error: tripsError,
  refresh: refreshTrips,
} = await useAsyncData(
  'recent-trips',
  () => $fetchApi('/trips', { query: { page: 1 } }),
  {
    server: false,
  }
);

// Dashboard metrics
const metrics = computed(() => [
  {
    label: 'Total Vehicles',
    value: summary.value?.totalVehicles ?? '—',
    description: 'Vehicles in your fleet',
    icon: 'vehicles',
    color: 'indigo',
  },
  {
    label: 'Active Trips',
    value: summary.value?.activeTrips ?? '—',
    description: 'Trips currently in progress',
    icon: 'trips',
    color: 'emerald',
  },
  {
    label: 'Vehicles In Service',
    value: summary.value?.vehiclesInService ?? '—',
    description: 'Vehicles under maintenance',
    icon: 'service',
    color: 'amber',
  },
  {
    label: 'Trips Today',
    value: summary.value?.tripsToday ?? '—',
    description: 'Trips started today',
    icon: 'calendar',
    color: 'sky',
  },
]);

const metricColors = {
  indigo: 'bg-indigo-500/10 text-indigo-400 ring-indigo-500/20',
  emerald: 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
  amber: 'bg-amber-500/10 text-amber-400 ring-amber-500/20',
  sky: 'bg-sky-500/10 text-sky-400 ring-sky-500/20',
};

// Show only the first five trips from the first API page
const recentTrips = computed(() =>
  (recentTripsResponse.value?.data ?? []).slice(0, 5)
);

const statusConfig = {
  planned: {
    label: 'Planned',
    classes: 'bg-amber-500/10 text-amber-400 ring-amber-500/20',
  },
  ongoing: {
    label: 'Ongoing',
    classes: 'bg-sky-500/10 text-sky-400 ring-sky-500/20',
  },
  completed: {
    label: 'Completed',
    classes: 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
  },
  canceled: {
    label: 'Canceled',
    classes: 'bg-red-500/10 text-red-400 ring-red-500/20',
  },
};

const getStatus = (status) =>
  statusConfig[status] ?? {
    label: status ?? 'Unknown',
    classes: 'bg-slate-500/10 text-slate-400 ring-slate-500/20',
  };

const formatDate = (date) => {
  if (!date) return '—';

  const parsedDate = new Date(date);

  if (Number.isNaN(parsedDate.getTime())) return '—';

  return new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(parsedDate);
};

const refreshDashboard = async () => {
  await Promise.all([refreshSummary(), refreshTrips()]);
};
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-slate-100">
    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">

      <!-- Page Header -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-white">
            Dashboard
          </h1>

          <p class="mt-2 text-sm text-slate-400">
            Monitor your fleet operations at a glance.
          </p>
        </div>

        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-700 bg-slate-800/50 px-4 py-2.5 text-sm font-medium text-slate-300 transition-colors hover:border-slate-600 hover:bg-slate-800 hover:text-white"
          @click="refreshDashboard"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M20 11a8 8 0 0 0-14.9-4M4 4v4h4" />
            <path d="M4 13a8 8 0 0 0 14.9 4M20 20v-4h-4" />
          </svg>

          Refresh
        </button>
      </div>

      <!-- Summary Error -->
      <div
        v-if="summaryError"
        class="rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-300"
      >
        Unable to load fleet statistics.
      </div>

      <!-- Metric Cards -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div
          v-for="metric in metrics"
          :key="metric.label"
          class="group rounded-xl border border-slate-700/70 bg-slate-800/40 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-600 hover:bg-slate-800/60"
        >
          <div class="flex items-start justify-between gap-3">
            <p class="text-sm font-medium text-slate-400">
              {{ metric.label }}
            </p>

            <!-- Metric Icon -->
            <div
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ring-1 ring-inset"
              :class="metricColors[metric.color]"
            >
              <svg
                v-if="metric.icon === 'vehicles'"
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M5 17h14l1-7-3-4H7l-3 4 1 7Z" />
                <path d="M5 17v2M19 17v2M4 10h16" />
                <circle cx="8" cy="15" r="1" />
                <circle cx="16" cy="15" r="1" />
              </svg>

              <svg
                v-else-if="metric.icon === 'trips'"
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="6" cy="18" r="2" />
                <circle cx="18" cy="6" r="2" />
                <path d="M8 18h5a5 5 0 0 0 5-5V8" />
                <path d="m15 10 3-3 3 3" />
              </svg>

              <svg
                v-else-if="metric.icon === 'service'"
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 15.6a2 2 0 0 0 2.8 2.8l9.3-6.3a4 4 0 0 0 5.4-5.4l-3.4 3.4-3.4-3.4Z" />
              </svg>

              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="3" y="5" width="18" height="16" rx="2" />
                <path d="M7 3v4M17 3v4M3 10h18" />
              </svg>
            </div>
          </div>

          <div class="mt-4">
            <p class="text-3xl font-bold tracking-tight text-white">
              {{ summaryPending ? '—' : metric.value }}
            </p>

            <p class="mt-2 text-xs text-slate-500">
              {{ metric.description }}
            </p>
          </div>
        </div>
      </div>

      <!-- Recent Trips -->
      <section class="space-y-5">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-lg font-semibold text-white">
              Recent Trips
            </h2>

            <p class="mt-1 text-sm text-slate-400">
              Latest activity across your fleet.
            </p>
          </div>

          <NuxtLink
            to="/trips"
            class="inline-flex items-center gap-2 text-sm font-medium text-indigo-400 transition-colors hover:text-indigo-300"
          >
            View all trips
            <span aria-hidden="true">→</span>
          </NuxtLink>
        </div>

        <div
          v-if="tripsPending"
          class="rounded-xl border border-slate-700/70 bg-slate-800/40 px-6 py-16 text-center text-sm text-slate-400"
        >
          Loading recent trips...
        </div>

        <div
          v-else-if="tripsError"
          class="rounded-xl border border-red-500/20 bg-red-500/10 px-6 py-6 text-sm text-red-300"
        >
          Unable to load recent trips.
        </div>

        <div
          v-else
          class="overflow-hidden rounded-xl border border-slate-700/70 bg-slate-800/40 shadow-sm"
        >
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-800/70">
                <tr class="border-b border-slate-700/70">
                  <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Vehicle
                  </th>

                  <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Driver
                  </th>

                  <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Start Time
                  </th>

                  <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Status
                  </th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-700/50">
                <tr
                  v-for="trip in recentTrips"
                  :key="trip.id"
                  class="transition-colors hover:bg-slate-700/30"
                >
                  <td class="whitespace-nowrap px-6 py-4">
                    <p class="font-semibold text-white">
                      {{ trip.vehicle?.plate ?? '—' }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                      {{ trip.vehicle?.brand ?? 'Unknown vehicle' }}
                    </p>
                  </td>

                  <td class="px-6 py-4 font-medium text-slate-200">
                    {{ trip.driver?.name ?? '—' }}
                  </td>

                  <td class="whitespace-nowrap px-6 py-4 text-slate-300">
                    {{ formatDate(trip.start_time) }}
                  </td>

                  <td class="whitespace-nowrap px-6 py-4">
                    <span
                      class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                      :class="getStatus(trip.status).classes"
                    >
                      <span class="mr-2 h-1.5 w-1.5 rounded-full bg-current"></span>
                      {{ getStatus(trip.status).label }}
                    </span>
                  </td>
                </tr>

                <tr v-if="recentTrips.length === 0">
                  <td
                    colspan="4"
                    class="px-6 py-16 text-center text-sm text-slate-400"
                  >
                    No trips found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </div>
  </div>
</template>