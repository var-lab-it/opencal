<template>
  <div class="row mt-xl-5 mt-0">
    <div class="offset-xl-4 col-xl-4 offset-lg-3 col-lg-6 col-md-12">
      <Logo :logo-url="logoUrl" />

      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h3>
            {{ $t('password_reset.headline') }}
          </h3>

          <div
            v-if="success"
            class="alert alert-success"
          >
            {{ $t('password_reset.message_requested') }}
          </div>
          <div
            v-if="error"
            class="alert alert-danger"
          >
            {{ error }}
          </div>

          <form @submit.prevent="handlePasswordReset">
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
              <label for="email">Password:</label>
              <input
                id="password"
                v-model="password"
                type="password"
                class="form-control"
                required
                data-testid="password-input"
              />
            </div>
            <div class="form-group mb-3">
              <label for="email">Password (repeat):</label>
              <input
                id="password_repeat"
                v-model="passwordRepeat"
                type="password"
                class="form-control"
                required
                data-testid="password-repeat-input"
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
                {{ $t('password_reset.submit_button') }}
              </button>
            </div>
            <div class="mb-1">
              <router-link to="/dashboard/login">
                {{ $t('login.button') }}
              </router-link>
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
import {requestPassword, resetPassword} from "../services/passwordReset";
import {useI18n} from "vue-i18n";
import {useRoute} from "vue-router";

const {t} = useI18n()

const logoUrl = ref(import.meta.env.VITE_LOGO_URL || null);

const $route = useRoute();
const token = String($route.params.token);
const emailFromUrl = String($route.params.email);

const loadSubmit = ref(false);
const email = ref('');
const password = ref('');
const passwordRepeat = ref('');

const error = ref('');
const success = ref('');

async function handlePasswordReset() {
  error.value = '';
  loadSubmit.value = true;

  console.log(email.value, emailFromUrl);

  if (emailFromUrl !== email.value) {
    error.value = t('password_reset.errors.email_invalid');
    loadSubmit.value = false;
    return;
  }
  if (passwordRepeat.value !== password.value) {
    error.value = t('password_reset.errors.password_dont_match');
    loadSubmit.value = false;
    return;
  }

  try {
    await resetPassword(email.value, token, password.value);

    success.value = t('password_reset.message_changed');
  } catch (err) {
    console.error("Request failed:", err);
    error.value = "An error occurred. Please check your email and try again.";
  } finally {
    loadSubmit.value = false;
  }
}
</script>
