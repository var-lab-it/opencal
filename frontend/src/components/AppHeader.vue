<template>
  <nav class="navbar navbar-expand-lg bg-light sticky-top shadow-sm border-bottom">
    <div class="container-fluid">
      <router-link
        class="navbar-brand"
        to="/"
      >
        <font-awesome-icon icon="calendar-check" />
        OpenCal
      </router-link>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div
        id="navbarSupportedContent"
        class="collapse navbar-collapse"
      >
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link
              class="nav-link"
              to="/"
              active-class="active"
            >
              {{ $t('header.nav.dashboard') }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link
              class="nav-link"
              to="/appointments"
              active-class="active"
            >
              {{ $t('header.nav.appointments') }}
            </router-link>
          </li>
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <font-awesome-icon icon="user" />
              {{ user?.email || '' }}
            </a>
            <ul class="dropdown-menu dropdown-menu-lg-end">
              <li>
                <router-link
                  id="my-account-link"
                  to="/account"
                  class="nav-link"
                >
                  <font-awesome-icon icon="user" />
                  {{ $t('header.nav.account') }}
                </router-link>
              </li>
              <li class="dropdown-divider"></li>
              <li>
                <a
                  id="logout-link"
                  href="#"
                  class="nav-link"
                  @click="logout"
                >
                  <font-awesome-icon icon="sign-out" />
                  {{ $t('header.nav.logout') }}
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {User} from "../types/User";
import {logout as doLogout} from "../services/auth";

defineProps<{ user: User }>()

function logout() {
  doLogout();
  window.location.href = '/';
}
</script>
