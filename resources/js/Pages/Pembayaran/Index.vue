<template>
  <AppLayout>
    <div class="mb-6">
      <v-card class="p-4">
        <v-row align="center">
          <v-col cols="12" md="4">
            <v-menu v-model="startMenu">
              <template v-slot:activator="{ props }">
                <v-btn v-bind="props" variant="outlined" block>
                  <v-icon left>mdi-calendar</v-icon>
                  {{ startDate || 'Tanggal Mulai' }}
                </v-btn>
              </template>
              <v-date-picker v-model="startDate" @update:model-value="() => startMenu.value = false" />
            </v-menu>
          </v-col>
          <v-col cols="12" md="4">
            <v-menu v-model="endMenu">
              <template v-slot:activator="{ props }">
                <v-btn v-bind="props" variant="outlined" block>
                  <v-icon left>mdi-calendar</v-icon>
                  {{ endDate || 'Tanggal Selesai' }}
                </v-btn>
              </template>
              <v-date-picker v-model="endDate" @update:model-value="() => endMenu.value = false" />
            </v-menu>
          </v-col>
          <v-col cols="12" md="4">
            <v-btn block color="primary" @click="applyFilters">
              <v-icon left>mdi-filter</v-icon>
              Terapkan Filter
            </v-btn>
          </v-col>
        </v-row>
      </v-card>
    </div>

    <v-card class="mb-6">
      <v-card-title class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Daftar Pembayaran</h2>
        <Link href="/pembayaran/create">
          <v-btn color="primary">
            <v-icon left>mdi-plus</v-icon>
            Tambah Pembayaran
          </v-btn>
        </Link>
      </v-card-title>
      <v-card-text>
        <v-data-table
          :headers="headers"
          :items="pembayarans.data"
          class="elevation-1"
        >
          <template v-slot:item.actions="{ item }">
            <Link :href="`/pembayaran/${item.id}`">
              <v-btn size="small" variant="outlined" color="primary" class="mr-2">
                Lihat
              </v-btn>
            </Link>
            <Link :href="`/pembayaran/${item.id}/edit`">
              <v-btn size="small" variant="outlined" color="warning" class="mr-2">
                Edit
              </v-btn>
            </Link>
            <v-btn
              size="small"
              variant="outlined"
              color="error"
              @click="deletePembayaran(item.id)"
            >
              Hapus
            </v-btn>
          </template>
          <template v-slot:item.nilai_bayar="{ item }">
            Rp {{ formatNumber(item.nilai_bayar) }}
          </template>
        </v-data-table>
        
        <div class="mt-4">
          <v-pagination
            v-model="currentPage"
            :length="pembayarans.last_page"
            @update:model-value="changePage"
          />
        </div>
      </v-card-text>
    </v-card>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  pembayarans: Object
})

const headers = [
  { title: 'Kode Pembayaran', key: 'kode_pembayaran' },
  { title: 'Tanggal Pembayaran', key: 'tanggal_pembayaran' },
  { title: 'Penjualan', key: 'penjualan.kode_penjualan' },
  { title: 'Nilai Bayar', key: 'nilai_bayar' },
  { title: 'Aksi', key: 'actions', sortable: false }
]

const currentPage = ref(props.pembayarans.current_page)
const startMenu = ref(false)
const endMenu = ref(false)
const startDate = ref(null)
const endDate = ref(null)

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const changePage = (page) => {
  router.visit(`/pembayaran?page=${page}`)
}

const applyFilters = () => {
  startMenu.value = false
  endMenu.value = false
  router.visit('/pembayaran', {
    data: {
      tanggal_mulai: startDate.value,
      tanggal_selesai: endDate.value
    }
  })
}

const deletePembayaran = (id) => {
  if (confirm('Yakin ingin menghapus?')) {
    router.delete(`/pembayaran/${id}`)
  }
}
</script>