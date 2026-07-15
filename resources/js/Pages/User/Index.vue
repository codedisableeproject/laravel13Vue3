<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Daftar User</h2>
        <Link href="/master/user/create">
          <v-btn color="primary">
            <v-icon left>mdi-plus</v-icon>
            Tambah User
          </v-btn>
        </Link>
      </v-card-title>
      <v-card-text>
        <v-data-table
          :headers="headers"
          :items="users.data"
          class="elevation-1"
        >
          <template v-slot:item.actions="{ item }">
            <Link :href="`/master/user/${item.id}/edit`">
              <v-btn size="small" variant="outlined" color="warning" class="mr-2">
                Edit
              </v-btn>
            </Link>
            <v-btn
              size="small"
              variant="outlined"
              color="error"
              @click="deleteUser(item.id)"
            >
              Hapus
            </v-btn>
          </template>
        </v-data-table>
        
        <div class="mt-4">
          <v-pagination
            v-model="currentPage"
            :length="users.last_page"
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
  users: Object
})

const headers = [
  { title: 'Nama', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Aksi', key: 'actions', sortable: false }
]

const currentPage = ref(props.users.current_page)

const changePage = (page) => {
  router.visit(`/master/user?page=${page}`)
}

const deleteUser = (id) => {
  if (confirm('Yakin ingin menghapus?')) {
    router.delete(`/master/user/${id}`)
  }
}
</script>