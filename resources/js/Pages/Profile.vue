<template>
  <AppLayout>
    <v-row>
      <v-col cols="12" md="4">
        <v-card border flat rounded="lg" class="text-center pa-6">
          <v-avatar color="indigo" size="90" class="mb-4">
            <span class="text-white text-h3">A</span>
          </v-avatar>
          <div class="text-h5 font-weight-bold">Administrator</div>
          <div class="text-subtitle-1 text-grey">Super Admin</div>
          <v-divider class="my-4"></v-divider>
          
          <div class="text-subtitle-2 text-indigo-darken-3 bg-indigo-lighten-5 pa-3 rounded-lg">
            State Pinia Terjaga: <strong>{{ counterStore.count }}</strong>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" md="8">
        <v-card border flat rounded="lg" class="pa-6">
          <div class="text-h6 font-weight-bold text-indigo-darken-4 mb-4">Pengaturan Profil</div>
          
          <v-form>
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field label="Nama Lengkap" model-value="Admin Utama" variant="outlined" density="comfortable"></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field label="Kontak Telepon" model-value="+62812345678" variant="outlined" density="comfortable"></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field label="Alamat Email" model-value="admin@example.com" variant="outlined" density="comfortable"></v-text-field>
              </v-col>
            </v-row>
            <v-btn color="indigo" class="mt-4" prepend-icon="mdi-content-save">
              Simpan Perubahan
            </v-btn>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </AppLayout>
</template>

<script setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { useCounterStore } from '../store/counter';

const counterStore = useCounterStore();
</script>