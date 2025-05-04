<template>
  <div class="login-container">
    <form @submit.prevent="handleLogin">
      <div class="form-group mb-3">
        <label for="email">
          {{ $t('login.email') }}
        </label>
        <input
          id="email"
          v-model="email"
          type="email"
          class="form-control"
          required
        >
      </div>
      <div class="form-group mb-4">
        <label for="password">
          {{ $t('login.password') }}
        </label>
        <input
          id="password"
          v-model="password"
          type="password"
          class="form-control"
          required
        >
      </div>
      <button
        type="submit"
        class="btn btn-primary w-100"
      >
        {{ $t('login.button') }}
      </button>
    </form>
    <p
      v-if="error"
      class="text-danger mt-3"
    >
      {{ error }}
    </p>
  </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import apiClient from '../services/api';

export default defineComponent({
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      error: '',
    };
  },
  methods: {
    async handleLogin() {
      this.error = ''; // Fehler-Reset
      try {
        // Sende die POST-Anfrage mit den Login-Daten
        const response = await apiClient.post('/auth', {
          email: this.email,
          password: this.password,
        });

        // Überprüfen, ob Token im Backend zurückgegeben wird
        if (response.data && response.data.token) {
          const token = response.data.token;

          // Speichere den JWT im LocalStorage
          sessionStorage.setItem('jwtToken', token);

          // Weiterleitung nach erfolgreichem Login (z. B. zur Startseite)
          this.$router.push('/dashboard');
        } else {
          this.error = 'Kein gültiges Token erhalten.';
        }
      } catch (error: any) {
        console.error('Login failed:', error);
        this.error = 'Login failed. Please try again.';
      }
    },
  },
});
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 50px auto;
}
</style>
