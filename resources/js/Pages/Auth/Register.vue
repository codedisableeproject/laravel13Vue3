<template>
  <GuestLayout>
    <div class="auth-page">
      <div class="auth-card">
        <div class="auth-header">
          <div class="auth-logo-badge">
            <v-icon size="28" icon="mdi-account-plus-outline" color="white" />
          </div>
          <h1 class="auth-title">Daftar Akun Baru</h1>
          <p class="auth-subtitle">Isi formulir untuk membuat akun Anda</p>
        </div>

        <v-form ref="formRef" @submit.prevent="submit" class="auth-form">
          <div class="auth-input-group">
            <label class="auth-input-label">Nama Lengkap</label>
            <v-text-field
              v-model="form.name"
              placeholder="Masukkan nama lengkap"
              prepend-inner-icon="mdi-account-outline"
              variant="outlined"
              required
              :error-messages="errors.name"
              density="comfortable"
              hide-details="auto"
              single-line
            />
          </div>

          <div class="auth-input-group">
            <label class="auth-input-label">Email</label>
            <v-text-field
              v-model="form.email"
              placeholder="nama@email.com"
              prepend-inner-icon="mdi-email-outline"
              variant="outlined"
              type="email"
              required
              :error-messages="errors.email"
              density="comfortable"
              hide-details="auto"
              single-line
            />
          </div>

          <div class="auth-input-group">
            <label class="auth-input-label">Password</label>
            <v-text-field
              v-model="form.password"
              placeholder="Masukkan password"
              prepend-inner-icon="mdi-lock-outline"
              variant="outlined"
              :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? 'mdi-eye-outline' : 'mdi-eye-off-outline'"
              @click:append-inner="showPassword = !showPassword"
              required
              :error-messages="errors.password"
              density="comfortable"
              hide-details="auto"
              single-line
            />
          </div>

          <div class="auth-input-group">
            <label class="auth-input-label">Konfirmasi Password</label>
            <v-text-field
              v-model="form.password_confirmation"
              placeholder="Ulangi password"
              prepend-inner-icon="mdi-lock-check-outline"
              variant="outlined"
              :type="showPassword ? 'text' : 'password'"
              required
              :error-messages="errors.password_confirmation"
              density="comfortable"
              hide-details="auto"
              single-line
            />
          </div>

          <button type="submit" class="auth-submit-btn mt-2" :disabled="processing">
            <span v-if="!processing">Daftar Sekarang</span>
            <span v-else class="auth-btn-loading">
              <v-progress-circular indeterminate size="18" width="2" color="white" />
              Memproses...
            </span>
          </button>
        </v-form>

        <p class="auth-switch-text mt-6">
          Sudah punya akun?
          <a class="auth-link" @click.prevent="router.visit('/login')" href="#">Masuk sekarang</a>
        </p>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const page = usePage()
const toast = useToast()
const formRef = ref(null)
const showPassword = ref(false)
const processing = ref(false)

watch(
  () => page.props.flash,
  (newFlash) => {
    if (newFlash?.success) {
      toast.success(newFlash.success)
    }
    if (newFlash?.error) {
      toast.error(newFlash.error)
    }
  },
  { deep: true, immediate: true }
)

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const errors = computed(() => page.props.errors || {})

const submit = () => {
  processing.value = true
  form.post('/register', { 
    onSuccess: () => {
      if (page.props.flash?.error) return
      emit('update:modelValue', false)
    },
    onError: () => {
      // tambahan pesan atau validasi error dari server jika ada
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>