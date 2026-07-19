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
            <h2 class="text-xl font-bold">Tambah User</h2>
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
              <v-icon color="indigo" size="20" icon="mdi-account" />
              Informasi User
            </h3>
            <v-row>
              <v-col cols="12" md="6" class="py-1">
                <v-text-field
                  v-model="form.name"
                  label="Nama"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.name"
                  required
                />
              </v-col>
              <v-col cols="12" md="6" class="py-1">
                <v-text-field
                  v-model="form.email"
                  label="Email"
                  type="email"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.email"
                  required
                />
              </v-col>
              <v-col cols="12" md="6" class="py-1">
                <v-select
                  v-model="form.role_id"
                  :items="roles"
                  item-title="name"
                  item-value="id"
                  label="Pilih Role"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.role_id"
                  required
                />
              </v-col>
              <v-col cols="12" md="6" class="py-1">
                <v-text-field
                  v-model="form.password"
                  label="Password"
                  type="password"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="errors.password"
                  required
                />
              </v-col>
            </v-row>
          </div>

          <div class="flex justify-end gap-3 items-center">
            <v-btn variant="text" class="!text-gray-500 rounded-xl px-5" height="44" @click="$emit('update:modelValue', false)">
              Batal
            </v-btn>
            <v-btn type="submit" class="apply-btn min-w-[140px]" :loading="isSubmitting" :disabled="isSubmitting">
              <v-icon left size="18" class="mr-1" icon="mdi-content-save-outline" />
              Simpan User
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
  roles: Array
 })

const emit = defineEmits(['update:modelValue'])

const page = usePage()
const errors = computed(() => page.props.errors)

const isSubmitting = ref(false)
const form = ref({
  name: '',
  email: '',
  role_id: null,
  password: ''
})

// Reset form ketika dialog dibuka
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    page.props.errors = {}
    form.value = {
      name: '',
      email: '',
      role_id: null,
      password: ''
    }
  }
})

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const submitForm = () => {
  isSubmitting.value = true
  router.post('/master/user', form.value, {
    onSuccess: () => {
      if (page.props.flash?.error) return
      emit('update:modelValue', false)
    },
    onError: () => {
      // tambahan pesan atau validasi error dari server jika ada
    },
    onFinish: () => {
      isSubmitting.value = false
    }
  })
}
</script>
