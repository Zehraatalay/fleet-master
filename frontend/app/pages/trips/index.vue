<script setup>
import TripTable from '~/components/TripTable.vue';
import TripForm from '~/components/TripForm.vue';
import ConfirmDialog from '~/components/ConfirmDialog.vue';

const { $fetchApi } = useApi();

definePageMeta({
  title: 'Trips',
});

// Available status filters
const filters = [
  { label: 'All Trips', value: 'all' },
  { label: 'Planned', value: 'planned' },
  { label: 'Ongoing', value: 'ongoing' },
  { label: 'Completed', value: 'completed' },
  { label: 'Canceled', value: 'canceled' },
];

const statusFilter = ref('all');
const currentPage = ref(1);

// Form state
const showForm = ref(false);
const editingTrip = ref(null);

// Delete confirmation state
const showDeleteConfirm = ref(false);
const tripToDelete = ref(null);

const deleteMessage = computed(() =>
  tripToDelete.value
    ? 'Do you want to delete this trip?'
    : ''
);

// Fetch trips from backend
const fetchTrips = () => {
  const query = {
    page: currentPage.value,
  };

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
} = await useAsyncData('trips-list', fetchTrips, {
  server: false,
  default: () => ({
    data: [],
    total: 0,
    current_page: 1,
    last_page: 1,
    from: null,
    to: null,
  }),
});

// Refresh data when filter changes
watch(statusFilter, async () => {
  currentPage.value = 1;
  await refresh();
});

// Derived data
const trips = computed(() => tripsResponse.value?.data ?? []);

const totalTrips = computed(() => tripsResponse.value?.total ?? 0);

const lastPage = computed(() => tripsResponse.value?.last_page ?? 1);

const from = computed(() => tripsResponse.value?.from ?? 0);

const to = computed(() => tripsResponse.value?.to ?? 0);

// Pagination
const goToPage = async (page) => {
  if (
    page < 1 ||
    page > lastPage.value ||
    page === currentPage.value
  ) {
    return;
  }

  currentPage.value = page;
  await refresh();
};

// Form actions
const openCreate = () => {
  editingTrip.value = null;
  showForm.value = true;
};

const openEdit = (trip) => {
  editingTrip.value = trip;
  showForm.value = true;
};

const handleSaved = async () => {
  showForm.value = false;
  await refresh();
};

const handleCancel = () => {
  showForm.value = false;
};

// Delete actions
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

    // If the last item on a page was deleted,
    // return to the previous page.
    if (trips.value.length === 1 && currentPage.value > 1) {
      currentPage.value -= 1;
    }

    await refresh();
  } catch (err) {
    console.error(err);
    alert('An error occurred while deleting the trip.');
  } finally {
    showDeleteConfirm.value = false;
    tripToDelete.value = null;
  }
};
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-slate-100">
    <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">

      <!-- Page Header -->
      <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight text-white">
            Trips
          </h1>

          <p class="mt-2 text-sm text-slate-400">
            Manage trips and track your fleet's activity.
          </p>
        </div>

        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-950/30 transition-all hover:bg-indigo-500"
          @click="openCreate"
        >
          <span class="text-lg leading-none">+</span>
          Add Trip
        </button>
      </div>

      <!-- Trips Overview -->
      <div class="space-y-5">

        <!-- Section Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-lg font-semibold text-slate-100">
              Trips Overview
            </h2>

            <p class="mt-1 text-sm text-slate-400">
              {{ totalTrips }} trips found
            </p>
          </div>

          <!-- Filters -->
          <div class="flex flex-wrap items-center gap-2">
            <button
              v-for="filter in filters"
              :key="filter.value"
              type="button"
              class="rounded-lg border px-3.5 py-2 text-xs font-medium transition-colors"
              :class="
                statusFilter === filter.value
                  ? 'border-indigo-500/40 bg-indigo-500/15 text-indigo-300'
                  : 'border-slate-700 bg-slate-800/40 text-slate-400 hover:border-slate-600 hover:text-slate-200'
              "
              @click="statusFilter = filter.value"
            >
              {{ filter.label }}
            </button>
          </div>
        </div>

        <!-- Loading -->
        <div
          v-if="pending"
          class="rounded-xl border border-slate-700/70 bg-slate-800/40 px-6 py-16 text-center"
        >
          <p class="text-sm text-slate-400">
            Loading trips...
          </p>
        </div>

        <!-- Error -->
        <div
          v-else-if="error"
          class="rounded-xl border border-red-500/20 bg-red-500/10 px-6 py-6"
        >
          <p class="text-sm font-medium text-red-300">
            Unable to load trips.
          </p>

          <button
            type="button"
            class="mt-3 text-xs font-semibold text-red-200 underline"
            @click="refresh()"
          >
            Try again
          </button>
        </div>

        <!-- Trips Table + Pagination -->
        <template v-else>
          <TripTable
            :trips="trips"
            @edit="openEdit"
            @delete="handleDelete"
          />

          <!-- Pagination -->
          <div
            v-if="totalTrips > 0"
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
          >
            <!-- Showing X-Y of Z -->
            <p class="text-sm text-slate-400">
              Showing
              <span class="font-medium text-slate-200">
                {{ from }}
              </span>
              –
              <span class="font-medium text-slate-200">
                {{ to }}
              </span>
              of
              <span class="font-medium text-slate-200">
                {{ totalTrips }}
              </span>
              trips
            </p>

            <!-- Pagination Buttons -->
            <div class="flex items-center gap-3">
              <button
                type="button"
                :disabled="currentPage === 1"
                class="rounded-lg border border-slate-700 bg-slate-800/40 px-4 py-2 text-sm font-medium text-slate-300 transition-colors hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-40"
                @click="goToPage(currentPage - 1)"
              >
                Previous
              </button>

              <span class="text-sm text-slate-400">
                Page
                <span class="font-semibold text-slate-200">
                  {{ currentPage }}
                </span>
                of
                <span class="font-semibold text-slate-200">
                  {{ lastPage }}
                </span>
              </span>

              <button
                type="button"
                :disabled="currentPage === lastPage"
                class="rounded-lg border border-slate-700 bg-slate-800/40 px-4 py-2 text-sm font-medium text-slate-300 transition-colors hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-40"
                @click="goToPage(currentPage + 1)"
              >
                Next
              </button>
            </div>
          </div>
        </template>
      </div>

      <!-- Trip Form Modal -->
      <TripForm
        v-if="showForm"
        :model-value="editingTrip"
        @saved="handleSaved"
        @cancel="handleCancel"
      />

      <!-- Delete Confirmation -->
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
  </div>
</template>