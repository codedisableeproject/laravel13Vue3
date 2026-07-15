<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title>
        <h2 class="text-xl font-bold">Tambah User</h2>
      </v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submitForm">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.name"
                label="Nama"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.email"
                label="Email"
                type="email"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.password"
                label="Password"
                type="password"
                required
              />
            </v-col>
          </v-row>

          <div class="flex justify-end gap-2">
            <Link href="/master/user">
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

const form = ref({
  name: '',
  email: '',
  password: ''
})

const submitForm = () => {
  router.post('/master/user', form.value)
}
</script>