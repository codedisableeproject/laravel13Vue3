<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title>
        <h2 class="text-xl font-bold">Tambah Item</h2>
      </v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submitForm">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.kode"
                label="Kode Item"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.nama"
                label="Nama Item"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model.number="form.harga"
                label="Harga"
                type="number"
                min="0"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-file-input
                v-model="form.image"
                label="Gambar Item"
                accept="image/*"
                show-size
              />
            </v-col>
          </v-row>

          <div class="flex justify-end gap-2">
            <Link href="/master/item">
              <v-btn variant="outlined" color="secondary">Kembali</v-btn>
            </Link>
            <v-btn type="submit" color="primary">Simpan</v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  kodeItem: String
})

const form = ref({
  kode: props.kodeItem,
  nama: '',
  harga: 0,
  image: null
})

const submitForm = () => {
  const data = new FormData()
  data.append('kode', form.value.kode)
  data.append('nama', form.value.nama)
  data.append('harga', form.value.harga)
  if (form.value.image) {
    data.append('image', form.value.image)
  }
  
  router.post('/master/item', data)
}
</script>