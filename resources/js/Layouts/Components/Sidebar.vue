<template>
  <v-navigation-drawer v-model="drawer" class="bg-indigo-darken-4 text-white" elevation="2">
    
    <v-list class="pa-4 text-center">
      <v-avatar size="64" class="mb-2 bg-white text-indigo-darken-4 font-weight-bold">
        ADM
      </v-avatar>
      <div class="text-h6 font-weight-bold">{{ $page.props.auth.user?.name || 'guest' }}</div>
      <div class="text-caption text-indigo-lighten-3">{{ $page.props.auth.user?.email || 'guest Email' }}</div>
    </v-list>

    <v-divider class="border-opacity-25"></v-divider>

    <v-list density="comfortable" nav>
      
      <template v-for="(menu, index) in $page.props.menus" :key="index">
        <v-list-group 
          v-if="menu.children"
          :prepend-icon="menu.icon" 
          :title="menu.title"
          color="white"
          class="text-white"
        >
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props"></v-list-item>
          </template>
          
          <Link 
            v-for="(child, childIndex) in menu.children" 
            :key="childIndex"
            :href="child.url" 
            prefetch 
            class="text-decoration-none text-white"
          >
            <v-list-item 
              :prepend-icon="child.icon" 
              :title="child.title" 
              :value="child.title"
              :active="$page.url === child.url"
              color="white"
            ></v-list-item>
          </Link>
        </v-list-group>
        
        <Link 
          v-else
          :href="menu.url" 
          prefetch 
          class="text-decoration-none text-white"
        >
          <v-list-item 
            :prepend-icon="menu.icon" 
            :title="menu.title" 
            :value="menu.title"
            :active="$page.url === menu.url"
            color="white"
          ></v-list-item>
        </Link>
      </template>

    </v-list>
  </v-navigation-drawer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const drawer = defineModel();
</script>