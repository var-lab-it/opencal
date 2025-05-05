<template>
  <div class="login-container">
    <h3 class="mb-3">
      <font-awesome-icon icon="calendar-check" />
      OpenCal
    </h3>
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
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default defineComponent({
  name: 'Login',
  components: {FontAwesomeIcon},
  data() {
    return {
      email: '',
      password: '',
      error: '',
    };
  },
  methods: {
    async handleLogin() {
      this.error = '';
      try {
        const response = await apiClient.post('/auth', {
          email: this.email,
          password: this.password,
        });

        if (response.data && response.data.token) {
          const token = response.data.token;

          sessionStorage.setItem('jwtToken', token);
          window.location.href = '/';
        } else {
          this.error = 'No valid token.';
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
