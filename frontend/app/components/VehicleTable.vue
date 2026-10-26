<script setup>
defineProps({
  vehicles: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['edit', 'delete']);

const statusConfig = {
  active: {
    label: 'Active',
    classes: 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
  },
  in_service: {
    label: 'In Service',
    classes: 'bg-amber-500/10 text-amber-400 ring-amber-500/20',
  },
  inactive: {
    label: 'Inactive',
    classes: 'bg-slate-500/10 text-slate-400 ring-slate-500/20',
  },
};

const getStatus = (status) => {
  return statusConfig[status] ?? {
    label: status ?? 'Unknown',
    classes: 'bg-slate-500/10 text-slate-400 ring-slate-500/20',
  };
};

const formatKm = (km) => {
  if (km === null || km === undefined) return '—';

  return new Intl.NumberFormat('en-US').format(km);
};
</script>

<template>
  <div
    class="overflow-hidden rounded-xl border border-slate-700/70 bg-slate-800/40 shadow-sm"
  >
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-800/70">
          <tr class="border-b border-slate-700/70">
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
              Plate
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
              Brand / Model
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
              Year
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
              Mileage
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">
              Status
            </th>

            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-400">
              Actions
            </th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-700/50">
          <tr
            v-for="vehicle in vehicles"
            :key="vehicle.id"
            class="transition-colors duration-150 hover:bg-slate-700/30"
          >
            <!-- Plate -->
            <td class="whitespace-nowrap px-6 py-4">
              <span class="font-semibold text-white">
                {{ vehicle.plate }}
              </span>
            </td>

            <!-- Brand / Model -->
            <td class="px-6 py-4">
              <div class="font-medium text-slate-200">
                {{ vehicle.brand }}
              </div>

              <div class="mt-0.5 text-xs text-slate-400">
                {{ vehicle.model }}
              </div>
            </td>

            <!-- Year -->
            <td class="whitespace-nowrap px-6 py-4 text-slate-300">
              {{ vehicle.year ?? '—' }}
            </td>

            <!-- Mileage -->
            <td class="whitespace-nowrap px-6 py-4 text-slate-300">
              {{ formatKm(vehicle.current_km) }}
              <span class="text-xs text-slate-500">km</span>
            </td>

            <!-- Status -->
            <td class="whitespace-nowrap px-6 py-4">
              <span
                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                :class="getStatus(vehicle.status).classes"
              >
                <span
                  class="mr-2 h-1.5 w-1.5 rounded-full bg-current"
                ></span>

                {{ getStatus(vehicle.status).label }}
              </span>
            </td>

            <!-- Actions -->
            <td class="whitespace-nowrap px-6 py-4 text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                  type="button"
                  class="rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-medium text-slate-200 transition-colors hover:border-slate-500 hover:bg-slate-700"
                  @click="emit('edit', vehicle)"
                >
                  Edit
                </button>

                <button
                  type="button"
                  class="rounded-lg border border-red-500/20 bg-red-500/10 px-3 py-1.5 text-xs font-medium text-red-400 transition-colors hover:bg-red-500/20"
                  @click="emit('delete', vehicle)"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty state -->
          <tr v-if="vehicles.length === 0">
            <td colspan="6" class="px-6 py-16 text-center">
              <div class="text-sm font-medium text-slate-300">
                No vehicles found
              </div>

              <p class="mt-1 text-xs text-slate-500">
                Try changing your filters or add a new vehicle.
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>