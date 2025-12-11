<script setup>
import TripTable from '~/components/TripTable.vue';
import TripForm from '~/components/TripForm.vue';
import ConfirmDialog from '~/components/ConfirmDialog.vue';

const { $fetchApi } = useApi();

definePageMeta({
  title: 'Trips',
});

// status filter
const statusFilter = ref('all');

// form modal state
const showForm = ref(false);
const editingTrip = ref(null);

// confirm delete icin 
const showDeleteConfirm = ref(false);
const tripToDelete = ref(null);

const deleteMessage = computed(() =>
  tripToDelete.value
    ? 'Do you want to delete this trip?'
    : ''
);

const fetchTrips = () => {
  const query = {};
  if (statusFilter.value !== 'all') {
    query['status[]'] = statusFilter.value;
  }
  return $fetchApi('/trips', { query });
};

const {
  data: tripsResponse,
  pending,
  error,
  refresh,
} = await useAsyncData('trips-list', fetchTrips);

// filter degisince listeyi yeniden cek
watch(statusFilter, () => {
  refresh();
});

const openCreate = () => {
  editingTrip.value = null;
  showForm.value = true;
};

const openEdit = (trip) => {
  editingTrip.value = trip;
  showForm.value = true;
};

const handleSaved = () => {
  showForm.value = false;
  refresh();
};

const handleCancel = () => {
  showForm.value = false;
};

const handleDelete = (trip) => {
  tripToDelete.value = trip;
  showDeleteConfirm.value = true;
};

const cancelDelete = () => {
  showDeleteConfirm.value = false;
  tripToDelete.value = null;
};

const confirmDelete = async () => {
  if (!tripToDelete.value) return;

  try {
    await $fetchApi(`/trips/${tripToDelete.value.id}`, {
      method: 'DELETE',
    });
    refresh();
  } catch (err) {
    console.error(err);
    alert('An error occured during deletion');
  } finally {
    showDeleteConfirm.value = false;
    tripToDelete.value = null;
  }
};
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Trips</h1>

      <button
        type="button"
        class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-medium text-white"
        @click="openCreate"
      >
        + New Trip
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-2">
      <button
        class="px-3 py-1 rounded-full border text-xs"
        :class="statusFilter === 'all' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
        @click="statusFilter = 'all'"
      >
        All
      </button>
      <button
        class="px-3 py-1 rounded-full border text-xs"
        :class="statusFilter === 'planned' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
        @click="statusFilter = 'planned'"
      >
        Planned
      </button>
      <button
        class="px-3 py-1 rounded-full border text-xs"
        :class="statusFilter === 'ongoing' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
        @click="statusFilter = 'ongoing'"
      >
        Ongoing
      </button>
      <button
        class="px-3 py-1 rounded-full border text-xs"
        :class="statusFilter === 'completed' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
        @click="statusFilter = 'completed'"
      >
        Completed
      </button>
      <button
        class="px-3 py-1 rounded-full border text-xs"
        :class="statusFilter === 'canceled' ? 'bg-slate-700 border-slate-500' : 'border-slate-600'"
        @click="statusFilter = 'canceled'"
      >
        Canceled
      </button>
    </div>

    <!-- Trips Table -->
    <TripTable
      :trips="tripsResponse?.data || []"
      @edit="openEdit"
      @delete="handleDelete"
    />

    <!-- State Messages -->
    <div v-if="pending" class="text-sm text-slate-400">
      Loading trips...
    </div>

    <div v-if="error" class="text-sm text-red-400">
      An error occurred while retrieving the trip list
    </div>

    <!-- Trip Form Modal -->
    <TripForm
      v-if="showForm"
      :model-value="editingTrip"
      @saved="handleSaved"
      @cancel="handleCancel"
    />

    <!-- Delete Confirm Dialog -->
    <ConfirmDialog
      :show="showDeleteConfirm"
      title="Delete Trip"
      :message="deleteMessage"
      confirm-label="Delete"
      cancel-label="Cancel"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>
