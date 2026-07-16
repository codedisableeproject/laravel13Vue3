<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Daftar User</h1>
        </div>
      </div>
    </template>

    <div class="card mb-6 overflow-hidden">
      <div class="p-5 flex justify-between items-center border-b border-gray-100 bg-slate-50/50">
        <h3 class="font-bold text-gray-800 text-lg">Data User</h3>
        <v-btn
          color="primary"
          class="apply-btn !h-10 text-sm"
          elevation="0"
          @click="createDialogOpen = true"
        >
          <v-icon left size="18" class="mr-1" icon="mdi-plus" />
          Tambah User
        </v-btn>
      </div>

      <div class="px-5 py-5">
        <v-data-table
          :headers="headers"
          :items="users.data"
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
                @click="deleteUser(item.id)"
              >
                <v-icon size="16" icon="mdi-delete" />
              </v-btn>
            </div>
          </template>
        </v-data-table>

        <div class="mt-4 flex justify-end">
          <v-pagination
            v-model="currentPage"
            :length="users.last_page"
            total-visible="5"
            density="comfortable"
            active-color="primary"
            variant="flat"
            @update:model-value="changePage"
          />
        </div>
      </div>
    </div>

    <DialogCreate v-model="createDialogOpen" />
    <DialogShow v-model="showDialogOpen" :user="selectedUser" />
    <DialogEdit v-model="editDialogOpen" :user="selectedUser" />
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
  users: Object
})

const { proxy } = getCurrentInstance()
const $dialogNotif = proxy.$dialogNotif

const headers = [
  { title: 'Nama', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Aksi', key: 'actions', sortable: false, align: 'end' }
]

const currentPage = ref(props.users.current_page)
const createDialogOpen = ref(false)
const showDialogOpen = ref(false)
const editDialogOpen = ref(false)
const selectedUser = ref(null)

const changePage = (page) => {
  router.visit(`/master/user?page=${page}`)
}

const deleteUser = async (id) => {
  const confirm = await $dialogNotif.confirm({
    title: 'Hapus User',
    text: 'Apakah Anda yakin ingin menghapus data user ini?',
    icon: 'info',
    textConfirm: 'Hapus',
    colorConfirm: 'danger', 
  })
  if (!confirm.confirmed) return
  
  router.delete(`/master/user/${id}`)
}

const openShowDialog = (user) => {
  selectedUser.value = user
  showDialogOpen.value = true
}

const openEditDialog = (user) => {
  selectedUser.value = user
  editDialogOpen.value = true
}
</script>
