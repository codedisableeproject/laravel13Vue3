<template>
  <v-dialog
    v-model="dialogModel"
    max-width="600px"
    persistent
    @click:outside="$emit('update:modelValue', false)"
  >
    <v-card class="rounded-2xl shadow-xl">
      <v-card-title class="bg-indigo-800 text-white">
        <div class="flex justify-between items-center w-full">
          <div>
            <h2 class="text-xl font-bold">Tambah Item</h2>
          </div>
          <v-btn icon color="white" :disabled="isLoading" @click="$emit('update:modelValue', false)" class="rounded-full">
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
                  :error-messages="errors.kode"
                  required
                />
              </v-col>
              <v-col cols="12" md="6" class="py-1">
                <v-text-field
                  v-model="form.nama"
                  label="Nama Item"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.nama"
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
                  :error-messages="errors.harga"
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
                  :error-messages="errors.image"
                  @update:model-value="handleImageChange"
                />
              </v-col>
              
              <!-- SECTION PREVIEW GAMBAR BARU -->
              <v-col cols="12" md="12" v-if="imagePreview">
                <div class="text-xs font-medium text-gray-500 mb-1 text-left">Pratinjau Gambar:</div>
                <v-img
                  :src="imagePreview"
                  max-height="150"
                  class="rounded-lg border border-gray-200 mt-1 bg-gray-100"
                  contain
                />
              </v-col>
            </v-row>
          </div>

          <div class="flex justify-end gap-3 items-center">
            <v-btn variant="text" class="!text-gray-500 rounded-xl px-5" height="44" :disabled="isLoading" @click="$emit('update:modelValue', false)">
              Batal
            </v-btn>
            <v-btn 
              type="submit" 
              class="apply-btn min-w-[140px]"
              :loading="isLoading"
              :disabled="isLoading"
            >
              <template v-slot:loader>
                <v-progress-circular indeterminate size="20" width="2" class="mr-2" />
                Menyimpan...
              </template>
              <v-icon left size="18" class="mr-1" icon="mdi-content-save-outline" />
              Simpan Item
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  modelValue: Boolean,
  kodeItem: String
})

const emit = defineEmits(['update:modelValue'])

const isLoading = ref(false)
const imagePreview = ref(null)
const page = usePage()
const errors = computed(() => page.props.errors)

const form = ref({
  kode: props.kodeItem,
  nama: '',
  harga: 0,
  image: null
})

const handleImageChange = (file) => {
  if (file) {
    // ambil indeks pertama
    const targetFile = Array.isArray(file) ? file[0] : file
    imagePreview.value = URL.createObjectURL(targetFile)
  } else {
    imagePreview.value = null
  }
}

watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    page.props.errors = {}

    form.value = {
      kode: props.kodeItem,
      nama: '',
      harga: 0,
      image: null
    }
    imagePreview.value = null // Reset pratinjau saat dialog terbuka baru
  }
})

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const submitForm = () => {
  isLoading.value = true

  const data = new FormData()
  data.append('kode', form.value.kode)
  data.append('nama', form.value.nama)
  data.append('harga', form.value.harga)
  
  if (form.value.image) {
    // file input mengembalikan File object atau Array of File
    const fileToSend = Array.isArray(form.value.image) ? form.value.image[0] : form.value.image
    data.append('image', fileToSend)
  }
  
  router.post('/master/item', data, {
    onSuccess: () => {
      if (page.props.flash?.error) return
      emit('update:modelValue', false)
    },
    onError: () => {
      // tambahan pesan atau validasi error dari server jika ada
    },
    onFinish: () => {
      isLoading.value = false
    }
  })
}
</script>