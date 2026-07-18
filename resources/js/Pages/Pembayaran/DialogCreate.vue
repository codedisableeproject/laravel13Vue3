<template>
  <v-dialog
    v-model="dialogModel"
    max-width="600px"
    persistent
    @click:outside="$emit('update:modelValue', false)"
  >
    <v-card class="rounded-2xl shadow-xl">
      <v-card-title class="bg-indigo-800 text-white">
        <div class="flex justify-between items-center w-full">
          <div>
            <h2 class="text-xl font-bold">Tambah Pembayaran Baru</h2>
            <p class="text-sm opacity-80">Catat pembayaran untuk transaksi penjualan</p>
          </div>
          <v-btn icon color="white" @click="$emit('update:modelValue', false)" class="rounded-full">
            <v-icon size="24" color="black" icon="mdi-close" />
          </v-btn>
        </div>
      </v-card-title>

      <v-card-text class="p-6">
        <v-form @submit.prevent="submitForm">

          <div class="border border-gray-100 p-5 rounded-xl mb-6 bg-gray-50/30">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
              <v-icon color="indigo" size="20" icon="mdi-information-outline" />
              Informasi Pembayaran
            </h3>
            <v-row>
              <v-col cols="12" md="6" class="py-1">
                <v-text-field
                  v-model="form.kode_pembayaran"
                  label="Kode Pembayaran"
                  disabled
                  variant="flat"
                  density="comfortable"
                  class="bg-slate-50 rounded-xl"
                  required
                />
              </v-col>
              <v-col cols="12" md="6" class="py-1">
                <v-menu v-model="dateMenu" :close-on-content-click="false">
                  <template v-slot:activator="{ props }">
                    <v-btn type="button" v-bind="props" class="date-trigger h-[50px] !border-gray-200">
                      <v-icon size="18" class="mr-3 text-indigo-500" icon="mdi-calendar" />
                      <div class="text-left">
                        <div class="text-[10px] uppercase tracking-wide text-gray-400 leading-none mb-0.5">Tanggal Pembayaran</div>
                        <div class="text-sm font-semibold text-gray-800">{{ form.tanggal_pembayaran || 'Pilih Tanggal' }}</div>
                      </div>
                    </v-btn>
                  </template>
                  <v-date-picker v-model="form.tanggal_pembayaran" @update:model-value="dateMenu = false" />
                </v-menu>
              </v-col>
            </v-row>
          </div>

          <div class="mb-6">
            <v-select
              v-model="form.penjualan_id"
              :items="penjualans.map(p => ({ title: `${p.kode_penjualan} - Rp ${$formatNumber(p.total)}`, value: p.id }))"
              label="Pilih Penjualan"
              item-title="title"
              item-value="value"
              variant="outlined"
              density="comfortable"
              :error-messages="errors.penjualan_id"
              required
              @update:model-value="updateSelectedPenjualan"
            />
          </div>

          <!-- Tampilan Detail Barang di DialogCreate.vue -->
          <div v-if="selectedPenjualan && (selectedPenjualan.items && selectedPenjualan.items.length > 0)" class="mb-6 border border-gray-200 rounded-xl overflow-hidden">
            <div class="bg-gray-100 px-4 py-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
              Rincian Item yang Dibeli
            </div>
            <div class="divide-y divide-gray-100 max-h-[200px] overflow-y-auto">
              <div v-for="item in selectedPenjualan.items" :key="item.id" class="p-3 flex justify-between items-center bg-white">
                <div>
                  <!-- Mengambil nama produk dari relasi item -->
                  <p class="text-sm font-semibold text-gray-800">{{ item.item?.nama || 'Produk Tanpa Nama' }}</p>
                  <p class="text-xs text-gray-500">
                    Rp {{ $formatNumber(item.price) }} × {{ item.qty }}
                  </p>
                </div>
                <div class="text-sm font-bold text-gray-700">
                  Rp {{ $formatNumber(item.total_price) }}
                </div>
              </div>
            </div>
          </div>

          <div v-if="selectedPenjualan" class="mb-6 p-4 bg-amber-50 border border-amber-100 rounded-xl">
            <p class="text-sm text-amber-800 mb-1">
              <strong>Total Penjualan:</strong> Rp {{ $formatNumber(selectedPenjualan.total) }}
            </p>
            <p class="text-sm text-amber-800 mb-1">
              <strong>Sudah Dibayar:</strong> Rp {{ $formatNumber(selectedPenjualanTotal) }}
            </p>
            <p class="text-sm text-amber-800">
              <strong>Sisa Bayar:</strong> Rp {{ $formatNumber(selectedPenjualan.total - selectedPenjualanTotal) }}
            </p>
          </div>

          <div class="mb-6">
            <v-text-field
              :model-value="$formatNumber(form.nilai_bayar)"
              label="Nilai Bayar"
              min="1"
              variant="outlined"
              density="comfortable"
              prefix="Rp"
              :error-messages="errors.nilai_bayar"
              required
              @update:model-value="val => $handleInputNumber(val, 'nilai_bayar', form)"
            />
          </div>

          <div class="flex justify-end gap-3 items-center">
            <v-btn variant="text" class="!text-gray-500 rounded-xl px-5" height="44" @click="$emit('update:modelValue', false)">
              Batal
            </v-btn>
            <v-btn type="submit" class="apply-btn min-w-[140px]" :loading="isSubmitting" :disabled="isSubmitting">
              <v-icon left size="18" class="mr-1" icon="mdi-content-save-outline" />
              Simpan Pembayaran
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  modelValue: Boolean,
  penjualans: Array,
  kodePembayaran: String
})

const emit = defineEmits(['update:modelValue'])

const page = usePage()
const errors = computed(() => page.props.errors)

const dateMenu = ref(false)
const selectedPenjualan = ref(null)
const selectedPenjualanTotal = ref(0)
const isSubmitting = ref(false)
const form = ref({
  kode_pembayaran: props.kodePembayaran,
  tanggal_pembayaran: new Date().toISOString().split('T')[0],
  penjualan_id: null,
  nilai_bayar: 0
})

// Reset form ketika dialog dibuka
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    form.value = {
      kode_pembayaran: props.kodePembayaran,
      tanggal_pembayaran: new Date().toISOString().split('T')[0],
      penjualan_id: null,
      nilai_bayar: 0
    }
    selectedPenjualan.value = null
    selectedPenjualanTotal.value = 0
  }
})

const updateSelectedPenjualan = () => {
  if (form.value.penjualan_id) {
    selectedPenjualan.value = props.penjualans.find(p => p.id === form.value.penjualan_id)
    
    // Tinggal panggil properti ini, otomatis realtime saat select diganti
    selectedPenjualanTotal.value = selectedPenjualan.value.pembayarans_sum_nilai_bayar || 0
  } else {
    selectedPenjualan.value = null
    selectedPenjualanTotal.value = 0
  }
}

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const submitForm = () => {
  isSubmitting.value = true
  router.post('/pembayaran', form.value, {
    onSuccess: () => {
      // Cek dulu apakah backend ngirim flash error
      if (page.props.flash?.error) {
        return // jangan tutup dialog, biar user liat pesannya
      }
      emit('update:modelValue', false)
    },
    onError: () => {
      // ini buat validation error (422) — dialog tetap kebuka otomatis
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}
</script>