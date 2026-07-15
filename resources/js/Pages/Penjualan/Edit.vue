<template>
  <AppLayout>
    <v-card class="mb-6">
      <v-card-title>
        <h2 class="text-xl font-bold">Edit Penjualan</h2>
      </v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submitForm">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.kode_penjualan"
                label="Kode Penjualan"
                disabled
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-menu v-model="dateMenu" :close-on-content-click="false">
                <template v-slot:activator="{ props }">
                  <v-text-field
                    v-bind="props"
                    v-model="form.tanggal_penjualan"
                    label="Tanggal Penjualan"
                    prepend-icon="mdi-calendar"
                    readonly
                    required
                  />
                </template>
                <v-date-picker v-model="form.tanggal_penjualan" @update:model-value="dateMenu = false" />
              </v-menu>
            </v-col>
          </v-row>

          <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
              <h3 class="text-lg font-semibold">Items</h3>
              <v-btn color="success" @click="addItem">
                <v-icon left>mdi-plus</v-icon>
                Tambah Item
              </v-btn>
            </div>

            <v-card v-for="(item, index) in form.items" :key="index" class="mb-2 p-4">
              <v-row align="center">
                <v-col cols="12" md="4">
                  <v-select
                    v-model="item.item_id"
                    :items="items.map(i => ({ title: i.nama, value: i.id, harga: i.harga }))"
                    label="Item"
                    item-title="title"
                    item-value="value"
                    required
                    @update:model-value="updateItemPrice(index)"
                  />
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model.number="item.qty"
                    label="Qty"
                    type="number"
                    min="1"
                    required
                    @input="calculateTotal(index)"
                  />
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="item.price"
                    label="Harga"
                    type="number"
                    readonly
                  />
                </v-col>
                <v-col cols="12" md="1">
                  <v-text-field
                    v-model="item.total_price"
                    label="Total"
                    type="number"
                    readonly
                  />
                </v-col>
                <v-col cols="12" md="1">
                  <v-btn color="error" @click="removeItem(index)" icon>
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </v-col>
              </v-row>
            </v-card>
          </div>

          <div class="flex justify-end gap-2">
            <Link href="/penjualan">
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
  items: Array,
  penjualan: Object
})

const dateMenu = ref(false)
const form = ref({
  kode_penjualan: '',
  tanggal_penjualan: '',
  items: []
})

onMounted(() => {
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
})

const addItem = () => {
  form.value.items.push({ item_id: null, qty: 1, price: 0, total_price: 0 })
}

const removeItem = (index) => {
  form.value.items.splice(index, 1)
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
  item.total_price = item.qty * item.price
}

const submitForm = () => {
  router.put(`/penjualan/${props.penjualan.id}`, form.value)
}
</script>