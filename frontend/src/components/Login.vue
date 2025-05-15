<template>
  <div class="login-form">
    <h2>Login</h2>
    <form @submit.prevent="handleLogin">
      <div class="form-group mb-3">
        <label for="email">Email:</label>
        <input
          id="email"
          v-model="email"
          type="email"
          class="form-control"
          required
          data-testid="email-input"
        />
      </div>
      <div class="form-group mb-3">
        <label for="password">Password:</label>
        <input
          id="password"
          v-model="password"
          type="password"
          class="form-control"
          required
          data-testid="password-input"
        />
      </div>
      <div class="d-grid">
        <button
          type="submit"
          class="btn btn-primary"
          data-testid="login-btn"
        >
          Login
        </button>
      </div>
    </form>
    <p
      v-if="error"
      class="text-danger mt-3"
    >
      {{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
import {ref} from "vue";
import apiClient from "../services/api";
import {redirectAfterLogin} from "../services/auth";

const email = ref('');
const password = ref('');
const error = ref('');

async function handleLogin() {
  error.value = '';
  try {
    const response = await apiClient.post('/auth', {
      email: email.value,
      password: password.value,
    });

    if (response.data && response.data.token) {
      const token = response.data.token;

      sessionStorage.setItem('jwtToken', token);
      redirectAfterLogin();
    } else {
      error.value = 'No valid token.';
    }
  } catch (err) {
    console.error("Login failed:", err);
    error.value = "Login failed. Please try again.";
  }
}
</script>

<style scoped>
.login-form {
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
}
</style>
