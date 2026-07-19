<template>
  <AppLayout>
    <!-- HEADER BAR (Disamakan dengan style Dashboard) -->
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Daftar Penjualan</h1>
          <p class="text-sm text-gray-500 mt-1">
            Kelola, pantau, dan filter seluruh data transaksi penjualan Anda
          </p>
        </div>
      </div>
    </template>

    <!-- FILTER BAR (Menggunakan style date-trigger seperti Dashboard) -->
    <div class="card filter-card mb-6">
      <v-row align="center" class="ma-0">
        <v-col cols="12" md="4">
          <v-menu v-model="startMenu" :close-on-content-click="false">
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" class="date-trigger">
                <v-icon size="18" class="mr-2 text-indigo-500" icon="mdi-calendar-start" />
                <div class="text-left">
                  <div class="text-[11px] uppercase tracking-wide text-gray-400 leading-none mb-1">Dari Tanggal</div>
                  <div class="text-sm font-medium text-gray-800">{{ formatDate(startDate) || 'Pilih Tanggal' }}</div>
                </div>
              </v-btn>
            </template>
            <v-date-picker v-model="startDate" @update:model-value="startMenu = false" />
          </v-menu>
        </v-col>

        <v-col cols="12" md="4">
          <v-menu v-model="endMenu" :close-on-content-click="false">
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" class="date-trigger">
                <v-icon size="18" class="mr-2 text-indigo-500" icon="mdi-calendar-end" />
                <div class="text-left">
                  <div class="text-[11px] uppercase tracking-wide text-gray-400 leading-none mb-1">Sampai Tanggal</div>
                  <div class="text-sm font-medium text-gray-800">{{ formatDate(endDate) || 'Pilih Tanggal' }}</div>
                </div>
              </v-btn>
            </template>
            <v-date-picker v-model="endDate" @update:model-value="endMenu = false" />
          </v-menu>
        </v-col>

        <v-col cols="12" md="4">
          <v-btn
            block
            color="primary"
            class="apply-btn"
            :loading="isFiltering"
            @click="applyFilters"
          >
            <v-icon left size="18" class="mr-1" icon="mdi-filter-variant" />
            Terapkan Filter
          </v-btn>
        </v-col>
      </v-row>
    </div>

    <!-- MAIN TABLE SECTION (Menggunakan style card premium) -->
    <div class="card mb-6 overflow-hidden">
      <!-- Table Header & Action v-btn -->
      <div class="p-5 flex justify-between items-center border-b border-gray-100 bg-slate-50/50">
        <div>
          <h3 class="font-bold text-gray-800 text-lg">Data Transaksi</h3>
          <p class="text-xs text-gray-400 mt-0.5">Menampilkan seluruh riwayat penjualan</p>
        </div>
        <v-btn
          color="primary"
          class="apply-btn !h-10 text-sm"
          elevation="0"
          @click="createDialogOpen = true"
        >
          <v-icon left size="18" class="mr-1" icon="mdi-plus" />
          Tambah Penjualan
        </v-btn>
      </div>

      <div class="px-5 py-5">
        <!-- Vuetify Data Table dengan Custom Styling Premium -->
        <v-data-table
          :headers="headers"
          :items="penjualans.data"
          class="custom-table"
          hide-default-footer
        >
          <!-- Column: Kode Penjualan -->
          <template v-slot:item.kode_penjualan="{ item }">
            <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded text-xs tracking-wider">
              {{ item.kode_penjualan }}
            </span>
          </template>

          <!-- Column: Tanggal Penjualan -->
          <template v-slot:item.tanggal_penjualan="{ item }">
            <span class="text-gray-600 font-medium text-sm">{{ formatDate(item.tanggal_penjualan) }}</span>
          </template>

          <!-- Column: Total -->
          <template v-slot:item.total="{ item }">
            <span class="font-semibold text-gray-900 tabular-nums">Rp {{ formatNumber(item.total) }}</span>
          </template>

          <!-- Column: Status -->
          <template v-slot:item.status="{ item }">
            <span
              class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold tracking-wide"
              :class="{
                'bg-emerald-50 text-emerald-700 border border-emerald-200': item.status === 'Sudah Dibayar',
                'bg-amber-50 text-amber-700 border border-amber-200': item.status === 'Belum Dibayar Sepenuhnya',
                'bg-rose-50 text-rose-700 border border-rose-200': item.status === 'Belum Dibayar'
              }"
            >
              <span
                class="w-1.5 h-1.5 rounded-full mr-1.5"
                :class="{
                  'bg-emerald-500': item.status === 'Sudah Dibayar',
                  'bg-amber-500': item.status === 'Belum Dibayar Sepenuhnya',
                  'bg-rose-500': item.status === 'Belum Dibayar'
                }"
              ></span>
              {{ item.status }}
            </span>
          </template>

          <!-- Column: Aksi (Menggunakan v-btn Ikon Lingkaran Tipis Premium) -->
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
                v-if="item.status !== 'Sudah Dibayar'"
                class="action-icon-btn text-amber-600 hover:bg-amber-50"
                title="Edit"
                @click="openEditDialog(item)"
              >
                <v-icon size="16" icon="mdi-pencil" />
              </v-btn>
              <v-btn
                v-if="item.status !== 'Sudah Dibayar'"
                class="action-icon-btn text-rose-600 hover:bg-rose-50"
                title="Hapus"
                @click="deletePenjualan(item.id)"
              >
                <v-icon size="16" icon="mdi-delete" />
              </v-btn>
            </div>
          </template>
        </v-data-table>

        <!-- Pagination Section Premium -->
        <div class="mt-5 flex justify-end">
          <v-pagination
            v-model="currentPage"
            :length="penjualans.last_page"
            total-visible="5"
            density="comfortable"
            active-color="primary"
            variant="flat"
            @update:model-value="changePage"
          />
        </div>
      </div>
    </div>

    <!-- Dialog Tambah Penjualan dari komponen DialogCreate.vue -->
    <DialogCreate
      v-model="createDialogOpen"
      :items="items"
      :kode-penjualan="kodePenjualan"
    />

    <!-- Dialog Detail Penjualan -->
    <DialogShow
      v-model="showDialogOpen"
      :penjualan="selectedPenjualan"
      :total-sudah-dibayar="selectedPenjualanTotal"
    />

    <!-- Dialog Edit Penjualan -->
    <DialogEdit
      v-model="editDialogOpen"
      :items="items"
      :penjualan="selectedPenjualan"
    />
  </AppLayout>
</template>

<script setup>
import { ref, getCurrentInstance, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import AppLayout from '@/Layouts/AppLayout.vue'
import DialogCreate from './DialogCreate.vue'
import DialogShow from './DialogShow.vue'
import DialogEdit from './DialogEdit.vue'

const props = defineProps({
  penjualans: Object,
  items: Array,
  kodePenjualan: String,
  filters: Object
})

const toast = useToast()
const page = usePage()

watch(
  () => page.props.flash,
  (newFlash) => {
    if (newFlash?.success) {
      toast.success(newFlash.success)
    }
    if (newFlash?.error) {
      toast.error(newFlash.error)
    }
  },
  { deep: true, immediate: true }
)


const { proxy } = getCurrentInstance()
const $dialogNotif = proxy.$dialogNotif

const headers = [
  { title: 'KODE PENJUALAN', key: 'kode_penjualan', sortable: true },
  { title: 'TANGGAL PENJUALAN', key: 'tanggal_penjualan', sortable: true },
  { title: 'TOTAL TRANSAKSI', key: 'total', sortable: true },
  { title: 'STATUS', key: 'status', sortable: true, width: '200px' },
  { title: 'AKSI', key: 'actions', sortable: false, align: 'end', width: '130px' }
]

const currentPage = ref(props.penjualans.current_page)
const startMenu = ref(false)
const endMenu = ref(false)
const startDate = ref(props.filters?.start_date || null)
const endDate = ref(props.filters?.end_date || null)
const isFiltering = ref(false)
const createDialogOpen = ref(false)
const showDialogOpen = ref(false)
const editDialogOpen = ref(false)
const selectedPenjualan = ref(null)
const selectedPenjualanTotal = ref(0)

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const formatDate = (dateStr) => {
  if (!dateStr) return null
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(date)
}

const changePage = (page) => {
  router.visit(`/penjualan?page=${page}`)
}

const applyFilters = () => {
  startMenu.value = false
  endMenu.value = false
  isFiltering.value = true
  router.visit('/penjualan', {
    data: {
      start_date: startDate.value,
      end_date: endDate.value
    },
    onFinish: () => {
      isFiltering.value = false
    }
  })
}

const deletePenjualan = async (id) => {
  const confirm = await $dialogNotif.confirm({
    title: 'Hapus Penjualan',
    text: 'Apakah Anda yakin ingin menghapus data penjualan ini?',
    icon: 'info',
    textConfirm: 'Hapus',
    colorConfirm: 'danger', 
  })
  if (!confirm.confirmed) return
  
  router.delete(`/penjualan/${id}`)
}

const openShowDialog = (penjualan) => {
  selectedPenjualan.value = penjualan
  // Hitung total dari pembayarans
  selectedPenjualanTotal.value = (penjualan.pembayarans || []).reduce((sum, p) => sum + (p.nilai_bayar || 0), 0)
  showDialogOpen.value = true
}

const openEditDialog = (penjualan) => {
  selectedPenjualan.value = penjualan
  editDialogOpen.value = true
}
</script>
