<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Detail Pembayaran</h2>
        <Link href="/pembayaran">
          <v-btn variant="outlined" color="secondary">Kembali</v-btn>
        </Link>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="6">
            <p class="mb-2"><strong>Kode Pembayaran:</strong> {{ pembayaran.kode_pembayaran }}</p>
            <p class="mb-2"><strong>Tanggal Pembayaran:</strong> {{ pembayaran.tanggal_pembayaran }}</p>
            <p class="mb-2"><strong>Penjualan:</strong> {{ pembayaran.penjualan.kode_penjualan }}</p>
            <p class="mb-2"><strong>Total Penjualan:</strong> Rp {{ formatNumber(pembayaran.penjualan.total) }}</p>
            <p class="mb-2"><strong>Nilai Bayar:</strong> Rp {{ formatNumber(pembayaran.nilai_bayar) }}</p>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  pembayaran: Object
})

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}
</script>