<template>
  <v-dialog
    v-model="dialogModel"
    max-width="600px"
    persistent
    @click:outside="$emit('update:modelValue', false)"
  >
    <v-card class="rounded-2xl shadow-xl">
      <v-card-title class="bg-amber-800 text-white">
        <div class="flex justify-between items-center w-full">
          <div>
            <h2 class="text-xl font-bold">Edit Pembayaran</h2>
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
                  :error-messages="errors.kode_pembayaran"
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
                        <div class="text-sm font-semibold text-gray-800">{{ $formatDate(form.tanggal_pembayaran) || 'Pilih Tanggal' }}</div>
                      </div>
                    </v-btn>
                  </template>
                  <v-date-picker v-model="form.tanggal_pembayaran" @update:model-value="dateMenu = false" />
                </v-menu>
              </v-col>
            </v-row>
          </div>

          <div class="mb-6">
            <v-text-field
              v-model="penjualanKode"
              label="Penjualan"
              disabled
              variant="outlined"
              density="comfortable"
              :error-messages="errors.penjualan_id"
            />
          </div>

          <div v-if="penjualan" class="mb-6 p-4 bg-amber-50 border border-amber-100 rounded-xl">
            <p class="text-sm text-amber-800 mb-1">
              <strong>Total Penjualan:</strong> Rp {{ $formatNumber(penjualan.total) }}
            </p>
            <p class="text-sm text-amber-800 mb-1">
              <strong>Sudah Dibayar (tanpa ini):</strong> Rp {{ $formatNumber(penjualanTotal - currentNilaiBayar) }}
            </p>
            <p class="text-sm text-amber-800">
              <strong>Sisa Bayar:</strong> Rp {{ $formatNumber(penjualan.total - (penjualanTotal - currentNilaiBayar)) }}
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
              required@update:model-value="val => $handleInputNumber(val, 'nilai_bayar', form)"

            />
          </div>

          <div class="flex justify-end gap-3 items-center">
            <v-btn variant="text" class="!text-gray-500 rounded-xl px-5" height="44" @click="$emit('update:modelValue', false)">
              Batal
            </v-btn>
            <v-btn type="submit" class="apply-btn min-w-[140px]" :loading="isSubmitting" :disabled="isSubmitting">
              <v-icon left size="18" class="mr-1" icon="mdi-content-save-outline" />
              Simpan Perubahan
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
  pembayaran: Object
})

const emit = defineEmits(['update:modelValue'])

const page = usePage()
const errors = computed(() => page.props.errors)

const dateMenu = ref(false)
const penjualanKode = ref('')
const penjualan = ref(null)
const penjualanTotal = ref(0)
const currentNilaiBayar = ref(0)
const isSubmitting = ref(false)
const form = ref({
  kode_pembayaran: '',
  tanggal_pembayaran: '',
  nilai_bayar: 0
})

// Reset form when dialog opens
watch(() => props.modelValue, (newVal) => {
  if (newVal && props.pembayaran) {

    page.props.errors = {}

    form.value = {
      kode_pembayaran: props.pembayaran.kode_pembayaran,
      tanggal_pembayaran: props.pembayaran.tanggal_pembayaran,
      nilai_bayar: props.pembayaran.nilai_bayar
    }
    penjualanKode.value = props.pembayaran.penjualan.kode_penjualan
    penjualan.value = props.pembayaran.penjualan
    currentNilaiBayar.value = props.pembayaran.nilai_bayar
    
    // Tinggal panggil dari properti hasil hitung belakang
    penjualanTotal.value = penjualan.value.pembayarans_sum_nilai_bayar || 0
  }
})

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// const formatNumber = (num) => {
//   return new Intl.NumberFormat('id-ID').format(num || 0)
// }

const submitForm = () => {
  isSubmitting.value = true
  router.put(`/pembayaran/${props.pembayaran.id}`, form.value, {
    onSuccess: () => {
      // Sama seperti create, jika dilempar flash error dari session Laravel,
      // jangan tutup dialog agar user dapat membaca pesan toast/balon errornya.
      if (page.props.flash?.error) {
        return
      }
      emit('update:modelValue', false)
    },
    onError: () => {
      // Error validasi form input otomatis terikat di komponen v-text-field
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}
</script>
