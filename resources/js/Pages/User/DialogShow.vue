<template>
  <v-dialog
    v-model="dialogModel"
    max-width="600px"
    persistent
    @click:outside="$emit('update:modelValue', false)"
  >
    <v-card class="rounded-2xl shadow-xl">
      <v-card-title class="bg-indigo-800 text-white">
        <div class="flex justify-between items-center w-full">
          <div>
            <h2 class="text-xl font-bold">Detail User</h2>
          </div>
          <v-btn icon color="white" @click="$emit('update:modelValue', false)" class="rounded-full">
            <v-icon size="24" color="black" icon="mdi-close" />
          </v-btn>
        </div>
      </v-card-title>

      <v-card-text class="p-6">
        <div class="border border-gray-100 p-5 rounded-xl mb-6 bg-gray-50/30">
          <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
            <v-icon color="indigo" size="20" icon="mdi-account" />
            Informasi User
          </h3>
          <v-row>
            <v-col cols="12" md="6" class="py-1">
              <p class="mb-2 text-sm text-gray-700">
                <strong class="text-gray-900">Nama:</strong> {{ user.name }}
              </p>
              <p class="mb-2 text-sm text-gray-700">
                <strong class="text-gray-900">Email:</strong> {{ user.email }}
              </p>
            </v-col>
          </v-row>
        </div>
      </v-card-text>

      <v-card-actions class="px-6 pb-6 justify-end">
        <v-btn variant="text" class="!text-gray-500 rounded-xl px-5" height="44" @click="$emit('update:modelValue', false)">
          Tutup
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  user: Object
})

const emit = defineEmits(['update:modelValue'])

const dialogModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})
</script>
