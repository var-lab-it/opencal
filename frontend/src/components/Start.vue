<template>
  <div class="row mt-xl-5 mt-0">
    <div class="offset-xl-4 col-xl-4 offset-lg-3 col-lg-6 col-md-12">
      <Logo :logo-url="logoUrl" />

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h3>
            {{ $t('start.headline') }}
          </h3>
          <div class="alert alert-info">
            {{ $t('start.message') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Logo from "./Logo.vue";
import {ref} from "vue";
const logoUrl = ref(import.meta.env.VITE_LOGO_URL || null);
</script>
