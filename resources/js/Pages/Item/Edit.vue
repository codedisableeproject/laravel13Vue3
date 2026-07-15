<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title>
        <h2 class="text-xl font-bold">Edit Item</h2>
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
            <v-col cols="12" md="12">
              <v-img
                v-if="currentImage"
                :src="`/storage/${currentImage}`"
                max-height="100"
                class="mt-2"
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
import { ref, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  item: Object
})

const form = ref({
  kode: '',
  nama: '',
  harga: 0,
  image: null
})

const currentImage = ref('')

onMounted(() => {
  form.value.kode = props.item.kode
  form.value.nama = props.item.nama
  form.value.harga = props.item.harga
  currentImage.value = props.item.image
})

const submitForm = () => {
  const data = new FormData()
  data.append('kode', form.value.kode)
  data.append('nama', form.value.nama)
  data.append('harga', form.value.harga)
  data.append('_method', 'PUT')
  if (form.value.image) {
    data.append('image', form.value.image)
  }
  
  router.post(`/master/item/${props.item.id}`, data)
}
</script>