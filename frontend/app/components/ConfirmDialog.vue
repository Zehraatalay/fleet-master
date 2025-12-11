<script setup>
const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Confirm',
  },
  message: {
    type: String,
    default: '',
  },
  confirmLabel: {
    type: String,
    default: 'OK',
  },
  cancelLabel: {
    type: String,
    default: 'Cancel',
  },
});

const emit = defineEmits(['confirm', 'cancel']);

const onCancel = () => {
  emit('cancel');
};

const onConfirm = () => {
  emit('confirm');
};
</script>

<!-- 
Genel onay penceresi:
- show: true/false ile ac/kapa
- title, message: ust yazi ve aciklama
- confirmLabel / cancelLabel: buton textleri
- @confirm / @cancel eventleri ile parent'a haber verir
-->

<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    >
      <div
        class="w-full max-w-md bg-slate-900 border border-slate-700 rounded-xl p-6 shadow-xl"
      >
        <h2 class="text-lg font-semibold mb-3 text-slate-50">
          {{ title }}
        </h2>
        <p class="text-sm text-slate-200 mb-6">
          {{ message }}
        </p>

        <div class="flex justify-end gap-2">
          <button
            type="button"
            class="px-4 py-2 rounded bg-slate-700 text-sm hover:bg-slate-600"
            @click="onCancel"
          >
            {{ cancelLabel }}
          </button>
          <button
            type="button"
            class="px-4 py-2 rounded bg-red-600 text-sm font-medium hover:bg-red-500"
            @click="onConfirm"
          >
            {{ confirmLabel }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>
