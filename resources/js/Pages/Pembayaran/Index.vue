<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Pembayaran</h1>
          <p class="text-sm text-gray-500 mt-1">
            Daftar pembayaran yang telah dilakukan
          </p>
        </div>
      </div>
    </template>
    <div class="card filter-card mb-6">
      <v-row align="center" class="ma-0">
        <v-col cols="12" md="4">
          <v-menu v-model="startMenu" :close-on-content-click="false">
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" class="date-trigger">
                <v-icon
                  size="18"
                  class="mr-2 text-indigo-500"
                  icon="mdi-calendar-start"
                />
                <div class="text-left">
                  <div
                    class="text-[11px] uppercase tracking-wide text-gray-400 leading-none mb-1"
                  >
                    Dari Tanggal
                  </div>
                  <div
                    class="text-sm font-medium text-gray-800"
                  >
                    {{
                      formatDate(startDate) ||
                      "Pilih Tanggal"
                    }}
                  </div>
                </div>
              </v-btn>
            </template>
            <v-date-picker
              v-model="startDate"
              @update:model-value="startMenu = false"
            />
          </v-menu>
        </v-col>
        <v-col cols="12" md="3">
          <v-menu v-model="endMenu">
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" class="date-trigger">
                <v-icon size="18" class="mr-2 text-indigo-500" icon="mdi-calendar-end" />
                <div class="text-left">
                  <div class="text-[11px] uppercase tracking-wide text-gray-400 leading-none mb-1">Sampai Tanggal</div>
                  <div class="text-sm font-medium text-gray-800">{{ formatDate(endDate)  || 'Pilih Tanggal' }}</div>
                </div>
              </v-btn>
            </template>
            <v-date-picker
              v-model="endDate"
              @update:model-value="endMenu = false"
            />
          </v-menu>
        </v-col>

        <v-col cols="12" md="4">
          <v-btn 
            block 
            color="primary" 
            :loading="isFiltering"
            @click="applyFilters"
          >
            <v-icon left size="18" class="mr-1" icon="mdi-filter-variant" />
            Terapkan Filter
          </v-btn>
        </v-col>
      </v-row>
    </div>

    <!-- Table -->
    <v-card class="card mb-6 overflow-hidden">
      <v-card-title class="p-5 flex justify-between items-center border-b border-gray-100 bg-slate-50/50">
        <h3 class="font-bold text-gray-800 text-lg">Daftar Pembayaran</h3>
          <v-btn 
            color="primary"
            class="apply-btn !h-10 text-sm"
            elevation="0"
            @click="createDialogOpen = true"
          >
            <v-icon left size="18" class="mr-1" icon="mdi-plus" />
            Tambah Pembayaran
          </v-btn>
      </v-card-title>
      <v-card-text
        class="px-5 py-5"
      >
        <v-data-table
          :headers="headers"
          :items="pembayarans.data"
          class="custom-table"
          hide-default-footer
        >
          <template v-slot:item.actions="{ item }">
            <div class="flex items-center justify-end gap-1.5">
              <v-btn
                class="action-icon-btn text-indigo-600 hover:bg-indigo-50"
                title="Lihat Detail"
                @click="openShowDialog(item)"
              >
                <v-icon size="16" icon="mdi-eye" />
              </v-btn>
              <v-btn
                class="action-icon-btn text-amber-600 hover:bg-amber-50"
                title="Edit"
                @click="openEditDialog(item)"
              >
                <v-icon size="16" icon="mdi-pencil" />
              </v-btn>
              <v-btn
                class="action-icon-btn text-rose-600 hover:bg-rose-50"
                title="Hapus"
                @click="deletePembayaran(item.id)"
              >
                <v-icon size="16" icon="mdi-delete" />
              </v-btn>
            </div>
          </template>
          <template v-slot:item.nilai_bayar="{ item }">
            Rp {{ formatNumber(item.nilai_bayar) }}
          </template>
        </v-data-table>

        <div class="mt-4 flex justify-end">
          <v-pagination
            v-model="currentPage"
            :length="pembayarans.last_page"
            total-visible="5"
            density="comfortable"
            active-color="primary"
            variant="flat"
            @update:model-value="changePage"
          />
        </div>
      </v-card-text>
    </v-card>

    <!-- Dialogs -->
    <DialogCreate
      v-model="createDialogOpen"
      :penjualans="penjualans"
      :kode-pembayaran="kodePembayaran"
    />
    <DialogShow
      v-model="showDialogOpen"
      :pembayaran="selectedPembayaran"
    />
    <DialogEdit
      v-model="editDialogOpen"
      :pembayaran="selectedPembayaran"
    />
  </AppLayout>
</template>

<script setup>
import { ref, getCurrentInstance } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DialogCreate from './DialogCreate.vue'
import DialogShow from './DialogShow.vue'
import DialogEdit from './DialogEdit.vue'

const props = defineProps({
  pembayarans: Object,
  penjualans: Array,
  kodePembayaran: String
})

const { proxy } = getCurrentInstance()
const $dialogNotif = proxy.$dialogNotif

const headers = [
  { title: 'Kode Pembayaran', key: 'kode_pembayaran', sortable: true },
  { title: 'Tanggal Pembayaran', key: 'tanggal_pembayaran', sortable: true },
  { title: 'Penjualan', key: 'penjualan.kode_penjualan' },
  { title: 'Nilai Bayar', key: 'nilai_bayar', sortable: true },
  { title: 'Aksi', key: 'actions', sortable: false, align: 'end' }
]

const currentPage = ref(props.pembayarans.current_page)
const startMenu = ref(false)
const endMenu = ref(false)
const startDate = ref(null)
const endDate = ref(null)
const isFiltering = ref(false)
const createDialogOpen = ref(false)
const showDialogOpen = ref(false)
const editDialogOpen = ref(false)
const selectedPembayaran = ref(null)

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const formatDate = (dateStr) => {
  if (!dateStr) return null
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(date)
}

const changePage = (page) => {
  router.visit(`/pembayaran?page=${page}`)
}

const applyFilters = () => {
  startMenu.value = false
  endMenu.value = false
  isFiltering.value = true
  router.visit('/pembayaran', {
    data: {
      tanggal_mulai: startDate.value,
      tanggal_selesai: endDate.value
    },
    onFinish: () => {
      isFiltering.value = false
    }
  })
}

const deletePembayaran = async (id) => {
  const confirm = await $dialogNotif.confirm({
    title: 'Hapus Pembayaran',
    text: 'Apakah Anda yakin ingin menghapus data pembayaran ini?',
    icon: 'info',
    textConfirm: 'Hapus',
    colorConfirm: 'danger', 
  })
  if (!confirm.confirmed) return
  
  router.delete(`/pembayaran/${id}`)
}

const openShowDialog = (pembayaran) => {
  selectedPembayaran.value = pembayaran
  showDialogOpen.value = true
}

const openEditDialog = (pembayaran) => {
  selectedPembayaran.value = pembayaran
  editDialogOpen.value = true
}
</script>
