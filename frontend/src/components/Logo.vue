<template>
  <div
    v-if="logoUrl !== null"
    class="text-center pt-4 pb-5"
  >
    <img
      :src="logoUrl"
      width="200px"
      alt="Logo"
    />
  </div>
</template>

<script setup lang="ts">
import {ref} from 'vue';

const logoUrl = ref(import.meta.env.VITE_LOGO_URL || null);
</script>
