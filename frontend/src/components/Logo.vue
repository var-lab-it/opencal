<template>
  <div
    v-if="logoUrl"
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

defineProps({
  logoUrl: {type: String, required: false, default: null},
})
</script>
