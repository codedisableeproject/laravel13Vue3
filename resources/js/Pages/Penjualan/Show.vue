<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Detail Penjualan</h2>
        <Link href="/penjualan">
          <v-btn variant="outlined" color="secondary">Kembali</v-btn>
        </Link>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="6">
            <p class="mb-2"><strong>Kode Penjualan:</strong> {{ penjualan.kode_penjualan }}</p>
            <p class="mb-2"><strong>Tanggal Penjualan:</strong> {{ penjualan.tanggal_penjualan }}</p>
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
        <v-data-table
          :headers="itemHeaders"
          :items="penjualan.items"
          class="elevation-1"
        >
          <template v-slot:item.price="{ item }">
            Rp {{ formatNumber(item.price) }}
          </template>
          <template v-slot:item.total_price="{ item }">
            Rp {{ formatNumber(item.total_price) }}
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  penjualan: Object,
  totalSudahDibayar: Number
})

const itemHeaders = [
  { title: 'Item', key: 'item.nama' },
  { title: 'Qty', key: 'qty' },
  { title: 'Harga', key: 'price' },
  { title: 'Total', key: 'total_price' }
]

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}
</script>