<template>
  <GuestLayout>
    <div class="auth-page">
      <div class="auth-card">
        <div class="auth-header">
          <div class="auth-logo-badge">
            <v-icon size="28" icon="mdi-store" color="white" />
          </div>
          <h1 class="auth-title">Sistem Informasi Internal</h1>
          <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
        </div>

        <v-form ref="formRef" @submit.prevent="submit" class="auth-form">
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

          <div class="d-flex justify-space-between align-center flex-wrap auth-form-options">
            <v-checkbox
              v-model="form.remember"
              label="Ingat saya"
              color="indigo-darken-2"
              density="compact"
              hide-details
            />
            <a class="auth-link" @click.prevent="router.visit('/password-reset')" v-if="canResetPassword" href="#">
              Lupa password?
            </a>
          </div>

          <button type="submit" class="auth-submit-btn" :disabled="processing">
            <span v-if="!processing">Masuk</span>
            <span v-else class="auth-btn-loading">
              <v-progress-circular indeterminate size="18" width="2" color="white" />
              Memproses...
            </span>
          </button>
        </v-form>

        <p class="auth-switch-text mt-6">
          Belum punya akun?
          <a class="auth-link" @click.prevent="router.visit('/register')" href="#">Daftar sekarang</a>
        </p>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const page = usePage()
const formRef = ref(null)
const showPassword = ref(false)
const processing = ref(false)

const form = useForm({ email: '', password: '', remember: false })
const errors = computed(() => page.props.errors || {})
const canResetPassword = computed(() => page.props.canResetPassword || false)

const submit = () => {
  processing.value = true
  form.post('/login', { onFinish: () => (processing.value = false) })
}
</script>