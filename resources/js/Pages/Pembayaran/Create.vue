<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title>
        <h2 class="text-xl font-bold">Tambah Pembayaran</h2>
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
            <v-col cols="12" md="12">
              <v-select
                v-model="form.penjualan_id"
                :items="penjualans.map(p => ({ title: `${p.kode_penjualan} - Rp ${formatNumber(p.total)}`, value: p.id }))"
                label="Penjualan"
                item-title="title"
                item-value="value"
                required
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
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  penjualans: Array,
  kodePembayaran: String
})

const dateMenu = ref(false)
const form = ref({
  kode_pembayaran: props.kodePembayaran,
  tanggal_pembayaran: new Date().toISOString().split('T')[0],
  penjualan_id: null,
  nilai_bayar: 0
})

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const submitForm = () => {
  router.post('/pembayaran', form.value)
}
</script>