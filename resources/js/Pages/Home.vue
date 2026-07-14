<template>
  <AppLayout>
    <v-card class="pa-5" rounded="lg" flat border>
      <v-card-title class="text-h4 text-primary px-0">
        {{ judul_dari_laravel }}
      </v-card-title>

      <v-card-text class="px-0 mt-3">
        <p class="text-body-1 text-grey-darken-1">
          Selamat datang di halaman utama! Ini adalah bagian konten dinamis yang berada di dalam slot Layout.
        </p>

        <v-sheet class="pa-4 mt-5 bg-grey-lighten-4 rounded-lg" border>
          <h3 class="text-subtitle-1 mb-2">Counter Global (Pinia Store): {{ counterStore.count }}</h3>
          <v-btn color="primary" prepend-icon="mdi-plus" @click="counterStore.increment">
            Tambah Angka
          </v-btn>
        </v-sheet>
      </v-card-text>
    </v-card>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue'; // Import layout
import { useCounterStore } from '../store/counter';

defineProps({
  judul_dari_laravel: String
});

const counterStore = useCounterStore();
</script>