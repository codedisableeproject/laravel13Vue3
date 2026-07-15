<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title>
        <h2 class="text-xl font-bold">Edit User</h2>
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
                label="Password (isi jika ingin diubah)"
                type="password"
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
import { ref, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  user: Object
})

const form = ref({
  name: '',
  email: '',
  password: ''
})

onMounted(() => {
  form.value.name = props.user.name
  form.value.email = props.user.email
})

const submitForm = () => {
  router.put(`/master/user/${props.user.id}`, form.value)
}
</script>