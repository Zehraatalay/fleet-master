<script setup>
const props = defineProps({
  vehicles: {
    type: Array,
    default: () => [],
  },
});

// tablo satırlarından edit / delete event'leri ile haber vermek için:
const emit = defineEmits(['edit', 'delete']);
</script>

<!-- 
vehicles prop'u olarak gelen list'i tabloda gosteriyor
Tailwind ile koyu tema + borderlı gorunum
hic arac yoksa arac bulunmadi 
-->

<template>
  <div class="overflow-x-auto rounded-lg border border-slate-700 bg-slate-800/60">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-900/70 text-slate-300">
        <tr>
          <th class="px-4 py-2 text-left font-medium">Plate</th>
          <th class="px-4 py-2 text-left font-medium">Brand / Model</th>
          <th class="px-4 py-2 text-left font-medium">Year</th>
          <th class="px-4 py-2 text-left font-medium">KM</th>
          <th class="px-4 py-2 text-left font-medium">State</th>
          <th class="px-4 py-2 text-right font-medium">Transactions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="vehicle in vehicles"
          :key="vehicle.id"
          class="border-t border-slate-700"
        >
          <td class="px-4 py-2">
            {{ vehicle.plate }}
          </td>
          <td class="px-4 py-2">
            {{ vehicle.brand }} {{ vehicle.model }}
          </td>
          <td class="px-4 py-2">
            {{ vehicle.year ?? '-' }}
          </td>
          <td class="px-4 py-2">
            {{ vehicle.current_km }}
          </td>
          <td class="px-4 py-2">
            {{ vehicle.status }}
          </td>
          <td class="px-4 py-2 text-right space-x-2">
            <!-- edit butonu -->
            <button
              type="button"
              class="px-2 py-1 text-xs rounded bg-slate-700 hover:bg-slate-600"
              @click="emit('edit', vehicle)"
            >
              Edit
            </button>
            <!-- silme butonu -->
            <button
              type="button"
              class="px-2 py-1 text-xs rounded bg-red-600 hover:bg-red-500"
              @click="emit('delete', vehicle)"
            >
              Delete
            </button>
          </td>
        </tr>

        <tr v-if="vehicles.length === 0">
          <td
            colspan="6"
            class="px-4 py-6 text-center text-slate-400"
          >
            Vehicle not found
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
