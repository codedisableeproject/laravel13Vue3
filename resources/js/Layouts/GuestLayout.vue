<template>
  <v-app class="bg-slate-50">
    <v-main class="d-flex align-center justify-center">
      <v-container class="py-12">
        <v-row justify="center">
          <v-col cols="12" sm="10" md="8" lg="6">
            <slot />
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
</script>
