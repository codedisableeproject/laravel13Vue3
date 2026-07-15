<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Daftar Item</h2>
        <Link href="/master/item/create">
          <v-btn color="primary">
            <v-icon left>mdi-plus</v-icon>
            Tambah Item
          </v-btn>
        </Link>
      </v-card-title>
      <v-card-text>
        <v-data-table
          :headers="headers"
          :items="items.data"
          class="elevation-1"
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
            <Link :href="`/master/item/${item.id}/edit`">
              <v-btn size="small" variant="outlined" color="warning" class="mr-2">
                Edit
              </v-btn>
            </Link>
            <v-btn
              size="small"
              variant="outlined"
              color="error"
              @click="deleteItem(item.id)"
            >
              Hapus
            </v-btn>
          </template>
        </v-data-table>
        
        <div class="mt-4">
          <v-pagination
            v-model="currentPage"
            :length="items.last_page"
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
  items: Object
})

const headers = [
  { title: 'Kode', key: 'kode' },
  { title: 'Nama', key: 'nama' },
  { title: 'Image', key: 'image' },
  { title: 'Harga', key: 'harga' },
  { title: 'Aksi', key: 'actions', sortable: false }
]

const currentPage = ref(props.items.current_page)

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const changePage = (page) => {
  router.visit(`/master/item?page=${page}`)
}

const deleteItem = (id) => {
  if (confirm('Yakin ingin menghapus?')) {
    router.delete(`/master/item/${id}`)
  }
}
</script>