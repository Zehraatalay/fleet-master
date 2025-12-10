<script setup>
const props = defineProps({
  vehicles: {
    type: Array,
    default: () => [],
  },
});
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
          <th class="px-4 py-2 text-left font-medium">Plaka</th>
          <th class="px-4 py-2 text-left font-medium">Marka / Model</th>
          <th class="px-4 py-2 text-left font-medium">Yıl</th>
          <th class="px-4 py-2 text-left font-medium">KM</th>
          <th class="px-4 py-2 text-left font-medium">Durum</th>
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
        </tr>

        <tr v-if="vehicles.length === 0">
          <td
            colspan="5"
            class="px-4 py-6 text-center text-slate-400"
          >
            Araç bulunamadı.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
