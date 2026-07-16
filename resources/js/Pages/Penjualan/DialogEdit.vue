<template>
  <v-dialog
    v-model="dialogModel"
    max-width="900px"
    persistent
    @click:outside="$emit('update:modelValue', false)"
  >
    <v-card class="rounded-2xl shadow-xl">
      <v-card-title class="bg-amber-800 text-white">
        <div class="flex justify-between items-center w-full">
          <div>
            <h2 class="text-xl font-bold">Edit Penjualan</h2>
          </div>
          <v-btn icon color="white" @click="$emit('update:modelValue', false)" class="rounded-full">
            <v-icon size="24" color="black" icon="mdi-close" />
          </v-btn>
        </div>
      </v-card-title>

      <v-card-text class="p-6">
        <v-form @submit.prevent="submitForm">
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
                  <v-btn type="button" v-bind="props" class="date-trigger h-[50px] !border-gray-200">
                    <v-icon size="18" class="mr-3 text-indigo-500" icon="mdi-calendar" />
                    <div class="text-left">
                      <div class="text-[10px] uppercase tracking-wide text-gray-400 leading-none mb-0.5">Tanggal Penjualan</div>
                      <div class="text-sm font-semibold text-gray-800">{{ form.tanggal_penjualan || 'Pilih Tanggal' }}</div>
                    </div>
                  </v-btn>
                </template>
                <v-date-picker v-model="form.tanggal_penjualan" @update:model-value="dateMenu = false" />
              </v-menu>
            </v-col>
          </v-row>

          <div class="mb-4 mt-4">
            <div class="flex justify-between items-center mb-2">
              <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <v-icon color="indigo" size="20" icon="mdi-basket-outline" />
                Daftar Barang / Item
              </h3>
              <v-btn color="success" class="!h-9 text-xs rounded-lg font-bold" elevation="0" @click="addItem">
                <v-icon left size="16" class="mr-1" icon="mdi-plus" />
                Tambah Baris
              </v-btn>
            </div>

            <div class="bg-slate-50/30">
              <div
                v-for="(item, index) in form.items"
                :key="index"
                class="bg-white border border-gray-100 rounded-xl p-4 mb-3 shadow-sm transition-all hover:border-indigo-100 relative group"
              >
                <div class="absolute -left-2 top-4 bg-slate-200 text-slate-700 text-[10px] font-bold px-1.5 py-0.5 rounded font-mono">
                  #{{ index + 1 }}
                </div>

                <v-row align="center" class="ma-0">
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

                  <v-col cols="12" md="1" class="pa-1 flex justify-end">
                    <v-btn
                      type="button"
                      class="action-icon-btn text-rose-600 hover:bg-rose-50 border-rose-100 disabled:opacity-30 disabled:pointer-events-none"
                      title="Hapus Baris"
                      :disabled="form.items.length === 1"
                      @click="removeItem(index)"
                    >
                      <v-icon size="16" icon="mdi-delete" />
                    </v-btn>
                  </v-col>
                </v-row>
              </div>

              <div class="mt-4 p-4 bg-white border border-dashed border-gray-200 rounded-xl flex justify-between items-center px-6">
                <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Grand Total Penjualan</span>
                <span class="text-xl font-extrabold text-indigo-600 tabular-nums">
                  Rp {{ formatNumber(grandTotal) }}
                </span>
              </div>
            </div>
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
  items: Array,
  penjualan: Object
})

const emit = defineEmits(['update:modelValue'])

const dateMenu = ref(false)
const form = ref({
  kode_penjualan: '',
  tanggal_penjualan: '',
  items: []
})

// Reset form when dialog opens
watch(() => props.modelValue, (newVal) => {
  if (newVal && props.penjualan) {
    form.value = {
      kode_penjualan: props.penjualan.kode_penjualan,
      tanggal_penjualan: props.penjualan.tanggal_penjualan,
      items: props.penjualan.items.map(i => ({
        item_id: i.item_id,
        qty: i.qty,
        price: i.price,
        total_price: i.total_price
      }))
    }
  }
})

// Menghitung Grand Total secara reaktif
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
  router.put(`/penjualan/${props.penjualan.id}`, form.value, {
    onFinish: () => {
      emit('update:modelValue', false)
    }
  })
}
</script>
