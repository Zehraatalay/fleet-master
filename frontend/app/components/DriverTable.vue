<script setup>
defineProps({
  drivers: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['edit', 'delete']);

// Driver status configuration
const statusConfig = {
  active: {
    label: 'Active',
    classes: 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
  },
  inactive: {
    label: 'Inactive',
    classes: 'bg-slate-500/10 text-slate-400 ring-slate-500/20',
  },
};

const getStatus = (status) =>
  statusConfig[status] ?? {
    label: status ?? 'Unknown',
    classes: 'bg-slate-500/10 text-slate-400 ring-slate-500/20',
  };

// Format Turkish mobile phone numbers for display
const formatPhone = (phone) => {
  if (!phone) return '—';

  const digits = String(phone).replace(/\D/g, '');

  if (digits.length === 11 && digits.startsWith('0')) {
    return `${digits.slice(0, 4)} ${digits.slice(4, 7)} ${digits.slice(7, 9)} ${digits.slice(9)}`;
  }

  return phone;
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
            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400"
            >
              Driver
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400"
            >
              Phone Number
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400"
            >
              License Type
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
            v-for="driver in drivers"
            :key="driver.id"
            class="transition-colors duration-150 hover:bg-slate-700/30"
          >
            <!-- Driver Name -->
            <td class="whitespace-nowrap px-6 py-4">
              <span class="font-semibold text-white">
                {{ driver.name }}
              </span>
            </td>

            <!-- Phone -->
            <td class="whitespace-nowrap px-6 py-4 text-slate-300">
              {{ formatPhone(driver.phone) }}
            </td>

            <!-- License Type -->
            <td class="whitespace-nowrap px-6 py-4">
              <span
                v-if="driver.license_type"
                class="inline-flex rounded-md border border-slate-600/70 bg-slate-700/40 px-2.5 py-1 text-xs font-semibold text-slate-300"
              >
                {{ driver.license_type }}
              </span>

              <span v-else class="text-slate-500">
                —
              </span>
            </td>

            <!-- Status -->
            <td class="whitespace-nowrap px-6 py-4">
              <span
                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset"
                :class="getStatus(driver.status).classes"
              >
                <span
                  class="mr-2 h-1.5 w-1.5 rounded-full bg-current"
                ></span>

                {{ getStatus(driver.status).label }}
              </span>
            </td>

            <!-- Actions -->
            <td class="whitespace-nowrap px-6 py-4 text-right">
              <div class="flex items-center justify-end gap-2">
                <button
                  type="button"
                  class="rounded-lg border border-slate-600 px-3 py-1.5 text-xs font-medium text-slate-200 transition-colors hover:border-slate-500 hover:bg-slate-700"
                  @click="emit('edit', driver)"
                >
                  Edit
                </button>

                <button
                  type="button"
                  class="rounded-lg border border-red-500/20 bg-red-500/10 px-3 py-1.5 text-xs font-medium text-red-400 transition-colors hover:bg-red-500/20"
                  @click="emit('delete', driver)"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="drivers.length === 0">
            <td colspan="5" class="px-6 py-16 text-center">
              <p class="text-sm font-medium text-slate-300">
                No drivers found
              </p>

              <p class="mt-1 text-xs text-slate-500">
                Try changing your filters or add a new driver.
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>