<template>
  <button
    class="btn btn-danger"
    @click="logout"
  >
    Logout
  </button>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { logout } from '../services/auth';

export default defineComponent({
  methods: {
    logout() {
      logout();
      this.$router.push('/login');
    },
  },
});
</script>
