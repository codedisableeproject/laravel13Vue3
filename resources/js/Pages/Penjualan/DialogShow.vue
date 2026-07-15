<template>
  <v-dialog
    v-model="dialogModel"
    max-width="1000px"
    persistent
    @click:outside="$emit('update:modelValue', false)"
  >
    <v-card class="rounded-2xl shadow-xl">
      <v-card-title class="bg-indigo-800 text-white">
        <div class="flex justify-between items-center w-full">
          <div>
            <h2 class="text-xl font-bold">Detail Penjualan</h2>
          </div>
          <v-btn icon color="white" @click="$emit('update:modelValue', false)" class="rounded-full">
            <v-icon size="24" color="black" icon="mdi-close" />
          </v-btn>
        </div>
      </v-card-title>

      <v-card-text class="p-6">
        <v-row>
          <v-col cols="12" md="6">
            <p class="mb-2"><strong>Kode Penjualan:</strong> {{ penjualan.kode_penjualan }}</p>
            <p class="mb-2"><strong>Tanggal Penjualan:</strong> {{ formatDate(penjualan.tanggal_penjualan) }}</p>
            <p class="mb-2">
              <strong>Status:</strong>
              <v-chip :color="penjualan.status === 'Sudah Dibayar' ? 'success' : penjualan.status === 'Belum Dibayar Sepenuhnya' ? 'warning' : 'error'" variant="outlined">
                {{ penjualan.status }}
              </v-chip>
            </p>
            <p class="mb-2"><strong>Total:</strong> Rp {{ formatNumber(penjualan.total) }}</p>
            <p class="mb-2"><strong>Total Sudah Dibayar:</strong> Rp {{ formatNumber(totalSudahDibayar) }}</p>
          </v-col>
        </v-row>

        <h3 class="text-lg font-semibold mt-6 mb-4">Items</h3>
        <div v-if="penjualan.items && penjualan.items.length > 0" class="mb-5">
          <v-data-table
            :headers="itemHeaders"
            :items="paginatedItems"
            class="custom-table elevation-1"
            hide-default-footer
          >
            <template v-slot:item.price="{ item }">
              Rp {{ formatNumber(item.price) }}
            </template>
            <template v-slot:item.total_price="{ item }">
              Rp {{ formatNumber(item.total_price) }}
            </template>
          </v-data-table>
          <div class="mt-4 flex justify-end">
            <v-pagination
              v-model="itemCurrentPage"
              :length="totalItemPages"
              total-visible="5"
              density="comfortable"
              active-color="primary"
              variant="flat"
            />
          </div>
        </div>

        <h3 class="text-lg font-semibold mt-6 mb-4">Pembayaran</h3>
        <div v-if="penjualan.pembayarans && penjualan.pembayarans.length > 0" class="mb-5">
          <v-data-table
            :headers="paymentHeaders"
            :items="paginatedPembayarans"
            class="elevation-1"
            hide-default-footer
          >
            <template v-slot:item.tanggal_pembayaran="{ item }">
              {{ formatDate(item.tanggal_pembayaran) }}
            </template>
            <template v-slot:item.nilai_bayar="{ item }">
              Rp {{ formatNumber(item.nilai_bayar) }}
            </template>
          </v-data-table>
          <div class="mt-4 flex justify-end">
            <v-pagination
              v-model="paymentCurrentPage"
              :length="totalPaymentPages"
              total-visible="5"
              density="comfortable"
              active-color="primary"
              variant="flat"
            />
          </div>
        </div>
        <v-alert v-else-if="penjualan.pembayarans && penjualan.pembayarans.length === 0" variant="tonal" type="info" text="Belum ada pembayaran untuk transaksi ini" />

      </v-card-text>

      <v-card-actions class="px-6 pb-6 justify-end">
        <v-btn variant="text" class="!text-gray-500 rounded-xl px-5" height="44" @click="$emit('update:modelValue', false)">
          Tutup
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  penjualan: Object,
  totalSudahDibayar: Number
})

const emit = defineEmits(['update:modelValue'])

const itemHeaders = [
  { title: 'Item', key: 'item.nama' },
  { title: 'Qty', key: 'qty' },
  { title: 'Harga', key: 'price' },
  { title: 'Total', key: 'total_price' }
]

const paymentHeaders = [
  { title: 'Kode Pembayaran', key: 'kode_pembayaran' },
  { title: 'Tanggal Pembayaran', key: 'tanggal_pembayaran' },
  { title: 'Nilai Bayar', key: 'nilai_bayar' }
]

const paymentCurrentPage = ref(1)
const itemCurrentPage = ref(1)
const paymentsPerPage = 5
const itemsPerPage = 5

// Reset page when dialog opens
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    paymentCurrentPage.value = 1
    itemCurrentPage.value = 1
  }
})

const totalPaymentPages = computed(() => {
  if (!props.penjualan?.pembayarans) return 0
  return Math.ceil(props.penjualan.pembayarans.length / paymentsPerPage)
})

const paginatedPembayarans = computed(() => {
  if (!props.penjualan?.pembayarans) return []
  const start = (paymentCurrentPage.value - 1) * paymentsPerPage
  return props.penjualan.pembayarans.slice(start, start + paymentsPerPage)
})

const totalItemPages = computed(() => {
  if (!props.penjualan?.items) return 0
  return Math.ceil(props.penjualan.items.length / itemsPerPage)
})

const paginatedItems = computed(() => {
  if (!props.penjualan?.items) return []
  const start = (itemCurrentPage.value - 1) * itemsPerPage
  return props.penjualan.items.slice(start, start + itemsPerPage)
})

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const formatDate = (dateStr) => {
  if (!dateStr) return null
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(date)
}

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})
</script>
