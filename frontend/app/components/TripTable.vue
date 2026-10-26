<script setup>
defineProps({
  trips: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['edit', 'delete']);

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

// Backend'den gelen tarihi kullanıcıya okunabilir biçimde gösterir.
const formatDate = (value) => {
  if (!value) return '—';

  const date = new Date(value);

  if (Number.isNaN(date.getTime())) return '—';

  return new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  }).format(date);
};
</script>

<template>
  <div
    class="overflow-hidden rounded-xl border border-slate-700/70 bg-slate-800/40 shadow-sm"
  >
    <div class="overflow-x-auto">
      <table class="w-full min-w-[850px] text-sm">
        <thead class="bg-slate-800/70">
          <tr class="border-b border-slate-700/70">
            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400"
            >
              Vehicle
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400"
            >
              Driver
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400"
            >
              Start Time
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400"
            >
              Status
            </th>

            <th
              class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-400"
            >
              Actions
            </th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-700/50">
          <tr
            v-for="trip in trips"
            :key="trip.id"
            class="transition-colors duration-150 hover:bg-slate-700/30"
          >
            <!-- Vehicle -->
            <td class="px-6 py-4">
              <div class="flex flex-col gap-1">
                <span class="whitespace-nowrap font-semibold text-white">
                  {{ trip.vehicle?.plate ?? 'Unknown Vehicle' }}
                </span>

                <span class="text-xs text-slate-400">
                  {{ trip.vehicle?.brand ?? '—' }}
                </span>
              </div>
            </td>

            <!-- Driver -->
            <td class="px-6 py-4">
              <span class="font-medium text-slate-200">
                {{ trip.driver?.name ?? 'Unknown Driver' }}
              </span>
            </td>

            <!-- Start Time -->
            <td class="whitespace-nowrap px-6 py-4 text-slate-300">
              {{ formatDate(trip.start_time) }}
            </td>

            <!-- Status -->
            <td class="whitespace-nowrap px-6 py-4">
              <span
                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                :class="getStatus(trip.status).classes"
              >
                <span
                  class="mr-2 h-1.5 w-1.5 rounded-full bg-current"
                ></span>

                {{ getStatus(trip.status).label }}
              </span>
            </td>

            <!-- Actions -->
            <td class="whitespace-nowrap px-6 py-4 text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                  type="button"
                  class="rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-medium text-slate-200 transition-colors hover:border-slate-500 hover:bg-slate-700"
                  @click="emit('edit', trip)"
                >
                  Edit
                </button>

                <button
                  type="button"
                  class="rounded-lg border border-red-500/20 bg-red-500/10 px-3 py-1.5 text-xs font-medium text-red-400 transition-colors hover:bg-red-500/20"
                  @click="emit('delete', trip)"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="trips.length === 0">
            <td colspan="5" class="px-6 py-16 text-center">
              <p class="text-sm font-medium text-slate-300">
                No trips found
              </p>

              <p class="mt-1 text-xs text-slate-500">
                Try changing your filters or create a new trip.
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>