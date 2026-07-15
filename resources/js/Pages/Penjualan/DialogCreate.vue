<template>
  <v-dialog
    v-model="dialogModel"
    max-width="900px"
    persistent
    @click:outside="$emit('update:modelValue', false)"
  >
    <v-card class="rounded-2xl shadow-xl">
      <v-card-title class="bg-indigo-800 text-white">
        <div class="flex justify-between items-center w-full">
          <div>
            <h2 class="text-xl font-bold">Tambah Penjualan Baru</h2>
            <p class="text-sm opacity-80">Buat invoice dan catat transaksi penjualan</p>
          </div>
          <v-btn icon color="white" @click="$emit('update:modelValue', false)" class="rounded-full">
            <v-icon size="24" color="black" icon="mdi-close" />
          </v-btn>
        </div>
      </v-card-title>

      <v-card-text class="p-6">
        <v-form @submit.prevent="submitForm">
          <!-- 1. INFORMASI UTAMA TRANSAKSI -->
          <div class="border border-gray-100 p-5 rounded-xl mb-6 bg-gray-50/30">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
              <v-icon color="indigo" size="20" icon="mdi-information-outline" />
              Informasi Utama
            </h3>
            <v-row>
              <v-col cols="12" md="6" class="py-1">
                <v-text-field
                  v-model="form.kode_penjualan"
                  label="Kode Penjualan"
                  disabled
                  variant="flat"
                  density="comfortable"
                  class="bg-slate-50 rounded-xl"
                  required
                />
              </v-col>
              <v-col cols="12" md="6" class="py-1">
                <v-menu v-model="dateMenu" :close-on-content-click="false">
                  <template v-slot:activator="{ props }">
                    <button type="button" v-bind="props" class="date-trigger h-[50px] !border-gray-200">
                      <v-icon size="18" class="mr-3 text-indigo-500" icon="mdi-calendar" />
                      <div class="text-left">
                        <div class="text-[10px] uppercase tracking-wide text-gray-400 leading-none mb-0.5">Tanggal Transaksi</div>
                        <div class="text-sm font-semibold text-gray-800">{{ form.tanggal_penjualan || 'Pilih Tanggal' }}</div>
                      </div>
                    </button>
                  </template>
                  <v-date-picker v-model="form.tanggal_penjualan" @update:model-value="dateMenu = false" />
                </v-menu>
              </v-col>
            </v-row>
          </div>

          <!-- 2. DAFTAR ITEMS -->
          <div class="border border-gray-100 rounded-xl overflow-hidden mb-6">
            <div class="p-5 flex justify-between items-center border-b border-gray-100 bg-slate-50/50">
              <div>
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                  <v-icon color="indigo" size="20" icon="mdi-basket-outline" />
                  Daftar Barang / Item
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Masukkan item yang dibeli beserta kuantitasnya</p>
              </div>
              <v-btn color="success" class="!h-9 text-xs rounded-lg font-bold" elevation="0" @click="addItem">
                <v-icon left size="16" class="mr-1" icon="mdi-plus" />
                Tambah Baris
              </v-btn>
            </div>

            <div class="p-5 bg-slate-50/30">
              <!-- Looping Baris Item dengan Desain Card Box yang Bersih -->
              <div 
                v-for="(item, index) in form.items" 
                :key="index" 
                class="bg-white border border-gray-100 rounded-xl p-4 mb-3 shadow-sm transition-all hover:border-indigo-100 relative group"
              >
                <!-- Label Penanda Urutan Baris Item -->
                <div class="absolute -left-2 top-4 bg-slate-200 text-slate-700 text-[10px] font-bold px-1.5 py-0.5 rounded font-mono">
                  #{{ index + 1 }}
                </div>

                <v-row align="center" class="ma-0">
                  <!-- Pilihan Barang -->
                  <v-col cols="12" md="4" class="pa-1">
                    <v-select
                      v-model="item.item_id"
                      :items="items.map(i => ({ title: i.nama, value: i.id, harga: i.harga }))"
                      label="Pilih Barang"
                      item-title="title"
                      item-value="value"
                      variant="outlined"
                      density="comfortable"
                      hide-details
                      required
                      @update:model-value="updateItemPrice(index)"
                    />
                  </v-col>

                  <!-- Kuantitas -->
                  <v-col cols="12" sm="4" md="2" class="pa-1">
                    <v-text-field
                      v-model.number="item.qty"
                      label="Qty"
                      type="number"
                      min="1"
                      variant="outlined"
                      density="comfortable"
                      hide-details
                      required
                      class="text-center"
                      @input="calculateTotal(index)"
                    />
                  </v-col>

                  <!-- Harga Satuan -->
                  <v-col cols="12" sm="4" md="3" class="pa-1">
                    <v-text-field
                      v-model="item.price"
                      label="Harga Satuan"
                      prefix="Rp"
                      variant="outlined"
                      density="comfortable"
                      hide-details
                      readonly
                      class="bg-slate-50/50 tabular-nums"
                    />
                  </v-col>

                  <!-- Total Harga per Baris -->
                  <v-col cols="12" sm="4" md="2" class="pa-1">
                    <v-text-field
                      :model-value="formatNumber(item.total_price)"
                      label="Total"
                      prefix="Rp"
                      variant="outlined"
                      density="comfortable"
                      hide-details
                      readonly
                      class="bg-slate-50/50 font-semibold text-gray-900 tabular-nums"
                    />
                  </v-col>

                  <!-- Tombol Hapus Baris -->
                  <v-col cols="12" md="1" class="pa-1 flex justify-end">
                    <button 
                      type="button" 
                      class="action-icon-btn text-rose-600 hover:bg-rose-50 border-rose-100 disabled:opacity-30 disabled:pointer-events-none"
                      title="Hapus Baris"
                      :disabled="form.items.length === 1"
                      @click="removeItem(index)"
                    >
                      <v-icon size="16" icon="mdi-delete" />
                    </button>
                  </v-col>
                </v-row>
              </div>
              
              <!-- Ringkasan Grand Total di Bagian Bawah -->
              <div class="mt-4 p-4 bg-white border border-dashed border-gray-200 rounded-xl flex justify-between items-center px-6">
                <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Grand Total Penjualan</span>
                <span class="text-xl font-extrabold text-indigo-600 tabular-nums">
                  Rp {{ formatNumber(grandTotal) }}
                </span>
              </div>
            </div>
          </div>

          <!-- 3. ACTION FOOTER -->
          <div class="flex justify-end gap-3 items-center">
            <v-btn variant="text" class="!text-gray-500 rounded-xl px-5" height="44" @click="$emit('update:modelValue', false)">
              Batal
            </v-btn>
            <button type="submit" class="apply-btn min-w-[140px]">
              <v-icon left size="18" class="mr-1" icon="mdi-content-save-outline" />
              Simpan Transaksi
            </button>
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
  items: Array,
  kodePenjualan: String
})

const emit = defineEmits(['update:modelValue'])

const dateMenu = ref(false)
const form = ref({
  kode_penjualan: props.kodePenjualan,
  tanggal_penjualan: new Date().toISOString().split('T')[0],
  items: [
    { item_id: null, qty: 1, price: 0, total_price: 0 }
  ]
})

// Reset form ketika dialog dibuka
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    form.value = {
      kode_penjualan: props.kodePenjualan,
      tanggal_penjualan: new Date().toISOString().split('T')[0],
      items: [
        { item_id: null, qty: 1, price: 0, total_price: 0 }
      ]
    }
  }
})

// Menghitung Grand Total secara reaktif dari seluruh baris item
const grandTotal = computed(() => {
  return form.value.items.reduce((sum, item) => sum + (item.total_price || 0), 0)
})

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const addItem = () => {
  form.value.items.push({ item_id: null, qty: 1, price: 0, total_price: 0 })
}

const removeItem = (index) => {
  if (form.value.items.length > 1) {
    form.value.items.splice(index, 1)
  }
}

const updateItemPrice = (index) => {
  const selectedItem = props.items.find(i => i.id === form.value.items[index].item_id)
  if (selectedItem) {
    form.value.items[index].price = selectedItem.harga
    calculateTotal(index)
  }
}

const calculateTotal = (index) => {
  const item = form.value.items[index]
  item.total_price = (item.qty || 0) * (item.price || 0)
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID').format(num || 0)
}

const submitForm = () => {
  router.post('/penjualan', form.value, {
    onFinish: () => {
      emit('update:modelValue', false)
    }
  })
}
</script>
