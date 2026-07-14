<template>
  <v-app id="inspire">
    
    <Sidebar v-model="drawer" />

    <v-app-bar flat border>
      <v-app-bar-nav-icon @click="drawer = !drawer" class="text-indigo-darken-4"></v-app-bar-nav-icon>
      
      <v-app-bar-title class="font-weight-bold text-indigo-darken-4">
        Sistem Informasi Internal
      </v-app-bar-title>

      <v-spacer></v-spacer>

      <v-menu min-width="200px" rounded>
        <template v-slot:activator="{ props }">
          <v-btn icon v-bind="props" class="mr-2">
            <v-avatar color="indigo" size="36">
              <span class="text-white text-subtitle-1">A</span>
            </v-avatar>
          </v-btn>
        </template>
        <v-card class="mt-2">
          <v-card-text>
            <div class="mx-auto text-center">
              <v-avatar color="indigo" size="48">
                <span class="text-white text-h6">A</span>
              </v-avatar>
              <h3 class="mt-2 text-subtitle-1">Admin Utama</h3>
              <p class="text-caption text-grey-darken-1">admin@example.com</p>
              <v-divider class="my-3"></v-divider>
              <v-btn variant="text" color="error" block prepend-icon="mdi-logout">
                Keluar
              </v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-menu>
    </v-app-bar>

    <v-main class="bg-grey-lighten-4">
      <v-container class="py-6 px-6" fluid>
        <slot />
      </v-container>
    </v-main>

  </v-app>
</template>

<script setup>
import { ref } from 'vue';
import Sidebar from './Components/Sidebar.vue'; // <-- Import komponen Sidebar

// State drawer tetap disimpan di parent (Layout) agar bisa diakses oleh tombol Header
const drawer = ref(true);
</script>