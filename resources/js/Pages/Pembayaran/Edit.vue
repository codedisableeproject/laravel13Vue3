<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title>
        <h2 class="text-xl font-bold">Edit Pembayaran</h2>
      </v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submitForm">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.kode_pembayaran"
                label="Kode Pembayaran"
                disabled
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-menu v-model="dateMenu" :close-on-content-click="false">
                <template v-slot:activator="{ props }">
                  <v-text-field
                    v-bind="props"
                    v-model="form.tanggal_pembayaran"
                    label="Tanggal Pembayaran"
                    prepend-icon="mdi-calendar"
                    readonly
                    required
                  />
                </template>
                <v-date-picker v-model="form.tanggal_pembayaran" @update:model-value="dateMenu = false" />
              </v-menu>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.penjualan_kode"
                label="Penjualan"
                disabled
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model.number="form.nilai_bayar"
                label="Nilai Bayar"
                type="number"
                min="1"
                required
              />
            </v-col>
          </v-row>

          <div class="flex justify-end gap-2">
            <Link href="/pembayaran">
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
  pembayaran: Object,
  penjualans: Array
})

const dateMenu = ref(false)
const form = ref({
  kode_pembayaran: '',
  tanggal_pembayaran: '',
  penjualan_id: null,
  penjualan_kode: '',
  nilai_bayar: 0
})

onMounted(() => {
  form.value = {
    kode_pembayaran: props.pembayaran.kode_pembayaran,
    tanggal_pembayaran: props.pembayaran.tanggal_pembayaran,
    penjualan_id: props.pembayaran.penjualan_id,
    penjualan_kode: props.pembayaran.penjualan.kode_penjualan,
    nilai_bayar: props.pembayaran.nilai_bayar
  }
})

const submitForm = () => {
  router.put(`/pembayaran/${props.pembayaran.id}`, {
    kode_pembayaran: form.value.kode_pembayaran,
    tanggal_pembayaran: form.value.tanggal_pembayaran,
    nilai_bayar: form.value.nilai_bayar
  })
}
</script>