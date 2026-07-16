<template>
  <v-dialog
    v-model="dialogModel"
    max-width="600px"
    persistent
    @click:outside="$emit('update:modelValue', false)"
  >
    <v-card class="rounded-2xl shadow-xl">
      <v-card-title class="bg-amber-800 text-white">
        <div class="flex justify-between items-center w-full">
          <div>
            <h2 class="text-xl font-bold">Edit Item</h2>
          </div>
          <v-btn icon color="white" @click="$emit('update:modelValue', false)" class="rounded-full">
            <v-icon size="24" color="black" icon="mdi-close" />
          </v-btn>
        </div>
      </v-card-title>

      <v-card-text class="p-6">
        <v-form @submit.prevent="submitForm">
          <div class="border border-gray-100 p-5 rounded-xl mb-6 bg-gray-50/30">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
              <v-icon color="indigo" size="20" icon="mdi-information" />
              Informasi Item
            </h3>
            <v-row>
              <v-col cols="12" md="6" class="py-1">
                <v-text-field
                  v-model="form.kode"
                  label="Kode Item"
                  variant="outlined"
                  density="comfortable"
                  disabled
                  required
                />
              </v-col>
              <v-col cols="12" md="6" class="py-1">
                <v-text-field
                  v-model="form.nama"
                  label="Nama Item"
                  variant="outlined"
                  density="comfortable"
                  required
                />
              </v-col>
              <v-col cols="12" md="6" class="py-1">
                <v-text-field
                  v-model.number="form.harga"
                  label="Harga"
                  type="number"
                  variant="outlined"
                  density="comfortable"
                  prefix="Rp"
                  min="0"
                  required
                />
              </v-col>
              <v-col cols="12" md="6" class="py-1">
                <v-file-input
                  v-model="form.image"
                  label="Gambar Item"
                  accept="image/*"
                  show-size
                  variant="outlined"
                  density="comfortable"
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
          </div>

          <div class="flex justify-end gap-3 items-center">
            <v-btn variant="text" class="!text-gray-500 rounded-xl px-5" height="44" @click="$emit('update:modelValue', false)">
              Batal
            </v-btn>
            <v-btn type="submit" class="apply-btn min-w-[140px]">
              <v-icon left size="18" class="mr-1" icon="mdi-content-save-outline" />
              Simpan Perubahan
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  modelValue: Boolean,
  item: Object
})

const emit = defineEmits(['update:modelValue'])

const form = ref({
  kode: '',
  nama: '',
  harga: 0,
  image: null
})

const currentImage = ref('')
const isLoading = ref({
  save: false,
  load: false
})

watch(() => props.modelValue, (newValue) => {
  if (newValue && props.item) {
    form.value = {
      kode: props.item.kode,
      nama: props.item.nama,
      harga: props.item.harga,
      image: null
    }
    currentImage.value = props.item.image
  }
})

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const submitForm = () => {
  isLoading.value.save = true

  const data = new FormData()
  data.append('kode', form.value.kode)
  data.append('nama', form.value.nama)
  data.append('harga', form.value.harga)
  data.append('_method', 'PUT')
  
  if (form.value.image) {
    data.append('image', form.value.image)
  }
  
  router.post(`/master/item/${props.item.id}`, data, {
    preserveScroll: true,
    // Bertindak seperti 'catch' untuk menangkap error validasi dari Laravel
    onError: (errors) => {
      console.log('Validasi gagal:', errors)
    },
    // Bertindak seperti 'finally', selalu jalan baik sukses maupun error
    onFinish: () => {
      isLoading.value.save = false
      emit('update:modelValue', false)
    }
  })
}
</script>
