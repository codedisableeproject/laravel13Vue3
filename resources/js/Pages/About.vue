<template>
  <AppLayout>
    <v-card class="pa-5" rounded="lg" flat border>
      <v-card-title class="text-h4 text-teal px-0">
        Halaman Tentang Kami
      </v-card-title>

      <v-card-text class="px-0 mt-3">
        <p class="text-body-1 text-grey-darken-1 mb-4">
          Saat kamu berpindah ke halaman ini melalui tombol di navbar atas, perhatikan bahwa **tidak ada reload halaman sama sekali**! Browser tidak berkedip, melainkan langsung berganti konten dengan mulus.
        </p>

        <v-sheet class="pa-4 bg-teal-lighten-5 text-teal-darken-3 rounded-lg" border>
          <h3 class="text-subtitle-1">
            Nilai Counter Pinia tetap terjaga: <strong>{{ counterStore.count }}</strong>
          </h3>
        </v-sheet>
      </v-card-text>
    </v-card>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { useCounterStore } from '../store/counter';

const counterStore = useCounterStore();
</script>