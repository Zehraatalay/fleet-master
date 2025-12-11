<script setup>
const props = defineProps({
  trips: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['edit', 'delete']);
</script>

<!-- 
trips prop'u olarak gelen listeyi tabloda gosterir
araç, surucu, baslangic, durum kolonlari + edit/sil
-->

<template>
  <div class="overflow-x-auto rounded-lg border border-slate-700 bg-slate-800/60">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-900/70 text-slate-300">
        <tr>
          <th class="px-4 py-2 text-left font-medium">Vehicle</th>
          <th class="px-4 py-2 text-left font-medium">Driver</th>
          <th class="px-4 py-2 text-left font-medium">Start Time</th>
          <th class="px-4 py-2 text-left font-medium">Status</th>
          <th class="px-4 py-2 text-right font-medium">Transactions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="trip in trips"
          :key="trip.id"
          class="border-t border-slate-700"
        >
          <td class="px-4 py-2">
            {{ trip.vehicle?.plate }} ({{ trip.vehicle?.brand }})
          </td>
          <td class="px-4 py-2">
            {{ trip.driver?.name }}
          </td>
          <td class="px-4 py-2">
            {{ trip.start_time ? new Date(trip.start_time).toLocaleString() : '-' }}
          </td>
          <td class="px-4 py-2">
            {{ trip.status }}
          </td>
          <td class="px-4 py-2 text-right space-x-2">
            <button
              type="button"
              class="px-2 py-1 text-xs rounded bg-slate-700 hover:bg-slate-600"
              @click="emit('edit', trip)"
            >
              Edit
            </button>
            <button
              type="button"
              class="px-2 py-1 text-xs rounded bg-red-600 hover:bg-red-500"
              @click="emit('delete', trip)"
            >
              Delete
            </button>
          </td>
        </tr>

        <tr v-if="trips.length === 0">
          <td colspan="5" class="px-4 py-6 text-center text-slate-400">
            There is no trip yet.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
