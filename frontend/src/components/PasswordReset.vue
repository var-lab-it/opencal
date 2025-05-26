<template>
  <div class="row mt-xl-5 mt-0">
    <div class="offset-xl-4 col-xl-4 offset-lg-3 col-lg-6 col-md-12">
      <Logo :logo-url="logoUrl"/>

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h3>
            {{$t('password_reset.headline')}}
          </h3>

          <form @submit.prevent="handlePasswordRequest">
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

            <div class="d-grid mb-3">
              <button
                  type="submit"
                  class="btn btn-primary"
                  data-testid="login-btn"
              >
              <span
                  v-if="loadSubmit"
                  class="spinner-grow text-light spinner-grow-sm"
                  role="status"
              ></span>
                {{$t('password_reset.submit_button')}}
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Logo from "./Logo.vue";
import {ref} from "vue";
import apiClient from "../services/api";
import {redirectAfterLogin} from "../services/auth";
import {requestPassword} from "../services/passwordReset";

const logoUrl = ref(import.meta.env.VITE_LOGO_URL || null);

const loadSubmit = ref(false);
const email = ref('');
const error = ref('');

async function handlePasswordRequest() {
  error.value = '';
  loadSubmit.value = true;

  try {
    const response = await requestPassword(email.value);

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
  } finally {
    loadSubmit.value = false;
  }
}
</script>
