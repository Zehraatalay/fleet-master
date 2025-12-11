<script setup>
const props = defineProps({
  drivers: {
    type: Array,
    default: () => [],
  },
});

// tablo satirlarindan edit/delete eventleri ile haber vermek icin
const emit = defineEmits(['edit', 'delete']);
</script>

<!-- 
drivers prop'u olarak gelen listeyi tabloda gosterir
Tailwind ile koyu tema + borderli gorunum
hic surucu yoksa 'Driver bulunamadı' yazar
-->

<template>
  <div class="overflow-x-auto rounded-lg border border-slate-700 bg-slate-800/60">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-900/70 text-slate-300">
        <tr>
          <th class="px-4 py-2 text-left font-medium">Name</th>
          <th class="px-4 py-2 text-left font-medium">Telephone Number</th>
          <th class="px-4 py-2 text-left font-medium">Licence Type</th>
          <th class="px-4 py-2 text-left font-medium">State</th>
          <th class="px-4 py-2 text-right font-medium">Transactions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="driver in drivers"
          :key="driver.id"
          class="border-t border-slate-700"
        >
          <td class="px-4 py-2">
            {{ driver.name }}
          </td>
          <td class="px-4 py-2">
            {{ driver.phone || '-' }}
          </td>
          <td class="px-4 py-2">
            {{ driver.license_type || '-' }}
          </td>
          <td class="px-4 py-2">
            {{ driver.status }}
          </td>
          <td class="px-4 py-2 text-right space-x-2">
            <button
              type="button"
              class="px-2 py-1 text-xs rounded bg-slate-700 hover:bg-slate-600"
              @click="emit('edit', driver)"
            >
              Edit
            </button>
            <button
              type="button"
              class="px-2 py-1 text-xs rounded bg-red-600 hover:bg-red-500"
              @click="emit('delete', driver)"
            >
              Delete
            </button>
          </td>
        </tr>

        <tr v-if="drivers.length === 0">
          <td colspan="5" class="px-4 py-6 text-center text-slate-400">
            Driver not found 
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
