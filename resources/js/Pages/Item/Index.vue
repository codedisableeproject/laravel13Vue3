<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Daftar Item</h1>
        </div>
      </div>
    </template>

    <div class="card mb-6 overflow-hidden">
      <div class="p-5 flex justify-between items-center border-b border-gray-100 bg-slate-50/50">
        <h3 class="font-bold text-gray-800 text-lg">Data Item</h3>
        <v-btn
          color="primary"
          class="apply-btn !h-10 text-sm"
          elevation="0"
          @click="createDialogOpen = true"
        >
          <v-icon left size="18" class="mr-1" icon="mdi-plus" />
          Tambah Item
        </v-btn>
      </div>

      <div class="px-5 py-5">
        <v-data-table
          :headers="headers"
          :items="items.data"
          class="custom-table"
          hide-default-footer
        >
          <template v-slot:item.image="{ item }">
            <v-img
              v-if="item.image"
              :src="`/storage/${item.image}`"
              max-height="50"
              max-width="50"
              cover
            />
            <span v-else>-</span>
          </template>
          <template v-slot:item.harga="{ item }">
            Rp {{ formatNumber(item.harga) }}
          </template>
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
                @click="deleteItem(item.id)"
              >
                <v-icon size="16" icon="mdi-delete" />
              </v-btn>
            </div>
          </template>
        </v-data-table>

        <div class="mt-4 flex justify-end">
          <v-pagination
            v-model="currentPage"
            :length="items.last_page"
            total-visible="5"
            density="comfortable"
            active-color="primary"
            variant="flat"
            @update:model-value="changePage"
          />
        </div>
      </div>
    </div>

    <DialogCreate
      v-model="createDialogOpen"
      :kode-item="kodeItem"
    />
    <DialogShow
      v-model="showDialogOpen"
      :item="selectedItem"
    />
    <DialogEdit
      v-model="editDialogOpen"
      :item="selectedItem"
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
  items: Object,
  kodeItem: String
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
  { title: 'Kode', key: 'kode', sortable: true },
  { title: 'Nama', key: 'nama', sortable: true },
  { title: 'Image', key: 'image' },
  { title: 'Harga', key: 'harga', sortable: true },
  { title: 'Aksi', key: 'actions', sortable: false, align: 'end' }
]

const currentPage = ref(props.items.current_page)
const createDialogOpen = ref(false)
const showDialogOpen = ref(false)
const editDialogOpen = ref(false)
const selectedItem = ref(null)

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const changePage = (page) => {
  router.visit(`/master/item?page=${page}`)
}

const deleteItem = async (id) => {
  const confirm = await $dialogNotif.confirm({
    title: 'Hapus Item',
    text: 'Apakah Anda yakin ingin menghapus data item ini?',
    icon: 'info',
    textConfirm: 'Hapus',
    colorConfirm: 'danger', 
  })
  if (!confirm.confirmed) return
  
  router.delete(`/master/item/${id}`)
}

const openShowDialog = (item) => {
  selectedItem.value = item
  showDialogOpen.value = true
}

const openEditDialog = (item) => {
  selectedItem.value = item
  editDialogOpen.value = true
}
</script>
